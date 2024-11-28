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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mabsi Soy</title>
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
        <!-- Left: Logo -->
        <div class="flex items-center">
            <img src="./img/logo.jpg" alt="Business Logo" class="h-20 w-20 mr-4 rounded-full">
        </div>

        <!-- Hamburger Icon for Mobile -->
        <div class="md:hidden">
            <button onclick="toggleMobileMenu()" class="text-gray-50 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>

        <!-- Center: Navigation Links (Hidden on small screens) -->
        <div class="hidden md:flex space-x-4">
            <a href="index.php" class="w-21"><img class="w-12 hover:orange" src="./img/home.png" alt=""></a>
            <a href="about.php" class="w-21"><img class="w-12 ml-12" src="./img/info.png" alt=""></a>
            <a href="services.php" class="w-21"><img class="w-12 ml-12" src="./img/customer-support.png" alt=""></a>
        </div>

        <!-- Right: Auth Links or User Profile for Desktop -->
        <div class="hidden md:flex items-center space-x-4">
            <?php if ($isLoggedIn): ?>
                <div class="relative">
                    <div class="flex items-center" onclick="toggleDropdown('userDropdown')">
                        <!-- Profile Image -->
                        <img src="<?= isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'default-profile.jpg' ?>" alt="Profile Picture" class="w-10 h-10 rounded-full cursor-pointer">
                        <!-- Username -->
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

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="fixed w-full mobile-menu bg-orange-500 pt-28 shadow-lg md:hidden hidden">
        <div class="flex flex-col p-4 space-y-2">
            <?php if ($isLoggedIn): ?>
                <div class="flex items-center space-x-2">
                    <img src="<?= $_SESSION['profile_pic'] ?>" alt="Profile" class="w-8 h-8 rounded-full">
                    <p class="text-gray-50"><?= $_SESSION['username'] ?></p>
                </div>
                <a href="profile.php" class="text-gray-50 hover:text-orange-500">View Profile</a>
                <a href="order_history.php" class="text-gray-50 hover:text-orange-500">Order History</a>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="admin.php" class="text-gray-50 hover:text-orange-500">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php" class="text-red-600 hover:text-red-50">Logout</a>
            <?php else: ?>
                <button onclick="togglePopup('loginPopup')" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 w-full">Login</button>
                <button onclick="togglePopup('registerPopup')" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 w-full">Register</button>
            <?php endif; ?>
            <hr class="my-2">
            <a href="index.php" class="text-gray-50 hover:text-orange-500">Home</a>
            <a href="about.php" class="text-gray-50 hover:text-orange-500">About Us</a>
            <a href="services.php" class="text-gray-50 hover:text-orange-500">Services</a>
        </div>
    </div>

    <section class="py-16 bg-gradient-to-r from-orange-500 via-red-500 to-yellow-500 text-white pt-32">
    <h2 class="text-4xl font-bold text-center text-gray-50 mb-12">Our Services</h2>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-8 mx-6">
        <!-- Service 1 -->
        <div class="bg-white p-12 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold text-orange-500 mb-4">Food Delivery</h3>
            <p class="text-gray-600">Get your favorite dishes delivered to your door quickly and efficiently, fresh and hot.</p>
        </div>

        <!-- Service 2 -->
        <div class="bg-white p-12 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold text-orange-500 mb-4">Table Reservation</h3>
            <p class="text-gray-600">Reserve a table at our restaurant and enjoy your meal in a cozy, hassle-free environment.</p>
        </div>

        <!-- Service 3 -->
        <div class="bg-white p-12 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold text-orange-500 mb-4">Custom Catering</h3>
            <p class="text-gray-600">Our catering service provides personalized meal options for all your special events.</p>
        </div>

        <!-- Service 4 -->
        <div class="bg-white p-12 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold text-orange-500 mb-4">Meal Subscriptions</h3>
            <p class="text-gray-600">Subscribe to our weekly or monthly meal plans for convenient and delicious options.</p>
        </div>

        <!-- Service 5 -->
        <div class="bg-white p-12 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold text-orange-500 mb-4">Private Chef</h3>
            <p class="text-gray-600">Hire a private chef to prepare gourmet meals in the comfort of your own home.</p>
        </div>

        <!-- Service 6 -->
        <div class="bg-white p-12 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold text-orange-500 mb-4">Corporate Services</h3>
            <p class="text-gray-600">We provide meal solutions tailored for corporate meetings, events, and lunches.</p>
        </div>
    </div>

    <!-- Call-to-Action -->
    <div class="mt-12 text-center">
        <a href="contact.php" class="bg-orange-500 text-white px-6 py-3 rounded-lg text-lg hover:bg-orange-600">
            Contact Us for More Information
        </a>
    </div>
</section>

    
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
