-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2026 at 11:19 AM
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
-- Database: `hostel_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `property_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `payment_reference` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('pending','success','failed') DEFAULT 'pending',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `property_id`, `student_id`, `payment_reference`, `amount`, `payment_status`, `booking_date`) VALUES
(1, 1, 1, '224952239', 0.00, 'success', '2026-01-16 11:40:37');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL,
  `landlord_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('available','taken') DEFAULT 'available',
  `is_approved` tinyint(1) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `landlord_id`, `title`, `description`, `address`, `price`, `status`, `is_approved`, `image_url`, `created_at`) VALUES
(1, 2, 'A room at osara market', 'The room has it\'s toilet and bathroom together and also it\'s kitchen, but the rules include that you must not damage any resources in the compound', 'No. 19, osara market street, osara', 120000.00, 'taken', 1, 'uploads/1768182655_room 1.jfif', '2026-01-12 01:50:55'),
(2, 4, 'A room opposite police station', 'It is a well furnished room with it\'s own bedroom, kitchen, and toilet ', 'Room 9, No. 52, opposite police station, osara', 200000.00, 'available', 0, 'uploads/1768564688_room 1.jfif', '2026-01-16 11:58:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `role` enum('student','landlord','admin') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `phone`, `role`, `created_at`) VALUES
(1, 'student', 'student@gmail.com', '$2y$10$RBlSY/D1EMLyxNu2RjbNPuqpoBowFLifGzosKHZg4/7//pRToB4k2', NULL, 'student', '2026-01-12 00:39:12'),
(2, 'landlord', 'landlord@gmail.com', '$2y$10$Ne.b5MtGaOl.UkgaiwJmIebHzmi4kqLoY1zHP1rViqUiQf5mt1IO6', NULL, 'landlord', '2026-01-12 00:43:20'),
(3, 'admin', 'admin@gmail.com', '$2y$10$DOfW8u2DUbjScBIhKNdxienIECPM9e8Gpdu.yLcsh0s.aYAgrOf22', NULL, 'admin', '2026-01-12 01:52:26'),
(4, 'landlord2', 'landlord2@gmail.com', '$2y$10$Q4PkOQOqHC4/j4GIyFDoKeKT8tRqr2vJ2KpEFkZpLjaiCqN8AnEaO', NULL, 'landlord', '2026-01-12 18:42:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`property_id`),
  ADD KEY `landlord_id` (`landlord_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
