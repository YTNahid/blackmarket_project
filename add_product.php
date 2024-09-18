<?php
// Start session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'connect.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form input values
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name']; // File name
    $target = './assets/' . basename($image); // Upload path
    $added_by = $_SESSION['username']; // Get the username from the session
    $type = $_POST['type']; // Get the weapon type from the form

    // Move uploaded file to the target directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // Prepare SQL statement to insert product data
        $stmt = $conn->prepare("INSERT INTO products (name, image, price, added_by, type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $name, $image, $price, $added_by, $type);

        // Execute query and check for success
        if ($stmt->execute()) {
            // If successful, redirect to the dashboard or another success page
            header("Location: dashboard.php?message=Product+added+successfully");
        } else {
            // On failure, show error message
            echo "Error: " . $stmt->error;
        }

        $stmt->close(); // Close the statement
    } else {
        echo "Failed to upload image.";
    }

    $conn->close(); // Close the database connection
}
