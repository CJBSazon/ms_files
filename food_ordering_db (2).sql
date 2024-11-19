-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2024 at 05:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `food_ordering_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `description`, `image`) VALUES
(17, 'We Will Close Today!!!', 'November 18, 2024', 'istockphoto-640941188-612x612.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `type` enum('special_menu','combo_meal','budget_meal','ala_carte','add_ons','drinks_dessert') NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `price`, `type`, `image`) VALUES
(7, 'CREAMY CHICKEN', 80.00, 'special_menu', 'img.png'),
(8, 'CRISPY CHICKEN', 70.00, 'special_menu', 'img.png'),
(9, 'SISIG', 120.00, 'ala_carte', 'Authentic_Kapampangan_Sisig.jpg'),
(10, 'LECHON KAWALI', 120.00, 'ala_carte', 'RM-237899-FilippinoLechonKawali-ddmfs-2x1-6355-bb901851b57c4827a48dff9e54d7bb3a.jpg'),
(11, 'CREAMY CHICKEN / SISIG', 135.00, 'combo_meal', '467330286_122127778880486772_6431200053040511190_n.jpg'),
(12, 'BURGER STEAK LECHON', 135.00, 'combo_meal', '466858514_122127778838486772_425546958279757109_n.jpg'),
(13, 'TOCILOG', 60.00, 'budget_meal', 'IMG_1898.webp'),
(14, 'LONGSILOG', 50.00, 'budget_meal', '1.png'),
(15, 'EGG', 15.00, 'add_ons', 'Sunny_Side_Up_Eggs_007-fe57becdb5c4473092cba5e14e407bfc.jpg'),
(16, 'RICE', 15.00, 'add_ons', 'IMG_6839.jpeg'),
(17, 'CREAMY SAUCE', 10.00, 'add_ons', 'creamy-garlic-sauce-recipe-10-scaled.jpg'),
(18, 'COKE SMALL', 15.00, 'drinks_dessert', '17237297.webp'),
(19, 'MOUNTAIN DEW SMALL', 20.00, 'drinks_dessert', '16427684.webp');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `service_type` enum('pickup','delivery') NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `landmark` varchar(255) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','in_progress','completed','canceled') DEFAULT 'pending',
  `latitude` decimal(9,6) NOT NULL DEFAULT 0.000000,
  `longitude` decimal(9,6) NOT NULL DEFAULT 0.000000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `email`, `service_type`, `address`, `contact_number`, `landmark`, `order_date`, `status`, `latitude`, `longitude`) VALUES
(1, 1, 'aris@gmail.com', 'pickup', 'blk.38 lot 66', '092783774782', 'plaza', '2024-11-15 15:21:37', 'completed', 0.000000, 0.000000),
(2, 1, 'aris@gmail.com', 'pickup', 'blk.38 lot 66', '092783774782', 'plaza', '2024-11-15 15:29:54', 'completed', 0.000000, 0.000000),
(3, 1, 'aris@gmail.com', 'pickup', 'blk.38 lot 65', '092783774782', 'plaza', '2024-11-15 15:35:32', 'completed', 0.000000, 0.000000),
(4, 1, 'aris@gmail.com', 'pickup', 'blk.38 lot 65', '092783774782', 'plaza', '2024-11-15 15:36:08', 'pending', 0.000000, 0.000000),
(5, 1, 'aris@gmail.com', 'delivery', 'blk.38 lot 65', '092783774782', 'plaza', '2024-11-15 15:36:51', 'pending', 0.000000, 0.000000),
(6, 1, 'aris@gmail.com', 'pickup', 'blk.38 lot 65', '092783774782', 'plaza', '2024-11-15 15:45:53', 'pending', 0.000000, 0.000000),
(7, 6, 'cris@gmail.com', 'pickup', 'blk.38 lot 65', '9452287864', 'plaza', '2024-11-15 19:19:37', 'pending', 0.000000, 0.000000),
(8, 3, 'chris@example.com', 'pickup', 'blk:12 lot 17 guagua', '9452287864', 'plaza', '2024-11-16 07:51:50', 'completed', 0.000000, 0.000000);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `best_seller` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `image`, `created_at`, `best_seller`) VALUES
(1, 'Creamy Chicken', 'This is good for everyone!!!', 'img.png', '2024-11-14 21:01:56', 0),
(25, 'CREAMY CHICKEN SISIG', 'POPULAR NOW!!!', '467330286_122127778880486772_6431200053040511190_n.jpg', '2024-11-18 17:13:13', 0),
(26, 'BURGER STEAK LECHON', 'POPULAR NOW!!!', '466858514_122127778838486772_425546958279757109_n.jpg', '2024-11-18 17:14:37', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_no` varchar(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `profile_pic` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `contact_no`, `address`, `role`, `profile_pic`, `profile_picture`) VALUES
(1, 'aris', 'aris@gmail.com', '$2y$10$DWTBmbeWSHThpJ665Gtdi.wVYbsyLUvizDNaPah8//juFZF2XkR8i', '09267453635', 'blk.38 lot 66', 'user', NULL, 'uploads/673bb6815c8a3_393893437_706694284286377_3550222858300246747_n.jpg'),
(2, 'admin', 'admin@gmail.com', '$2y$10$OI54angZ5GfBqTVzw7T6WeBQJxZan2Aab8rEEOY2EmCqAZ9TDR4F6', '09267387362', 'mabsi soy admin panel', 'admin', NULL, 'uploads/67368ccb7ea8a_Mabsi Soy Logo.jpg'),
(3, 'chris', 'chris@example.com', '$2y$10$LDdlPKFZ3FTAQ0ppnlHi7OGSZFztqyv7.kJQmm8lQdI6cTVFA6rJO', '09452287864', 'blk:12 lot 17 guagua', 'user', NULL, 'uploads/673a9c7dcba80_dg40s1l-b671f9e9-a0a2-40e1-8444-c193247291f1.jpg'),
(6, 'cris', 'cris@gmail.com', '$2y$10$.ODI3cEJpwlwDZKN8sA9sux2q5y7kk/4U2iMWoNAfYm0lLLRL1gA2', '09376584627', 'bulacan', 'user', NULL, 'uploads/67368d00d41dc_461227747_2621119264752332_224467310744310976_n.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_fk` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `order_items_ibfk_2` (`menu_item_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
