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
    <?php
    include './templates/head.php';
    ?>

    <style>
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .tab-btn.active {
            background-color: white;
            color: black; 
        }
    </style>
</head>
<body class="dashboard bg-black">
    
    <?php
    // Header
    include './templates/header.php';

    // Cart Drawer
    include './templates/cart_drawer.php';
    ?>

    <!-- Main Content -->
    <main class="content ml-72 min-h-screen">
        <section class="section px-20 py-11">
            <div class="column">
                <h1 class="text-white text-5xl mb-[30px] font-bold">SHOP</h1>

                <!-- Tabs -->
                <div class="tabs mb-8">
                    <button class="tab-btn font-medium bg-bg-color text-white px-8 py-2 hover:bg-white hover:text-black" data-tab="ar">ASSAULT RIFLE</button>
                    <button class="tab-btn font-medium bg-bg-color text-white px-8 py-2 hover:bg-white hover:text-black" data-tab="dmr">DMR</button>
                    <button class="tab-btn font-medium bg-bg-color text-white px-8 py-2 hover:bg-white hover:text-black" data-tab="smg">SMG</button>
                    <button class="tab-btn font-medium bg-bg-color text-white px-8 py-2 hover:bg-white hover:text-black" data-tab="sr">SHOTGUN</button>
                    <button class="tab-btn font-medium bg-bg-color text-white px-8 py-2 hover:bg-white hover:text-black" data-tab="hg">HANDGUN</button>
                    <button class="tab-btn font-medium bg-bg-color text-white px-8 py-2 hover:bg-white hover:text-black" data-tab="melee">MELEE</button>
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                    <?php foreach ($products as $product): ?>
                        <div class="product-item tab-content <?php echo htmlspecialchars($product['type']); ?> bg-bg-color shadow-md rounded-lg overflow-hidden">
                            <img src="./assets/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image" class="w-full h-48 object-contain hover:scale-125">
                            <div class="p-4">
                                <?php if ($role === 'admin'): ?>
                                    <p class="text-sm text-gray-400 mb-2">added by <?php echo htmlspecialchars($product['added_by']); ?></p>
                                <?php endif; ?>

                                <h3 class="text-xl font-semibold mb-2 text-white"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-white">$<?php echo number_format($product['price'], 2); ?></span>
                                    
                                    <div class="">
                                        <?php if ($role !== 'admin'): ?>
                                            <button onclick="addToCart('<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>)" class="bg-white text-black px-4 py-2 rounded hover:bg-black hover:text-white">Add to Cart</button>
                                        <?php else: ?>
                                            <!-- Edit Button -->
                                            <a onclick="openModal(<?php echo $product['id']; ?>)" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-800 cursor-pointer">Edit</a>
                                            
                                            <!-- Remove Button -->
                                            <a href="remove_product.php?id=<?php echo $product['id']; ?>" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-800">Remove</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Edit Modal -->
                        <div id="modal-<?php echo $product['id']; ?>" class="modal hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex justify-center items-center">
                            <div class="modal-content bg-bg-color p-6 rounded-lg shadow-lg relative">
                                <span onclick="closeModal(<?php echo $product['id']; ?>)" class="close-modal absolute top-2 right-2 text-white cursor-pointer">×</span>
                                <h2 class="text-[20px] font-bold mb-4 text-white">Edit Product <br> (Product ID: <?php echo $product['id']; ?>)</h2>
                                <form method="POST" action="edit_product.php" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                    <div class="mb-4">
                                        <label class="block text-white">Product Name:</label>
                                        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" class="w-full p-2 bg-transparent border mt-2 text-white rounded">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-white">Product Price:</label>
                                        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" class="w-full p-2 bg-transparent border mt-2 text-white rounded">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-white">Product Image:</label>
                                        <input type="file" name="image" class="w-full p-2 bg-transparent border mt-2 text-white rounded">
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-800">Save Changes</button>
                                        <button type="button" onclick="closeModal(<?php echo $product['id']; ?>)" class="bg-red-600 text-white px-4 py-2 ml-2 rounded hover:bg-red-800">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Hidden Add Product Modal -->
                <div id="add-product-modal" class="modal hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex justify-center items-center">
                    <div class="modal-content bg-bg-color p-6 rounded-lg shadow-lg relative">
                        <span onclick="closeAddProductModal()" class="close-modal absolute top-2 right-2 text-white cursor-pointer">×</span>
                        <h2 class="text-[20px] font-bold mb-4 text-white">Add New Product</h2>
                        <form method="POST" action="add_product.php" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label class="block text-white">Product Name:</label>
                                <input type="text" name="name" class="w-full p-2 bg-transparent border mt-2 text-white rounded" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-white">Product Price:</label>
                                <input type="number" step="0.01" name="price" class="w-full p-2 bg-transparent border mt-2 text-white rounded" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-white">Product Image:</label>
                                <input type="file" name="image" class="w-full p-2 bg-transparent border mt-2 text-white rounded" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-white">Weapon Type</label>
                                <select name="type" class="w-full p-2 bg-transparent border mt-2 text-white rounded" required>
                                    <option class="bg-black" value="ar">AR</option>
                                    <option class="bg-black" value="dmr">DMR</option>
                                    <option class="bg-black" value="smg">SMG</option>
                                    <option class="bg-black" value="sr">Shotgun</option>
                                    <option class="bg-black" value="hg">Handgun</option>
                                    <option class="bg-black" value="melee">Melee</option>
                                </select>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-800">Add Product</button>
                                <button type="button" onclick="closeAddProductModal()" class="bg-red-600 text-white px-4 py-2 ml-2 rounded hover:bg-red-800">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </section>
    </main>

    <script>
        // ----------------- Dashboard Tab
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const selectedTab = button.getAttribute('data-tab');

                // Remove 'active' class from all buttons
                tabButtons.forEach((btn) => btn.classList.remove('active'));

                // Hide all products
                tabContents.forEach((content) => content.classList.remove('active'));

                // Add 'active' class to the clicked button
                button.classList.add('active');

                // Show products matching the selected tab
                document.querySelectorAll(`.${selectedTab}`).forEach((product) => {
                    product.classList.add('active');
                });
            });
        });

        // Trigger the first tab on page load
        document.querySelector('.tab-btn').click();

        // --------Add Product Form
        // Open Add Product Modal
        function openAddProductModal() {
            const modal = document.getElementById('add-product-modal');
            modal.classList.remove('hidden');
        }

        // Close Add Product Modal
        function closeAddProductModal() {
            const modal = document.getElementById('add-product-modal');
            modal.classList.add('hidden');
        }

        // Close the modal when clicking outside of it
        window.onclick = function (event) {
            const addModal = document.getElementById('add-product-modal');
            if (event.target === addModal) {
                addModal.classList.add('hidden');
            }
        };

        // --------Edit Product Form
        function openModal(productId) {
            const modal = document.getElementById(`modal-${productId}`);
            modal.classList.remove('hidden');
        }

        // Function to close the modal
        function closeModal(productId) {
            const modal = document.getElementById(`modal-${productId}`);
            modal.classList.add('hidden');
        }

        // Close the modal when clicking outside of it
        window.onclick = function (event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach((modal) => {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        };

    </script>
    <!-- JavaScript -->
    <script src="./js/script.js"></script>
</body>
</html>
