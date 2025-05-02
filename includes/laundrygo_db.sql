-- Create database if it doesn't exist and switch to it
CREATE DATABASE IF NOT EXISTS laundrygo;
USE laundrygo;
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2025 at 06:03 PM
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
-- Database: `laundrygo`
--

-- --------------------------------------------------------

--
-- Table structure for table `cleaning_items`
--

CREATE TABLE `cleaning_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_type` enum('Shoe','Dress') NOT NULL,
  `cleaning_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `pickup_date` date DEFAULT NULL,
  `drop_date` date DEFAULT NULL,
  `status` enum('Pending','Processing','Completed') DEFAULT 'Pending',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `mobile_number` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cleaning_items`
--

INSERT INTO `cleaning_items` (`id`, `user_id`, `item_type`, `cleaning_type`, `description`, `image_path`, `pickup_date`, `drop_date`, `status`, `uploaded_at`, `mobile_number`) VALUES
(36, 6, 'Shoe', 'Dry Clean', 'aa', '', '2025-05-01', '2025-05-02', 'Pending', '2025-05-01 09:06:48', NULL),
(37, 6, 'Shoe', 'Dry Clean', 'aa', '', '2025-05-09', '2025-05-09', 'Pending', '2025-05-01 09:12:24', NULL),
(40, 7, 'Shoe', 'Dry Clean', 'assd', '', NULL, NULL, 'Pending', '2025-05-01 13:36:06', NULL),
(41, 7, 'Shoe', 'Dry Clean', 'as', '', NULL, NULL, 'Pending', '2025-05-01 13:40:55', '9544263223'),
(42, 7, 'Shoe', 'Dry Clean', 'dsa', '', NULL, NULL, 'Pending', '2025-05-01 17:46:53', '9544263223'),
(43, 7, 'Dress', 'Premium Wash', 'asdghjkl', '', NULL, NULL, 'Pending', '2025-05-01 17:48:03', '1234567899'),
(44, 7, 'Shoe', 'Dry Clean', 'a', '', NULL, NULL, 'Pending', '2025-05-02 09:13:43', '9544263223'),
(45, 7, 'Shoe', 'Dry Clean', 'sd', '', NULL, NULL, 'Pending', '2025-05-02 09:23:14', '9544263223'),
(46, 7, 'Shoe', 'Dry Clean', 'sd', '', NULL, NULL, 'Pending', '2025-05-02 09:28:39', '9544263223'),
(47, 7, 'Shoe', 'Dry Clean', 'sxd', '', NULL, NULL, 'Pending', '2025-05-02 09:30:49', '9544263223'),
(48, 7, 'Shoe', 'Dry Clean', 'sd', '', NULL, NULL, 'Pending', '2025-05-02 09:33:32', '9544263223'),
(49, 7, 'Dress', 'Dry Clean', 'as', '', NULL, NULL, 'Pending', '2025-05-02 09:36:11', '9544263223'),
(50, 7, 'Shoe', 'Dry Clean', 'asdcf', '', NULL, NULL, 'Pending', '2025-05-02 09:43:40', '9544263223'),
(51, 7, 'Shoe', 'Dry Clean', 'asdf', '', NULL, NULL, 'Pending', '2025-05-02 09:49:15', '9544263223'),
(52, 7, 'Shoe', 'Dry Clean', 'as', '', NULL, NULL, 'Pending', '2025-05-02 10:21:01', '9544263223');

-- --------------------------------------------------------

--
-- Table structure for table `cleaning_rates`
--

CREATE TABLE `cleaning_rates` (
  `id` int(11) NOT NULL,
  `item_type` enum('Shoe','Dress') NOT NULL,
  `cleaning_type` enum('Dry Clean','Wet Wash','Premium Wash','Polish') NOT NULL,
  `rate` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cleaning_rates`
--

INSERT INTO `cleaning_rates` (`id`, `item_type`, `cleaning_type`, `rate`) VALUES
(1, 'Shoe', 'Dry Clean', 180.00),
(2, 'Shoe', 'Wet Wash', 120.00),
(3, 'Shoe', 'Polish', 80.00),
(4, 'Shoe', 'Premium Wash', 200.00),
(5, 'Dress', 'Dry Clean', 250.00),
(6, 'Dress', 'Wet Wash', 180.00),
(7, 'Dress', 'Premium Wash', 350.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cleaning_item_id` int(11) NOT NULL,
  `address` text NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('Pending','Paid') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_status` enum('Pending','In Progress','Ready','Completed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `cleaning_item_id`, `address`, `payment_method`, `total_amount`, `payment_status`, `created_at`, `order_status`) VALUES
(33, 6, 36, 'aa', 'cod', 180.00, 'Pending', '2025-05-01 09:06:48', 'Pending'),
(34, 6, 37, 'aa', 'cod', 180.00, 'Pending', '2025-05-01 09:12:24', 'Cancelled'),
(35, 7, 41, 'sdfg', 'cod', 180.00, 'Pending', '2025-05-01 13:40:55', 'Pending'),
(36, 7, 42, 'hostel', 'cod', 180.00, 'Pending', '2025-05-01 17:46:53', 'Completed'),
(37, 7, 43, 'sd', 'cod', 350.00, 'Pending', '2025-05-01 17:48:03', 'Pending'),
(38, 7, 44, 'asdv', 'cod', 180.00, 'Pending', '2025-05-02 09:13:43', 'Pending'),
(39, 7, 45, 'asdc', 'cod', 180.00, 'Pending', '2025-05-02 09:23:14', 'Pending'),
(40, 7, 46, 'asdf', 'cod', 180.00, 'Pending', '2025-05-02 09:28:39', 'Cancelled'),
(41, 7, 47, 'as', 'cod', 180.00, 'Pending', '2025-05-02 09:30:49', 'Cancelled'),
(42, 7, 48, 'as', 'cod', 180.00, 'Pending', '2025-05-02 09:33:32', 'Ready'),
(43, 7, 49, 'asdfg', 'cod', 250.00, 'Pending', '2025-05-02 09:36:11', 'Pending'),
(44, 7, 50, 'sdfg', 'cod', 180.00, 'Pending', '2025-05-02 09:43:40', 'Pending'),
(45, 7, 51, 'asd', 'cod', 180.00, 'Pending', '2025-05-02 09:49:15', 'Completed'),
(46, 7, 52, 'hostel', 'cod', 180.00, 'Pending', '2025-05-02 10:21:01', 'Cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `role`) VALUES
(6, 'Akash P', 'akashp4010@gmail.com', '$2y$12$iMX7IxkduyyRRAQyhImS/e85nN/8Zh1yDZAyoEuvgKVQyr3m.73AG', '2025-04-30 16:54:08', 'user'),
(7, 'Akash P2', 'aka@gmail.com', '$2y$12$vyP//4gCFZ5piKbIcwcaAe7HMa5TDN4LCnAPQKXiydufaIsC83bKS', '2025-05-01 10:05:50', 'user'),
(9, 'Laundrygo CEO', 'admin@gmail.com', '$2y$12$gso7YmXNLfItSbjuNx0GeuWRS0q5EOeg7Mu1xSmKusi.pPVHKjpe6', '2025-05-01 11:09:05', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cleaning_items`
--
ALTER TABLE `cleaning_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cleaning_rates`
--
ALTER TABLE `cleaning_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `cleaning_item_id` (`cleaning_item_id`);

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
-- AUTO_INCREMENT for table `cleaning_items`
--
ALTER TABLE `cleaning_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `cleaning_rates`
--
ALTER TABLE `cleaning_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cleaning_items`
--
ALTER TABLE `cleaning_items`
  ADD CONSTRAINT `cleaning_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`cleaning_item_id`) REFERENCES `cleaning_items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
