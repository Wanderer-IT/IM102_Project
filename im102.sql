-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 24, 2026 at 07:41 PM
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
-- Database: `im102`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_ID` int(11) NOT NULL,
  `category_Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_ID`, `category_Name`) VALUES
(1, 'Electronics'),
(2, 'Furniture'),
(3, 'Books'),
(4, 'Clothing'),
(5, 'Sports');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `products_ID` int(11) NOT NULL,
  `products_name` varchar(255) DEFAULT NULL,
  `products_Description` varchar(255) DEFAULT NULL,
  `product_Price` double DEFAULT NULL,
  `product_Stock` int(11) DEFAULT NULL,
  `category_ID` int(11) DEFAULT NULL,
  `suppliers_ID` int(11) DEFAULT NULL,
  `created_At` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`products_ID`, `products_name`, `products_Description`, `product_Price`, `product_Stock`, `category_ID`, `suppliers_ID`, `created_At`) VALUES
(1, 'Laptop', '15-inch business laptop', 899.99, 20, 1, 1, '2026-06-14'),
(2, 'Smartphone', 'Android smartphone', 499.99, 35, 1, 1, '2026-06-14'),
(3, 'Wireless Mouse', 'Bluetooth mouse', 29.99, 100, 1, 2, '2026-06-14'),
(4, 'Office Chair', 'Ergonomic chair', 149.99, 15, 2, 2, '2026-06-14'),
(5, 'Study Desk', 'Wooden study desk', 249.99, 10, 2, 3, '2026-06-14'),
(6, 'Database Design', 'SQL learning book', 39.99, 50, 3, 3, '2026-06-14'),
(7, 'PHP for Beginners', 'PHP programming guide', 34.99, 40, 3, 3, '2026-06-14'),
(8, 'T-Shirt', 'Cotton t-shirt', 19.99, 80, 4, 2, '2026-06-14'),
(9, 'Jeans', 'Blue denim jeans', 49.99, 60, 4, 2, '2026-06-14'),
(10, 'Football', 'Professional football', 24.99, 30, 5, 1, '2026-06-14'),
(11, 'Basketball', 'Indoor/outdoor basketball', 29.99, 25, 1, 1, '2026-06-14'),
(12, 'Tennis Racket', 'Lightweight racket', 89.99, 12, 5, 3, '2026-06-14'),
(13, 'magic', 'asd', 12, 12, 4, 2, '2026-06-25'),
(14, 'magic', 'asdasd', 12, 13, 3, 2, '2026-06-25');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `suppliers_ID` int(11) NOT NULL,
  `supplier_Name` varchar(255) DEFAULT NULL,
  `contact_Person` int(11) DEFAULT NULL,
  `phone` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`suppliers_ID`, `supplier_Name`, `contact_Person`, `phone`) VALUES
(1, 'TechSource Ltd', 0, 555),
(2, 'Global Traders', 0, 555),
(3, 'Prime Supplies', 0, 555);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_ID` int(11) NOT NULL,
  `userName` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_ID`, `userName`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'admin', 'admin123@gmail.com', '$2y$10$DathXY6akvIfhrub8G.BE.ORnxRX45EwbmrUEsgaqyTN7qEJ5gt2S', 'admin', '2026-06-24 16:49:11'),
(2, 'ian', 'ianGodfred@gmail.com', '$2y$10$wrx9By4DL0YIt8xqzLYU2eFvAvaFoPnLOq1.OBfGkvMbZQvKD1dbe', 'staff', '2026-06-24 17:15:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_ID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`products_ID`),
  ADD KEY `category_ID` (`category_ID`),
  ADD KEY `suppliers_ID` (`suppliers_ID`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`suppliers_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_ID`),
  ADD UNIQUE KEY `useNname` (`userName`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `products_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `suppliers_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_ID`) REFERENCES `category` (`category_ID`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`suppliers_ID`) REFERENCES `suppliers` (`suppliers_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
