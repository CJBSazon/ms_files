<?php
session_start();
require 'config.php'; // Include your database connection

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or handle accordingly
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
$user['contact_number'] = $user['contact_number'] ?? '';

// If user doesn't exist, redirect
if (!$user) {
    // Handle user not found, possibly redirect to login
    header('Location: login.php');
    exit;
}

// Initialize total price variable
$totalPrice = 0;

// Handle adding an item to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $itemId = $_POST['item_id'] ?? null;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

    // Ensure item ID is valid
    if ($itemId && $quantity > 0) {
        // Fetch the item from the database
        $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // Add or update the item in the cart
            if (isset($_SESSION['cart'][$itemId])) {
                $_SESSION['cart'][$itemId]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$itemId] = [
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $quantity,
                    'image' => $item['image'],
                ];
            }
        }
    }

    // Calculate updated total price and item count
    $totalPrice = array_reduce($_SESSION['cart'], function ($sum, $item) {
        $itemPrice = $item['price'] ?? 0;
        $itemQuantity = $item['quantity'] ?? 0;
        return $sum + ($itemPrice * $itemQuantity);
    }, 0);
    $totalItems = array_sum(array_map(fn($item) => $item['quantity'] ?? 0, $_SESSION['cart'] ?? []));
    echo json_encode(['success' => true, 'cart' => $_SESSION['cart'], 'totalPrice' => $totalPrice, 'totalItems' => $totalItems]);
    exit;
}

// Handle deleting an item from the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item'])) {
    $itemId = $_POST['item_id'] ?? null;

    if ($itemId && isset($_SESSION['cart'][$itemId])) {
        unset($_SESSION['cart'][$itemId]);
    }

    // Calculate updated total price and item count
    $totalPrice = array_reduce($_SESSION['cart'], function ($sum, $item) {
        $itemPrice = $item['price'] ?? 0;
        $itemQuantity = $item['quantity'] ?? 0;
        return $sum + ($itemPrice * $itemQuantity);
    }, 0);
    $totalItems = array_sum(array_map(fn($item) => $item['quantity'] ?? 0, $_SESSION['cart'] ?? []));
    echo json_encode(['success' => true, 'cart' => $_SESSION['cart'], 'totalPrice' => $totalPrice, 'totalItems' => $totalItems]);
    exit;
}

// Fetch all menu items from all categories
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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>

<style>
    #map {
        width: 100%;
        height: 250px; /* Adjust as needed */
    }
</style>


<body class="bg-gray-100">
    <!-- Top Bar -->
    <div class="fixed bg-orange-500 text-white p-4 flex justify-between items-center bg-opacity-75 w-full">
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
    <h2 class="text-xl md:text-2xl font-bold mb-6 pt-32">Menu</h2>
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
                echo "<h3 class='text-lg md:text-xl font-semibold mb-4'>{$categoryName}</h3>";
                echo "<div class='grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8'>";
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
        <div id="cartItemsList" class="mb-4">
            <!-- Cart items will be dynamically injected here -->
        </div>
        <div class="flex justify-between mt-4">
            <p class="text-lg font-semibold">Total Price: ₱<span id="totalPrice"><?= number_format($totalPrice, 2); ?></span></p>
            <button id="checkoutButton" class="bg-green-500 text-white px-4 py-2 rounded">Proceed to Checkout</button>
        </div>
        <button id="closeCartButton" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded w-full">Close</button>
    </div>
</div>


   <!-- Checkout Modal -->
<div id="checkoutModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full sm:w-1/2 md:w-1/3 max-h-full overflow-auto">
        <h2 class="text-2xl font-semibold mb-6 text-center">Checkout</h2>
        <form id="checkoutForm" action="order_history.php" method="POST">
            <!-- Service Type Selection -->
            <label for="service_type" class="block text-gray-700 mb-2">Service Type</label>
            <select id="service_type" name="service_type" class="border w-full p-3 rounded-lg mb-6 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="pickup">Pickup</option>
                <option value="delivery">Delivery</option>
            </select>


            <!-- Address -->
            <label for="address" class="block text-gray-700 mb-2">Address</label>
            <input type="text" id="address" name="address" class="border w-full p-3 rounded-lg mb-6 focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?= htmlspecialchars($user['address']) ?>" required>

            <!-- Contact Number -->
            <label for="contact" class="block text-gray-700 mb-2">Contact Number</label>
            <input type="text" id="contact" name="contact" class="border w-full p-3 rounded-lg mb-6 focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?= htmlspecialchars($user['contact_number']) ?>" required>


            <!-- Coordinates Display above the Map -->
            <div id="coordinatesDisplay" class="mb-4 text-gray-700 text-center hidden">
                Coordinates: <span id="latitude">0.00000</span>, <span id="longitude">0.00000</span>
            </div>

            <!-- Map Container, initially hidden -->
            <div id="mapContainer" class="w-full h-64 mt-4 hidden">
                <div id="map" style="height: 100%;"></div> <!-- Map will go here -->
            </div>

            <!-- Order Now Button -->
            <button type="submit" id="orderNowButton" class="bg-blue-600 hover:bg-blue-700 text-white w-full py-3 rounded-lg mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">Order Now!</button>

            <!-- Close Button -->
            <button type="button" id="closeCheckoutButton" class="bg-gray-600 hover:bg-gray-700 text-white w-full py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500">Close</button>
        </form>
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
            <button id="addToCartButton" class="bg-green-500 text-white px-4 py-2 rounded mr-2 mb-2 sm:mb-0">Add to Cart</button>
            <button id="closeAddQuantityModal" class="bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
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
const checkoutForm = document.getElementById('checkoutForm');

let cartData = <?= json_encode($_SESSION['cart']); ?>;
let totalPrice = <?= $totalPrice ?>;

   

// Helper function for animations
function showModal(modal) {
    modal.classList.remove('hidden');
    modal.classList.remove('opacity-0', '-translate-y-10');
    modal.classList.add('opacity-100', 'translate-y-0');
}

function hideModal(modal) {
    modal.classList.remove('opacity-100', 'translate-y-0');
    modal.classList.add('opacity-0', '-translate-y-10');
    setTimeout(() => modal.classList.add('hidden'), 300); // Matches the Tailwind animation duration
}

// Update cart count and items
function updateCart() {
    const totalItems = Object.values(cartData).reduce((acc, item) => acc + item.quantity, 0);
    cartCount.textContent = totalItems;
    renderCartItems();
}

// Render cart items in modal
function renderCartItems() {
    cartItemsList.innerHTML = '';
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
            <div>
                <p class="text-sm">Qty: ${item.quantity}</p>
                <button class="text-red-500 mt-2" data-item-id="${itemId}">Remove</button>
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

document.getElementById('closeCheckoutButton').addEventListener('click', () => {
    hideModal(checkoutModal);
});

// Add to cart
itemsContainer.addEventListener('click', (e) => {
    const itemCard = e.target.closest('.item-card');
    if (itemCard) {
        const { itemId, itemName, itemPrice, itemImage } = itemCard.dataset;

        modalItemName.textContent = itemName;
        quantityInput.value = 1;
        showModal(addQuantityModal);

        addToCartButton.onclick = () => {
            const quantity = parseInt(quantityInput.value, 10);
            if (!cartData[itemId]) {
                cartData[itemId] = { name: itemName, price: parseFloat(itemPrice), quantity, image: itemImage };
            } else {
                cartData[itemId].quantity += quantity;
            }
            totalPrice += parseFloat(itemPrice) * quantity;
            updateCart();
            hideModal(addQuantityModal);
        };
    }
});

// Remove from cart
cartItemsList.addEventListener('click', (e) => {
    const button = e.target;
    if (button.tagName === 'BUTTON') {
        const itemId = button.dataset.itemId;
        if (cartData[itemId]) {
            totalPrice -= cartData[itemId].price * cartData[itemId].quantity;
            delete cartData[itemId];
            updateCart();
        }
    }
});

// Checkout
checkoutButton.addEventListener('click', () => {
    showModal(checkoutModal);
    if (!map) loadHEREMapsScript();
});

// Submit checkout form
checkoutForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(checkoutForm);

    fetch('order_history.php', { method: 'POST', body: formData })
        .then((response) => {
            // Log the response status and text to debug
            console.log('Response status:', response.status);
            return response.text(); // Get raw response text to debug
        })
        .then((text) => {
            console.log('Response text:', text); // Log the text for inspection
            try {
                const data = JSON.parse(text); // Attempt to parse JSON manually
                if (data.success) {
                    alert('Order placed successfully!');
                    cartData = {};
                    totalPrice = 0;
                    updateCart();
                    hideModal(checkoutModal);
                } else {
                    alert('Error placing order. Please try again.');
                }
            } catch (error) {
                console.error('Error parsing JSON:', error);
                alert('Error Found');
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('Error Found');
        });
});



// Initialize cart count on page load
updateCart();

</script>

<!-- Include Leaflet.js and Tailwind JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // Get modal elements
    const checkoutModal = document.getElementById('checkoutModal');
    const closeCheckoutButton = document.getElementById('closeCheckoutButton');
    const serviceTypeSelect = document.getElementById('service_type');
    const mapContainer = document.getElementById('mapContainer');
    const locationOutput = document.getElementById('locationOutput');
    const mapElement = document.getElementById('map');
    const coordinatesDisplay = document.getElementById('coordinatesDisplay');
    const latitudeSpan = document.getElementById('latitude');
    const longitudeSpan = document.getElementById('longitude');

    // Initialize Leaflet Map (Pampanga, Philippines as default)
    let map, marker;
    let currentCoordinates = { lat: 15.084, lng: 120.648 };

    function initMap() {
        map = L.map(mapElement).setView([currentCoordinates.lat, currentCoordinates.lng], 13);

        // Set up OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add a marker to the map
        marker = L.marker([currentCoordinates.lat, currentCoordinates.lng]).addTo(map);

        // Update marker and coordinates on map click
        map.on('click', function(e) {
            currentCoordinates.lat = e.latlng.lat;
            currentCoordinates.lng = e.latlng.lng;

            // Update marker position
            marker.setLatLng(e.latlng);

            // Update the coordinates display
            latitudeSpan.textContent = e.latlng.lat.toFixed(5);
            longitudeSpan.textContent = e.latlng.lng.toFixed(5);

            // Show the coordinates display div
            coordinatesDisplay.classList.remove('hidden');
        });
    }

    // Show the modal when the "Order Now" button is clicked
    serviceTypeSelect.addEventListener('change', function() {
        if (this.value === 'delivery') {
            mapContainer.classList.remove('hidden'); // Show the map container
            initMap(); // Initialize the map
        } else {
            mapContainer.classList.add('hidden'); // Hide the map for pickup
        }
    });

    // Close modal functionality
    closeCheckoutButton.addEventListener('click', function() {
        checkoutModal.classList.add('hidden');
    });

    // Handle modal visibility when order button is clicked
    const orderBtn = document.getElementById('orderBtn');  // Assuming there's a button to trigger the modal
    if (orderBtn) {
        orderBtn.addEventListener('click', function() {
            checkoutModal.classList.remove('hidden');  // Show the modal
        });
    }

    // Close modal when clicking on Close button inside the modal
    closeCheckoutButton.addEventListener('click', function() {
        checkoutModal.classList.add('hidden');
    });

    // Handle form submission
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const name = document.getElementById('name').value;
        const address = document.getElementById('address').value;
        const contact = document.getElementById('contact').value;
        const coordinates = `${currentCoordinates.lat.toFixed(5)}, ${currentCoordinates.lng.toFixed(5)}`;

        // Here, you can send the data to the server using fetch or any other method.
        console.log(`Name: ${name}, Address: ${address}, Contact: ${contact}, Coordinates: ${coordinates}`);

        // Hide the modal after submission
        checkoutModal.classList.add('hidden');
    });

    
// Handle form submission
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const name = document.getElementById('name').value;
    const address = document.getElementById('address').value;
    const contact = document.getElementById('contact').value;
    const coordinates = `${currentCoordinates.lat.toFixed(5)}, ${currentCoordinates.lng.toFixed(5)}`;
    
    // Append coordinates to the form
    const latInput = document.createElement('input');
    latInput.type = 'hidden';
    latInput.name = 'latitude';
    latInput.value = currentCoordinates.lat.toFixed(6);  // Set the latitude value

    const lngInput = document.createElement('input');
    lngInput.type = 'hidden';
    lngInput.name = 'longitude';
    lngInput.value = currentCoordinates.lng.toFixed(6);  // Set the longitude value

    // Append the new inputs to the form
    this.appendChild(latInput);
    this.appendChild(lngInput);

    // Submit the form
    this.submit();
});


</script>





</body>
</html>
