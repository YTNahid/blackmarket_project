<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Read JSON input
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if data was successfully decoded
    if (json_last_error() === JSON_ERROR_NONE) {
        // Retrieve data
        $email = $data['email'] ?? '';
        $total = $data['total'] ?? 0;
        $items = $data['items'] ?? '';

        // Prepare and execute query
        $stmt = $conn->prepare("INSERT INTO orders (email, total, items) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $total, $items);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }

        $stmt->close();
    } else {
        echo 'Invalid JSON';
    }

    $conn->close();
}
?>
