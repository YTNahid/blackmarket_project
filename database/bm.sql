-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2024 at 06:44 PM
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
-- Database: `bm`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `items` text NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `email`, `order_date`, `total`, `items`, `status`) VALUES
(1, 'tester@gmail.com', '2024-09-13 18:59:19', 96.99, '[{\"name\":\"AKM\",\"price\":96.99,\"quantity\":1}]', 'canceled'),
(2, 'tester@gmail.com', '2024-09-17 00:00:26', 300.00, '[{\"name\":\"test\",\"price\":100,\"quantity\":3}]', 'canceled'),
(3, 'tester@gmail.com', '2024-09-17 00:59:21', 100.00, '[{\"name\":\"test\",\"price\":100,\"quantity\":1}]', 'confirmed'),
(4, 'tester@gmail.com', '2024-09-17 01:27:02', 11170.00, '[{\"name\":\"hehe\",\"price\":100,\"quantity\":3},{\"name\":\"321421\",\"price\":5435,\"quantity\":2}]', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `added_by` varchar(50) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `image`, `price`, `added_by`, `type`) VALUES
(1, 'AKM', 'Akm_new.png', 96.99, '', ''),
(2, '123', 'img-weapons-k2.png', 21.00, 'YTNahid', ''),
(5, 'hehe', 'mk47_mutant.png', 100.00, 'YTNahid', 'ar'),
(6, '321421', 'groza.png', 5435.00, 'YTNahid', 'ar'),
(7, '3414122', 'qbz95.png', 423523.00, 'YTNahid', 'ar'),
(8, 'AKM', 'akm.png', 3214.00, 'YTNahid', 'ar'),
(9, '44', 'akm.png', 423.00, 'YTNahid', 'smg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `birth_date` date NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `contact`, `gender`, `birth_date`, `role`, `created_at`) VALUES
(1, 'YTNahid', 'ytnahid@team.com', '$2y$10$N3QPbBSgUQ8JgYZ7zvoSB.qKzLIFJULKbx0Y6J.Oh75u7egdsXBUm', '01964800828', 'male', '2001-08-05', 'admin', '2024-09-13 14:54:47'),
(3, 'Taizol', 'taizol@team.com', '$2y$10$QScVKc8fv6BdFAZchQGyT.nwddKZlMo8GHAFQGdwqf/a7WSWQYVua', '01000000000', 'male', '2000-02-02', 'admin', '2024-09-13 14:56:43'),
(4, 'Ismayl', 'ismayl@team.com', '$2y$10$6qhUba0tjrV5UzqcHyv0S.b.kn12C49TCa8AmKKWYByfZ926n/Vca', '01000000000', 'male', '2000-02-02', 'admin', '2024-09-13 14:57:15'),
(16, 'tester', 'tester@gmail.com', '$2y$10$Nxt5ioW7zoHPtOLyOYs6WuHKUxznAq.X9k8nXUI/WNP2snIMwKGTm', '1234', 'male', '1111-01-01', 'customer', '2024-09-23 17:30:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
