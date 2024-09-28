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

<script>
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
</script>