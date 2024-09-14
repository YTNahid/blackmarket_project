<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include 'connect.php';

// Fetch user data
$email = $_SESSION['email'];
$sql = $conn->prepare("SELECT username, role FROM users WHERE email = ?");
$sql->bind_param("s", $email);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = htmlspecialchars($row['username']);
    $role = htmlspecialchars($row['role']);
} else {
    $username = "Unknown User";
    $role = "Unknown Role";
}

// Fetch products
$productQuery = $conn->query("SELECT * FROM products");
$products = $productQuery->fetch_all(MYSQLI_ASSOC);

$sql->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Dashboard</title>
    <link rel="stylesheet" href="./css/global-style.css">
    <link rel="stylesheet" href="./css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/fa69f7130e.js" crossorigin="anonymous"></script>
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
    <style>
        .cart-drawer {
            transition: transform 0.3s ease-in-out;
        }

        .cart-drawer-closed {
            transform: translateX(100%);
        }

        .cart-drawer-open {
            transform: translateX(0%);
        }
    </style>
</head>
<body class="dashboard bg-black">
    <header class="column sidebar bg-bg-color w-72 h-screen justify-between fixed">
        <div class="column">
            <p class="text-white opacity-25">Menu</p>
            <nav>
                <ul class="nav-menu space-y-3">
                    <li class=""><a href="./dashboard.php" class="open"><i class="fa-solid fa-house"></i>Dashboard</a></li>
                    <?php if ($role !== 'admin'): ?>
                        <li>
                            <a href="#" id="view-cart-btn" class="flex items-center">
                                <i class="fa-solid fa-cart-shopping"></i>
                                View Cart 
                                <div id="cart-count" class="count rounded-full ml-3 bg-red-600 flex items-center justify-center h-5 w-5">0</div>
                            </a>
                        </li>
                        <li><a href="./order_history.php"><i class="fa-solid fa-bag-shopping"></i> Order History</a></li>
                    <?php endif; ?>

                    <?php if ($role === 'admin'): ?>
                        <li><a href="./orders.php"><i class="fa-solid fa-bag-shopping"></i> Orders</a></li>
                        <li><a href="./users.php"><i class="fa-solid fa-users pr-0"></i>Users</a></li>
                        <li><a href="./add_product.php"><i class="fa-solid fa-plus"></i> Add Product</a></li>
                    <?php endif; ?>

                    <li class="border-t pt-4 mt-[50px]"><a href="./profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
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

    <div id="cart-drawer" class="cart-drawer fixed right-0 top-0 h-full w-80 bg-gray-800 text-white p-6 cart-drawer-closed">
        <h2 class="text-2xl font-bold mb-4">Shopping Cart</h2>
        <ul id="cart-items" class="space-y-4"></ul>
        <div id="cart-summary" class="mt-4">
            <h3>Total: $<span id="cart-total">0.00</span></h3>
            <button id="order-btn" class="bg-green-600 text-white px-4 py-2 mt-6 rounded">Place Order</button>
        </div>
        <button id="close-cart-btn" class="bg-red-600 text-white px-4 py-2 mt-6 rounded">Close Cart</button>
    </div>

    <main class="content ml-72 min-h-screen">
        <section class="section px-20 py-11">
            <div class="column">
                <h1 class="text-white text-5xl mb-[30px] font-bold">SHOP</h1>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                    <?php foreach ($products as $product): ?>
                        <div class="bg-bg-color shadow-md rounded-lg overflow-hidden">
                            <img src="./assets/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image" class="w-full h-48 object-contain hover:scale-125">
                            <div class="p-4">
                                <p class="text-sm text-gray-400 mb-2">added by <?php echo htmlspecialchars($product['added_by']); ?></p> <!-- Display added_by -->
                                <h3 class="text-xl font-semibold mb-2 text-white"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-white">$<?php echo number_format($product['price'], 2); ?></span>
                                    <?php if ($role !== 'admin'): ?>
                                        <button onclick="addToCart('<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>)" class="bg-white text-black px-4 py-2 rounded hover:bg-black hover:text-white">Add to Cart</button>
                                    <?php else: ?>
                                        <a href="remove_product.php?id=<?php echo $product['id']; ?>" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-800">Remove Product</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <script src="./js/script.js"></script>
</body>
</html>
