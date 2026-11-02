-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 01, 2026 at 05:43 PM
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
-- Database: `mahavir`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'Ayush', 'Vyas1111');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(13, 'Krishna', 'kvyas3900@gmail.com', 'Good Services And Facility!', '2026-11-01 15:10:57'),
(14, 'Mahaveer', 'mahaveerdevil65@gmail.com', 'Good Memory this Hotel In my life!', '2026-11-01 15:17:07'),
(15, 'Jasprit Bumrah', 'jb93@gmail.com', 'Good Services!', '2026-11-01 16:31:24');

-- --------------------------------------------------------

--
-- Table structure for table `food_items`
--

CREATE TABLE `food_items` (
  `id` int(11) NOT NULL,
  `food_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_items`
--

INSERT INTO `food_items` (`id`, `food_name`, `price`, `image`) VALUES
(1, 'Mumbai Vadapav', 60.00, 'https://images.unsplash.com/photo-1601050690597-df0568f70950'),
(3, 'Continental Breakfast', 600.00, 'https://images.unsplash.com/photo-1533089860892-a7c6f0a88666'),
(4, 'Paneer Tikka', 360.00, 'https://images.unsplash.com/photo-1565557623262-b51c2513a641'),
(5, 'Cheesy Pizza', 350.00, 'https://images.unsplash.com/photo-1513104890138-7c749659a591'),
(6, 'Veggie Burger', 180.00, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd'),
(7, 'White Sauce Pasta', 280.00, 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5'),
(8, 'Healthy Green Salad', 150.00, 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c'),
(9, 'Chocolate Brownie', 350.00, 'https://images.unsplash.com/photo-1578985545062-69928b1d9587'),
(10, 'Cold Coffee with Ice Cream', 260.00, 'https://images.unsplash.com/photo-1517701604599-bb29b565090c'),
(11, 'Fresh Lemon Juice', 90.00, 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd'),
(12, 'Special Masala Dosa', 260.00, 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976');

-- --------------------------------------------------------

--
-- Table structure for table `food_orders`
--

CREATE TABLE `food_orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `room_no` varchar(20) DEFAULT NULL,
  `items` text DEFAULT NULL,
  `total_amount` int(11) DEFAULT NULL,
  `payment_mode` varchar(50) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Ordered',
  `payment_status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_orders`
--

INSERT INTO `food_orders` (`id`, `customer_name`, `room_no`, `items`, `total_amount`, `payment_mode`, `order_date`, `status`, `payment_status`) VALUES
(21, 'Krishna', '105', 'Cold Coffee with Ice Cream (₹260), Cold Coffee with Ice Cream (₹260)', 520, 'Online', '2026-11-01 11:09:31', 'Preparing', 'Paid'),
(22, 'Mahaveer', '101', 'Paneer Tikka (₹360), Cheesy Pizza (₹350), Veggie Burger (₹180), Fresh Lemon Juice (₹90), Fresh Lemon Juice (₹90), Fresh Lemon Juice (₹90), Fresh Lemon Juice (₹90)', 1250, 'Online', '2026-11-01 15:16:28', 'Ordered', 'Paid'),
(23, 'Jasprit Bumrah', '105', 'Cold Coffee with Ice Cream (₹260), Cold Coffee with Ice Cream (₹260), Cold Coffee with Ice Cream (₹260), Continental Breakfast (₹600), Cheesy Pizza (₹350), Chocolate Brownie (₹350), White Sauce Pasta (₹280)', 2360, 'Cash', '2026-11-01 16:31:06', 'Preparing', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_no` varchar(20) NOT NULL,
  `floor` varchar(50) DEFAULT NULL,
  `room_type` varchar(50) DEFAULT NULL,
  `room_status` varchar(50) DEFAULT 'Clean & Ready',
  `price` decimal(10,2) NOT NULL DEFAULT 1800.00,
  `bed_type` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `max_adults` int(11) DEFAULT 2,
  `max_children` int(11) DEFAULT 1,
  `extra_adult_charge` decimal(10,2) DEFAULT 500.00,
  `extra_child_charge` decimal(10,2) DEFAULT 300.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_no`, `floor`, `room_type`, `room_status`, `price`, `bed_type`, `image`, `max_adults`, `max_children`, `extra_adult_charge`, `extra_child_charge`) VALUES
(1, '101', '1st Floor', 'Single Room', 'Clean & Ready', 1300.00, 'Single Bed', 'https://images.unsplash.com/photo-1591088398332-8a7791972843', 1, 0, 400.00, 200.00),
(2, '102', '1st Floor', 'Standard Room', 'Clean & Ready', 1800.00, 'Queen Size Bed', 'https://images.unsplash.com/photo-1611892440504-42a792e24d32', 2, 0, 500.00, 300.00),
(3, '103', '1st Floor', 'Deluxe Room', 'Clean & Ready', 2500.00, 'Twin Beds', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b', 2, 1, 600.00, 300.00),
(4, '104', '1st Floor', 'Executive Suite', 'Clean & Ready', 3800.00, 'King Size Bed', 'https://images.unsplash.com/photo-1566073771259-6a8506099945', 3, 1, 800.00, 400.00),
(5, '105', '1st Floor', 'King Size Luxury', 'Clean & Ready', 9499.00, 'King Size Luxury', 'https://images.unsplash.com/photo-1591088398332-8a7791972843', 4, 2, 1200.00, 600.00);

-- --------------------------------------------------------

--
-- Table structure for table `room_bookings`
--

CREATE TABLE `room_bookings` (
  `id` int(11) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `id_proof_type` varchar(100) DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `room_id` int(11) DEFAULT NULL,
  `room_no` varchar(50) DEFAULT NULL,
  `check_in` datetime DEFAULT NULL,
  `check_out` datetime DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'Cash',
  `payment_status` varchar(50) DEFAULT 'Pending',
  `id_proof_number` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_bookings`
--

INSERT INTO `room_bookings` (`id`, `phone`, `id_proof_type`, `booking_date`, `room_id`, `room_no`, `check_in`, `check_out`, `user_email`, `user_name`, `total_price`, `payment_method`, `payment_status`, `id_proof_number`) VALUES
(56, '9327014244', 'PAN Card', '2026-11-01 11:09:08', 5, '105', '2026-11-01 17:00:00', '2026-11-01 23:00:00', 'kvyas3900@gmail.com', 'Krishna', 9499.00, 'Credit / Debit Card', 'Paid Successfully', 'OBL5633A99'),
(57, '7878600111', 'Passport', '2026-11-01 15:15:31', 1, '101', '2026-11-01 21:00:00', '2026-11-02 10:00:00', 'mahaveerdevil65@gmail.com', 'Mahaveer ', 1300.00, 'Cash', 'Pending (Pay at Hotel)', 'ABC78941SG'),
(58, '9393000111', 'Driving License', '2026-11-01 16:30:17', 5, '105', '2026-12-25 12:00:00', '2026-12-31 11:00:00', 'jb93@gmail.com', 'Jasprit Bumrah', 56994.00, 'Credit / Debit Card', 'Paid Successfully', 'JB934593JB');

-- --------------------------------------------------------

--
-- Table structure for table `room_reviews`
--

CREATE TABLE `room_reviews` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `guest_name` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_reviews`
--

INSERT INTO `room_reviews` (`id`, `room_id`, `guest_name`, `rating`, `comment`, `created_at`) VALUES
(2, 5, 'Krishna', 5, 'Good Staff And Facility!', '2026-11-01 11:10:12'),
(3, 1, 'Mahaveer', 5, 'Good Area And Good Staff And Location,City view', '2026-11-01 15:13:47'),
(4, 5, 'Jasprit Bumrah', 5, 'Good Services!', '2026-11-01 16:28:11');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'Staff',
  `work_assigned` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `name`, `email`, `password`, `role`, `work_assigned`, `status`, `created_at`) VALUES
(1, 'Rohit Pareekh', 'rohit.p@hotelelite.com', 'rohit123', 'Manager', 'Overall hotel management, finances, and admin supervision', 'Active', '2026-10-25 14:23:10'),
(2, 'Het Mordiya', 'het@hotelelite.com', 'het123', 'Kitchen Manager', 'Manage food orders, menu updates, and kitchen delivery', 'Active', '2026-10-25 14:23:10'),
(3, 'Muskan Malik', 'muskan@hotelelite.com', 'muskan123', 'Receptionist', 'Handle room bookings, guest check-ins, and phone calls', 'Active', '2026-10-25 14:23:10'),
(4, 'Sneha Shukla', 'sneha@hotelelite.com', 'sneha123', 'Room Service', 'Guest room assistance, luggage handling, and food room delivery', 'Active', '2026-10-25 14:23:10'),
(5, 'Priyanshi Yadav', 'priyanshi@hotelelite.com', 'priyanshi123', 'Housekeeping Lead', 'Room cleaning, maintenance, and hotel hygiene management', 'Active', '2026-10-25 14:23:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(16, 'Krishna', 'kvyas3900@gmail.com', '$2y$10$Ru7cQMYiaEbbE5kCHPkKG.Ti5C18wlRHkskXh6ckuNkanxmrnVB2G'),
(17, 'Mahaveer', 'mahaveerdevil65@gmail.com', '$2y$10$/HsPRb.WkQ2y6VvU/6kcs.xB5NPMF.YO/5hkIuXL2HcZfaVq/Py1q'),
(18, 'Jasprit Bumrah', 'jb93@gmail.com', '$2y$10$y..wiprw/a2RDNytcEMPm.yrF8vHIpz2k0P0G.Rhmb8HvCZg2LVQy');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `food_items`
--
ALTER TABLE `food_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `food_orders`
--
ALTER TABLE `food_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_bookings`
--
ALTER TABLE `room_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_reviews`
--
ALTER TABLE `room_reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

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
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `food_items`
--
ALTER TABLE `food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `food_orders`
--
ALTER TABLE `food_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `room_bookings`
--
ALTER TABLE `room_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `room_reviews`
--
ALTER TABLE `room_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
