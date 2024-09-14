<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Handle Password Change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $email = $_SESSION['email'];

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="shortcut icon" href="./assets/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <h2>Change Password</h2>
        <form action="change_pass.php" method="post">
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Change Password" name="change_password">
            </div>
        </form>
        <a href="dashboard.php"><button>Go Back</button></a>
    </div>
</body>
</html>
