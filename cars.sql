-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2023 at 11:41 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `carrentone`
--

-- --------------------------------------------------------

--
-- Table structure for table `active_sessions`
--

CREATE TABLE `active_sessions` (
  `uid` int(11) NOT NULL,
  `session_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `travel_distance` varchar(255) DEFAULT NULL,
  `transmission` int(11) DEFAULT NULL,
  `available` int(11) DEFAULT NULL,
  `next_order` date DEFAULT NULL,
  `order_count` int(11) NOT NULL DEFAULT 0,
  `created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `name`, `price`, `description`, `travel_distance`, `transmission`, `available`, `next_order`, `order_count`, `created_at`) VALUES
(1, 'test', 20, 'test', NULL, 0, 1, '2023-07-27', 1, '2023-07-20'),
(2, 'test 2', 25, 'test 2 desc', NULL, 1, 1, NULL, 2, '2023-07-20'),
(3, 'test 3', 25, 'test 3 desc', NULL, 1, 1, NULL, 5, '2023-07-20'),
(4, 'test 4', 20, 'test 4 desc', NULL, 1, 1, NULL, 3, '2023-07-20'),
(5, 'test 5', 600, '456+', NULL, 1, 1, NULL, 9, '2023-07-20'),
(13, 'test 6', 20, 'test 6 descr', NULL, 1, 1, NULL, 3, '2023-07-20');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `firstname`, `lastname`, `email`, `phone_number`, `created_at`) VALUES
(1, 'Arlind', 'Ismalaja', 'asd@asd.com', '123456789', '2023-07-09'),
(2, 'Biorni', 'Ismalaja', 'asd@asd.com', '123456789', '2023-07-09'),
(4, 'Charlie', 'Brown', 'charlie@example.com', '', '2023-07-23'),
(5, 'Bob', 'Smith', 'bob@example.com', '', '2023-07-23'),
(8, 'TEST', 'TEST', 'TEST@example.com', '', '2023-07-26'),
(9, 'Alice', 'Johnson', 'alice@example.com', '', '2023-07-26');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `data` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `data`) VALUES
(9, '{\"senders\":[{\"name\":\"Alice\",\"email\":\"alice@example.com\",\"lastname\":\"Johnson\"}],\"data\":[{\"id\":1,\"sender\":\"userA\",\"content\":\"Hello there!\",\"timestamp\":\"2023-07-21T10:30:00\"}]}\r\n'),
(10, '{\"senders\":[{\"name\":\"Bob\",\"email\":\"bob@example.com\",\"lastname\":\"Smith\"}],\"data\":[{\"id\":2,\"sender\":\"userB\",\"content\":\"Hi, how are you?\",\"timestamp\":\"2023-07-21T11:15:00\"}]}\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `revenue`
--

CREATE TABLE `revenue` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rental_date` date NOT NULL DEFAULT current_timestamp(),
  `car_id` int(11) NOT NULL,
  `rental_duration` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `revenue`
--

INSERT INTO `revenue` (`id`, `customer_id`, `rental_date`, `car_id`, `rental_duration`, `price`) VALUES
(1, 1, '2023-05-09', 4, 1, 10),
(3, 3, '2023-07-19', 4, 1, 20),
(4, 1, '2023-06-03', 4, 5, 15),
(5, 2, '2023-07-19', 4, 1, 30),
(6, 5, '2023-07-19', 4, 1, 30),
(7, 5, '2023-07-19', 4, 3, 30);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_created` date NOT NULL DEFAULT current_timestamp(),
  `currency` varchar(255) NOT NULL DEFAULT 'Euro',
  `last_login` date NOT NULL DEFAULT current_timestamp(),
  `is_admin` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `date_created`, `currency`, `last_login`, `is_admin`) VALUES
(2, 'admin', '21232f297a57a5a743894a0e4a801fc3', '2023-07-05', 'Euro', '2023-07-28', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `revenue`
--
ALTER TABLE `revenue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `revenue`
--
ALTER TABLE `revenue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
