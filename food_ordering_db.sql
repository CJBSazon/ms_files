-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2024 at 12:08 AM
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
(1, 'We Will Close Today!!!', 'Due to Bad Weather Condition', 'Become a GNCian’s Now !.png');

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
(1, 'Creamy Chicken', 75.00, 'special_menu', 'img.png'),
(2, 'champoratdog', 85.00, 'combo_meal', '464094677_122138166698351806_2577636045120714956_n.jpg'),
(3, 'champoratdog1', 85.00, 'budget_meal', '464094677_122138166698351806_2577636045120714956_n.jpg'),
(4, 'champoratdog2', 85.00, 'ala_carte', '464094677_122138166698351806_2577636045120714956_n.jpg'),
(5, 'champoratdog3', 85.00, 'add_ons', '464094677_122138166698351806_2577636045120714956_n.jpg'),
(6, 'champoratdog4', 85.00, 'drinks_dessert', '464094677_122138166698351806_2577636045120714956_n.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `service_type` enum('pickup','delivery') NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `landmark` varchar(255) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','in_progress','completed','canceled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `email`, `service_type`, `address`, `contact_number`, `landmark`, `order_date`, `status`) VALUES
(1, 'aris@gmail.com', 'pickup', 'blk.38 lot 66', '092783774782', 'plaza', '2024-11-15 15:21:37', 'completed'),
(2, 'aris@gmail.com', 'pickup', 'blk.38 lot 66', '092783774782', 'plaza', '2024-11-15 15:29:54', 'pending'),
(3, 'aris@gmail.com', 'pickup', 'blk.38 lot 65', '092783774782', 'plaza', '2024-11-15 15:35:32', 'pending'),
(4, 'aris@gmail.com', 'pickup', 'blk.38 lot 65', '092783774782', 'plaza', '2024-11-15 15:36:08', 'pending'),
(5, 'aris@gmail.com', 'delivery', 'blk.38 lot 65', '092783774782', 'plaza', '2024-11-15 15:36:51', 'pending'),
(6, 'aris@gmail.com', 'pickup', 'blk.38 lot 65', '092783774782', 'plaza', '2024-11-15 15:45:53', 'pending'),
(7, 'cris@gmail.com', 'pickup', 'blk.38 lot 65', '9452287864', 'plaza', '2024-11-15 19:19:37', 'pending');

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

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 75.00),
(2, 1, 2, 1, 85.00),
(3, 1, 3, 1, 85.00),
(4, 1, 4, 1, 85.00),
(5, 1, 6, 2, 85.00),
(6, 2, 1, 1, 75.00),
(7, 2, 2, 1, 85.00),
(8, 2, 3, 1, 85.00),
(9, 2, 4, 1, 85.00),
(10, 2, 6, 2, 85.00),
(11, 3, 4, 1, 85.00),
(12, 4, 3, 2, 85.00),
(13, 4, 4, 1, 85.00),
(14, 4, 5, 1, 85.00),
(15, 4, 6, 1, 85.00),
(16, 5, 3, 2, 85.00),
(17, 5, 4, 1, 85.00),
(18, 5, 5, 1, 85.00),
(19, 5, 6, 1, 85.00),
(20, 6, 1, 8, 75.00),
(21, 7, 1, 1, 75.00),
(22, 7, 2, 1, 85.00);

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
(1, 'Creamy Chicken', 'This is good for everyone!!!', 'img.png', '2024-11-14 21:01:56', 0);

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
(1, 'aris', 'aris@gmail.com', '$2y$10$DWTBmbeWSHThpJ665Gtdi.wVYbsyLUvizDNaPah8//juFZF2XkR8i', '09267453635', 'blk.38 lot 66', 'user', NULL, 'uploads/67365f7891f9c_tada.jpg'),
(2, 'admin', 'admin@gmail.com', '$2y$10$OI54angZ5GfBqTVzw7T6WeBQJxZan2Aab8rEEOY2EmCqAZ9TDR4F6', '09267387362', 'mabsi soy admin panel', 'admin', NULL, 'uploads/67368ccb7ea8a_Mabsi Soy Logo.jpg'),
(3, 'chris', 'chris@example.com', '$2y$10$LDdlPKFZ3FTAQ0ppnlHi7OGSZFztqyv7.kJQmm8lQdI6cTVFA6rJO', '09452287864', 'blk:12 lot 17 guagua', 'user', NULL, NULL),
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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
