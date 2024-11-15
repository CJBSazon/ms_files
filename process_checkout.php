<?php
session_start();
require 'config.php';

// Check if cart exists and form data is valid
if (isset($_SESSION['cart']) && !empty($_POST['email']) && isset($_POST['serviceType'])) {
    $email = $_POST['email'];
    $serviceType = $_POST['serviceType'];
    $address = $_POST['address'] ?? null;
    $contactNumber = $_POST['contactNumber'] ?? null;
    $landmark = $_POST['landmark'] ?? null;
    $cart = json_decode($_POST['cart'], true);

    // Save user email and contact in session if they are provided
    $_SESSION['user_email'] = $email;
    $_SESSION['user_contact'] = $contactNumber;

    // Insert order data into the orders table
    $stmt = $pdo->prepare("INSERT INTO orders (email, service_type, address, contact_number, landmark, order_date) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$email, $serviceType, $address, $contactNumber, $landmark]);

    // Get the last inserted order ID
    $orderId = $pdo->lastInsertId();

    // Insert items from the cart into the order_items table
    foreach ($cart as $itemId => $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $itemId, $item['quantity'], $item['price']]);
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid order data']);
}
?>
