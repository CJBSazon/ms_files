-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2024 at 03:09 AM
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
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `payment_method` enum('cash_on_delivery','gcash') NOT NULL DEFAULT 'cash_on_delivery',
  `order_summary` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `email`, `service_type`, `address`, `contact_number`, `landmark`, `order_date`, `status`, `latitude`, `longitude`, `payment_method`, `order_summary`) VALUES
(1, 1, 'aris@gmail.com', 'pickup', 'blk.38 lot 66', '092783774782', 'plaza', '2024-11-15 15:21:37', 'completed', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(2, 1, 'aris@gmail.com', 'pickup', 'blk.38 lot 66', '092783774782', 'plaza', '2024-11-15 15:29:54', 'completed', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(3, 1, 'aris@gmail.com', 'pickup', 'blk.38 lot 65', '092783774782', 'plaza', '2024-11-15 15:35:32', 'completed', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(4, 1, 'aris@gmail.com', 'pickup', 'blk.38 lot 65', '092783774782', 'plaza', '2024-11-15 15:36:08', 'completed', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(5, 1, 'aris@gmail.com', 'delivery', 'blk.38 lot 65', '092783774782', 'plaza', '2024-11-15 15:36:51', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(6, 1, 'aris@gmail.com', 'pickup', 'blk.38 lot 65', '092783774782', 'plaza', '2024-11-15 15:45:53', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(7, 6, 'cris@gmail.com', 'pickup', 'blk.38 lot 65', '9452287864', 'plaza', '2024-11-15 19:19:37', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(8, 3, 'chris@example.com', 'pickup', 'blk:12 lot 17 guagua', '9452287864', 'plaza', '2024-11-16 07:51:50', 'completed', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(9, 1, '', 'pickup', 'blk:12 lot 17 guagua', '09452287864', NULL, '2024-11-27 02:27:06', 'pending', 14.937650, 120.622070, 'cash_on_delivery', NULL),
(10, 1, 'aris@gmail.com', 'pickup', 'blk.38 lot 66', '09267453635', 'plaza', '2024-11-28 02:23:58', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(11, 1, 'aris@gmail.com', 'delivery', 'blk.38 lot 66', '09267453635', 'plaza', '2024-11-28 03:18:58', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(12, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 03:54:43', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(13, 6, '', 'delivery', 'bulacan', '09376584627', NULL, '2024-11-28 03:54:54', 'pending', 14.944450, 120.624130, 'cash_on_delivery', NULL),
(14, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 03:56:37', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(15, 6, '', 'delivery', 'bulacan', '09376584627', NULL, '2024-11-28 03:56:42', 'pending', 14.945120, 120.623450, 'cash_on_delivery', NULL),
(16, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 03:58:38', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(17, 6, '', 'delivery', 'bulacan', '09376584627', NULL, '2024-11-28 03:59:50', 'pending', 14.944620, 120.622590, 'cash_on_delivery', NULL),
(18, 6, '', 'delivery', 'bulacan', '09376584627', NULL, '2024-11-28 04:07:12', 'pending', 14.947110, 120.623620, 'cash_on_delivery', NULL),
(19, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 04:14:35', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(20, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 04:20:24', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(21, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 04:25:38', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(22, 1, '', 'delivery', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 04:26:05', 'pending', 14.946610, 120.624990, 'cash_on_delivery', NULL),
(23, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 04:26:54', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(24, 1, '', 'delivery', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 04:28:36', 'pending', 14.946280, 120.625160, 'cash_on_delivery', NULL),
(25, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 04:29:21', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(26, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 04:31:11', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(27, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 04:35:17', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(28, 1, '', 'delivery', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 04:43:39', 'pending', 14.944120, 120.625500, 'cash_on_delivery', NULL),
(29, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 04:48:01', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(30, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 04:51:08', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(31, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 04:53:12', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(32, 1, '', 'delivery', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 07:52:03', 'pending', 14.944950, 120.622070, 'cash_on_delivery', NULL),
(33, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 07:59:33', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(34, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 08:04:16', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(35, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 08:13:57', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(36, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 08:14:00', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(37, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 08:14:06', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(38, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 08:16:20', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(39, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 08:19:15', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(40, 1, '', 'delivery', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 08:19:55', 'pending', 14.946280, 120.624820, 'cash_on_delivery', NULL),
(41, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 08:22:17', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(42, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 08:22:42', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(43, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 08:23:51', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(44, 1, '', 'delivery', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 08:24:39', 'pending', 14.945610, 120.622930, 'cash_on_delivery', NULL),
(45, 1, '', 'pickup', 'blk.38 lot 66', '09267453635', NULL, '2024-11-28 08:28:20', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(46, 6, '', 'delivery', 'bulacan', '09376584627', NULL, '2024-11-28 08:32:05', 'pending', 14.945780, 120.622590, 'cash_on_delivery', NULL),
(47, 6, '', 'delivery', 'bulacan', '09376584627', NULL, '2024-11-28 08:32:12', 'pending', 14.945780, 120.622590, 'cash_on_delivery', NULL),
(48, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:35:44', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(49, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:35:57', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(50, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:36:53', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(51, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:37:42', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(52, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:37:53', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(53, 6, '', 'delivery', 'bulacan', '09376584627', NULL, '2024-11-28 08:38:17', 'pending', 14.945610, 120.626190, 'cash_on_delivery', NULL),
(54, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:41:58', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(55, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:42:08', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(56, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:42:10', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(57, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:42:10', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(58, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:42:11', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(59, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:42:11', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(60, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:42:12', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(61, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:42:12', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(62, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:42:12', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(63, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:42:12', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(64, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:42:12', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(65, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:42:13', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(66, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:42:13', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(67, 6, '', 'pickup', 'bulacan', '09376584627', NULL, '2024-11-28 08:43:26', 'pending', 0.000000, 0.000000, 'cash_on_delivery', NULL),
(68, 6, '', 'delivery', 'bulacan', '09376584627', NULL, '2024-11-28 08:43:35', 'pending', 14.946280, 120.623450, 'cash_on_delivery', NULL),
(69, 6, '', 'delivery', 'bulacan', '09376584627', NULL, '2024-11-28 08:46:08', 'pending', 14.966680, 120.636390, 'cash_on_delivery', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `menu_item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`, `image`) VALUES
(1, 38, 7, 1, 80.00, 'img.png'),
(2, 39, 8, 1, 70.00, 'img.png'),
(3, 39, 12, 1, 135.00, '466858514_122127778838486772_425546958279757109_n.jpg'),
(4, 39, 13, 1, 60.00, 'IMG_1898.webp'),
(5, 40, 7, 1, 80.00, 'img.png'),
(6, 40, 8, 1, 70.00, 'img.png'),
(7, 40, 12, 1, 135.00, '466858514_122127778838486772_425546958279757109_n.jpg'),
(8, 40, 18, 1, 15.00, '17237297.webp'),
(9, 41, 8, 1, 70.00, 'img.png'),
(10, 42, 8, 2, 70.00, 'img.png'),
(11, 43, 7, 1, 80.00, 'img.png'),
(12, 44, 11, 1, 135.00, '467330286_122127778880486772_6431200053040511190_n.jpg'),
(13, 45, 8, 1, 70.00, 'img.png'),
(14, 46, 11, 1, 135.00, '467330286_122127778880486772_6431200053040511190_n.jpg'),
(15, 47, 11, 1, 135.00, '467330286_122127778880486772_6431200053040511190_n.jpg'),
(16, 49, 7, 1, 80.00, 'img.png'),
(17, 50, 8, 1, 70.00, 'img.png'),
(18, 51, 7, 1, 80.00, 'img.png'),
(19, 52, 7, 1, 80.00, 'img.png'),
(20, 53, 8, 1, 70.00, 'img.png'),
(21, 54, 8, 1, 70.00, 'img.png'),
(22, 68, 8, 1, 70.00, 'img.png'),
(23, 69, 8, 1, 70.00, 'img.png'),
(24, 69, 12, 1, 135.00, '466858514_122127778838486772_425546958279757109_n.jpg'),
(25, 69, 16, 5, 15.00, 'IMG_6839.jpeg'),
(26, 69, 18, 1, 15.00, '17237297.webp');

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
(6, 'cris', 'cris@gmail.com', '$2y$10$.ODI3cEJpwlwDZKN8sA9sux2q5y7kk/4U2iMWoNAfYm0lLLRL1gA2', '09376584627', 'bulacan', 'user', NULL, 'uploads/67368d00d41dc_461227747_2621119264752332_224467310744310976_n.jpg'),
(7, 'aristarub', 'artarub@gmail.com', '$2y$10$60QAf2sfTIIeoNKKyo0hvO7wWf9rqVdmwt9J8qpyyVgN7pYReYk/a', '09947283727', 'blk.38 lot 65', 'user', NULL, 'uploads/6743274548486_aris1.jpg');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
