<?php
session_start();
require 'config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}
// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');

    try {
        // Delete Item
        if (isset($_POST['delete_item'])) {
            $item_id = (int)$_POST['item_id'];
            $query = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
            $query->execute([$item_id]);
            echo json_encode(['status' => 'success', 'message' => 'Item deleted successfully!']);
        }
        // Update Item
        elseif (isset($_POST['update_item'])) {
            $item_id = (int)$_POST['item_id'];
            $name = trim($_POST['item_name']);
            $price = trim($_POST['item_price']);
            $type = trim($_POST['order_type']);

            if (!empty($name) && !empty($price) && !empty($type)) {
                $query = $pdo->prepare("UPDATE menu_items SET name = ?, price = ?, type = ? WHERE id = ?");
                $query->execute([$name, $price, $type, $item_id]);
                echo json_encode(['status' => 'success', 'message' => 'Item updated successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            }
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch data for display
try {
    $menu_items = $pdo->query("SELECT * FROM menu_items")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error fetching data: " . $e->getMessage();
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
                echo "";
            } else {
                echo "Error uploading file.";
            }
        } elseif (isset($_POST['update_product'])) {
            $product_id = $_POST['product_id'];
            $name = $_POST['product_name'];
            $description = $_POST['product_description'];

            $query = $pdo->prepare("UPDATE products SET name = ?, description = ? WHERE id = ?");
            $query->execute([$name, $description, $product_id]);
            echo "";
        } elseif (isset($_POST['delete_product'])) {
            $product_id = $_POST['product_id'];
            $query = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $query->execute([$product_id]);
            echo "";
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
                echo "";
            } else {
                echo "Error uploading file.";
            }
        } elseif (isset($_POST['update_announcement'])) {
            $announcement_id = $_POST['announcement_id'];
            $title = $_POST['announcement_title'];
            $description = $_POST['announcement_description'];

            $query = $pdo->prepare("UPDATE announcements SET title = ?, description = ? WHERE id = ?");
            $query->execute([$title, $description, $announcement_id]);
            echo "";
        } elseif (isset($_POST['delete_announcement'])) {
            $announcement_id = $_POST['announcement_id'];
            $query = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
            $query->execute([$announcement_id]);
            echo "";
        }

        // Menu Item Management
        elseif (isset($_POST['upload_item'])) {
            $name = trim($_POST['item_name']);
            $price = trim($_POST['item_price']);
            $type = trim($_POST['order_type']);
            $image = $_FILES['item_image']['name'];

            if (!empty($name) && !empty($price) && !empty($type) && !empty($image)) {
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($image);

                if (move_uploaded_file($_FILES['item_image']['tmp_name'], $target_file)) {
                    $query = $pdo->prepare("INSERT INTO menu_items (name, price, type, image) VALUES (?, ?, ?, ?)");
                    $query->execute([$name, $price, $type, $image]);
                    echo "";
                } else {
                    echo "";
                }
            } else {
                echo "All fields are required.";
            }
        } elseif (isset($_POST['update_item'])) {
            $item_id = (int)$_POST['item_id'];
            $name = trim($_POST['item_name']);
            $price = trim($_POST['item_price']);
            $type = trim($_POST['order_type']);

            if (!empty($name) && !empty($price) && !empty($type)) {
                $query = $pdo->prepare("UPDATE menu_items SET name = ?, price = ?, type = ? WHERE id = ?");
                $query->execute([$name, $price, $type, $item_id]);
                echo "";
            } else {
                echo "";
            }
        } elseif (isset($_POST['delete_item'])) {
            $item_id = (int)$_POST['item_id'];
            $query = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
            $query->execute([$item_id]);
            echo "";
        }

        // Order Status Update
        elseif (isset($_POST['update_status'])) {
            $order_id = $_POST['order_id'];
            $status = $_POST['status'] ?? '';

            if (!empty($status)) {
                $query = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
                $query->execute([$status, $order_id]);
                header('Location: admin.php?page=orders');
                exit();
            } else {
                echo "";
            }
        }
    } catch (Exception $e) {
        echo "An error occurred: " . htmlspecialchars($e->getMessage());
    }
}

// Fetch Data for Display
try {
    $menu_items = $pdo->query("SELECT * FROM menu_items")->fetchAll(PDO::FETCH_ASSOC);
    $products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
    $announcements = $pdo->query("SELECT * FROM announcements")->fetchAll(PDO::FETCH_ASSOC);
    $orders = $pdo->query("SELECT * FROM orders")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error fetching data: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <!-- Load Architects Daughter font from Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="./img/logo.jpg">

    <style>
        /* Apply the Architects Daughter font */
        body {
            font-family: 'Architects Daughter', cursive;
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Sidebar -->
<div class="w-64 h-screen bg-orange-500 text-white p-6 fixed top-0 left-0 shadow-lg">
    <div class="flex items-center mb-8">
        <img src="./img/logo.jpg" alt="Business Logo" class="h-16 w-16 mr-4 rounded-full border-2 border-white">
        <span class="text-2xl font-semibold">Admin Panel</span>
    </div>

    <div class="space-y-6">
        <a href="javascript:void(0)" onclick="openTab('product-management')" 
            class="block py-3 px-5 bg-orange-600 rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-400 transition duration-200">
            Product Management
        </a>
        <a href="javascript:void(0)" onclick="openTab('announcement-management')" 
            class="block py-3 px-5 bg-orange-600 rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-400 transition duration-200">
            Announcement Management
        </a>
        <a href="javascript:void(0)" onclick="openTab('order-management')" 
            class="block py-3 px-5 bg-orange-600 rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-400 transition duration-200">
            Menu Management
        </a>
        <a href="javascript:void(0)" onclick="openTab('order-list')" 
            class="block py-3 px-5 bg-orange-600 rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-400 transition duration-200">
            Order List
        </a>
        <a href="logout.php">
            <button class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 mt-6">Logout</button>
        </a>
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
    <script>
        // Function to open specific tab and remember it
        function openTab(tabId) {
            // Hide all sections
            document.querySelectorAll('.section').forEach(section => section.classList.add('hidden'));
            
            // Show the active tab section
            document.getElementById(tabId).classList.remove('hidden');
            
            // Remember the active tab in localStorage
            localStorage.setItem('activeTab', tabId);
        }

        // On page load, open the tab that was previously active
        window.onload = function() {
            const activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                openTab(activeTab); // Open the tab stored in localStorage
            } else {
                openTab('product-management'); // Default to the first tab if no tab is saved
            }
        };

        // Handle form submission via AJAX
        function handleFormSubmission(event, formId) {
            event.preventDefault();
            const formData = new FormData(document.getElementById(formId));
            formData.append('ajax', true);

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    // After success, you may choose to refresh the list or update the UI
                    location.reload(); // Reload the page to reflect changes without switching tabs
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
