<?php
session_start();
require 'config.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
if ($isLoggedIn) {
    $user_id = $_SESSION['user_id']; // Fetch the user ID from session

    // Fetch the user details including the profile picture
    $query = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
    $query->execute([$user_id]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    // Set the profile picture in session if it's available
    $_SESSION['profile_pic'] = $user['profile_picture'] ?? 'default-profile.jpg'; // If no picture, set a default
}
// Fetch all orders for the logged-in user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE email = ? ORDER BY order_date DESC");
$stmt->execute([$_SESSION['user_email']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white">

<nav class="fixed w-full bg-orange-500 z-10 bg-opacity-75 shadow-lg p-4 flex items-center justify-between">
    <!-- Left: Logo -->
    <div class="flex items-center">
        <img src="./img/logo.jpg" alt="Business Logo" class="h-20 w-20 mr-4 rounded-full">
    </div>

    <!-- Hamburger Icon for Mobile -->
    <div class="md:hidden">
        <button onclick="toggleMobileMenu()" class="text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
    </div>

    <!-- Center: Navigation Links (Hidden on small screens) -->
    <div class="hidden md:flex space-x-4">
        <a href="index.php" class="w-21"><img class="w-12 hover:orange" src="./img/home.png" alt=""></a>
        <a href="contact.php" class="w-21"><img class="w-12 ml-12" src="./img/info.png" alt=""></a>
        <a href="services.php" class="w-21"><img class="w-12 ml-12" src="./img/customer-support.png" alt=""></a>
    </div>

    <!-- Right: Auth Links or User Profile for Desktop -->
    <div class="hidden md:flex items-center space-x-4">
        <?php if ($isLoggedIn): ?>
            <div class="relative">
                <img src="<?= isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'default-profile.jpg' ?>" alt="Profile Picture" class="w-10 h-10 rounded-full cursor-pointer" onclick="toggleDropdown('userDropdown')">
                <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-orange-500 rounded-md shadow-lg overflow-hidden z-20 hidden">
                    <p class="px-4 py-2 text-sm text-white"><?= $_SESSION['email'] ?></p>
                    <a href="view_profile.php" class="block px-4 py-2 text-sm text-white hover:bg-orange-600">View Profile</a>
                    <a href="order_history.php" class="block px-4 py-2 text-sm text-white hover:bg-orange-600">Order History</a>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="admin.php" class="block px-4 py-2 text-sm text-white hover:bg-orange-600">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-orange-600">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <button onclick="togglePopup('loginPopup')" class="bg-amber-800 text-white px-4 py-2 rounded hover:bg-amber-900">Login</button>
            <button onclick="togglePopup('registerPopup')" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Register</button>
        <?php endif; ?>
    </div>
</nav>

<!-- Mobile Menu -->
<div id="mobileMenu" class="fixed w-full mobile-menu bg-orange-500 bg-opacity-75 pt-28 shadow-lg md:hidden hidden">
    <div class="flex flex-col p-4 space-y-2">
        <?php if ($isLoggedIn): ?>
            <div class="flex items-center space-x-2">
                <img src="<?= $_SESSION['profile_pic'] ?>" alt="Profile" class="w-8 h-8 rounded-full">
                <p class="text-white"><?= $_SESSION['email'] ?></p>
            </div>
            <a href="view_profile.php" class="text-white hover:text-orange-500">View Profile</a>
            <a href="order_history.php" class="text-white hover:text-orange-500">Order History</a>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="admin.php" class="text-white hover:text-orange-500">Admin Panel</a>
            <?php endif; ?>
            <a href="logout.php" class="text-red-600 hover:text-red-700">Logout</a>
        <?php else: ?>
            <button onclick="togglePopup('loginPopup')" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 w-full">Login</button>
            <button onclick="togglePopup('registerPopup')" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 w-full">Register</button>
        <?php endif; ?>
        <hr class="my-2">
        <a href="index.php" class="text-white hover:text-orange-500">Home</a>
        <a href="contact.php" class="text-white hover:text-orange-500">Contact</a>
        <a href="services.php" class="text-white hover:text-orange-500">Services</a>
    </div>
</div>

    <!-- Order History Section -->
<div class="flex-col col-2 container mx-auto p-6 pt-64">
    <h2 class="text-2xl font-bold mb-4">Order History</h2>
    <?php foreach ($orders as $order): ?>
        <div class="bg-white p-6 mb-4 shadow-lg rounded-lg">
            <h3 class="text-xl font-bold">Order #<?= $order['id']; ?> - <?= ucfirst($order['service_type']); ?></h3>
            <p><strong>Email:</strong> <?= $order['email']; ?></p>
            <p><strong>Address:</strong> <?= $order['address']; ?></p>
            <p><strong>Contact:</strong> <?= $order['contact_number']; ?></p>
            <p><strong>Landmark:</strong> <?= $order['landmark']; ?></p>
            <p><strong>Order Date:</strong> <?= date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
            <p><strong>Status:</strong> 
                <span class="text-sm px-3 py-1 rounded-full <?= getStatusClass($order['status']); ?>">
                    <?= ucfirst($order['status']); ?>
                </span>
            </p>

            <!-- Items Section -->
            <div class="mt-4">
                <h4 class="text-lg font-semibold">Items:</h4>
                <ul class="space-y-2">
                    <?php
                    $stmt = $pdo->prepare("SELECT oi.*, mi.name FROM order_items oi JOIN menu_items mi ON oi.menu_item_id = mi.id WHERE oi.order_id = ?");
                    $stmt->execute([$order['id']]);
                    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($items as $item):
                    ?>
                        <li class="flex justify-between">
                            <span><?= $item['name']; ?> (x<?= $item['quantity']; ?>)</span>
                            <span class="text-white">₱<?= number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Order Total -->
            <div class="mt-4 flex justify-between">
                <strong class="text-xl">Total:</strong>
                <span class="text-xl">₱<?= number_format(array_sum(array_map(function($item) {
                    return $item['price'] * $item['quantity'];
                }, $items)), 2); ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>

<?php
// Helper function to assign a Tailwind class based on status
function getStatusClass($status) {
    switch ($status) {
        case 'pending':
            return 'bg-yellow-200 text-yellow-800';
        case 'in_progress':
            return 'bg-blue-200 text-blue-800';
        case 'completed':
            return 'bg-green-200 text-green-800';
        case 'canceled':
            return 'bg-red-200 text-red-800';
        default:
            return 'bg-white text-white';
    }
}
?>

<!-- JavaScript for Menu Toggle -->
<script>
        function toggleMobileMenu() {
            document.getElementById('mobileMenu').classList.toggle('hidden');
        }

        function showPopup(popupId) {
            document.getElementById('loginPopup').classList.add('hidden');
            document.getElementById('registerPopup').classList.add('hidden');
            document.getElementById(popupId).classList.remove('hidden');
        }
        
        function togglePopup(popupId) {
            const popup = document.getElementById(popupId);
            popup.classList.toggle('hidden');
        }

        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle('hidden');
        }
        </script>