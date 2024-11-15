<?php
session_start(); // Ensure session is started

// Check if item_id is set and valid
if (isset($_POST['item_id']) && !empty($_POST['item_id'])) {
    $itemId = $_POST['item_id'];

    // Check if the item exists in the cart
    if (isset($_SESSION['cart'][$itemId])) {
        // Remove the item from the cart
        unset($_SESSION['cart'][$itemId]);

        // Recalculate the new total price
        $newTotal = 0;
        foreach ($_SESSION['cart'] as $item) {
            $newTotal += $item['price'] * $item['quantity'];
        }

        // Respond with success and the new total
        echo json_encode(['success' => true, 'newTotal' => $newTotal]);
    } else {
        // Item not found in the cart
        echo json_encode(['success' => false, 'message' => 'Item not found']);
    }
} else {
    // Invalid request (no item_id provided)
    echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
}
