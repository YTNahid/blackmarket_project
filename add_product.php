<?php
// Start the session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $target = './assets/' . basename($image);
    $added_by = $_SESSION['username']; // Get the username from the session

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // Insert product details along with the username (added_by)
        $sql = $conn->prepare("INSERT INTO products (name, image, price, added_by) VALUES (?, ?, ?, ?)");
        $sql->bind_param("ssds", $name, $image, $price, $added_by);
        if ($sql->execute()) {
            echo "<p>Product added successfully.</p>";
        } else {
            echo "<p>Error: " . $sql->error . "</p>";
        }
        $sql->close();
    } else {
        echo "<p>Failed to upload image.</p>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/global-style.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body class="add-product">
    <div class="container">
        <h2>Add Product</h2>
        <form action="add_product.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="image">Product Image:</label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="price">Product Price:</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Add Product">
            </div>
        </form>
        <a href="dashboard.php"><button>Back to Dashboard</button></a>
    </div>
</body>
</html>
