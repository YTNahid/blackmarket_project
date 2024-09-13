<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

// Retrieve email from session
$email = $_SESSION['email'];

// Return email as JSON
echo json_encode(['email' => $email]);
?>
