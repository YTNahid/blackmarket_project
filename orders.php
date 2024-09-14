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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Dashboard</title>
    <link rel="stylesheet" href="./css/global-style.css">
    <link rel="stylesheet" href="./css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/fa69f7130e.js" crossorigin="anonymous"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "bg-color": '#212529',
                    }
                }
            }
        }
    </script>
</head>
<body class="dashboard bg-black">
    <header class="column sidebar bg-bg-color w-72 h-screen justify-between fixed">
        <div class="column">
            <p class="text-white opacity-25">Menu</p>
            <nav>
                <ul class="nav-menu space-y-3">
                    <li class=""><a href="./dashboard.php"><i class="fa-solid fa-house"></i>Dashboard</a></li>
                    <?php if ($role !== 'admin'): ?>
                        <li>
                            <a href="#" id="view-cart-btn" class="flex items-center">
                                <i class="fa-solid fa-cart-shopping"></i>
                                View Cart 
                                <div id="cart-count" class="count rounded-full ml-3 bg-red-600 flex items-center justify-center h-5 w-5">0</div>
                            </a>
                        </li>
                        <li><a href="./order_history.php"><i class="fa-solid fa-bag-shopping"></i> Order History</a></li>
                    <?php endif; ?>

                    <?php if ($role === 'admin'): ?>
                        <li><a href="./orders.php" class="open"><i class="fa-solid fa-bag-shopping"></i> Orders</a></li>
                        <li><a href="./users.php"><i class="fa-solid fa-users pr-0"></i>Users</a></li>
                        <li><a href="./add_product.php"><i class="fa-solid fa-plus"></i> Add Product</a></li>
                    <?php endif; ?>

                    <li class="border-t pt-4 mt-[50px]"><a href="./profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                    <li class=""><a href="./change_pass.php"><i class="fa-solid fa-unlock-keyhole"></i> Change Password</a></li>
                    <li class=""><a href="./change_number.php"><i class="fa-solid fa-phone-volume"></i> Change Number</a></li>
                    <li><a href="./logout.php"><i class="fa-solid fa-right-to-bracket"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
        <div class="column current-user bg-[#343a40] gap-2">
            <p>Logged In as:</p>
            <p><?php echo $username . " (" . $role . ")"; ?></p>
        </div>
    </header>

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
                        echo '<td class="border px-4 py-2">';
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
</body>
</html>
