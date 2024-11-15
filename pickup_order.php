<?php
session_start();
require 'config.php'; // Include the database connection file
$orderType = "Pickup"; // Specify the type of order

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding an item to the cart
if (isset($_POST['add_to_cart'])) {
    $itemId = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    // Fetch the item from the database
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        // Check if the item is already in the cart
        if (isset($_SESSION['cart'][$itemId])) {
            $_SESSION['cart'][$itemId]['quantity'] += $quantity; // Increase quantity
        } else {
            // Add item to the cart
            $_SESSION['cart'][$itemId] = [
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $quantity,
                'image' => $item['image'],
            ];
        }
    }
}

// Handle removing an item from the cart
if (isset($_POST['remove_item_id'])) {
    $itemId = $_POST['remove_item_id'];
    unset($_SESSION['cart'][$itemId]); // Remove the item from the cart
}

// Get the selected type from the URL parameter (default to 'special_menu')
$type = $_GET['type'] ?? 'special_menu';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="w-3/4 p-8">
            <h2 class="text-2xl font-bold mb-6"><?= ucfirst(str_replace('_', ' ', $type)); ?> Menu</h2>
            <div id="items-container" class="grid grid-cols-3 gap-6">
                <?php
                // Prepare query to fetch items based on the selected type
                $query = $pdo->prepare("SELECT * FROM menu_items WHERE type = ?");
                $query->execute([$type]);
                $items = $query->fetchAll(PDO::FETCH_ASSOC);

                if ($items) {
                    foreach ($items as $item) {
                        echo "<div class='bg-white p-6 rounded-lg shadow'>";
                        echo "<img src='uploads/{$item['image']}' alt='{$item['name']}' class='w-full h-48 object-cover rounded mb-4'>";
                        echo "<h3 class='text-lg font-bold'>{$item['name']}</h3>";
                        echo "<span class='block mt-2 text-orange-500 font-semibold'>\${$item['price']}</span>";
                        
                        // Add Quantity Selector and Add to Cart Button
                        echo "<form action='pickup_order.php' method='POST' class='mt-4'>";
                        echo "<label for='quantity' class='block text-sm text-gray-700'>Quantity:</label>";
                        echo "<input type='number' name='quantity' value='1' min='1' class='w-16 p-2 border rounded mt-1'>";
                        echo "<input type='hidden' name='item_id' value='{$item['id']}'>";
                        echo "<button type='submit' name='add_to_cart' class='w-full bg-orange-500 hover:bg-orange-600 text-white mt-3 py-2 rounded'>Add to Cart</button>";
                        echo "</form>";
                        echo "</div>";
                    }
                } else {
                    echo "<p class='text-gray-700'>No items available in this type.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
