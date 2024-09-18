// Initialize cart
let cart = JSON.parse(localStorage.getItem('cart')) || [];
let cartCountElement = document.getElementById('cart-count');
let cartItemsElement = document.getElementById('cart-items');
let cartDrawerElement = document.getElementById('cart-drawer');
let cartTotalElement = document.getElementById('cart-total');

// Add product to cart
function addToCart(name, price) {
    const itemIndex = cart.findIndex((item) => item.name === name);

    if (itemIndex > -1) {
        // If item already exists, increase the quantity
        cart[itemIndex].quantity += 1;
    } else {
        // Add new item
        cart.push({ name, price, quantity: 1 });
    }

    updateCart();
}

// Update cart
function updateCart() {
    updateCartCount();
    updateCartDrawer();
    localStorage.setItem('cart', JSON.stringify(cart));
}

// Update cart count
function updateCartCount() {
    cartCountElement.innerText = cart.length;
}

// Update cart drawer with items
function updateCartDrawer() {
    cartItemsElement.innerHTML = '';
    cart.forEach((item, index) => {
        let li = document.createElement('li');
        li.classList.add('flex', 'flex-col', 'items-start', 'gap-2', 'border-b', 'pb-5');
        li.innerHTML = `
            <div class="flex flex-col gap-2">
                <p>${item.name} - $${item.price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</p>
                <input type="number" value="${
                    item.quantity
                }" min="1" class="quantity-input bg-gray-600 text-center" onchange="changeQuantity(${index}, this.value)">
            </div>
            <p class="text-white">$<span>${(item.price * item.quantity).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })}</span></p>
            <button onclick="removeFromCart(${index})" class="bg-red-600 text-white px-2 py-1 rounded">Remove</button>
        `;
        cartItemsElement.appendChild(li);
    });

    updateCartTotal();
}

// Update total price of the cart
function updateCartTotal() {
    let total = cart.reduce((acc, item) => acc + item.price * item.quantity, 0);
    cartTotalElement.innerText = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Change quantity of a specific item
function changeQuantity(index, newQuantity) {
    cart[index].quantity = parseInt(newQuantity);
    updateCart();
}

// Remove an item from the cart
function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}

// Open cart drawer
document.getElementById('view-cart-btn').addEventListener('click', function () {
    cartDrawerElement.classList.remove('cart-drawer-closed');
    cartDrawerElement.classList.add('cart-drawer-open');
});

if (document.getElementById('view-cart-btn')) {
}

// Close cart drawer
document.getElementById('close-cart-btn').addEventListener('click', function () {
    cartDrawerElement.classList.remove('cart-drawer-open');
    cartDrawerElement.classList.add('cart-drawer-closed');
});

// Close cart drawer when clicking outside
// document.addEventListener('click', function (event) {
//     if (!cartDrawerElement.contains(event.target) && !event.target.closest('#view-cart-btn')) {
//         cartDrawerElement.classList.remove('cart-drawer-open');
//         cartDrawerElement.classList.add('cart-drawer-closed');
//     }
// });

// Initialize cart on page load
updateCart();

// Place order
document.getElementById('order-btn').addEventListener('click', function () {
    if (cart.length === 0) {
        alert('Your cart is empty.');
        return;
    }

    // Retrieve email dynamically
    fetch('get_user_email.php') // Make sure this script returns the user's email
        .then((response) => response.json())
        .then((data) => {
            const orderDetails = {
                email: data.email, // Use email from the server
                total: cart.reduce((acc, item) => acc + item.price * item.quantity, 0).toFixed(2),
                items: JSON.stringify(cart),
            };

            return fetch('place_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderDetails),
            });
        })
        .then((response) => response.text())
        .then((data) => {
            if (data === 'success') {
                // Clear the cart
                localStorage.removeItem('cart');
                cart = []; // Reset cart array
                updateCart(); // Update cart display
                alert('Order placed successfully. We will contact your shorty to confirm your order');
            } else {
                alert('Error placing order.');
            }
        })
        .catch((error) => console.error('Error:', error));
});
