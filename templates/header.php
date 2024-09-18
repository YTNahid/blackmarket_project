<?php
// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Header -->
<header class="column sidebar bg-bg-color w-72 h-screen justify-between fixed">
    <div class="column">
        <p class="text-white opacity-25">Menu</p>
        <nav>
            <ul class="nav-menu space-y-3">
                <li>
                    <a href="./dashboard.php" class="flex items-center <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-house p-0"></i> Dashboard
                    </a>
                </li>

                <?php if ($role !== 'admin'): ?>
                    <li>
                        <a href="#" id="view-cart-btn" class="flex items-center">
                            <i class="fa-solid fa-cart-shopping"></i> View Cart
                            <div id="cart-count" class="count rounded-full ml-3 bg-red-600 flex items-center justify-center h-5 w-5">0</div>
                        </a>
                    </li>
                    <li>
                        <a href="./order_history.php" class="<?php echo ($current_page == 'order_history.php') ? 'active' : ''; ?>">
                            <i class="fa-solid fa-bag-shopping"></i> Order History
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === 'admin'): ?>
                    <li>
                        <a href="./orders.php" class="<?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">
                            <i class="fa-solid fa-bag-shopping"></i> Orders
                        </a>
                    </li>
                    <li>
                        <a href="./users.php" class="flex items-center <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
                            <i class="fa-solid fa-users p-0"></i> Users
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="openAddProductModal()" class="<?php echo ($current_page == 'add_product.php') ? 'active' : ''; ?>">
                            <i class="fa-solid fa-plus"></i> Add Product
                        </a>
                    </li>
                <?php endif; ?>

                <li class="border-t pt-4 mt-[50px]">
                    <a href="./profile.php" class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-user"></i> Profile
                    </a>
                </li>
                <li>
                    <a href="./change_pass.php" class="<?php echo ($current_page == 'change_pass.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-unlock-keyhole"></i> Change Password
                    </a>
                </li>
                <li>
                    <a href="./change_number.php" class="<?php echo ($current_page == 'change_number.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-phone-volume"></i> Change Number
                    </a>
                </li>
                <li>
                    <a href="./logout.php">
                        <i class="fa-solid fa-right-to-bracket"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="column current-user bg-[#343a40] gap-2">
        <p>Logged In as:</p>
        <p><?php echo $username . " (" . $role . ")"; ?></p>
    </div>
</header>
