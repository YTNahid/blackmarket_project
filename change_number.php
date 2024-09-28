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

    //Hidden Add Product Modal 
    include './templates/add_product_modal.php';
    ?>

    <main class="content ml-72 pt-10 min-h-screen">
        <div class="max-w-[400px] mx-auto bg-bg-color p-10 rounded-lg">
            <h2 class="text-2xl text-white font-bold mb-5">Change Number</h2>

            <form action="./change_number.php" method="post">
                <div class="mb-4">
                    <label class="block text-white" for="new_number">New Contact Number:</label>
                    <input class="w-full p-2 bg-transparent border mt-2 text-white rounded" type="text" id="new_number" name="new_number" required>
                </div>
                <div class="mb-4">
                    <input  type="submit" value="Change Number" name="change_number" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-800 cursor-pointer w-full mb-5"> 
                </div>
            </form>
            <a href="dashboard.php" class="bg-white text-black px-4 py-2 rounded hover:bg-black hover:text-white"><button>Go Back</button></a>
        </div>
    </main>

    <!-- JavaScript -->
    <script src="./js/script.js"></script>
</body>
</html>

