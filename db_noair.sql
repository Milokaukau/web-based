-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307/
-- Generation Time: Apr 21, 2026 at 01:36 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_noair`
--
CREATE DATABASE IF NOT EXISTS `db_noair` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_noair`;

-- --------------------------------------------------------

--
-- Table structure for table `tb_address`
--

CREATE TABLE `tb_address` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `first_line` varchar(255) NOT NULL,
  `second_line` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_address`
--

INSERT INTO `tb_address` (`id`, `member_id`, `first_line`, `second_line`, `postcode`, `city`) VALUES
(1, 3, 'Jalan Setapak', 'Taman Setapak', '41266', 'Setapak');

-- --------------------------------------------------------

--
-- Table structure for table `tb_admin`
--

CREATE TABLE `tb_admin` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(1000) NOT NULL,
  `photo` varchar(1000) DEFAULT NULL,
  `is_superadmin` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_admin`
--

INSERT INTO `tb_admin` (`id`, `name`, `email`, `password`, `photo`, `is_superadmin`, `is_active`) VALUES
(1, 'Adam Ali', 'adam-ali@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 1, 1),
(2, 'Boom Shakalaka', 'boomsklk@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 0, 1),
(3, 'Amanda Cyre', 'cyreamanda@gmail.com', '$2y$10$rnX2fp6eExeuvNEsksAos.pMVOdMuymBIMgRjv/M2SQdYOgdu5TE.', NULL, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_admin_password_reset`
--

CREATE TABLE `tb_admin_password_reset` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_cart`
--

CREATE TABLE `tb_cart` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_cart`
--

INSERT INTO `tb_cart` (`id`, `member_id`, `product_id`, `quantity`) VALUES
(1, 1, 1, 2),
(2, 2, 3, 1),
(3, 2, 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `tb_category`
--

CREATE TABLE `tb_category` (
  `id` int(11) NOT NULL,
  `name` varchar(1000) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_category`
--

INSERT INTO `tb_category` (`id`, `name`, `is_active`) VALUES
(0, 'Uncategorized', 1),
(1, '500ML MINI', 1),
(2, '1.5L PRO', 1),
(3, '2.5L PROMAX', 1),
(4, 'COFFEE CUP', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_color`
--

CREATE TABLE `tb_color` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_color`
--

INSERT INTO `tb_color` (`id`, `name`, `is_active`) VALUES
(1, 'Signature Coral', 1),
(2, 'Onyx Black', 1),
(3, 'Pearl White', 1),
(4, 'Amethyst Purple', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_member`
--

CREATE TABLE `tb_member` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `photo` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `login_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_member`
--

INSERT INTO `tb_member` (`id`, `name`, `email`, `password`, `photo`, `gender`, `phone`, `login_attempts`, `locked_until`, `is_active`) VALUES
(1, 'John Cena', 'johncena@email.com', '$2y$10$xwWCR6jlro2/8fY7Aw/xp.wsJvHX78CQSnhJf.90gA/Upqz.jQHDi', NULL, NULL, NULL, 5, '2026-04-20 10:50:46', 1),
(2, 'Audrey', 'audrey@email.com', '$2y$10$iFeiCh2k1C3/NtikZPh2TOkE1MaAVe/Szu.8J9ZdmmLbje134p2Ge', 'photo_69e25677131151.32255712.png', 'female', '0176246636', 3, '2026-04-20 10:47:13', 1),
(3, 'Andrew', 'andrew123@email.com', '$2y$10$xwWCR6jlro2/8fY7Aw/xp.wsJvHX78CQSnhJf.90gA/Upqz.jQHDi', NULL, NULL, NULL, 0, NULL, 1),
(6, 'john', 'johnbbibbi@gmail.com', '$2y$10$2UorDmHIlYjFXV./kMMIMu4aPbvzsdWcHMTCE9TkjXuQ.dcA4OENu', NULL, 'male', '1234567891', 1, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_order`
--

CREATE TABLE `tb_order` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL COMMENT 'pending_payment, confirmed, in_delivery, delivered, completed, cancelled',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_order`
--

INSERT INTO `tb_order` (`id`, `member_id`, `amount`, `status`, `created_at`) VALUES
(1, 3, 29.90, 'confirmed', '2026-03-02 12:01:21'),
(3, 1, 39.90, 'confirmed', '2026-04-01 02:51:06'),
(4, 1, 39.90, 'confirmed', '2026-04-07 16:57:31');

-- --------------------------------------------------------

--
-- Table structure for table `tb_order_product`
--

CREATE TABLE `tb_order_product` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_order_product`
--

INSERT INTO `tb_order_product` (`id`, `product_id`, `order_id`, `quantity`, `price`) VALUES
(1, 4, 1, 1, 29.90),
(3, 5, 3, 1, 39.90),
(4, 5, 4, 1, 39.90);

-- --------------------------------------------------------

--
-- Table structure for table `tb_password_reset`
--

CREATE TABLE `tb_password_reset` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_password_reset`
--

INSERT INTO `tb_password_reset` (`id`, `member_id`, `token`, `created_at`, `expires_at`) VALUES
(4, 6, '1e127dd6bad10cda009733cac77d34562925990b84207008ed2d75389ca29082', '2026-04-17 21:34:26', '2026-04-17 15:49:26');

-- --------------------------------------------------------

--
-- Table structure for table `tb_payment`
--

CREATE TABLE `tb_payment` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `method` varchar(255) NOT NULL COMMENT 'e_wallet, online_banking, card',
  `status` varchar(255) NOT NULL COMMENT 'processing, success, failed, pending_refund, refunded\r\n',
  `created_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_payment`
--

INSERT INTO `tb_payment` (`id`, `order_id`, `method`, `status`, `created_at`, `completed_at`) VALUES
(1, 1, 'e_wallet', 'failed', '2026-03-02 12:01:23', NULL),
(2, 1, 'online_banking', 'success', '2026-03-02 12:02:50', '2026-03-02 12:03:00'),
(7, 3, 'card', 'success', '2026-04-01 02:51:06', '2026-04-01 02:51:06'),
(8, 4, 'card', 'success', '2026-04-07 16:57:31', '2026-04-07 16:57:31');

-- --------------------------------------------------------

--
-- Table structure for table `tb_product`
--

CREATE TABLE `tb_product` (
  `id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `weight_g` int(11) DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `base_diameter_cm` decimal(5,2) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `photo` varchar(1000) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_product`
--

INSERT INTO `tb_product` (`id`, `color_id`, `category_id`, `name`, `description`, `weight_g`, `height_cm`, `base_diameter_cm`, `material`, `price`, `stock`, `photo`, `is_active`) VALUES
(1, 1, 1, '500ML MINI', '\"The Perfect Fit for Your Pocket and Your Pace.\"\n\nMeet the 500ML MINI, the ultimate companion for those who move fast and travel light. Designed for maximum portability without sacrificing durability, this compact bottle is the perfect size for short commutes, quick gym sessions, or tucked into a small handbag.\n\nDespite its smaller footprint, it features the same premium, leak-proof engineering as our larger models. Its sleek, ergonomic frame fits perfectly in any standard cup holder and feels natural in your hand. Whether you’re grabbing a quick sip between meetings or staying refreshed on a morning jog, the 500ML MINI is built to go wherever you do.\n\nStay light. Stay hydrated. Take the 500ML MINI everywhere.', 295, 22.50, 7.50, '18/8 Pro-Grade Stainless Steel', 29.90, 34, 'mini_1.png', 1),
(2, 2, 1, '500ML MINI', '\"The Perfect Fit for Your Pocket and Your Pace.\"\n\nMeet the 500ML MINI, the ultimate companion for those who move fast and travel light. Designed for maximum portability without sacrificing durability, this compact bottle is the perfect size for short commutes, quick gym sessions, or tucked into a small handbag.\n\nDespite its smaller footprint, it features the same premium, leak-proof engineering as our larger models. Its sleek, ergonomic frame fits perfectly in any standard cup holder and feels natural in your hand. Whether you’re grabbing a quick sip between meetings or staying refreshed on a morning jog, the 500ML MINI is built to go wherever you do.\n\nStay light. Stay hydrated. Take the 500ML MINI everywhere.', 295, 22.50, 7.50, '18/8 Pro-Grade Stainless Steel', 29.90, 11, 'mini_2.png', 1),
(3, 3, 1, '500ML MINI', '\"The Perfect Fit for Your Pocket and Your Pace.\"\n\nMeet the 500ML MINI, the ultimate companion for those who move fast and travel light. Designed for maximum portability without sacrificing durability, this compact bottle is the perfect size for short commutes, quick gym sessions, or tucked into a small handbag.\n\nDespite its smaller footprint, it features the same premium, leak-proof engineering as our larger models. Its sleek, ergonomic frame fits perfectly in any standard cup holder and feels natural in your hand. Whether you’re grabbing a quick sip between meetings or staying refreshed on a morning jog, the 500ML MINI is built to go wherever you do.\n\nStay light. Stay hydrated. Take the 500ML MINI everywhere.', 295, 22.50, 7.50, '18/8 Pro-Grade Stainless Steel', 29.90, 8, 'mini_3.png', 1),
(4, 4, 1, '500ML MINI', '\"The Perfect Fit for Your Pocket and Your Pace.\"\r\n\r\nMeet the 500ML MINI, the ultimate companion for those who move fast and travel light. Designed for maximum portability without sacrificing durability, this compact bottle is the perfect size for short commutes, quick gym sessions, or tucked into a small handbag.\r\n\r\nDespite its smaller footprint, it features the same premium, leak-proof engineering as our larger models. Its sleek, ergonomic frame fits perfectly in any standard cup holder and feels natural in your hand. Whether you’re grabbing a quick sip between meetings or staying refreshed on a morning jog, the 500ML MINI is built to go wherever you do.\r\n\r\nStay light. Stay hydrated. Take the 500ML MINI everywhere.', 295, 22.50, 7.50, '18/8 Pro-Grade Stainless Steel', 29.90, 90, 'mini_4.png', 1),
(5, 1, 2, '1.5L PRO', '\"The Gold Standard for Your All-Day Hustle.\" Meet the 1.5L PRO, the versatile workhorse of our lineup. Built for the high-achiever who needs more than a standard bottle to get through the day, the PRO strikes the perfect balance between high capacity and easy carrying. Engineered for the office, the yoga studio, or a long day of classes, its 1.5-liter volume ensures you aren\'t constantly refilling while the wide-mouth opening allows for effortless ice-loading.', 450, 28.00, 9.50, '18/8 Pro-Grade Stainless Steel', 39.90, 30, 'pro_1.png', 1),
(6, 2, 2, '1.5L PRO', '\"The Gold Standard for Your All-Day Hustle.\" Meet the 1.5L PRO, the versatile workhorse of our lineup. Built for the high-achiever who needs more than a standard bottle to get through the day, the PRO strikes the perfect balance between high capacity and easy carrying. Engineered for the office, the yoga studio, or a long day of classes, its 1.5-liter volume ensures you aren\'t constantly refilling while the wide-mouth opening allows for effortless ice-loading.', 450, 28.00, 9.50, '18/8 Pro-Grade Stainless Steel', 39.90, 45, 'pro_2.png', 1),
(7, 3, 2, '1.5L PRO', '\"The Gold Standard for Your All-Day Hustle.\" Meet the 1.5L PRO, the versatile workhorse of our lineup. Built for the high-achiever who needs more than a standard bottle to get through the day, the PRO strikes the perfect balance between high capacity and easy carrying. Engineered for the office, the yoga studio, or a long day of classes, its 1.5-liter volume ensures you aren\'t constantly refilling while the wide-mouth opening allows for effortless ice-loading.', 450, 28.00, 9.50, '18/8 Pro-Grade Stainless Steel', 39.90, 25, 'pro_3.png', 1),
(8, 4, 2, '1.5L PRO', '\"The Gold Standard for Your All-Day Hustle.\" Meet the 1.5L PRO, the versatile workhorse of our lineup. Built for the high-achiever who needs more than a standard bottle to get through the day, the PRO strikes the perfect balance between high capacity and easy carrying. Engineered for the office, the yoga studio, or a long day of classes, its 1.5-liter volume ensures you aren\'t constantly refilling while the wide-mouth opening allows for effortless ice-loading.', 450, 28.00, 9.50, '18/8 Pro-Grade Stainless Steel', 39.90, 12, 'pro_4.png', 1),
(9, 1, 3, '2.5L PROMAX', '\"Fuel Your Ambition. Conquer Your Limits.\" Meet the 2.5L PROMAX, our highest-capacity bottle designed for those who refuse to slow down. Whether you’re hitting a grueling two-hour workout, heading out for a full day of hiking, or simply ensuring you hit your daily water intake goals, the PROMAX is your dedicated hydration station. Built with heavy-duty, impact-resistant materials, it features a reinforced handle for easy transport.', 750, 33.00, 12.50, '18/8 Pro-Grade Stainless Steel', 49.90, 15, 'promax_1.png', 1),
(10, 2, 3, '2.5L PROMAX', '\"Fuel Your Ambition. Conquer Your Limits.\" Meet the 2.5L PROMAX, our highest-capacity bottle designed for those who refuse to slow down. Whether you’re hitting a grueling two-hour workout, heading out for a full day of hiking, or simply ensuring you hit your daily water intake goals, the PROMAX is your dedicated hydration station. Built with heavy-duty, impact-resistant materials, it features a reinforced handle for easy transport.', 750, 33.00, 12.50, '18/8 Pro-Grade Stainless Steel', 49.90, 25, 'promax_2.png', 1),
(11, 3, 3, '2.5L PROMAX', '\"Fuel Your Ambition. Conquer Your Limits.\" Meet the 2.5L PROMAX, our highest-capacity bottle designed for those who refuse to slow down. Whether you’re hitting a grueling two-hour workout, heading out for a full day of hiking, or simply ensuring you hit your daily water intake goals, the PROMAX is your dedicated hydration station. Built with heavy-duty, impact-resistant materials, it features a reinforced handle for easy transport.', 750, 33.00, 12.50, '18/8 Pro-Grade Stainless Steel', 49.90, 20, 'promax_3.png', 1),
(12, 4, 3, '2.5L PROMAX', '\"Fuel Your Ambition. Conquer Your Limits.\" Meet the 2.5L PROMAX, our highest-capacity bottle designed for those who refuse to slow down. Whether you’re hitting a grueling two-hour workout, heading out for a full day of hiking, or simply ensuring you hit your daily water intake goals, the PROMAX is your dedicated hydration station. Built with heavy-duty, impact-resistant materials, it features a reinforced handle for easy transport.', 750, 33.00, 12.50, '18/8 Pro-Grade Stainless Steel', 49.90, 10, 'promax_4.png', 1),
(18, 1, 4, 'Coffee Cup', 'The perfect companion for your morning brew. Double-walled insulation keeps your coffee hot for hours while the splash-proof lid ensures a mess-free commute. Ergonomically designed to fit comfortably in your hand and most car cup holders.', 250, 15.50, 7.00, '18/8 Pro-Grade Stainless Steel', 24.90, 40, 'cup_1.png', 1),
(19, 2, 4, 'Coffee Cup', 'The perfect companion for your morning brew. Double-walled insulation keeps your coffee hot for hours while the splash-proof lid ensures a mess-free commute. Ergonomically designed to fit comfortably in your hand and most car cup holders.', 250, 15.50, 7.00, '18/8 Pro-Grade Stainless Steel', 24.90, 60, 'cup_2.png', 1),
(20, 3, 4, 'Coffee Cup', 'The perfect companion for your morning brew. Double-walled insulation keeps your coffee hot for hours while the splash-proof lid ensures a mess-free commute. Ergonomically designed to fit comfortably in your hand and most car cup holders.', 250, 15.50, 7.00, '18/8 Pro-Grade Stainless Steel', 24.90, 35, 'cup_3.png', 1),
(21, 4, 4, 'Coffee Cup', 'The perfect companion for your morning brew. Double-walled insulation keeps your coffee hot for hours while the splash-proof lid ensures a mess-free commute. Ergonomically designed to fit comfortably in your hand and most car cup holders.', 250, 15.50, 7.00, '18/8 Pro-Grade Stainless Steel', 24.90, 20, 'cup_4.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_remember_tokens`
--

CREATE TABLE `tb_remember_tokens` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_wishlist`
--

CREATE TABLE `tb_wishlist` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_address`
--
ALTER TABLE `tb_address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tb_admin_password_reset`
--
ALTER TABLE `tb_admin_password_reset`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `tb_cart`
--
ALTER TABLE `tb_cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tb_category`
--
ALTER TABLE `tb_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_color`
--
ALTER TABLE `tb_color`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_member`
--
ALTER TABLE `tb_member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tb_order`
--
ALTER TABLE `tb_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `tb_order_product`
--
ALTER TABLE `tb_order_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tb_password_reset`
--
ALTER TABLE `tb_password_reset`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `tb_payment`
--
ALTER TABLE `tb_payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `tb_product`
--
ALTER TABLE `tb_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `color_id` (`color_id`);

--
-- Indexes for table `tb_remember_tokens`
--
ALTER TABLE `tb_remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_token` (`token_hash`),
  ADD KEY `idx_member` (`member_id`);

--
-- Indexes for table `tb_wishlist`
--
ALTER TABLE `tb_wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_address`
--
ALTER TABLE `tb_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_admin_password_reset`
--
ALTER TABLE `tb_admin_password_reset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_cart`
--
ALTER TABLE `tb_cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_category`
--
ALTER TABLE `tb_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_color`
--
ALTER TABLE `tb_color`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_member`
--
ALTER TABLE `tb_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_order`
--
ALTER TABLE `tb_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_order_product`
--
ALTER TABLE `tb_order_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_password_reset`
--
ALTER TABLE `tb_password_reset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_payment`
--
ALTER TABLE `tb_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_product`
--
ALTER TABLE `tb_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tb_remember_tokens`
--
ALTER TABLE `tb_remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_wishlist`
--
ALTER TABLE `tb_wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_address`
--
ALTER TABLE `tb_address`
  ADD CONSTRAINT `tb_address_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `tb_member` (`id`);

--
-- Constraints for table `tb_admin_password_reset`
--
ALTER TABLE `tb_admin_password_reset`
  ADD CONSTRAINT `tb_admin_password_reset_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `tb_admin` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tb_cart`
--
ALTER TABLE `tb_cart`
  ADD CONSTRAINT `tb_cart_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `tb_member` (`id`),
  ADD CONSTRAINT `tb_cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tb_product` (`id`);

--
-- Constraints for table `tb_order`
--
ALTER TABLE `tb_order`
  ADD CONSTRAINT `tb_order_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `tb_member` (`id`);

--
-- Constraints for table `tb_order_product`
--
ALTER TABLE `tb_order_product`
  ADD CONSTRAINT `tb_order_product_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `tb_order` (`id`),
  ADD CONSTRAINT `tb_order_product_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tb_product` (`id`);

--
-- Constraints for table `tb_password_reset`
--
ALTER TABLE `tb_password_reset`
  ADD CONSTRAINT `tb_password_reset_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `tb_member` (`id`);

--
-- Constraints for table `tb_payment`
--
ALTER TABLE `tb_payment`
  ADD CONSTRAINT `tb_payment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `tb_order` (`id`);

--
-- Constraints for table `tb_product`
--
ALTER TABLE `tb_product`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `tb_category` (`id`),
  ADD CONSTRAINT `fk_product_color` FOREIGN KEY (`color_id`) REFERENCES `tb_color` (`id`);

--
-- Constraints for table `tb_remember_tokens`
--
ALTER TABLE `tb_remember_tokens`
  ADD CONSTRAINT `tb_remember_tokens_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `tb_member` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tb_wishlist`
--
ALTER TABLE `tb_wishlist`
  ADD CONSTRAINT `tb_wishlist_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `tb_member` (`id`),
  ADD CONSTRAINT `tb_wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tb_product` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
