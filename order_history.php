<?php
session_start();
include 'connect.php';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$email = $_SESSION['email'];

// Fetch user role and username
$sql = $conn->prepare("SELECT username, role FROM users WHERE email = ?");
$sql->bind_param("s", $email);
$sql->execute();
$result = $sql->get_result();
$user = $result->fetch_assoc();
$username = htmlspecialchars($user['username']);
$role = htmlspecialchars($user['role']);

// Fetch orders for the logged-in user
$sql = $conn->prepare("SELECT * FROM orders WHERE email = ? ORDER BY order_date DESC");
$sql->bind_param("s", $email);
$sql->execute();
$result = $sql->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include './templates/head.php';
    ?>
</head>

<body class="bg-black min-h-screen text-white">
    <?php
    // Header
    include './templates/header.php';

    // Cart Drawer
    include './templates/cart_drawer.php';
    ?>

    <main class="content ml-72 min-h-screen">
        <section class="section px-20 py-11">
            <div class="column">
                <h1 class="text-white text-3xl font-bold mb-6">Order History</h1>
                <?php if ($result->num_rows > 0): ?>
                    <table class="table-auto w-full bg-transparent text-white text-center border border-gray-700">
                        <thead>
                            <tr>
                                <th class="border px-4 py-2">ID</th>
                                <th class="border px-4 py-2">Date</th>
                                <th class="border px-4 py-2">Total</th>
                                <th class="border px-4 py-2">Items</th>
                                <th class="border px-4 py-2">Status</th> <!-- Add Status Column -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($row['order_date']); ?></td>
                                    <td class="border px-4 py-2">$<?php echo htmlspecialchars($row['total']); ?></td>
                                    <td class="border px-4 py-2">
                                        <?php
                                        $items = json_decode($row['items'], true); // Decode JSON string to array
                                        $itemList = [];

                                        foreach ($items as $item) {
                                            $itemList[] = htmlspecialchars($item['name']) . " - $" . number_format($item['price'], 2) . " x " . htmlspecialchars($item['quantity']) . " (Total: $" . number_format($item['price'] * $item['quantity'], 2) . ")";
                                        }

                                        echo implode("<br>", $itemList);
                                        ?>
                                    </td>
                                    <td class="border px-4 py-2">
                                        <?php
                                        // Display order status with appropriate styling
                                        if ($row['status'] === 'confirmed') {
                                            echo '<span class="text-green-500">Confirmed</span>';
                                        } elseif ($row['status'] === 'canceled') {
                                            echo '<span class="text-red-500">Canceled</span>';
                                        } else {
                                            echo '<span class="text-yellow-500">Pending</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-white">No orders found.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script src="./js/script.js"></script>
</body>
</html>

<?php
$sql->close();
$conn->close();
?>
