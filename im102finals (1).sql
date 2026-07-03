-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2026 at 04:33 AM
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
-- Database: `im102finals`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Snacks', 'Chips, biscuits, and other light snack foods'),
(2, 'Beverages', 'Soft drinks, juices, and bottled water'),
(3, 'Canned Goods', 'Canned meat, sardines, and vegetables'),
(4, 'Instant Noodles', 'Cup and pack instant noodles'),
(5, 'Toiletries', 'Soap, shampoo, and personal care items'),
(6, 'School Supplies', 'Notebooks, pens, and other school items'),
(7, 'Household Items', 'Cleaning and everyday household needs'),
(8, 'Dairy Products', 'Milk, creamer, and related items'),
(9, 'Condiments & Seasonings', 'Sauces, spices, and seasoning mixes'),
(10, 'Frozen Goods', 'Frozen meat, hotdogs, and ready-to-cook items');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image_url` varchar(500) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `image_url`, `category_id`, `supplier_id`, `added_by`, `created_at`) VALUES
(1, 'Piattos Cheese 40g', 'Ridged potato chips, cheese flavor', 22.00, 45, 'https://ddmmw-assets.s3.ap-southeast-1.amazonaws.com/products/18/10006916.png', 1, 1, NULL, '2026-07-02 04:31:35'),
(2, 'Nova Multigrain 78g', 'Multigrain snack, sour cream flavor', 27.50, 30, 'https://encrypted-tbn0.gstatic.com/shopping?q=tbn:ANd9GcTBtPcsvsJmiagDWha-LgOAXXMPMCdqQp__1BZp9Iv30u5dKqCruCnhYPwv1flGaoMHU3-zh3WPLEqEOPyZ0oRQX85cvXTWAOVmnanQk5rMmjwxTqecgFYh3rs', 1, 1, NULL, '2026-07-02 04:31:35'),
(3, 'Coca-Cola 1.5L', 'Regular soft drink, family size', 68.00, 24, 'https://encrypted-tbn2.gstatic.com/shopping?q=tbn:ANd9GcTykDPDZFDHdFkvSOuRmiI-W_GmLluKil2bYP7t6nuLcwtvfIrYEB2EIA79MZDpFHYNlhbUECHkjIaRaxX6KgLQkcG4od_CK4cgxDapTovjOY6GMNfl69E-CA', 2, 2, NULL, '2026-07-02 04:31:35'),
(4, 'Nature\'s Spring Water 500mL', 'Purified drinking water', 15.00, 60, 'https://encrypted-tbn3.gstatic.com/shopping?q=tbn:ANd9GcST50u60JmsSbEpuAqV3XGzellmvkUWY7TG1jlIBD8_f1ffN-h0iCfa_6uamNpc-zjEs8HB8WxwhTF_a7WfGW5rORFdbbM8N3HmivcnjtfQBJu4wSeTcOJX9A', 2, 2, NULL, '2026-07-02 04:31:35'),
(5, 'Argentina Corned Beef 150g', 'Classic corned beef', 45.00, 18, 'https://store.iloilosupermart.com/wp-content/uploads/2020/05/64.jpg', 3, 3, NULL, '2026-07-02 04:31:35'),
(6, '555 Sardines in Tomato Sauce 155g', 'Sardines in tomato sauce', 22.00, 48, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZlLCOFOoqP4Qay8PwtPp9Lz5JxM0uU3gGKKj5TT-6NQ&s', 3, 3, NULL, '2026-07-02 04:31:35'),
(7, 'Lucky Me Pancit Canton 60g', 'Instant pancit canton noodles', 15.00, 80, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRkTi7qa8xRljC2_dV4bzEu1OAULO7OISh6XfCHonbjk3_KXtJYvhkgaZaJ&s=10', 4, 4, NULL, '2026-07-02 04:31:35'),
(8, 'Nissin Cup Noodles Seafood 40g', 'Cup noodles, seafood flavor', 20.00, 35, 'https://www.shopfreshandgreen.com/cdn/shop/files/Screenshot_27-3-2025_16551_ever.ph_470x.jpg?v=1743775554', 4, 4, NULL, '2026-07-02 04:31:35'),
(9, 'Safeguard Soap 90g', 'Antibacterial bath soap', 32.00, 40, 'https://www.srssulit.com/wp-content/uploads/products/2004896094-2.png', 5, 5, NULL, '2026-07-02 04:31:35'),
(10, 'Palmolive Shampoo Sachet 12mL', 'Shampoo sachet, single use', 8.00, 100, 'https://encrypted-tbn1.gstatic.com/shopping?q=tbn:ANd9GcRASEWrbffS7-rlZHVW49lpSWItZLACyI9IldCKSFDWo9AUyo_B1LQWJG7EUjpyLJg7ofGyg8J0H6IBAlhuksq1zrLO0aDys8re65e69OgHkByzOZwwYhKcI-0', 5, 5, NULL, '2026-07-02 04:31:35'),
(11, 'Victory Yellow Ruled Pad', 'Ruled yellow writing pad', 25.00, 22, 'https://papercart.ph/cdn/shop/files/VictoryYellowRuledPad.png?v=1773041710', 6, 6, NULL, '2026-07-02 04:31:35'),
(12, 'Ballpen Black (Piece)', 'Standard black ballpoint pen', 10.00, 15, 'https://encrypted-tbn3.gstatic.com/shopping?q=tbn:ANd9GcQjp3J7cw5ih-amvNTH2BmdqrLX1bOgettvdGapzFhg1m5kUhBQbRu_JQrSQkG0roSlxu7h1703o4N7gs1efrcEN4mXo1DH', 6, 6, NULL, '2026-07-02 04:31:35'),
(13, 'Tide Powder Detergent 68g', 'Laundry powder detergent, small pack', 12.50, 55, 'https://primomart.ph/cdn/shop/files/4902430831406_ce6281e2-9145-4023-bab2-b9233c358792_1024x1024.jpg?v=1754894782', 7, 5, NULL, '2026-07-02 04:31:35'),
(14, 'Joy Dishwashing Liquid Kalamansi', 'Dishwashing liquid, lemon scent', 38.00, 12, 'https://encrypted-tbn2.gstatic.com/shopping?q=tbn:ANd9GcR0eE9dGmmFcRgxiWt39lRn6nBb1pQ20WUUx68KIxznIeP2Bnc9cFgHfApR7xhj6hdj8iJYK8KpDY8E1mfyB6abhe8NeYr1aiTBEChZNvICeAxI2NsvSUlbugc', 7, 5, NULL, '2026-07-02 04:31:35'),
(15, 'Alaska Evaporated Milk 370mL', 'Evaporated filled milk', 38.50, 26, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT8qaVhYbPvc5OemNmYAxyq7U9snL6WQfUdPjMXDbjHeA&s', 8, 7, NULL, '2026-07-02 04:31:35'),
(16, 'Bear Brand Powdered Milk 33g', 'Fortified powdered milk sachet', 13.00, 70, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSsGtlZ7Tb72yjLSg50dhiku9q8D2JERGzhRH6JVK8nRg&s', 8, 7, NULL, '2026-07-02 04:31:35'),
(17, 'Datu Puti Soy Sauce 1L', 'All-purpose soy sauce', 55.00, 20, 'https://imartgrocersph.com/wp-content/uploads/2020/09/Datu-Puti-Soy-Sauce-1L.png', 9, 8, NULL, '2026-07-02 04:31:35'),
(18, 'Knorr Sinigang Mix 44g', 'Sinigang sa sampalok seasoning mix', 18.00, 33, 'https://sunnyfreshdelivery.com/cdn/shop/products/knorrsinigang1_1600x.jpg?v=1627887637', 9, 8, NULL, '2026-07-02 04:31:35'),
(19, 'Purefoods Tender Juicy Hotdog Classic 1kg', 'Frozen hotdogs, regular size', 145.00, 10, 'https://www.shopfreshandgreen.com/cdn/shop/files/Screenshot_25-3-2025_2101_ever.ph_242x.jpg?v=1743775642', 10, 9, NULL, '2026-07-02 04:31:35'),
(20, 'CDO Chicken Franks', 'Frozen chicken hotdogs', 95.00, 8, 'https://www.shopfreshandgreen.com/cdn/shop/files/Screenshot_25-3-2025_202922_ever.ph_281x.jpg?v=1743775664', 10, 9, NULL, '2026-07-02 04:31:35'),
(21, 'Jack \'n Jill Mini Pretzels Chocolate 28g', 'Chocolate Pretzels sticks', 10.00, 10, 'https://shopsuki.ph/cdn/shop/files/4800016628283_600x600_crop_center.jpg?v=1744022457', 1, 6, 11, '2026-07-02 07:26:28');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `served_by` int(11) DEFAULT NULL,
  `sold_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `product_id`, `quantity`, `total_price`, `served_by`, `sold_at`) VALUES
(1, 1, 2, 44.00, NULL, '2026-06-20 01:15:00'),
(2, 3, 1, 68.00, NULL, '2026-06-20 02:02:00'),
(3, 7, 5, 75.00, NULL, '2026-06-20 03:30:00'),
(4, 9, 1, 32.00, NULL, '2026-06-21 00:45:00'),
(5, 16, 3, 39.00, NULL, '2026-06-21 01:10:00'),
(6, 6, 4, 88.00, NULL, '2026-06-21 05:20:00'),
(7, 10, 10, 80.00, NULL, '2026-06-22 02:05:00'),
(8, 13, 2, 25.00, NULL, '2026-06-22 06:40:00'),
(9, 19, 1, 145.00, NULL, '2026-06-23 08:00:00'),
(10, 4, 6, 90.00, NULL, '2026-06-23 09:15:00'),
(11, 8, 2, 40.00, NULL, '2026-06-24 01:30:00'),
(12, 17, 1, 55.00, NULL, '2026-06-24 03:50:00'),
(13, 2, 3, 82.50, NULL, '2026-06-25 02:00:00'),
(14, 20, 1, 95.00, NULL, '2026-06-25 07:25:00'),
(15, 11, 2, 50.00, NULL, '2026-06-26 00:50:00'),
(16, 18, 4, 72.00, NULL, '2026-06-26 04:10:00'),
(17, 5, 2, 90.00, NULL, '2026-06-27 01:40:00'),
(18, 14, 1, 38.00, NULL, '2026-06-27 05:05:00'),
(19, 15, 2, 77.00, NULL, '2026-06-28 02:20:00'),
(20, 12, 3, 30.00, NULL, '2026-06-28 08:45:00'),
(21, 6, 2, 44.00, 11, '2026-07-02 07:38:56');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact_person`, `phone`, `email`) VALUES
(1, 'JR Grocery Distributors', 'Jun Ramos', '09171234567', 'jrgrocery@supplier.com'),
(2, 'Metro Beverage Supply', 'Carla Ilagan', '09182345678', 'metrobeverage@supplier.com'),
(3, 'Golden Harvest Foods', 'Peter Alonzo', '09193456789', 'goldenharvest@supplier.com'),
(4, 'Sunrise Noodle Traders', 'Divina Reyes', '09204567890', 'sunrisenoodle@supplier.com'),
(5, 'CleanHome Wholesale', 'Manuel Bautista', '09215678901', 'cleanhome@supplier.com'),
(6, 'BrightKids School Supply Co.', 'Angela Fajardo', '09226789012', 'brightkids@supplier.com'),
(7, 'FarmFresh Dairy Traders', 'Noel Ocampo', '09237890123', 'farmfresh@supplier.com'),
(8, 'SpiceWorks Trading', 'Liza Manalo', '09248901234', 'spiceworks@supplier.com'),
(9, 'ColdChain Frozen Foods', 'Ramil Castillo', '09259012345', 'coldchain@supplier.com'),
(10, 'Central Luzon Merchandising', 'Fe Domingo', '09260123456', 'centralluzon@supplier.com');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`) VALUES
(11, 'admin', 'admin123@gmail.com', '$2y$10$hJ0RXp8qeh1oBSFuxC0Q4.DT/3Xe2k94rFVL3vK1/QItD3xxTlp5O', 'admin', '2026-07-02 04:34:04'),
(12, 'ian', 'ianGodfred@gmail.com', '$2y$10$sxnMV0W/n6DBOhtvZ7bSX.cjzX7WfBiUrNc62d/uRpIKGoTKkBwfK', 'staff', '2026-07-02 04:36:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `served_by` (`served_by`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`served_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
