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

// Fetch best-selling products from the database
$query = $pdo->prepare("SELECT * FROM products WHERE best_seller = 0");
$query->execute();
$bestSellers = $query->fetchAll(PDO::FETCH_ASSOC);

// Fetch announcements from the database
$query = $pdo->prepare("SELECT * FROM announcements");
$query->execute();
$announcements = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mabsi Soy</title>
    <link rel="shortcut icon" href="./img/logo.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media (min-width: 768px) {
            .mobile-menu { display: none; }
        }
    </style>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                keyframes: {
                    swipeUp: {
                        '0%, 50%': { transform: 'translateY(0%)' },
                        '50.1%, 100%': { transform: 'translateY(-100%)' },
                    },
                },
                animation: {
                    swipeUp: 'swipeUp 3s infinite',
                },
            },
        },
    };
</script>

</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="fixed w-full bg-orange-500 z-20 bg-opacity-75 shadow-lg p-4 flex items-center justify-between">
        <!-- Left: Logo -->
        <div class="flex items-center">
            <img src="./img/logo.jpg" alt="Business Logo" class="h-20 w-20 mr-4 rounded-full">
        </div>

        <!-- Hamburger Icon for Mobile -->
        <div class="md:hidden">
            <button onclick="toggleMobileMenu()" class="text-gray-700 focus:outline-none">
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
                    <img src="<?= isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'default-profile.jpg' ?>" alt="Profile Picture" class="w-10 h-10 rounded-full cursor-pointer" onclick="toggleDropdown('userDropdown')">
                    <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg overflow-hidden z-20 hidden">
                        <p class="px-4 py-2 text-sm text-gray-600"><?= $_SESSION['email'] ?></p>
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
    <div id="mobileMenu" class="fixed w-full z-10 mobile-menu bg-orange-500 bg-opacity-75 pt-28 shadow-lg md:hidden hidden">
        <div class="flex flex-col p-4 space-y-2">
            <?php if ($isLoggedIn): ?>
                <div class="flex items-center space-x-2">
                    <img src="<?= $_SESSION['profile_pic'] ?>" alt="Profile" class="w-8 h-8 rounded-full">
                    <p class="text-gray-700"><?= $_SESSION['email'] ?></p>
                </div>
                <a href="profile.php" class="text-gray-700 hover:text-orange-500">View Profile</a>
                <a href="order_history.php" class="text-gray-700 hover:text-orange-500">Order History</a>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="admin.php" class="text-gray-700 hover:text-orange-500">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php" class="text-red-600 hover:text-red-700">Logout</a>
            <?php else: ?>
                <button onclick="togglePopup('loginPopup')" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 w-full">Login</button>
                <button onclick="togglePopup('registerPopup')" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 w-full">Register</button>
            <?php endif; ?>
            <hr class="my-2">
            <a href="index.php" class="text-gray-700 hover:text-orange-500">Home</a>
            <a href="about.php" class="text-gray-700 hover:text-orange-500">About Us</a>
            <a href="services.php" class="text-gray-700 hover:text-orange-500">Services</a>
        </div>
    </div>
        <!-- First Section: Order Options -->
<section class="flex flex-col items-center py-12 bg-gray-100 pt-64" style="background-image: url('./img/bg home.jpg'); background-size: cover; background-position: center; height: 100vh;">
<section class="flex flex-col items-center py-12 bg-gray-100 p-8 sm:p-16 lg:p-32 xl:p-32 pt-16 sm:pt-24 lg:pt-32 pb-16 sm:pb-24 lg:pb-32 bg-opacity-50 rounded-lg">
    <h1 class="text-5xl font-bold text-gray-800 relative overflow-hidden h-24">
        <div class="absolute inset-0 flex flex-col items-center transition-transform duration-700 ease-in-out animate-[swipeUp_3s_infinite]">
            <span class="text-5xl font-bold">Mabsi</span>
            <span class="text-5xl font-bold mt-6">Soy</span>
        </div>
    </h1>
    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-center text-gray-700 mb-6">
        Peka Manyaman Silog Keni Sasmuan
    </h2>
    <div class="flex flex-wrap justify-center space-x-4">
        <a href="pickup_order.php">
            <button class="bg-orange-500 text-white px-4 py-2 sm:px-6 sm:py-3 rounded-lg shadow hover:bg-orange-600 transition">
                Order Now!
            </button>
        </a>
        <a href="">
            <button class="bg-amber-800 text-white px-4 py-2 sm:px-6 sm:py-3 rounded-lg shadow hover:bg-amber-900 transition">
                Table Reservation
            </button>
        </a>
    </div>
</section>

</section>


<!-- Best Seller Section -->
<section class="flex flex-col items-center py-12 bg-orange-800 bg-opacity-75 pt-32" id="best-seller">
    <h2 class="text-2xl font-bold text-gray-50 text-center mb-6">Best Seller Products</h2>

    <!-- Best Seller Carousel Section -->
<section class="relative w-full mt-12 mb-24">
    <!-- Carousel Container -->
    <div id="best-seller-carousel" class="relative w-full flex justify-center">
        <div class="carousel-container flex space-x-4 overflow-x-auto pb-4 snap-x snap-mandatory justify-center">
            <!-- Loop through best sellers and create cards -->
            <?php if (count($bestSellers) > 0): ?>
                <?php foreach ($bestSellers as $product): ?>
                    <div class="bg-white p-4 rounded-lg shadow-lg flex-none w-80 snap-center">
                        <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-48 object-cover rounded-md mb-4">
                        <h3 class="text-xl font-semibold text-gray-700"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="text-sm text-gray-600 mt-2"><?= htmlspecialchars($product['description']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-600">No best-sellers available at the moment.</p>
            <?php endif; ?>
        </div>

        <!-- Carousel Navigation Buttons -->
        <button onclick="prevSlide('best-seller-carousel')" class="absolute top-1/2 left-0 transform -translate-y-1/2 bg-gray-800 text-white p-2 rounded-r-lg hover:bg-gray-600 md:block hidden">
            &#10094;
        </button>
        <button onclick="nextSlide('best-seller-carousel')" class="absolute top-1/2 right-0 transform -translate-y-1/2 bg-gray-800 text-white p-2 rounded-l-lg hover:bg-gray-600 md:block hidden">
            &#10095;
        </button>
    </div>
</section>
</section>



<!-- Announcement Section -->
<section class="flex flex-col items-center py-12 bg-gray-100" id="announcement-management" style="background-image: url('./img/bg home.jpg'); background-size: cover; background-position: center;">
    <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">Announcements</h2>

    <!-- Announcement Carousel -->
    <div id="announcement-carousel" class="relative w-full flex justify-center">
        <div class="carousel-container flex space-x-4 overflow-x-auto pb-4 snap-x snap-mandatory">
            <!-- Loop through announcements and create cards -->
            <?php if (count($announcements) > 0): ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="bg-white p-4 rounded-lg shadow-lg flex-none w-80 snap-center">
                        <img src="uploads/<?= htmlspecialchars($announcement['image']) ?>" alt="<?= htmlspecialchars($announcement['title']) ?>" class="w-full h-48 object-cover rounded-md mb-4">
                        <h3 class="text-xl font-semibold text-gray-700"><?= htmlspecialchars($announcement['title']) ?></h3>
                        <p class="text-sm text-gray-600 mt-2"><?= htmlspecialchars($announcement['description']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-600">No announcements available at the moment.</p>
            <?php endif; ?>
        </div>

        <!-- Carousel Navigation Buttons -->
        <button onclick="prevSlide('announcement-carousel')" class="absolute top-1/2 left-0 transform -translate-y-1/2 bg-gray-800 text-white p-2 rounded-r-lg hover:bg-gray-600 md:block hidden">
            &#10094;
        </button>
        <button onclick="nextSlide('announcement-carousel')" class="absolute top-1/2 right-0 transform -translate-y-1/2 bg-gray-800 text-white p-2 rounded-l-lg hover:bg-gray-600 md:block hidden">
            &#10095;
        </button>
    </div>
</section>

    <!-- Login Popup -->
    <div id="loginPopup" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-8 max-w-sm w-full shadow-lg relative">
            <button onclick="togglePopup('loginPopup')" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
            <img src="./img/logo.jpg" alt="Business Logo" class="mx-auto mb-4 h-16 rounded-full">
            
            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
                    <?= $_SESSION['login_error']; ?>
                </div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-4">
                <input type="email" name="email" placeholder="Email" required class="w-full px-4 py-2 border rounded focus:outline-none">
                <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-2 border rounded focus:outline-none">
                <button type="submit" class="w-full bg-orange-500 text-white py-2 rounded hover:bg-orange-600">Login</button>
                <a href="#" class="text-sm text-orange-500 hover:underline">Forgot Password?</a>
                <p class="text-sm text-center">Don’t have an account? <a onclick="showPopup('registerPopup')" class="text-orange-500 hover:underline cursor-pointer">Register</a></p>
            </form>
        </div>
    </div>

    <!-- Register Popup -->
    <div id="registerPopup" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-8 max-w-sm w-full shadow-lg relative">
            <button onclick="togglePopup('registerPopup')" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
            <img src="./img/logo.jpg" alt="Business Logo" class="mx-auto mb-4 h-16 rounded-full">
            
            <?php if (isset($_SESSION['register_error'])): ?>
                <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
                    <?= $_SESSION['register_error']; ?>
                </div>
                <?php unset($_SESSION['register_error']); ?>
            <?php endif; ?>

            <form action="register.php" method="POST" class="space-y-4">
                <input type="text" name="username" placeholder="Username" required class="w-full px-4 py-2 border rounded focus:outline-none">
                <input type="email" name="email" placeholder="Email" required class="w-full px-4 py-2 border rounded focus:outline-none">
                <input type="text" name="contact_no" placeholder="Contact No" pattern="\d{11}" required class="w-full px-4 py-2 border rounded focus:outline-none">
                <input type="text" name="address" placeholder="Address" required class="w-full px-4 py-2 border rounded focus:outline-none">
                <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-2 border rounded focus:outline-none">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required class="w-full px-4 py-2 border rounded focus:outline-none">
                <button type="submit" class="w-full bg-orange-500 text-white py-2 rounded hover:bg-orange-600">Register</button>
                <p class="text-sm text-center">Already have an account? <a onclick="showPopup('loginPopup')" class="text-orange-500 hover:underline cursor-pointer">Login</a></p>
            </form>
        </div>
    </div>

    <?php include 'chatbot.php'; ?>

    <footer class="bg-orange-500 text-white py-8">
    <div class="container mx-auto text-center space-y-4">
        <p>&copy; 2024 Mabsi Soy. All Rights Reserved.</p>
        <div class="flex justify-center space-x-4">
            <a href="privacy.php" class="text-gray-400 hover:text-white">Privacy Policy</a>
            <a href="terms.php" class="text-gray-400 hover:text-white">Terms of Service</a>
            <a href="contact.php" class="text-gray-400 hover:text-white">Contact Us</a>
        </div>
    </div>
</footer>


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


// Function to move to the next slide
function nextSlide(carouselId) {
    const carousel = document.getElementById(carouselId);
    const container = carousel.querySelector('.carousel-container');
    const firstItem = container.firstElementChild;

    // Add class to start the transition
    container.style.transition = 'transform 0.5s ease-in-out';
    
    // Move the first item to the end and apply the transform to shift slides
    container.appendChild(firstItem);
    container.style.transform = 'translateX(-100%)';

    // Reset the transform and transition after the animation is complete
    setTimeout(() => {
        container.style.transition = 'none';
        container.style.transform = 'translateX(0)';
    }, 500); // Match this timeout with the transition duration
}

// Function to move to the previous slide
function prevSlide(carouselId) {
    const carousel = document.getElementById(carouselId);
    const container = carousel.querySelector('.carousel-container');
    const lastItem = container.lastElementChild;

    // Add class to start the transition
    container.style.transition = 'transform 0.5s ease-in-out';
    
    // Move the last item to the start and apply the transform to shift slides
    container.prepend(lastItem);
    container.style.transform = 'translateX(100%)';

    // Reset the transform and transition after the animation is complete
    setTimeout(() => {
        container.style.transition = 'none';
        container.style.transform = 'translateX(0)';
    }, 500); // Match this timeout with the transition duration
}


    </script>
</body>
</html>
