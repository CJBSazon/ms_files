<?php
session_start();
require 'config.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
if (!$isLoggedIn) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to view your order history.']);
    exit;
}

// Fetch the logged-in user's details
$user_id = $_SESSION['user_id']; // Fetch the user ID from session

// Fetch the user details including the profile picture and email
$query = $pdo->prepare("SELECT profile_picture, email, username FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

// Set the profile picture in session if it's available
$_SESSION['profile_pic'] = $user['profile_picture'] ?? 'default-profile.jpg'; // If no picture, set a default
$_SESSION['email'] = $user['email']; // Store the email in session for future use
$_SESSION['username'] = $user['username']; // Store the username in session for future use

// Fetch all orders for the logged-in user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mabsi Soy - Order History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="shortcut icon" href="./img/logo.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Apply the Architects Daughter font */
        body {
            font-family: 'Architects Daughter', cursive;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="fixed w-full bg-orange-500 z-10 shadow-lg p-4 flex items-center justify-between">
        <div class="flex items-center">
            <img src="./img/logo.jpg" alt="Business Logo" class="h-20 w-20 mr-4 rounded-full">
        </div>
        <div class="hidden md:flex space-x-4">
            <a href="index.php" class="w-21"><img class="w-12 hover:orange" src="./img/home.png" alt=""></a>
            <a href="about.php" class="w-21"><img class="w-12 ml-12" src="./img/info.png" alt=""></a>
            <a href="services.php" class="w-21"><img class="w-12 ml-12" src="./img/customer-support.png" alt=""></a>
        </div>
        <div class="hidden md:flex items-center space-x-4">
            <?php if ($isLoggedIn): ?>
                <div class="relative">
                    <div class="flex items-center" onclick="toggleDropdown('userDropdown')">
                        <img src="<?= isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'default-profile.jpg' ?>" alt="Profile Picture" class="w-10 h-10 rounded-full cursor-pointer">
                        <button class="ml-2 text-sm text-white"><?= $_SESSION['username'] ?></button>
                    </div>
                    <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg overflow-hidden z-20 hidden">
                        <p class="px-4 py-2 text-sm text-gray-600"><?= $_SESSION['username'] ?></p>
                        <a href="view_profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">View Profile</a>
                        <a href="order_history.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">Order History</a>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <a href="admin.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">Admin Panel</a>
                        <?php endif; ?>
                        <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-200">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <button onclick="togglePopup('loginPopup')" class="bg-amber-800 text-white px-4 py-2 rounded hover:bg-amber-900">Login</button>
                <button onclick="togglePopup('registerPopup')" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Register</button>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Order History Section -->
    <div class="flex-col col-2 container mx-auto p-6 pt-64">
        <h2 class="text-2xl font-bold mb-4">Order History</h2>

        <!-- Display all orders -->
        <?php foreach ($orders as $order): ?>
            <div class="bg-white p-6 mb-4 shadow-lg rounded-lg">
                <h3 class="text-xl font-bold">Order #<?= $order['id']; ?> - <?= ucfirst($order['service_type']); ?></h3>
                <p><strong>Email:</strong> <?= $_SESSION['email']; ?></p>
                <p><strong>Address:</strong> <?= $order['address']; ?></p>
                <p><strong>Contact:</strong> <?= $order['contact_number']; ?></p>
                <p><strong>Order Date:</strong> <?= date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
                <p><strong>Status:</strong> 
                    <span class="text-sm px-3 py-1 rounded-full <?= getStatusClass($order['status']); ?>">
                        <?= ucfirst($order['status']); ?>
                    </span>
                </p>
                
                <!-- Show Coordinates -->
                <p><strong>Location:</strong> Latitude: <?= $order['latitude']; ?>, Longitude: <?= $order['longitude']; ?></p>

                <!-- Payment Method -->
                <p><strong>Payment Method:</strong> <?= ucfirst($order['payment_method']); ?></p>

                <!-- Items Section -->
                <div class="mt-4">
                    <h4 class="text-lg font-semibold">Items:</h4>
                    <ul class="space-y-2">
                        <?php
                        $stmt = $pdo->prepare("SELECT oi.*, mi.name, mi.price FROM order_items oi JOIN menu_items mi ON oi.menu_item_id = mi.id WHERE oi.order_id = ?");
                        $stmt->execute([$order['id']]);
                        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $orderTotal = 0;
                        foreach ($items as $item):
                            $orderTotal += $item['price'] * $item['quantity'];
                        ?>
                            <li class="flex justify-between">
                                <span><?= $item['name']; ?> (x<?= $item['quantity']; ?>)</span>
                                <span class="text-black">₱<?= number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Order Total -->
                <div class="mt-4 flex justify-between">
                    <strong class="text-xl">Total:</strong>
                    <span class="text-xl">₱<?= number_format($orderTotal, 2); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Chatbot Section -->
    <?php include 'chatbot.php'; ?>

    <?php include 'footer.php'; ?>

    <!-- JavaScript for Dropdown and Popup -->
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
