<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$email = $_SESSION['email']; // Get the user's email from the session
$role = $_SESSION['role'];   // Get the user's role from the session
$username = $_SESSION['username'];   // Get the user's role from the session

// Handle Password Change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch user data
    $sql = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $result = $sql->get_result();
    $row = $result->fetch_assoc();
    $hashed_password = $row['password'];

    // Verify current password
    if (!password_verify($current_password, $hashed_password)) {
        $error = "Current password is incorrect.";
        echo '<script>
                alert("Current password is incorrect.");
            </script>';
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
        echo '<script>
                alert("New passwords do not match.");
            </script>';
    } else {
        // Hash the new password and update the database
        $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->bind_param("ss", $new_password_hash, $email);

        if ($update->execute()) {
            // Redirect with a success message
            echo '<script>
                alert("Password changed successfully.");
                window.location.href = "dashboard.php";
            </script>';
            exit();
        } else {
            $error = "Error updating password.";
            echo '<script>
                alert("Error updating password.");
            </script>';
        }

        $update->close();
    }

    $sql->close();
    $conn->close();
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
    ?>

    <main class="content ml-72 pt-10 min-h-screen">
        <div class="max-w-[400px] mx-auto bg-bg-color p-10 rounded-lg">
            <h2 class="text-2xl text-white font-bold mb-5">Change Password</h2>
            <form action="./change_pass.php" method="post">
                <div class="mb-4">
                    <label class="block text-white" for="current_password">Current Password:</label>
                    <input class="w-full p-2 bg-transparent border mt-2 text-white rounded" type="password" id="current_password" name="current_password" required>
                </div>
                <div class="mb-4">
                    <label class="block text-white" for="new_password">New Password:</label>
                    <input class="w-full p-2 bg-transparent border mt-2 text-white rounded" type="password" id="new_password" name="new_password" required>
                </div>
                <div class="mb-4">
                    <label class="block text-white" for="confirm_password">Confirm New Password:</label>
                    <input class="w-full p-2 bg-transparent border mt-2 text-white rounded" type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="mb-4">
                    <input type="submit" value="Change Password" name="change_password" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-800 cursor-pointer w-full mb-5">
                </div>
            </form>
            <a href="dashboard.php" class="bg-white text-black px-4 py-2 rounded hover:bg-black hover:text-white"><button>Go Back</button></a>
        </div>
    </main>

    <!-- JavaScript -->
    <script src="./js/script.js"></script>
</body>
</html>
