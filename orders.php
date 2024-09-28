<?php
session_start();
include 'connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email']; // Get the user's email from the session
$role = $_SESSION['role'];   // Get the user's role from the session
$username = $_SESSION['username'];   // Get the user's role from the session

// Fetch all orders from the database
$sql = $conn->query("
    SELECT orders.*, users.contact 
    FROM orders 
    JOIN users ON orders.email = users.email 
    ORDER BY orders.order_date DESC
");
$orders = $sql->fetch_all(MYSQLI_ASSOC);

// Handle order status update (Confirm/Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0; 
    $newStatus = $_POST['status']; // 'confirmed' or 'deleted'

    if ($orderId && ($newStatus === 'confirmed' || $newStatus === 'canceled')) {
        // Prepare the update statement
        $updateSql = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $updateSql->bind_param("si", $newStatus, $orderId);

        // Execute and check if the update was successful
        if ($updateSql->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }

        $updateSql->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
    $conn->close();
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include './templates/head.php';
    ?>
</head>

<body class="dashboard bg-black">
    <?php
    // Header
    include './templates/header.php';

    // Cart Drawer
    include './templates/cart_drawer.php';

    //Hidden Add Product Modal 
    include './templates/add_product_modal.php';
    ?>


    <main class="content ml-72 min-h-screen p-10">

        <h1 class="text-white text-3xl font-bold mb-6">Orders</h1>
        <table class="table-auto w-full bg-transparent text-white text-center border border-gray-700">
            <thead>
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Date</th>
                    <th class="border px-4 py-2">Contact</th>
                    <th class="border px-4 py-2">Total</th>
                    <th class="border px-4 py-2">Items</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Assuming you fetched the $orders from the database earlier

                // In your table or wherever you're displaying the items, decode the JSON and display
                foreach ($orders as $order) {
                    echo '<tr>';
                    echo '<td class="border px-4 py-2">' . htmlspecialchars($order['id']) . '</td>';
                    echo '<td class="border px-4 py-2">' . htmlspecialchars($order['email']) . '</td>';
                    echo '<td class="border px-4 py-2">' . htmlspecialchars($order['order_date']) . '</td>';
                    echo '<td class="border px-4 py-2">' . htmlspecialchars($order['contact']) . '</td>';
                    echo '<td class="border px-4 py-2">$' . htmlspecialchars($order['total']) . '</td>';
                    
                    // Decode the JSON in the 'items' column
                    $items = json_decode($order['items'], true);

                    // Check if the JSON was successfully decoded
                    if (is_array($items)) {
                        echo '<td class="border px-4 pt-8 pb-2">';
                        foreach ($items as $item) {
                            echo 'Name: ' . htmlspecialchars($item['name']) . '<br>';
                            echo 'Price: $' . htmlspecialchars($item['price']) . '<br>';
                            echo 'Quantity: ' . htmlspecialchars($item['quantity']) . '<br><br>';
                        }
                        echo '</td>';
                    } else {
                        // In case JSON decoding fails, fallback to the raw data
                        echo '<td>Invalid item data</td>';
                    }

                    // Continue with status and action buttons
                    echo '<td class="border px-4 py-2" id="status-' . htmlspecialchars($order['id']) . '">';
                    if ($order['status'] === 'confirmed') {
                        echo '<span class="text-green-500">Confirmed</span>';
                    } elseif ($order['status'] === 'canceled') {
                        echo '<span class="text-red-500">Canceled</span>';
                    } else {
                        echo '<span class="text-yellow-500">Pending</span>';
                    }
                    echo '</td>';

                    echo '<td class="border px-4 py-2" id="action-' . htmlspecialchars($order['id']) . '">';
                    if ($order['status'] === 'pending') {
                        echo '<button onclick="updateOrderStatus(' . htmlspecialchars($order['id']) . ', \'confirmed\')" class="block mx-auto bg-green-500 text-white px-4 py-2 rounded">Confirm</button>';
                        echo '<button onclick="updateOrderStatus(' . htmlspecialchars($order['id']) . ', \'canceled\')" class="block mx-auto mt-2 bg-red-500 text-white px-4 py-2 rounded">Cancel</button>';
                    }
                    echo '</td>';
                    
                    echo '</tr>';
                }
                ?>

            </tbody>
        </table>
    </main>

    <script>
        function updateOrderStatus(orderId, status) {
            fetch('orders.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `order_id=${orderId}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`status-${orderId}`).innerHTML = 
                        status === 'confirmed' 
                        ? '<span class="text-green-500">Confirmed</span>' 
                        : '<span class="text-red-500">Canceled</span>';
                    document.getElementById(`action-${orderId}`).innerHTML = '';
                } else {
                    alert('Error updating order status.');
                }
            });
        }
    </script>

    <!-- JavaScript -->
    <script src="./js/script.js"></script>
</body>
</html>
