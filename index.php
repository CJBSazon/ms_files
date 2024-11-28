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
    <!-- Load Architects Daughter font from Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
    <nav class="fixed w-full bg-orange-500 z-20 shadow-lg p-4 flex items-center justify-between">
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
                        <a href="view_profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-500">View Profile</a>
                        <a href="order_history.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-500">Order History</a>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <a href="admin.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-500">Admin Panel</a>
                        <?php endif; ?>
                        <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-orange-500">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <button onclick="togglePopup('loginPopup')" class="bg-amber-800 text-white px-4 py-2 rounded hover:bg-amber-900">Login</button>
                <button onclick="togglePopup('registerPopup')" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Register</button>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="fixed w-full z-10 mobile-menu bg-orange-500 pt-28 shadow-lg md:hidden hidden">
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

    <!-- First Section: Order Options -->
<section class="flex flex-col items-center py-12 bg-gray-100 pt-64" style="background-image: url('./img/bg home.jpg'); background-size: cover; background-position: center; height: 100vh;">
    <section class="flex flex-col items-center py-12 bg-gray-100 p-8 sm:p-16 lg:p-32 xl:p-32 pt-16 sm:pt-24 lg:pt-32 pb-16 sm:pb-24 lg:pb-32 bg-opacity-50 rounded-lg">
    <img src="./img/logo.jpg" alt="Business Logo" class="mx-auto mb-4 w-32 rounded-full">
    <h1 class="text-4xl text-orange-500 font-bold">Mabsi Soy</h1>
        <h2 class="text-2xl sm:text-2xl lg:text-2xl font-bold text-center text-red-500 mb-6">
            Peka Manyaman Silog Keni Sasmuan
        </h2>
        <div class="flex flex-wrap justify-center space-x-4">
            <!-- Order Now button -->
            <button onclick="checkLoginAndShowPopup('order')" class="bg-orange-500 text-white px-4 py-2 sm:px-6 sm:py-3 rounded-lg shadow hover:bg-orange-600 transition font-bold">
                Order Now!
            </button>
            <!-- Table Reservation button -->
            <button onclick="checkLoginAndShowPopup('reservation')" class="bg-amber-800 text-white px-4 py-2 sm:px-6 sm:py-3 rounded-lg shadow hover:bg-amber-900 transition font-bold">
                Table Reservation
            </button>
        </div>
    </section>
</section>


<section class="flex flex-col items-center py-12 bg-orange-500 pt-32" id="best-seller">
    <h2 class="text-3xl font-bold text-gray-50 text-center mb-6">Best Seller Products</h2>

    <!-- Best Seller Carousel Section -->
    <section class="relative w-full mt-12 mb-24">
        <!-- Carousel Container -->
        <div id="best-seller-carousel" class="relative w-full flex justify-center overflow-hidden">
            <!-- Cards container -->
            <div class="carousel-container flex transition-transform ease-in-out duration-500 w-full">
                <!-- Loop through best sellers and create cards -->
                <?php if (count($bestSellers) > 0): ?>
                    <?php foreach ($bestSellers as $product): ?>
                        <div class="bg-white mr-6 p-6 rounded-lg shadow-lg flex-none w-full max-w-md snap-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                            <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-48 object-cover rounded-md mb-4">
                            <h3 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="text-sm text-gray-600 mt-2"><?= htmlspecialchars($product['description']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-gray-600">No best-sellers available at the moment.</p>
                <?php endif; ?>
            </div>

            <!-- Carousel Navigation Buttons -->
            <button onclick="prevSlide('best-seller-carousel')" class="absolute top-1/2 left-0 transform -translate-y-1/2 bg-gray-800 text-white p-3 rounded-full shadow-md hover:bg-gray-600 md:block hidden">
                &#10094;
            </button>
            <button onclick="nextSlide('best-seller-carousel')" class="absolute top-1/2 right-0 transform -translate-y-1/2 bg-gray-800 text-white p-3 rounded-full shadow-md hover:bg-gray-600 md:block hidden">
                &#10095;
            </button>
        </div>
    </section>
</section>

<!-- Announcement Section -->
<section class="flex flex-col items-center py-12 bg-gray-100" id="announcement-management" style="background-image: url('./img/bg home.jpg'); background-size: cover; background-position: center;">
    <h2 class="text-3xl font-bold text-gray-50 text-center mb-6">Announcements</h2>

    <!-- Announcement Carousel -->
    <div id="announcement-carousel" class="relative w-full flex justify-center overflow-hidden">
        <div class="carousel-container flex transition-transform ease-in-out duration-500 w-full">
            <!-- Loop through announcements and create cards -->
            <?php if (count($announcements) > 0): ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="bg-white p-6 rounded-lg shadow-lg flex-none w-full max-w-lg snap-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                        <img src="uploads/<?= htmlspecialchars($announcement['image']) ?>" alt="<?= htmlspecialchars($announcement['title']) ?>" class="w-full h-56 object-cover rounded-md mb-4">
                        <h3 class="text-xl font-semibold text-gray-700"><?= htmlspecialchars($announcement['title']) ?></h3>
                        <p class="text-sm text-gray-600 mt-2"><?= htmlspecialchars($announcement['description']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-600">No announcements available at the moment.</p>
            <?php endif; ?>
        </div>

        <!-- Carousel Navigation Buttons -->
        <button onclick="prevSlide('announcement-carousel')" class="absolute top-1/2 left-0 transform -translate-y-1/2 bg-gray-800 text-white p-3 rounded-full shadow-md hover:bg-gray-600 md:block hidden">
            &#10094;
        </button>
        <button onclick="nextSlide('announcement-carousel')" class="absolute top-1/2 right-0 transform -translate-y-1/2 bg-gray-800 text-white p-3 rounded-full shadow-md hover:bg-gray-600 md:block hidden">
            &#10095;
        </button>
    </div>
</section>


    <!-- Login Popup -->
<div id="loginPopup" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
    <div class="bg-white rounded-lg p-8 max-w-sm w-full shadow-lg relative">
        <button onclick="togglePopup('loginPopup')" class="absolute top-2 right-2 text-gray-400 hover:text-red-600">&times;</button>
        <img src="./img/logo.jpg" alt="Business Logo" class="mx-auto mb-4 h-16 rounded-full">
        
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="bg-red-500 text-red-50 p-2 rounded mb-4">
                <?= $_SESSION['login_error']; ?>
            </div>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-4">
            <input type="text" name="username" placeholder="Username" required class="w-full px-4 py-2 border rounded focus:outline-none">
            <div class="relative">
                <input id="loginPassword" type="password" name="password" placeholder="Password" required class="w-full px-4 py-2 border rounded focus:outline-none pr-10">
                <button type="button" onclick="togglePasswordVisibility('loginPassword')" class="absolute right-3 top-3">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-gray-500">
                        <path d="M12 4C7.03 4 3.06 6.7 1 9.5C3.06 12.3 7.03 15 12 15C16.97 15 20.94 12.3 23 9.5C20.94 6.7 16.97 4 12 4ZM12 13C9.79 13 8 11.21 8 9C8 6.79 9.79 5 12 5C14.21 5 16 6.79 16 9C16 11.21 14.21 13 12 13ZM12 2C6.48 2 2 5.58 2 9C2 12.42 6.48 16 12 16C17.52 16 22 12.42 22 9C22 5.58 17.52 2 12 2Z"/>
                    </svg>
                </button>
            </div>
            <button type="submit" class="w-full bg-orange-500 text-white py-2 rounded hover:bg-orange-600">Login</button>
            <a href="#" class="text-sm text-orange-500 hover:underline">Forgot Password?</a>
            <p class="text-sm text-center">Don’t have an account? <a onclick="showPopup('registerPopup')" class="text-orange-500 hover:underline cursor-pointer">Register</a></p>
        </form>
    </div>
</div>

<!-- Register Popup -->
<div id="registerPopup" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
    <div class="bg-white rounded-lg p-8 max-w-sm w-full shadow-lg relative">
        <button onclick="togglePopup('registerPopup')" class="absolute top-2 right-2 text-gray-400 hover:text-red-600">&times;</button>
        <img src="./img/logo.jpg" alt="Business Logo" class="mx-auto mb-4 h-16 rounded-full">
        
        <?php if (isset($_SESSION['register_error'])): ?>
            <div class="bg-red-500 text-red-50 p-2 rounded mb-4">
                <?= $_SESSION['register_error']; ?>
            </div>
            <?php unset($_SESSION['register_error']); ?>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-4">
            <input type="text" name="username" placeholder="Username" required class="w-full px-4 py-2 border rounded focus:outline-none">
            <input type="email" name="email" placeholder="Email" required class="w-full px-4 py-2 border rounded focus:outline-none">
            <input type="text" name="contact_no" placeholder="Contact No" pattern="\d{11}" required class="w-full px-4 py-2 border rounded focus:outline-none">
            <input type="text" name="address" placeholder="Address" required class="w-full px-4 py-2 border rounded focus:outline-none">
            <div class="relative">
                <input id="registerPassword" type="password" name="password" placeholder="Password" required class="w-full px-4 py-2 border rounded focus:outline-none pr-10">
                <button type="button" onclick="togglePasswordVisibility('registerPassword')" class="absolute right-3 top-3">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-gray-500">
                        <path d="M12 4C7.03 4 3.06 6.7 1 9.5C3.06 12.3 7.03 15 12 15C16.97 15 20.94 12.3 23 9.5C20.94 6.7 16.97 4 12 4ZM12 13C9.79 13 8 11.21 8 9C8 6.79 9.79 5 12 5C14.21 5 16 6.79 16 9C16 11.21 14.21 13 12 13ZM12 2C6.48 2 2 5.58 2 9C2 12.42 6.48 16 12 16C17.52 16 22 12.42 22 9C22 5.58 17.52 2 12 2Z"/>
                    </svg>
                </button>
            </div>
            <div class="relative">
            <input id="confirmPassword" type="password" name="confirm_password" placeholder="Confirm Password" required class="w-full px-4 py-2 border rounded focus:outline-none">
            <button type="button" onclick="togglePasswordVisibility('confirmPassword')" class="absolute right-3 top-3">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-gray-500">
                        <path d="M12 4C7.03 4 3.06 6.7 1 9.5C3.06 12.3 7.03 15 12 15C16.97 15 20.94 12.3 23 9.5C20.94 6.7 16.97 4 12 4ZM12 13C9.79 13 8 11.21 8 9C8 6.79 9.79 5 12 5C14.21 5 16 6.79 16 9C16 11.21 14.21 13 12 13ZM12 2C6.48 2 2 5.58 2 9C2 12.42 6.48 16 12 16C17.52 16 22 12.42 22 9C22 5.58 17.52 2 12 2Z"/>
                    </svg>
                </button>
            </div>
            <button type="submit" class="w-full bg-orange-500 text-white py-2 rounded hover:bg-orange-600">Register</button>
            <p class="text-sm text-center">Already have an account? <a onclick="showPopup('loginPopup')" class="text-orange-500 hover:underline cursor-pointer">Login</a></p>
        </form>
    </div>
</div>

    <?php include 'chatbot.php'; ?>
    <?php include 'footer.php'; ?>

    
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
        // Function to check if the user is logged in and show the login popup if not
    function checkLoginAndShowPopup(action) {
        // Here we assume you are using a JavaScript variable to check login status
        // This is a placeholder, you need to integrate this with your backend login check
        const isLoggedIn = <?= json_encode($isLoggedIn) ?>; // This checks if the user is logged in

        if (!isLoggedIn) {
            // If not logged in, show the login popup
            togglePopup('loginPopup');
        } else {
            // If logged in, proceed with the action (e.g., redirect to page)
            if (action === 'order') {
                window.location.href = 'pickup_order.php';  // Redirect to the order page
            } else if (action === 'reservation') {
                // Handle table reservation logic here, e.g., show reservation form or redirect
                alert('Table reservation feature is under construction');
            }
        }
    }

    function togglePasswordVisibility(id) {
        var passwordField = document.getElementById(id);
        var eyeIcon = document.getElementById('eyeIcon');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.setAttribute('stroke', 'orange'); // Change icon color when password is visible
        } else {
            passwordField.type = 'password';
            eyeIcon.setAttribute('stroke', 'gray'); // Revert icon color when password is hidden
        }
    }

    


    let currentIndex = 0;

// Function to move to the next slide (one card at a time)
function nextSlide(carouselId) {
    const carousel = document.getElementById(carouselId);
    const container = carousel.querySelector('.carousel-container');
    const totalItems = container.children.length;
    
    // Calculate the next index
    currentIndex = (currentIndex + 1) % totalItems;
    
    // Adjust the transform to show the current item and hide others
    container.style.transition = 'transform 0.5s ease-in-out';
    container.style.transform = `translateX(-${currentIndex * 100}%)`;
}

// Function to move to the previous slide (one card at a time)
function prevSlide(carouselId) {
    const carousel = document.getElementById(carouselId);
    const container = carousel.querySelector('.carousel-container');
    const totalItems = container.children.length;
    
    // Calculate the previous index
    currentIndex = (currentIndex - 1 + totalItems) % totalItems;
    
    // Adjust the transform to show the current item and hide others
    container.style.transition = 'transform 0.5s ease-in-out';
    container.style.transform = `translateX(-${currentIndex * 100}%)`;
}

// Function to automatically move to the next slide every 3 seconds (one card at a time)
function autoSlide(carouselId) {
    setInterval(() => {
        nextSlide(carouselId); // Trigger nextSlide function
    }, 3000); // Change slide every 3 seconds (3000ms)
}

// Initialize auto sliding when the page loads
document.addEventListener('DOMContentLoaded', () => {
    const carouselId = 'best-seller-carousel'; // The ID of the carousel
    autoSlide(carouselId); // Start auto sliding
});



    </script>
</body>
</html>
