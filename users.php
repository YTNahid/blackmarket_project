<?php
session_start();
include 'connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login page if not logged in or not an admin
    exit();
}

$email = $_SESSION['email']; // Get the user's email from the session
$role = $_SESSION['role'];   // Get the user's role from the session
$username = $_SESSION['username'];   // Get the user's role from the session


// Fetch all users from the database in ascending order by id
$sql = $conn->query("SELECT id, email, username, contact, role FROM users ORDER BY id ASC");
$users = $sql->fetch_all(MYSQLI_ASSOC);

// Check if the AJAX request to change the password was made
if (isset($_POST['user_id']) && isset($_POST['new_password'])) {
    $userId = intval($_POST['user_id']);
    $newPassword = $_POST['new_password'];

    // Check if the password is not empty
    if (empty($newPassword)) {
        echo json_encode(['success' => false, 'message' => 'Password cannot be empty.']);
        exit();
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update the user's password in the database
    $updateSql = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $updateSql->bind_param("si", $hashedPassword, $userId);

    if ($updateSql->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating password.']);
    }

    $updateSql->close();
    exit(); // End script after processing AJAX request
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
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
                        <li><a href="./orders.php"><i class="fa-solid fa-bag-shopping"></i> Orders</a></li>
                        <li><a href="./users.php" class="open"><i class="fa-solid fa-users pr-0"></i>Users</a></li>
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
            <p><?php echo htmlspecialchars($username) . " (" . htmlspecialchars($role) . ")"; ?></p>
        </div>
    </header>

    <main class="content ml-72 min-h-screen">
        <section class="section px-20 py-11">
            <h1 class="text-white text-3xl font-bold mb-6">Users</h1>
            <div class="table-container">
                <?php if (count($users) > 0): ?>
                    <table class="table-auto w-full bg-transparent text-white text-center border border-gray-700">
                        <thead>
                            <tr>
                                <th class="border px-4 py-2">ID</th>
                                <th class="border px-4 py-2">Email</th>
                                <th class="border px-4 py-2">Username</th>
                                <th class="border px-4 py-2">Contact</th>
                                <th class="border px-4 py-2">Role</th>
                                <th class="border px-4 py-2">Change Password</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr class="border-b">
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($user['contact']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td class="border px-4 py-2">
                                        <?php if ($user['role'] !== 'admin'): ?>
                                            <input type="password" id="new-password-<?php echo $user['id']; ?>" placeholder="New Password" class="text-black px-2 py-1 rounded">
                                            <button onclick="changePassword(<?php echo $user['id']; ?>)" class="bg-blue-500 text-white px-4 py-2 rounded ml-2">Change Password</button>
                                        <?php else: ?>
                                            <!-- Hidden for admin users -->
                                            <p class="text-gray-400">Not changeable</p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-white">No users found.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        function changePassword(userId) {
            const newPassword = document.getElementById(`new-password-${userId}`).value;

            if (!newPassword) {
                alert("Please enter a new password.");
                return;
            }

            // Send the new password to the server via AJAX
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `user_id=${userId}&new_password=${newPassword}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Password changed successfully!");
                } else {
                    alert("Failed to change password. " + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("An error occurred.");
            });
        }
    </script>

    <script src="./js/script.js"></script>
</body>
</html>
