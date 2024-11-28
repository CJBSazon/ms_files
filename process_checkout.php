<?php
// process_checkout.php
session_start();
require 'config.php'; // Include your database connection

if (isset($_POST['confirm_order']) && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Gather the order details from the POST data
    $serviceType = $_POST['service_type'];
    $paymentMethod = $_POST['payment_method'];
    $address = $_POST['address'];
    $contactNumber = $_POST['contact_no'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    try {
        // Insert the order into the orders table
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, service_type, payment_method, address, contact_number, latitude, longitude) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $serviceType, $paymentMethod, $address, $contactNumber, $latitude, $longitude]);

        // Get the last inserted order ID
        $orderId = $pdo->lastInsertId();

        // Insert the cart items into the order_items table
        if (isset($_POST['cart']) && is_array($_POST['cart'])) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price, image) VALUES (?, ?, ?, ?, ?)");

            foreach ($_POST['cart'] as $itemId => $item) {
                // Insert each cart item into the order_items table
                $stmt->execute([$orderId, $itemId, $item['quantity'], $item['price'], $item['image']]);
            }

            // Check if order items were successfully inserted
            if ($stmt->rowCount() > 0) {
                // JavaScript to display a success pop-up message
                echo "<script type='text/javascript'>
                        alert('Order has been placed successfully! Order ID: $orderId');
                        window.location.href = 'index.php'; // Redirect to index.php
                      </script>";
            } else {
                // JavaScript to display a failure pop-up message
                echo "<script type='text/javascript'>
                        alert('Failed to add items to the order.');
                        window.location.href = 'index.php'; // Redirect to index.php
                      </script>";
            }
        } else {
            // No items found in the cart, show an error pop-up
            echo "<script type='text/javascript'>
                    alert('No items found in the cart.');
                    window.location.href = 'index.php'; // Redirect to index.php
                  </script>";
        }
    } catch (PDOException $e) {
        // If there's a database error, show an error pop-up
        echo "<script type='text/javascript'>
                alert('Error: " . $e->getMessage() . "');
                window.location.href = 'index.php'; // Redirect to index.php
              </script>";
    }
} else {
    // Invalid request, show an error pop-up
    echo "<script type='text/javascript'>
            alert('Invalid request.');
            window.location.href = 'index.php'; // Redirect to index.php
          </script>";
}
?>
