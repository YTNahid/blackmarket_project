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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="shortcut icon" href="./assets/favicon.png" type="image/x-icon">

    <!-- Connect CSS -->
    <link rel="stylesheet" href="./css/global-style.css">
    <link rel="stylesheet" href="./css/style.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/fa69f7130e.js" crossorigin="anonymous"></script>

    <!-- Optional: Tailwind custom config -->
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
<body class="bg-black min-h-screen text-white">
    <!-- Header -->
    <header class="column sidebar bg-bg-color w-72 h-screen justify-between fixed">
        <div class="column">
            <p class="text-white opacity-25">Menu</p>
            <nav>
                <ul class="nav-menu space-y-3">
                    <li><a href="./dashboard.php"><i class="fa-solid fa-house"></i>Dashboard</a></li>
                    <?php if ($role !== 'admin'): ?>
                        <!-- Only show Order History for non-admins -->
                        <li><a href="./order_history.php" class="open"><i class="fa-solid fa-bag-shopping"></i> Order History</a></li>
                    <?php endif; ?>

                    <?php if ($role === 'admin'): ?>
                        <!-- Only show Orders for admins -->
                        <li><a href="./orders.php"><i class="fa-solid fa-bag-shopping"></i> Orders</a></li>
                    <?php endif; ?>

                    <li class="border-t pt-4 mt-[50px]"><a href="./profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                    <li><a href="./change_pass.php"><i class="fa-solid fa-unlock-keyhole"></i> Change Password</a></li>
                    <li><a href="./change_number.php"><i class="fa-solid fa-phone-volume"></i> Change Number</a></li>
                    <li><a href="./logout.php"><i class="fa-solid fa-right-to-bracket"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
        <div class="column current-user bg-[#343a40] gap-2">
            <p>Logged In as:</p>
            <p><?php echo $username . " (" . $role . ")"; ?></p>
        </div>
    </header>

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
