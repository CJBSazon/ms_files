<?php
session_start();
require_once 'db_connection.php'; // Assuming you have a file for DB connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_order'])) {
    try {
        // Prepare database connection (PDO)
        $pdo = new PDO('mysql:host=localhost;dbname=food_ordering_db', 'username', 'password');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Capture form data
        $user_id = $_SESSION['user_id']; // Assuming you have user ID in session
        $email = $_SESSION['email'];     // Assuming you have user's email in session
        $service_type = $_POST['service_type'];
        $payment_method = $_POST['payment_method'];
        $address = $_POST['address'];
        $contact_no = $_POST['contact_no'];
        $latitude = $_POST['latitude'] ?? null;
        $longitude = $_POST['longitude'] ?? null;

        // Get cart items (you might store the cart items in a session variable)
        $cartItems = $_SESSION['cart_items']; // Assuming cart items are stored in session

        // Calculate total price from cart items (you would loop through cart items to get the total)
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }

        // Start a transaction to ensure data integrity
        $pdo->beginTransaction();

        // Insert into the orders table
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, email, service_type, address, contact_number, order_date, status, latitude, longitude, payment_method, total_price)
            VALUES (:user_id, :email, :service_type, :address, :contact_no, NOW(), 'pending', :latitude, :longitude, :payment_method, :total_price)
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':email' => $email,
            ':service_type' => $service_type,
            ':address' => $address,
            ':contact_no' => $contact_no,
            ':latitude' => $latitude,
            ':longitude' => $longitude,
            ':payment_method' => $payment_method,
            ':total_price' => $totalPrice,
        ]);

        // Get the last inserted order ID
        $order_id = $pdo->lastInsertId();

        // Insert each item into the order_items table
        foreach ($cartItems as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, menu_item_id, quantity, price)
                VALUES (:order_id, :menu_item_id, :quantity, :price)
            ");
            $stmt->execute([
                ':order_id' => $order_id,
                ':menu_item_id' => $item['menu_item_id'],
                ':quantity' => $item['quantity'],
                ':price' => $item['price'],
            ]);
        }

        // Commit the transaction
        $pdo->commit();

        // Clear the cart after order is placed (optional)
        unset($_SESSION['cart_items']);

        // Redirect to order confirmation page
        header('Location: order_confirmation.php');
        exit;
    } catch (Exception $e) {
        // Rollback if an error occurs
        $pdo->rollBack();
        echo "Failed: " . $e->getMessage();
    }
}
?>
