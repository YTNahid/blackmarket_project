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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Cart</title>

    <!-- Connect CSS -->
    <link rel="stylesheet" href="./css/global-style.css">
    <link rel="stylesheet" href="./css/style.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/fa69f7130e.js" crossorigin="anonymous"></script>

    <!-- Optional: Tailwind custom config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "bg-color": '#212529',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-black min-h-screen text-white">
     <!-- Header -->
     <header class="column sidebar bg-bg-color w-72 h-screen justify-between fixed">
        <div class="column">
            <p class="text-white opacity-25">Menu</p>
            <nav>
                <ul class="nav-menu space-y-3">
                    <li class=""><a href="./dashboard.php" class=""><i class="fa-solid fa-house"></i>Dashboard</a></li>
                    <?php if ($role !== 'admin'): ?>
                        <!-- Only show Add to Cart and Order History for non-admins -->
                        <li><a href="./order_history.php"><i class="fa-solid fa-bag-shopping"></i> Order History</a></li>
                    <?php endif; ?>

                    <?php if ($role === 'admin'): ?>
                        <!-- Only show Orders for admins -->
                        <li><a href="./orders.php"><i class="fa-solid fa-bag-shopping"></i> Orders</a></li>
                        <li><a href="./users.php"><i class="fa-solid fa-users pr-0"></i>Users</a></li>
                        <li><a href="./add_product.php"><i class="fa-solid fa-plus"></i> Add Product</a></li>
                    <?php endif; ?>

                    <li class="border-t pt-4 mt-[50px]"><a href="./profile.php" class="open"><i class="fa-solid fa-user"></i> Profile</a></li>
                    <li class=""><a href="./change_pass.php"><i class="fa-solid fa-unlock-keyhole"></i> Change Password</a></li>
                    <li class=""><a href="./change_number.php"><i class="fa-solid fa-phone-volume"></i> Change Number</a></li>
                    <li><a href="./logout.php"><i class="fa-solid fa-right-to-bracket"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
        <div class="column current-user bg-[#343a40] gap-2">
            <p>Logged In as:</p>
            <p><?php echo $username . " (" . $role . ")"; ?></p>
        </div>
    </header>

    <main class="content ml-72 min-h-screen">
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
</body>
</html>
