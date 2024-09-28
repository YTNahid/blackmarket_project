<?php
session_start();
include 'connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$role = $_SESSION['role'];
$username = $_SESSION['username'];

// Fetch all users from the database
$sql = $conn->query("SELECT id, email, username, contact, role FROM users ORDER BY id ASC");
$users = $sql->fetch_all(MYSQLI_ASSOC);

// Handle AJAX request to change password
if (isset($_POST['user_id']) && isset($_POST['new_password'])) {
    $userId = intval($_POST['user_id']);
    $newPassword = $_POST['new_password'];

    if (empty($newPassword)) {
        echo json_encode(['success' => false, 'message' => 'Password cannot be empty.']);
        exit();
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $updateSql = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $updateSql->bind_param("si", $hashedPassword, $userId);

    if ($updateSql->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating password.']);
    }

    $updateSql->close();
    exit();
}

// Handle AJAX request to delete user
if (isset($_POST['delete_user_id'])) {
    $userIdToDelete = intval($_POST['delete_user_id']);

    $deleteSql = $conn->prepare("DELETE FROM users WHERE id = ?");
    $deleteSql->bind_param("i", $userIdToDelete);

    if ($deleteSql->execute()) {
        echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting user.']);
    }

    $deleteSql->close();
    exit();
}

$conn->close();
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
                                <th class="border px-4 py-2">Delete User</th>
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
                                            <button onclick="changePassword(<?php echo $user['id']; ?>)" class="bg-blue-500 text-white px-4 py-2 rounded ml-2 hover:bg-blue-600">Change Password</button>
                                        <?php else: ?>
                                            <!-- Hidden for admin users -->
                                            <p class="text-gray-400">Not changeable</p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="border px-4 py-2">
                                        <?php if ($user['role'] !== 'admin'): ?>
                                            <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded ml-2">Delete</button>
                                        <?php else: ?>
                                            <!-- Hidden for admin users -->
                                            <p class="text-gray-400">Not deletable</p>
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

        function deleteUser(userId) {
            if (confirm("Are you sure you want to delete this user?")) {
                // Send the delete request to the server via AJAX
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `delete_user_id=${userId}`
                })
                .then(response => response.json()) // Directly parse the response as JSON
                .then(data => {
                    if (data.success) {
                        alert("User deleted successfully!");
                        location.reload(); // Optionally, reload the page to update the list of users
                    } else {
                        alert("Failed to delete user: " + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error); // Log error for debugging
                    alert("An error occurred.");
                });
            }
        }


    </script>

    <script src="./js/script.js"></script>
</body>
</html>
