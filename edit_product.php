<?php
include 'connect.php';

// Get product ID
$id = $_POST['id'];
$name = $_POST['name'];
$price = $_POST['price'];

// Handle image upload
$image = $_FILES['image']['name'];
$target = "./assets/" . basename($image);

if (!empty($image)) {
    // If a new image is uploaded
    $sql = "UPDATE products SET name = ?, price = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);  // Prepare the SQL statement
    $stmt->bind_param("sdsi", $name, $price, $image, $id);  // Bind parameters
} else {
    // If no new image is uploaded
    $sql = "UPDATE products SET name = ?, price = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);  // Prepare the SQL statement
    $stmt->bind_param("sdi", $name, $price, $id);  // Bind parameters
}

if ($stmt->execute()) {
    // Save the uploaded image file if a new image was uploaded
    if (!empty($image)) {
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    header("Location: dashboard.php");  // Redirect to dashboard on success
} else {
    echo "Error updating product.";  // Error handling
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
