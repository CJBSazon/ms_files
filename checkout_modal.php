<?php
session_start();
require 'config.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user data from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Ensure the user exists
if (!$user) {
    header('Location: login.php'); // Redirect to login if the user is not found
    exit;
}

// Initialize cart details
$cart = $_SESSION['cart'] ?? [];
$totalPrice = 0;

// Calculate total price from the cart
foreach ($cart as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}

// Handle order confirmation submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_order'])) {
    $serviceType = $_POST['service_type'] ?? '';
    $paymentMethod = $_POST['payment_method'] ?? '';
    $address = $_POST['address'] ?? '';
    $contact = $_POST['contact_number'] ?? '';
    $latitude = $_POST['latitude'] ?? 0;
    $longitude = $_POST['longitude'] ?? 0;

    // Validate the form inputs
    if (empty($serviceType) || empty($paymentMethod) || empty($address) || empty($contact)) {
        $errorMessage = "Please fill all required fields.";
    } else {
        // Insert the order into the database
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, service_type, payment_method, address, contact_number, latitude, longitude, total_price, order_date) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$userId, $serviceType, $paymentMethod, $address, $contact, $latitude, $longitude, $totalPrice]);

        // Get the last inserted order ID
        $orderId = $pdo->lastInsertId();

        // Insert order items into the order_items table
        foreach ($cart as $itemId => $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$orderId, $itemId, $item['quantity'], $item['price']]);
        }

        // Clear the cart after successful order
        unset($_SESSION['cart']);

        // Redirect to a success page or order history
        header('Location: order_history.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<style>
    /* Apply custom font */
    body {
        font-family: 'Architects Daughter', cursive;
    }

    /* Adjust map container size */
    #map {
        height: 300px;
        width: 100%;
    }
</style>

<body class="bg-gray-100">

    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-center text-amber-800 mb-6">Checkout</h1>

        <!-- Display error message if any -->
        <?php if (isset($errorMessage)): ?>
            <div class="bg-red-500 text-white p-4 mb-6 rounded">
                <p><?= htmlspecialchars($errorMessage) ?></p>
            </div>
        <?php endif; ?>

        <!-- Display cart items -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Your Cart</h2>
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="border-b p-2 text-left">Item</th>
                        <th class="border-b p-2 text-left">Price</th>
                        <th class="border-b p-2 text-left">Quantity</th>
                        <th class="border-b p-2 text-left">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item): ?>
                        <tr>
                            <td class="border-b p-2"><?= htmlspecialchars($item['name']) ?></td>
                            <td class="border-b p-2">₱<?= number_format($item['price'], 2) ?></td>
                            <td class="border-b p-2"><?= $item['quantity'] ?></td>
                            <td class="border-b p-2">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="border-b p-2 font-semibold text-right">Total:</td>
                        <td class="border-b p-2">₱<?= number_format($totalPrice, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Checkout Form -->
        <form method="POST" action="checkout.php" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="service_type" class="block font-semibold mb-2">Service Type</label>
                    <select name="service_type" id="service_type" class="border p-2 w-full">
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
                <input type="text" name="address" id="address" class="border p-2 w-full" required value="<?= htmlspecialchars($user['address']) ?>">
            </div>
            <div>
                <label for="contact_number" class="block font-semibold mb-2">Contact Number</label>
                <input type="text" name="contact_number" id="contact_number" class="border p-2 w-full" required value="<?= htmlspecialchars($user['contact_number']) ?>">
            </div>

            <!-- Hidden fields for latitude and longitude -->
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            <!-- Map Section -->
            <div id="mapContainer" class="w-full h-64 mt-6 hidden">
                <div id="map"></div> <!-- Map will go here -->
            </div>

            <div class="flex justify-between mt-6">
                <button type="submit" name="confirm_order" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg w-full md:w-auto">
                    Confirm Order
                </button>
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg w-full md:w-auto text-center">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        // Map handling
        const serviceTypeSelect = document.getElementById('service_type');
        const mapContainer = document.getElementById('mapContainer');
        const mapElement = document.getElementById('map');
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
                    latitudeInput.value = latLng.lat.toFixed(5);
                    longitudeInput.value = latLng.lng.toFixed(5);
                } else {
                    alert("Please select a location within Guagua or Sasmuan, Pampanga.");
                }
            });

            marker.on('dragend', function(e) {
                const latLng = e.target.getLatLng();
                if (bounds.contains(latLng)) {
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
