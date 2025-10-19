-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 19, 2025 at 11:26 AM
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
-- Database: `trimbookdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `customer_user_id` int(11) NOT NULL,
  `barber_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `customer_user_id`, `barber_id`, `service_id`, `schedule_id`, `appointment_date`, `appointment_time`, `status`, `created_at`) VALUES
(1, 1, 1, 2, NULL, '2025-10-18', '10:00:00', 'completed', '2025-10-18 11:05:50'),
(2, 1, 1, 2, NULL, '2025-10-18', '09:30:00', 'completed', '2025-10-18 11:16:48'),
(3, 1, 1, 2, NULL, '2025-10-18', '09:30:00', 'confirmed', '2025-10-18 13:02:08'),
(4, 1, 1, 1, NULL, '2025-10-18', '09:00:00', 'confirmed', '2025-10-18 13:39:08'),
(5, 1, 3, 1, NULL, '2025-10-25', '09:30:00', 'confirmed', '2025-10-18 14:50:56'),
(6, 1, 2, 2, NULL, '2025-10-18', '09:30:00', 'confirmed', '2025-10-18 15:26:49'),
(7, 1, 2, 2, NULL, '2025-10-19', '18:00:00', 'confirmed', '2025-10-19 08:08:26');

-- --------------------------------------------------------

--
-- Table structure for table `barbers`
--

CREATE TABLE `barbers` (
  `barber_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barbers`
--

INSERT INTO `barbers` (`barber_id`, `user_id`, `specialization`, `experience_years`) VALUES
(1, 2, 'Modern Haircut', 18),
(2, 3, 'Hairstyling', 5),
(3, 4, 'Kids Haircut', 5);

-- --------------------------------------------------------

--
-- Stand-in structure for view `barber_details`
-- (See below for the actual view)
--
CREATE TABLE `barber_details` (
`barber_id` int(11)
,`barber_name` varchar(101)
,`specialization` varchar(100)
,`experience_years` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `barber_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `barber_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(1, 1, 'Monday', '09:00:00', '18:00:00'),
(2, 1, 'Tuesday', '09:00:00', '18:00:00'),
(3, 1, 'Wednesday', '09:00:00', '18:00:00'),
(4, 1, 'Friday', '09:00:00', '18:00:00'),
(5, 1, 'Saturday', '09:00:00', '18:00:00'),
(6, 2, 'Monday', '09:00:00', '18:00:00'),
(7, 2, 'Tuesday', '09:00:00', '18:00:00'),
(8, 2, 'Wednesday', '09:00:00', '18:00:00'),
(9, 2, 'Thursday', '09:00:00', '18:00:00'),
(10, 2, 'Friday', '09:00:00', '18:00:00'),
(11, 2, 'Saturday', '09:00:00', '18:00:00'),
(12, 2, 'Sunday', '09:00:00', '18:00:00'),
(13, 3, 'Monday', '09:00:00', '18:00:00'),
(14, 3, 'Tuesday', '09:00:00', '18:00:00'),
(15, 3, 'Wednesday', '09:00:00', '18:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `description`, `price`) VALUES
(1, 'Modern Haircut', '', 200.00),
(2, 'Classic Haircut', '', 200.00),
(3, 'Beard Styling', '', 300.00);

-- --------------------------------------------------------

--
-- Table structure for table `trimbook_contact`
--

CREATE TABLE `trimbook_contact` (
  `id` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `hours` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trimbook_contact`
--

INSERT INTO `trimbook_contact` (`id`, `address`, `phone`, `email`, `hours`) VALUES
(1, 'Patayak, Sta.Barbara, Pangasinan', '09384396047', 'trimbookSB@example.com', '9:00 AM - 6:00 PM');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `user_type` enum('admin','barber','customer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `phone_no`, `username`, `password`, `profile_photo`, `user_type`, `created_at`) VALUES
(1, 'Brenan', 'Cervantes', 'brma.cervantes.up@phinmaed.com', '09384396040', 'brma789', '$2y$10$n7SCBGMOWRorZWwjtBDDCuq4XY0A05VTb0.jQsn3QgvE5x8ojuAlm', NULL, 'customer', '2025-10-18 10:14:30'),
(2, 'Kuya', 'June', 'kuyajune@example.com', '0938639512', 'kuyaj24', '$2y$10$nBrTKCZId50KjXXCwW97dOrRU0gbVwR/cGFHzkjYN1nJ56DR4FozS', 'uploads/profile_photos/barber_2_1760800704.webp', 'barber', '2025-10-18 10:17:10'),
(3, 'Stephanie', 'Mabalot', 'stephaniemb@example.com', '0938639513', 'stpnmblt', '$2y$10$8dCdcMSoKmRARw8XyGnxeumSmAhUmAkpBgQg/rFF3kl.kSoH.iFi6', 'uploads/profile_photos/barber_3_1760800804.jpg', 'barber', '2025-10-18 12:18:52'),
(4, 'Jomari', 'Lucena', 'janonglucena@example.com', '09386399514', 'janonglangto', '$2y$10$yy68yfCCkvh7Gb0YBHutbOxgng0lT.1QSFrHPeUEPHGYJFZRu9/yK', 'uploads/profile_photos/barber_68f3a69b20e387.99783440.jpg', 'barber', '2025-10-18 14:39:23');

-- --------------------------------------------------------

--
-- Structure for view `barber_details`
--
DROP TABLE IF EXISTS `barber_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `barber_details`  AS SELECT `b`.`barber_id` AS `barber_id`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `barber_name`, `b`.`specialization` AS `specialization`, `b`.`experience_years` AS `experience_years` FROM (`barbers` `b` join `users` `u` on(`b`.`user_id` = `u`.`user_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `customer_user_id` (`customer_user_id`),
  ADD KEY `barber_id` (`barber_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `barbers`
--
ALTER TABLE `barbers`
  ADD PRIMARY KEY (`barber_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `barber_id` (`barber_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `trimbook_contact`
--
ALTER TABLE `trimbook_contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `barbers`
--
ALTER TABLE `barbers`
  MODIFY `barber_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `trimbook_contact`
--
ALTER TABLE `trimbook_contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`customer_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`barber_id`) REFERENCES `barbers` (`barber_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`),
  ADD CONSTRAINT `appointments_ibfk_4` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`);

--
-- Constraints for table `barbers`
--
ALTER TABLE `barbers`
  ADD CONSTRAINT `barbers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`barber_id`) REFERENCES `barbers` (`barber_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
