<div class="w-1/4 h-screen bg-orange-500 text-white flex flex-col justify-between p-6">
    <!-- Logo and Service Type -->
    <div>
        <div class="flex items-center space-x-2 mb-8">
            <img src="./img/logo.jpg" alt="Business Logo" class="w-12 h-12 rounded-full">
            <h1 class="text-lg font-semibold">Mabsi Soy</h1>
        </div>
        <h2 class="text-xl font-bold">Order</h2>
    </div>

    <!-- Menu Tabs -->
    <div class="mt-8">
        <nav class="space-y-4">
            <a href="?type=special_menu" class="block text-gray-50 hover:bg-orange-700 p-3 rounded">Special Menu</a>
            <a href="?type=budget_meal" class="block text-gray-50 hover:bg-orange-700 p-3 rounded">Budget Meal</a>
            <a href="?type=combo_meal" class="block text-gray-50 hover:bg-orange-700 p-3 rounded">Combo Meal</a>
            <a href="?type=ala_carte" class="block text-gray-50 hover:bg-orange-700 p-3 rounded">Ala Carte</a>
            <a href="?type=add_ons" class="block text-gray-50 hover:bg-orange-700 p-3 rounded">Add Ons</a>
            <a href="?type=drinks_dessert" class="block text-gray-50 hover:bg-orange-700 p-3 rounded">Drinks & Desserts</a>
        </nav>
    </div>

    <!-- Cart Section -->
    <div class="mt-auto">
        <h3 class="text-lg font-semibold mb-4">Cart</h3>
        <div class="bg-orange-900 p-4 rounded shadow-lg">
            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <ul id="cart-items-list" class="text-sm text-gray-400">
                    <?php foreach ($_SESSION['cart'] as $itemId => $item): ?>
                        <li class="flex justify-between mb-2" id="cart-item-<?= $itemId; ?>">
                            <span><?= $item['name']; ?> (x<?= $item['quantity']; ?>)</span>
                            <span>₱<?= number_format($item['price'] * $item['quantity'], 2); ?></span>
                            <button class="remove-item text-red-500 ml-2 text-xl" data-item-id="<?= $itemId; ?>">❌</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="mt-3 text-white">
                    <strong>Total: ₱<?= number_format(array_sum(array_map(function ($item) {
                        return $item['price'] * $item['quantity'];
                    }, $_SESSION['cart'])), 2); ?></strong>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-400">Your cart is empty.</p>
            <?php endif; ?>
            <button class="w-full bg-orange-500 hover:bg-orange-600 text-white mt-4 py-2 rounded" id="checkoutBtn">Checkout</button>
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div id="checkoutModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
        <h2 class="text-2xl font-bold mb-4">Order Checkout</h2>
        <form id="checkoutForm">
            <div class="mb-4">
                <label for="serviceType" class="block text-sm font-semibold">Service Type</label>
                <select id="serviceType" name="serviceType" class="w-full p-2 border rounded">
                    <option value="pickup">Pickup</option>
                    <option value="delivery">Delivery</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold">Email</label>
                <input type="email" id="email" name="email" class="w-full p-2 border rounded" value="<?= isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''; ?>" required>
            </div>
            <div class="mb-4">
                <label for="contactNumber" class="block text-sm font-semibold">Contact Number</label>
                <input type="text" id="contactNumber" name="contactNumber" class="w-full p-2 border rounded" value="<?= isset($_SESSION['user_contact']) ? $_SESSION['user_contact'] : ''; ?>" required>
            </div>
            <div class="mb-4" id="deliveryFields">
                <label for="address" class="block text-sm font-semibold">Address</label>
                <input type="text" id="address" name="address" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4" id="deliveryFields">
                <label for="landmark" class="block text-sm font-semibold">Landmark</label>
                <input type="text" id="landmark" name="landmark" class="w-full p-2 border rounded" required>
            </div>
            <div class="flex justify-between">
                <button type="button" id="cancelBtn" class="w-1/4 bg-red-500 hover:bg-red-600 text-white py-2 rounded">Cancel</button>
                <button type="submit" class="w-1/4 bg-green-500 hover:bg-green-600 text-white py-2 rounded">Confirm</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Show the checkout modal
    document.getElementById('checkoutBtn').addEventListener('click', function() {
        document.getElementById('checkoutModal').classList.remove('hidden');
    });

    // Close the checkout modal
    document.getElementById('cancelBtn').addEventListener('click', function() {
        document.getElementById('checkoutModal').classList.add('hidden');
    });

    // Handle checkout form submission
    document.getElementById('checkoutForm').addEventListener('submit', function(event) {
        event.preventDefault();

        // Prepare form data
        const formData = new FormData(this);

        // Add cart data
        formData.append('cart', JSON.stringify(<?php echo json_encode($_SESSION['cart']); ?>));

        // Send the form data to the server to save the order
        fetch('process_checkout.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order placed successfully!');
                window.location.href = 'order_history.php'; // Redirect to the order history page
            } else {
                alert('Error placing order');
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Event listener for removing an item from the cart
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            
            // Perform AJAX request to remove the item
            fetch('remove_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded' // Use form data encoding
                },
                body: 'item_id=' + itemId // Send item_id as form data
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the item from the cart display
                    const itemElement = document.getElementById(`cart-item-${itemId}`);
                    itemElement.remove();
                    
                    // Update total price dynamically
                    const totalElement = document.querySelector('.text-white strong');
                    totalElement.textContent = `Total: $${data.newTotal.toFixed(2)}`;
                } else {
                    alert(data.message || 'Error removing item from cart');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>
