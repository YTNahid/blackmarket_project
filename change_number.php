<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Handle Contact Number Change
if (isset($_POST['change_number'])) {
    $new_number = $_POST['new_number'];

    $email = $_SESSION['email'];

    // Update the contact number in the database
    $update = $conn->prepare("UPDATE users SET contact = ? WHERE email = ?");
    $update->bind_param("ss", $new_number, $email);

    if ($update->execute()) {
        // Redirect with a success message
        echo '<script>
            alert("Contact number changed successfully.");
            window.location.href = "dashboard.php";
        </script>';
        exit();
    } else {
        echo '<script>
            alert("Error updating contact number.");
        </script>';
    }

    $update->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Contact Number</title>
    <link rel="shortcut icon" href="./assets/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <h2>Change Contact Number</h2>
        <form action="change_number.php" method="post">
            <div class="form-group">
                <label for="new_number">New Contact Number:</label>
                <input type="text" id="new_number" name="new_number" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Change Number" name="change_number">
            </div>
        </form>
        <a href="dashboard.php"><button>Go Back</button></a>
    </div>
</body>
</html>
