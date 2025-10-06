-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2025 at 09:24 AM
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
-- Database: `beemonitoring`
--

-- --------------------------------------------------------

--
-- Table structure for table `db_access`
--

CREATE TABLE `db_access` (
  `db_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `db_access`
--

INSERT INTO `db_access` (`db_id`, `username`, `password_hash`, `created_at`, `reset_token`, `reset_expires`) VALUES
(1, 'dbadmin', '$2y$10$tSvkE5g9lCg4MKnLGjwwG.lphlLsOs/21HPoexwWchpO7G2X0gvoe', '2025-09-28 11:52:49', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `db_access`
--
ALTER TABLE `db_access`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `db_access`
--
ALTER TABLE `db_access`
  MODIFY `db_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
