<?php
session_start();
require 'config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Product Management
        if (isset($_POST['upload_product'])) {
            $name = $_POST['product_name'];
            $description = $_POST['product_description'];
            $image = $_FILES['product_image']['name'];
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($image);

            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                $query = $pdo->prepare("INSERT INTO products (name, description, image) VALUES (?, ?, ?)");
                $query->execute([$name, $description, $image]);
                echo "Product uploaded successfully!";
            } else {
                echo "Error uploading file.";
            }
        } elseif (isset($_POST['update_product'])) {
            $product_id = $_POST['product_id'];
            $name = $_POST['product_name'];
            $description = $_POST['product_description'];

            $query = $pdo->prepare("UPDATE products SET name = ?, description = ? WHERE id = ?");
            $query->execute([$name, $description, $product_id]);
            echo "Product updated successfully!";
        } elseif (isset($_POST['delete_product'])) {
            $product_id = $_POST['product_id'];
            $query = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $query->execute([$product_id]);
            echo "Product deleted successfully!";
        }

        // Announcement Management
        elseif (isset($_POST['upload_announcement'])) {
            $title = $_POST['announcement_title'];
            $description = $_POST['announcement_description'];
            $image = $_FILES['announcement_image']['name'];
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($image);

            if (move_uploaded_file($_FILES['announcement_image']['tmp_name'], $target_file)) {
                $query = $pdo->prepare("INSERT INTO announcements (title, description, image) VALUES (?, ?, ?)");
                $query->execute([$title, $description, $image]);
                echo "Announcement uploaded successfully!";
            } else {
                echo "Error uploading file.";
            }
        } elseif (isset($_POST['update_announcement'])) {
            $announcement_id = $_POST['announcement_id'];
            $title = $_POST['announcement_title'];
            $description = $_POST['announcement_description'];

            $query = $pdo->prepare("UPDATE announcements SET title = ?, description = ? WHERE id = ?");
            $query->execute([$title, $description, $announcement_id]);
            echo "Announcement updated successfully!";
        } elseif (isset($_POST['delete_announcement'])) {
            $announcement_id = $_POST['announcement_id'];
            $query = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
            $query->execute([$announcement_id]);
            echo "Announcement deleted successfully!";
        }

        // Order Management (Uploading menu items)
        if (isset($_POST['upload_item'])) {
            $name = $_POST['item_name'];
            $price = $_POST['item_price'];
            $type = $_POST['order_type'];
            $image = $_FILES['item_image']['name'];
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($image);

            if (move_uploaded_file($_FILES['item_image']['tmp_name'], $target_file)) {
                $query = $pdo->prepare("INSERT INTO menu_items (name, price, type, image) VALUES (?, ?, ?, ?)");
                $query->execute([$name, $price, $type, $image]);
                echo "Item uploaded successfully!";
            } else {
                echo "Error uploading image.";
            }
        } elseif (isset($_POST['update_item'])) {
            $item_id = $_POST['item_id'];
            $name = $_POST['item_name'];
            $price = $_POST['item_price'];
            $type = $_POST['order_type'];

            $query = $pdo->prepare("UPDATE menu_items SET name = ?, price = ?, type = ? WHERE id = ?");
            $query->execute([$name, $price, $type, $item_id]);
            echo "Item updated successfully!";
        } elseif (isset($_POST['delete_item'])) {
            $item_id = $_POST['item_id'];
            $query = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
            $query->execute([$item_id]);
            echo "Item deleted successfully!";
        }
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
}

// Fetch data for display
try {
    $query = $pdo->query("SELECT * FROM menu_items");
    $menu_items = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error fetching menu items: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];

    // Check if 'status' is set and not empty
    if (isset($_POST['status']) && !empty($_POST['status'])) {
        $status = $_POST['status'];

        // Update the order status in the database
        $update_stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $update_stmt->execute([$status, $order_id]);

        // Redirect to prevent form resubmission
        header('Location: admin.php?page=orders');
        exit();
    } else {
        // Handle cases where 'status' is missing or empty
        echo "<div class='bg-red-500 text-white p-4 rounded'>Error: The status field is required.</div>";
    }
}




// Fetch all orders
if (isset($_GET['page']) && $_GET['page'] == 'orders') {
    $stmt = $pdo->prepare("SELECT * FROM orders ORDER BY order_date DESC");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch other data for display
$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
$announcements = $pdo->query("SELECT * FROM announcements")->fetchAll(PDO::FETCH_ASSOC);
$orders = $pdo->query("SELECT * FROM orders")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="./img/logo.jpg">
    <script>
        function openTab(tabId) {
            document.querySelectorAll('.section').forEach(section => section.classList.add('hidden'));
            document.getElementById(tabId).classList.remove('hidden');
        }

        window.onload = function() {
            openTab('order-management');
        };

         window.onload = function() {
            openTab('order-list');
        };
    </script>
</head>
<body class="bg-gray-100">

    <!-- Sidebar -->
    <div class="w-64 h-screen bg-orange-500 text-white p-4 fixed top-0 left-0">
        <div class="flex items-center mb-6">
            <img src="./img/logo.jpg" alt="Business Logo" class="h-16 w-16 mr-4 rounded-full">
            <span class="text-xl font-semibold">Admin Panel</span>
        </div>
        <div class="space-y-4">
            <a href="javascript:void(0)" onclick="openTab('product-management')" class="block py-2 px-4 bg-orange-600 text-white hover:bg-orange-700 rounded">Product Management</a>
            <a href="javascript:void(0)" onclick="openTab('announcement-management')" class="block py-2 px-4 bg-orange-600 text-white hover:bg-orange-700 rounded">Announcement Management</a>
            <a href="javascript:void(0)" onclick="openTab('order-management')" class="block py-2 px-4 bg-orange-600 text-white hover:bg-orange-700 rounded">Order Management</a>
            <a href="javascript:void(0)" onclick="openTab('order-list')" class="block py-2 px-4 bg-orange-600 text-white hover:bg-orange-700 rounded">Order List</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold mb-6">Welcome to Admin Panel, <?= $_SESSION['username'] ?></h1>

        <?php include 'product_management.php'; ?>
        <?php include 'announcement_management.php'; ?>
        <?php include 'order_management.php'; ?>
        <?php include 'order_list.php'; ?>
        

    </div>

</body>
</html>
