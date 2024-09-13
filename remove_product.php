<?php
session_start();
include 'connect.php';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Check if the user is an admin
$email = $_SESSION['email'];
$sql = $conn->prepare("SELECT role FROM users WHERE email = ?");
$sql->bind_param("s", $email);
$sql->execute();
$result = $sql->get_result();
$user = $result->fetch_assoc();

if ($user['role'] !== 'admin') {
    // If the user is not an admin, redirect to the dashboard
    header("Location: dashboard.php");
    exit();
}

// Check if the product ID is provided
if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Delete the product from the database
    $deleteQuery = $conn->prepare("DELETE FROM products WHERE id = ?");
    $deleteQuery->bind_param("i", $productId);

    if ($deleteQuery->execute()) {
        // Redirect back to the dashboard after deletion
        header("Location: dashboard.php?message=Product removed successfully");
    } else {
        // Display an error if the deletion fails
        header("Location: dashboard.php?error=Unable to remove product");
    }

    $deleteQuery->close();
} else {
    // If no product ID is provided, redirect back to the dashboard
    header("Location: dashboard.php");
}

$conn->close();
?>
