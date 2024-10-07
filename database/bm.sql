-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql301.infinityfree.com
-- Generation Time: Oct 03, 2024 at 01:33 PM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_37304778_bm`
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
(1, 'tester@gmail.com', '2024-09-13 16:42:11', '299.98', '[{\"name\":\"AKM\",\"price\":99.99,\"quantity\":1},{\"name\":\"M416\",\"price\":199.99,\"quantity\":1}]', 'confirmed'),
(2, 'tester@gmail.com', '2024-09-13 18:52:55', '1038.90', '[{\"name\":\"AKM\",\"price\":99.99,\"quantity\":7},{\"name\":\"K2\",\"price\":112.99,\"quantity\":3}]', 'confirmed'),
(3, 'tester@gmail.com', '2024-09-13 19:04:41', '112.99', '[{\"name\":\"K2\",\"price\":112.99,\"quantity\":1}]', 'canceled'),
(4, '2002anamulhasan@gamil.com', '2024-09-14 06:06:34', '99.99', '[{\"name\":\"AKM\",\"price\":99.99,\"quantity\":1}]', 'canceled'),
(5, 'tester@gmail.com', '2024-09-14 20:12:47', '999.95', '[{\"name\":\"M416\",\"price\":199.99,\"quantity\":5}]', 'confirmed'),
(6, 'tester@gmail.com', '2024-09-14 20:52:49', '579.96', '[{\"name\":\"AKM\",\"price\":99.99,\"quantity\":1},{\"name\":\"Mini14\",\"price\":159.99,\"quantity\":3}]', 'canceled'),
(7, 'abirgay@gmail.com', '2024-09-17 17:08:40', '480.00', '[{\"name\":\"AKM\",\"price\":120,\"quantity\":4}]', 'confirmed'),
(8, 'tester@gmail.com', '2024-09-17 17:12:01', '319.99', '[{\"name\":\"AKM\",\"price\":120,\"quantity\":1},{\"name\":\"M416\",\"price\":199.99,\"quantity\":1}]', 'canceled'),
(9, 'jabedhassanabir04@gmail.com', '2024-09-17 17:12:49', '903.96', '[{\"name\":\"AKM\",\"price\":120,\"quantity\":1},{\"name\":\"M416\",\"price\":199.99,\"quantity\":1},{\"name\":\"Beryl M762\",\"price\":74.99,\"quantity\":1},{\"name\":\"Groza\",\"price\":188.99,\"quantity\":1},{\"name\":\"SCAR-L\",\"price\":170,\"quantity\":1},{\"name\":\"G36C\",\"price\":149.99,\"quantity\":1}]', 'canceled'),
(10, 'tester@gmail.com', '2024-09-23 17:28:51', '467.96', '[{\"name\":\"AKM\",\"price\":129.99,\"quantity\":1},{\"name\":\"K2\",\"price\":112.99,\"quantity\":1},{\"name\":\"M16A4\",\"price\":64.99,\"quantity\":1},{\"name\":\"Mini14\",\"price\":159.99,\"quantity\":1}]', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `type` varchar(255) NOT NULL,
  `added_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `image`, `price`, `type`, `added_by`) VALUES
(1, 'AKM', 'akm.png', '129.99', 'ar', 'YTNahid'),
(5, 'M416', 'm416.png', '199.99', 'ar', 'YTNahid'),
(6, 'K2', 'k2.png', '112.99', 'ar', 'YTNahid'),
(7, 'ACE32', 'ace32.png', '155.00', 'ar', 'YTNahid'),
(8, 'Groza', 'groza.png', '188.99', 'ar', 'YTNahid'),
(9, 'MK47 Mutant', 'mk47_mutant.png', '120.00', 'ar', 'YTNahid'),
(10, 'Beryl M762', 'beryl_m762.png', '74.99', 'ar', 'YTNahid'),
(11, 'M16A4', 'm16a4.png', '64.99', 'ar', 'YTNahid'),
(12, 'Famas G2', 'famas_g2.png', '130.00', 'ar', 'YTNahid'),
(13, 'AUG A3', 'aug_a3.png', '299.99', 'ar', 'YTNahid'),
(14, 'QBZ95', 'qbz95.png', '99.99', 'ar', 'YTNahid'),
(15, 'G36C', 'g36c.png', '149.99', 'ar', 'YTNahid'),
(17, 'Mini14', 'img-weapons-mini14.png', '159.99', 'dmr', 'YTNahid'),
(18, 'SCAR-L', 'scar-l.png', '170.00', 'ar', 'YTNahid');

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
(1, 'YTNahid', 'ytnahid@team.com', '$2y$10$xPW1FvggTe8Lvd8zoWYXE.zNKwgVLokEH.mk47cnEPVc1IkRj.36i', '01964800828', 'male', '2001-08-05', 'admin', '2024-09-13 14:54:47'),
(2, 'Taizol', 'taizol@team.com', '$2y$10$QScVKc8fv6BdFAZchQGyT.nwddKZlMo8GHAFQGdwqf/a7WSWQYVua', '01000000000', 'male', '2000-02-02', 'admin', '2024-09-13 14:56:43'),
(3, 'Ismayl', 'ismayl@team.com', '$2y$10$6qhUba0tjrV5UzqcHyv0S.b.kn12C49TCa8AmKKWYByfZ926n/Vca', '01000000000', 'male', '2000-02-02', 'admin', '2024-09-13 14:57:15'),
(4, 'Tester', 'tester@gmail.com', '$2y$10$/xCpAdefvhGBFNkKBYFWM.fSdKHkl.TZByNeUWJDINTHp2fJ27rkS', '01964800828', 'male', '2024-09-11', 'customer', '2024-09-13 14:55:53'),
(5, 'Aurnob', 'aurgho13aurnob@gmail.com', '$2y$10$apD1ycOaDWYL8V/mct917.uEHZevsR8U6vBixoV.ytJBxQE9yW1JS', '01927881774', 'male', '2024-09-14', 'customer', '2024-09-13 18:46:29'),
(6, 'anamul2002', '2002anamulhasan@gamil.com', '$2y$10$NAkmncVN0wDmtoMaAQ8xfeC4iOMytIQzABAcSKC5wVxq2Ni3SUBVC', '01306575021', 'male', '2002-01-01', 'customer', '2024-09-14 06:05:32'),
(9, 'Abir_R1_Max', 'jabedhassanabir04@gmail.com', '$2y$10$XSFi2FmfI7MQ1WaG8ronoOxG1gMZv09yaDGTEjvDULmPDBDlndcAq', '01302741780', 'male', '2002-05-04', 'customer', '2024-09-17 17:06:42');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
