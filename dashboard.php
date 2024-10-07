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

    //Hidden Add Product Modal 
    include './templates/add_product_modal.php';
    ?>

    <!-- Main Content -->
    <main class="content ml-72 min-h-screen">
        <section class="section px-20 py-11">
            <div class="column">
                <h1 class="text-white text-5xl mb-[30px] font-bold">SHOP</h1>

                <!-- Sorting Dropdown -->
                <div class="mb-4">
                    <label for="sort" class="text-white">Sort by:</label>
                    <select id="sort" class="p-2 bg-gray-700 text-white">
                        <option value="default">Default</option>
                        <option value="name_asc">Name (A to Z)</option>
                        <option value="name_desc">Name (Z to A)</option>
                        <option value="price_asc">Price (Low to High)</option>
                        <option value="price_desc">Price (High to Low)</option>
                    </select>
                </div>

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
                <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                    <?php foreach ($products as $product): ?>
                        <div class="product-item tab-content <?php echo htmlspecialchars($product['type']); ?> bg-bg-color shadow-md rounded-lg overflow-hidden" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo $product['price']; ?>" data-id="<?php echo $product['id']; ?>">
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

            </div>
        </section>
    </main>

    <script>
        // ----------------- Dashboard Tab
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        // Function to activate a tab
        function activateTab(selectedTab) {
            // Remove 'active' class from all buttons
            tabButtons.forEach((btn) => btn.classList.remove('active'));

            // Hide all products
            tabContents.forEach((content) => content.classList.remove('active'));

            // Add 'active' class to the corresponding tab
            document.querySelector(`[data-tab="${selectedTab}"]`).classList.add('active');
            document.querySelectorAll(`.${selectedTab}`).forEach((product) => {
                product.classList.add('active');
            });
        }

        // Attach event listeners to all tab buttons
        tabButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const selectedTab = button.getAttribute('data-tab');

                // Save the selected tab to localStorage
                localStorage.setItem('activeTab', selectedTab);

                // Activate the selected tab
                activateTab(selectedTab);
            });
        });

        // On page load, check if there's an active tab saved in localStorage
        document.addEventListener('DOMContentLoaded', () => {
            const savedTab = localStorage.getItem('activeTab') || 'ar'; // Default to 'ar' (or any tab you prefer)
            activateTab(savedTab);
        });

        // Sorting functionality
        const sortSelect = document.getElementById('sort');
        sortSelect.addEventListener('change', () => {
            const sortOption = sortSelect.value;
            sortProducts(sortOption);
        });

        function sortProducts(option) {
            const productGrid = document.getElementById('productGrid');
            const products = Array.from(productGrid.querySelectorAll('.product-item'));

            // Sort logic based on the selected option
            let sortedProducts;
            if (option === 'default') {
                sortedProducts = products.sort((a, b) => parseFloat(a.getAttribute('data-id')) - parseFloat(b.getAttribute('data-id')));
            } else if (option === 'name_asc') {
                sortedProducts = products.sort((a, b) => a.getAttribute('data-name').localeCompare(b.getAttribute('data-name')));
            } else if (option === 'name_desc') {
                sortedProducts = products.sort((a, b) => b.getAttribute('data-name').localeCompare(a.getAttribute('data-name')));
            } else if (option === 'price_asc') {
                sortedProducts = products.sort((a, b) => parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price')));
            } else if (option === 'price_desc') {
                sortedProducts = products.sort((a, b) => parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price')));
            }

            // Re-arrange sorted products in the grid
            if (sortedProducts) {
                sortedProducts.forEach(product => {
                    productGrid.appendChild(product);
                });
            }
        }

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