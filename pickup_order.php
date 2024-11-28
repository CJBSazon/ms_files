<?php
session_start();
require 'config.php'; // Include your database connection

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user data from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Ensure keys in `$user` array exist to avoid warnings
$user['name'] = $user['name'] ?? '';
$user['address'] = $user['address'] ?? '';
$user['contact_no'] = $user['contact_no'] ?? '';

// If user doesn't exist, redirect
if (!$user) {
    header('Location: login.php');
    exit;
}

// Initialize total price variable
$totalPrice = 0;

// Handle the order confirmation
if (isset($_POST['confirm_order'])) {
    // Get order details from the POST request
    $serviceType = $_POST['service_type'] ?? '';
    $paymentMethod = $_POST['payment_method'] ?? '';
    $address = $_POST['address'] ?? '';
    $contactNumber = $_POST['contact_no'] ?? '';
    $latitude = $_POST['latitude'] ?? 0;
    $longitude = $_POST['longitude'] ?? 0;

    // Validate required fields
    if (empty($serviceType) || empty($paymentMethod) || empty($contactNumber)) {
        echo "Please fill all required fields.";
        exit;
    }

    // Insert the order into the 'orders' table
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, service_type, payment_method, address, contact_number, latitude, longitude) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $serviceType, $paymentMethod, $address, $contactNumber, $latitude, $longitude]);

    // Check if the order was successfully inserted
    if ($stmt->rowCount() > 0) {
        // Get the last inserted order ID
        $orderId = $pdo->lastInsertId();

        // Insert cart items into the 'order_items' table
        foreach ($_SESSION['cart'] as $itemId => $item) {
            // Ensure that item_id is being used as the correct reference
            $menuItemId = $item['item_id']; // Use item_id from session cart

            // Insert the order item (menu_item_id, quantity, price) into the 'order_items' table
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$orderId, $menuItemId, $item['quantity'], $item['price']]);
        }

        // Clear the cart after successful order placement
        unset($_SESSION['cart']);

        echo "Order has been placed successfully!";
        // Redirect to the order confirmation page or display success message
        header("Location: order_confirmation.php?order_id=$orderId");
        exit;
    } else {
        echo "Failed to place the order.";
    }
}

// Fetch all menu items
$query = $pdo->prepare("SELECT * FROM menu_items");
$query->execute();
$items = $query->fetchAll(PDO::FETCH_ASSOC);

// Group items by category (type)
$groupedItems = [];
foreach ($items as $item) {
    $groupedItems[$item['type']][] = $item;
}

// Calculate total price for displaying in the cart
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $itemId => $item) {
        $itemPrice = $item['price'] ?? 0;
        $itemQuantity = $item['quantity'] ?? 0;
        $totalPrice += $itemPrice * $itemQuantity;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Menu</title>
    <!-- Load Architects Daughter font from Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="./img/logo.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>
<style>
    /* Apply the Architects Daughter font */
    body {
            font-family: 'Architects Daughter', cursive;
        }

    #map {
        width: 100%;
        height: 250px;
    }
</style>
<body class="bg-gray-100" style="background-image: url('./img/bg home.jpg'); background-size: cover; background-position: center; height: 100vh;">
    <!-- Top Bar -->
    <div class="fixed bg-orange-500 text-white p-4 flex justify-between items-center w-full">
        <div class="flex items-center space-x-4">
            <img src="./img/logo.jpg" alt="Logo" class="w-10 h-10 rounded-full">
            <h1 class="text-lg font-semibold">Mabsi Soy</h1>
        </div>
        
        <div class="relative">
            <!-- Cart Icon with Item Count -->
            <button id="cartButton" class="relative">
                <img src="./img/add-to-cart.png" alt="Cart" class="w-12 h-12">
                <!-- Cart Count Badge -->
                <span id="cartCount" class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"><?= array_sum(array_column($_SESSION['cart'], 'quantity')) ?: 0; ?></span>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-4 md:p-8">
        <h2 class="text-4xl md:text-4xl font-bold mb-6 pt-32 text-center text-amber-800">Menu</h2>
        <div id="items-container" class="">
            <?php
            // Display items by category
            $categories = [
                'special_menu' => 'Special Menu',
                'combo_meal' => 'Combo Meal',
                'budget_meal' => 'Budget Meal',
                'ala_carte' => 'Ala Carte',
                'add_ons' => 'Add-Ons',
                'drinks_dessert' => 'Drinks & Dessert'
            ];

            foreach ($categories as $type => $categoryName) {
                if (isset($groupedItems[$type])) {
                    echo "<h3 class='text-lg md:text-xl font-bold mb-4'>{$categoryName}</h3>";
                    echo "<div class='grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8'>";
                    foreach ($groupedItems[$type] as $item) {
                        echo "<div class='bg-white p-4 md:p-6 rounded-lg shadow cursor-pointer item-card' data-item-id='{$item['id']}' data-item-name='{$item['name']}' data-item-price='{$item['price']}' data-item-image='{$item['image']}'>";
                        echo "<img src='uploads/{$item['image']}' alt='{$item['name']}' class='w-full h-40 sm:h-48 object-cover rounded mb-4'>";
                        echo "<h3 class='text-md md:text-lg font-bold'>{$item['name']}</h3>";
                        echo "<span class='block mt-2 text-orange-500 font-semibold'>₱{$item['price']}</span>";
                        echo "</div>";
                    }
                    echo "</div>";
                }
            }
            ?>
        </div>
    </div>

<!-- Cart Modal -->
<div id="cartModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full sm:w-1/2 md:w-1/3 max-h-full overflow-auto">
        <h2 class="text-xl font-bold mb-4">Your Cart</h2>

        <!-- Cart items list dynamically injected -->
        <div id="cartItemsList" class="mb-4">
            <!-- Cart items will be dynamically injected here -->
        </div>

        <!-- Cart Total Price -->
        <div class="flex justify-between items-center mt-4">
            <p class="text-lg font-semibold">Total Price: ₱<span id="totalPrice">0.00</span></p>
        </div>

        <!-- Checkout Form -->
        <form id="checkoutForm" method="POST" action="checkout.php" class="space-y-6 mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="service_type" class="block font-semibold mb-2">Service Type</label>
                    <select name="service_type" id="service_type" class="border p-2 w-full" onchange="toggleDeliveryFields()">
                        <option value="pickup" selected>Pickup</option>
                        <option value="delivery">Delivery</option>
                    </select>
                </div>
                <div>
                    <label for="payment_method" class="block font-semibold mb-2">Payment Method</label>
                    <select name="payment_method" id="payment_method" class="border p-2 w-full">
                        <option value="cash_on_delivery">Cash on Delivery</option>
                        <option value="gcash">Gcash</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="address" class="block font-semibold mb-2">Address</label>
                <input type="text" name="address" id="address" class="border p-2 w-full" value="<?= htmlspecialchars($user['address']) ?>" disabled>
            </div>

            <div>
                <label for="contact_no" class="block font-semibold mb-2">Contact Number</label>
                <input type="text" name="contact_no" id="contact_no" class="border p-2 w-full" required value="<?= htmlspecialchars($user['contact_no']) ?>">
            </div>

            <!-- Delivery Specific Fields (Map) -->
            <div id="mapContainer" class="hidden">
                <label class="block font-semibold mb-2">Select Delivery Location</label>
                <div id="map" class="h-64 mb-4"></div>
                <div class="coordinates hidden" id="coordinatesDisplay">
                    Latitude: <span id="latitudeDisplay"></span><br>
                    Longitude: <span id="longitudeDisplay"></span>
                </div>
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
            </div>

            <div class="flex justify-between mt-6">
                <button type="submit" name="confirm_order" id="confirmOrderButton" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg w-full md:w-auto">
                    Confirm Order
                </button>

                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg w-full md:w-auto text-center">Cancel</a>
            </div>
        </form>

        <!-- Close Cart Button -->
        <button id="closeCartButton" class="mt-4 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded w-full">Close</button>
    </div>
</div>




    <!-- Add Quantity Modal -->
    <div id="addQuantityModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full sm:w-1/2 md:w-1/3 max-h-full overflow-auto">
            <h2 class="text-xl font-bold mb-4">Select Quantity</h2>
            <p id="modalItemName" class="text-lg font-semibold"></p>
            <div class="flex items-center mt-4">
                <label for="quantity" class="mr-2">Quantity:</label>
                <input type="number" id="quantity" name="quantity" class="border rounded px-4 py-2 w-20" value="1" min="1">
            </div>
            <div class="mt-4 flex flex-col sm:flex-row sm:justify-between">
                <button id="addToCartButton" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded mr-2 mb-2 sm:mb-0">Add to Cart</button>
                <button id="closeAddQuantityModal" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Cancel</button>
            </div>
        </div>
    </div>
    <script>
// Variables
const cartButton = document.getElementById('cartButton');
const cartModal = document.getElementById('cartModal');
const cartCount = document.getElementById('cartCount');
const closeCartButton = document.getElementById('closeCartButton');
const checkoutButton = document.getElementById('checkoutButton');
const itemsContainer = document.getElementById('items-container');
const totalPriceElement = document.getElementById('totalPrice');
const addQuantityModal = document.getElementById('addQuantityModal');
const modalItemName = document.getElementById('modalItemName');
const quantityInput = document.getElementById('quantity');
const addToCartButton = document.getElementById('addToCartButton');
const closeAddQuantityModal = document.getElementById('closeAddQuantityModal');
const cartItemsList = document.getElementById('cartItemsList');

// Initialize cart data from PHP session or an empty object if no session cart
let cartData = <?= json_encode($_SESSION['cart'] ?? []); ?>; // Use session cart data
let totalPrice = <?= $totalPrice ?? 0; ?>; // Total price from PHP or 0 if not set

// Helper function for modal animations
function showModal(modal) {
    modal.classList.remove('hidden');
    modal.classList.remove('opacity-0', '-translate-y-10');
    modal.classList.add('opacity-100', 'translate-y-0');
}

function hideModal(modal) {
    modal.classList.remove('opacity-100', 'translate-y-0');
    modal.classList.add('opacity-0', '-translate-y-10');
    setTimeout(() => modal.classList.add('hidden'), 300); // Matches Tailwind animation duration
}

// Update cart count and render items
function updateCart() {
    const totalItems = Object.values(cartData).reduce((acc, item) => acc + item.quantity, 0);
    cartCount.textContent = totalItems;
    renderCartItems();
}

// Render cart items in modal
function renderCartItems() {
    cartItemsList.innerHTML = ''; // Clear the list first
    for (const [itemId, item] of Object.entries(cartData)) {
        const cartItem = document.createElement('div');
        cartItem.classList.add('flex', 'justify-between', 'items-center', 'mb-4');
        cartItem.innerHTML = `
            <div class="flex items-center">
                <img src="uploads/${item.image}" alt="${item.name}" class="w-12 h-12 object-cover mr-4">
                <div>
                    <p class="font-semibold">${item.name}</p>
                    <p class="text-sm">₱${item.price}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <p class="text-sm">Qty: ${item.quantity}</p>
                <button class="text-red-500 mt-2 remove-item" data-item-id="${itemId}">Remove</button>
            </div>
        `;
        cartItemsList.appendChild(cartItem);
    }
    totalPriceElement.textContent = totalPrice.toFixed(2);
}

// Show and close modals with animations
cartButton.addEventListener('click', () => {
    showModal(cartModal);
    updateCart();
});

closeCartButton.addEventListener('click', () => {
    hideModal(cartModal);
});

closeAddQuantityModal.addEventListener('click', () => {
    hideModal(addQuantityModal);
});

// Add item to cart from item container
itemsContainer.addEventListener('click', (e) => {
    const itemCard = e.target.closest('.item-card');
    if (itemCard) {
        const { itemId, itemName, itemPrice, itemImage } = itemCard.dataset;

        modalItemName.textContent = itemName;
        quantityInput.value = 1;
        showModal(addQuantityModal);

        // Handle Add to Cart Button
        addToCartButton.onclick = () => {
            const quantity = parseInt(quantityInput.value, 10);
            if (!cartData[itemId]) {
                // If item doesn't exist in the cart, add it
                cartData[itemId] = { name: itemName, price: parseFloat(itemPrice), quantity, image: itemImage };
            } else {
                // If item already exists, update the quantity
                cartData[itemId].quantity += quantity;
            }

            // Update total price
            totalPrice += parseFloat(itemPrice) * quantity;
            updateCart();

            // Close modal after adding to cart
            hideModal(addQuantityModal);
        };
    }
});

// Remove item from cart
cartItemsList.addEventListener('click', (e) => {
    const button = e.target;
    if (button.classList.contains('remove-item')) {
        const itemId = button.dataset.itemId;
        if (cartData[itemId]) {
            // Update total price before removing
            totalPrice -= cartData[itemId].price * cartData[itemId].quantity;
            delete cartData[itemId]; // Remove item from cartData
            updateCart(); // Re-render the cart
        }
    }
});

document.getElementById('confirmOrderButton').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Gather the order details from the form fields
    const orderDetails = {
        service_type: document.getElementById('service_type').value,
        payment_method: document.getElementById('payment_method').value,
        address: document.getElementById('address').value,
        contact_no: document.getElementById('contact_no').value,
        latitude: document.getElementById('latitude').value || 0,
        longitude: document.getElementById('longitude').value || 0
    };

    // Log the orderDetails to verify the data
    console.log('Order Details:', orderDetails);

    // Send data via AJAX to the server
    const data = new FormData();
    data.append('confirm_order', true);
    for (let key in orderDetails) {
        data.append(key, orderDetails[key]);
    }

    // Add cart items to FormData
    for (let itemId in cartData) {
        data.append('cart[' + itemId + '][name]', cartData[itemId].name);
        data.append('cart[' + itemId + '][quantity]', cartData[itemId].quantity);
        data.append('cart[' + itemId + '][price]', cartData[itemId].price);
        data.append('cart[' + itemId + '][image]', cartData[itemId].image);  // Assuming you want to save the image too
    }

    // Send the request via Fetch API
    fetch('process_checkout.php', {
        method: 'POST',
        body: data
    })
    .then(response => response.text())
    .then(result => {
        console.log('Response from server:', result);

        // Check if the order was placed successfully
        if (result.includes('Order has been placed successfully')) {
            console.log('Your order has been placed!');
            window.location.href = 'order_history.php'; // Redirect to order confirmation page
        } else {
            alert('Failed to place the order. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while placing the order.');
    });
});


// Initialize cart count and render items on page load
updateCart();
</script>


    <!-- Include Leaflet.js -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        const serviceTypeSelect = document.getElementById('service_type');
        const mapContainer = document.getElementById('mapContainer');
        const mapElement = document.getElementById('map');
        const latitudeDisplay = document.getElementById('latitudeDisplay');
        const longitudeDisplay = document.getElementById('longitudeDisplay');
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');

        let map, marker;
        let currentCoordinates = { lat: 14.93798, lng: 120.62178 };

        const bounds = L.latLngBounds([
            [14.900, 120.525], // Southwest
            [15.110, 120.675]  // Northeast
        ]);

        function initMap() {
            map = L.map(mapElement).setView([currentCoordinates.lat, currentCoordinates.lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
            map.setMaxBounds(bounds);
            marker = L.marker([currentCoordinates.lat, currentCoordinates.lng], { draggable: true }).addTo(map);

            map.on('click', function(e) {
                const latLng = e.latlng;
                if (bounds.contains(latLng)) {
                    marker.setLatLng(latLng);
                    latitudeDisplay.textContent = latLng.lat.toFixed(5);
                    longitudeDisplay.textContent = latLng.lng.toFixed(5);
                    latitudeInput.value = latLng.lat.toFixed(5);
                    longitudeInput.value = latLng.lng.toFixed(5);
                    document.getElementById('coordinatesDisplay').classList.remove('hidden');
                } else {
                    alert("Please select a location within Guagua or Sasmuan, Pampanga.");
                }
            });

            marker.on('dragend', function(e) {
                const latLng = e.target.getLatLng();
                if (bounds.contains(latLng)) {
                    latitudeDisplay.textContent = latLng.lat.toFixed(5);
                    longitudeDisplay.textContent = latLng.lng.toFixed(5);
                    latitudeInput.value = latLng.lat.toFixed(5);
                    longitudeInput.value = latLng.lng.toFixed(5);
                } else {
                    alert("Marker moved out of bounds.");
                    marker.setLatLng(currentCoordinates);
                }
            });
        }

        serviceTypeSelect.addEventListener('change', function() {
            if (this.value === 'delivery') {
                mapContainer.classList.remove('hidden');
                initMap();
            } else {
                mapContainer.classList.add('hidden');
            }
        });
    </script>

</body>
</html>
