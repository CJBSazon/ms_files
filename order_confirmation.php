<?php
session_start();
require 'config.php'; // Include your database connection

// Check if order_id is passed in the URL
if (!isset($_GET['order_id'])) {
    echo "Order ID is missing.";
    exit;
}

$orderId = $_GET['order_id'];

// Fetch order details from the database
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Ensure that the order exists
if (!$order) {
    echo "Order not found.";
    exit;
}

// Update the query to use 'menu_item_id' instead of 'item_id'
$stmt = $pdo->prepare("SELECT oi.*, mi.name, mi.price 
                       FROM order_items oi 
                       JOIN menu_items mi 
                       ON oi.menu_item_id = mi.id 
                       WHERE oi.order_id = ?");
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total price of the order
$totalPrice = 0;
foreach ($orderItems as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
</head>
<body class="bg-gray-100" style="background-image: url('./img/bg home.jpg'); background-size: cover; background-position: center; height: 100vh;">
    <div class="fixed bg-orange-500 text-white p-4 flex justify-between items-center w-full">
        <div class="flex items-center space-x-4">
            <img src="./img/logo.jpg" alt="Logo" class="w-10 h-10 rounded-full">
            <h1 class="text-lg font-semibold">Mabsi Soy</h1>
        </div>
    </div>

    <div class="p-4 md:p-8 pt-32">
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-3xl font-bold mb-6 text-center text-amber-800">Order Confirmation</h2>

            <p class="text-xl font-semibold mb-4">Thank you for your order, <?= htmlspecialchars($order['service_type']); ?>!</p>

            <div class="mb-4">
                <h3 class="text-lg font-semibold">Order Details:</h3>
                <p><strong>Order ID:</strong> <?= htmlspecialchars($orderId); ?></p>
                <p><strong>Service Type:</strong> <?= htmlspecialchars($order['service_type']); ?></p>
                <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']); ?></p>
                <p><strong>Delivery Address:</strong> <?= htmlspecialchars($order['address']); ?></p>
                <p><strong>Contact Number:</strong> <?= htmlspecialchars($order['contact_number']); ?></p>
            </div>

            <h3 class="text-lg font-semibold mb-4">Order Items:</h3>
            <table class="w-full table-auto border-collapse mb-6">
                <thead>
                    <tr>
                        <th class="border p-2 text-left">Item</th>
                        <th class="border p-2 text-left">Price</th>
                        <th class="border p-2 text-left">Quantity</th>
                        <th class="border p-2 text-left">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $item): ?>
                        <tr>
                            <td class="border p-2"><?= htmlspecialchars($item['name']); ?></td>
                            <td class="border p-2">₱<?= number_format($item['price'], 2); ?></td>
                            <td class="border p-2"><?= htmlspecialchars($item['quantity']); ?></td>
                            <td class="border p-2">₱<?= number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="flex justify-between font-semibold text-lg">
                <span>Total Price:</span>
                <span>₱<?= number_format($totalPrice, 2); ?></span>
            </div>

            <div class="mt-6 text-center">
                <a href="index.php" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg">
                    Go to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
