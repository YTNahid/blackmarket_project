<?php
session_start();
include 'connect.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user details from the database
$sql = $conn->prepare("SELECT username, email, contact, role FROM users WHERE email = ?");
$sql->bind_param("s", $email);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = htmlspecialchars($row['username']);
    $email = htmlspecialchars($row['email']);
    $contact = htmlspecialchars($row['contact']);
    $role = htmlspecialchars($row['role']);
} else {
    echo "User not found.";
    exit();
}

$sql->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include './templates/head.php';
    ?>
</head>

<body class="bg-black min-h-screen text-white">
    <?php
    // Header
    include './templates/header.php';

    // Cart Drawer
    include './templates/cart_drawer.php';

    //Hidden Add Product Modal 
    include './templates/add_product_modal.php';
    ?>

    <!-- Cart Drawer -->
    <div id="cart-drawer" class="cart-drawer fixed right-0 top-0 h-full w-80 bg-gray-800 text-white p-6 cart-drawer-closed z-50">
        <h2 class="text-2xl font-bold mb-4">Shopping Cart</h2>
        <ul id="cart-items" class="space-y-4"></ul>
        <div id="cart-summary" class="mt-4">
            <h3>Total: $<span id="cart-total">0.00</span></h3>
            <button id="order-btn" class="bg-green-600 text-white px-4 py-2 mt-6 rounded">Place Order</button>
        </div>
        <button id="close-cart-btn" class="bg-red-600 text-white px-4 py-2 mt-6 rounded">Close Cart</button>
    </div>

    <main class="content ml-72 pt-10 min-h-screen">
        <div class="max-w-md mx-auto bg-black rounded-lg shadow-md overflow-hidden md:max-w-2xl">
            <div class="p-6 bg-bg-color">
                <h2 class="text-2xl font-bold mb-6 text-white">Profile Details</h2>

                <div class="mb-4">
                    <label class="block text-white opacity-50">Username:</label>
                    <p class="text-white"><?php echo $username; ?></p>
                </div>

                <div class="mb-4">
                    <label class="block text-white opacity-50">Email:</label>
                    <p class="text-white"><?php echo $email; ?></p>
                </div>

                <div class="mb-4">
                    <label class="block text-white opacity-50">Contact Number:</label>
                    <p class="text-white"><?php echo $contact; ?></p>
                </div>

                <div class="mb-4">
                    <label class="block text-white opacity-50">Role:</label>
                    <p class="text-white"><?php echo $role; ?></p>
                </div>

                <div class="mt-6">
                    <a href="dashboard.php" class="bg-white text-black px-4 py-2 rounded hover:bg-black hover:text-white">Go Back</a>
                </div>
            </div>
        </div>
    </main>

    <script src="./js/script.js"></script>
</body>
</html>
