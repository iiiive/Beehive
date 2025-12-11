-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 04:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(10) UNSIGNED NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('active','disabled','pending') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `firstname`, `lastname`, `username`, `email`, `password_hash`, `status`, `created_at`, `reset_token`, `reset_expires`) VALUES
(1, 'Super', 'Admin', 'Charlin', 'charlinive@gmail.com', '$2y$10$EKnlvSH2mXFs4IRljxO8C.CfX0jm2oAMus6PIRC7xcYza/E4WqfVu', 'active', '2025-09-24 06:05:37', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `beehive_readings`
--

CREATE TABLE `beehive_readings` (
  `reading_id` int(11) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `temperature` int(11) DEFAULT NULL,
  `humidity` int(11) DEFAULT NULL,
  `weight` decimal(6,2) DEFAULT NULL,
  `fan_status` tinyint(1) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `heater_status` tinyint(1) DEFAULT NULL,
  `mist_status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beehive_readings`
--

INSERT INTO `beehive_readings` (`reading_id`, `timestamp`, `temperature`, `humidity`, `weight`, `fan_status`, `status`, `heater_status`, `mist_status`) VALUES
(1, '2025-10-06 12:56:04', 36, 64, 1.06, 1, 'Bad', 0, 0),
(2, '2025-10-06 12:56:08', 36, 64, 1.06, 1, 'Bad', 0, 0),
(3, '2025-10-06 12:56:12', 36, 64, 1.06, 1, 'Bad', 0, 0),
(4, '2025-10-06 12:56:17', 36, 64, 1.06, 1, 'Bad', 0, 0),
(5, '2025-10-06 12:56:22', 36, 64, 1.06, 1, 'Bad', 0, 0),
(6, '2025-10-06 12:56:26', 36, 64, 1.06, 1, 'Bad', 0, 0),
(7, '2025-10-06 12:56:31', 36, 64, 1.06, 1, 'Bad', 0, 0),
(8, '2025-10-06 15:05:13', 34, 50, 10.00, 1, 'Bad', 0, 0),
(9, '2025-10-06 15:05:13', 30, 70, 10.00, 1, 'Bad', 0, 0),
(10, '2025-10-06 15:06:51', 32, 55, 10.00, 1, 'Bad', 0, 0),
(11, '2025-10-06 15:13:00', 36, 61, 0.00, 1, 'Bad', 0, 0),
(12, '2025-10-06 15:13:03', 36, 61, 0.00, 1, 'Bad', 0, 0),
(13, '2025-10-06 15:13:09', 36, 61, 0.00, 1, 'Bad', 0, 0),
(14, '2025-10-06 15:13:12', 36, 61, 0.00, 1, 'Bad', 0, 0),
(15, '2025-10-06 15:13:16', 36, 61, -0.07, 1, 'Bad', 0, 0),
(16, '2025-10-06 15:13:22', 36, 61, 0.00, 1, 'Bad', 0, 0),
(17, '2025-10-06 15:13:25', 36, 61, 0.05, 1, 'Bad', 0, 0),
(18, '2025-10-06 15:13:28', 36, 61, 0.12, 1, 'Bad', 0, 0),
(19, '2025-10-06 15:13:32', 36, 60, 0.18, 1, 'Bad', 0, 0),
(20, '2025-10-06 15:13:35', 36, 60, 0.24, 1, 'Bad', 0, 0),
(21, '2025-10-06 15:13:38', 36, 60, 0.30, 1, 'Bad', 0, 0),
(22, '2025-10-06 15:13:42', 36, 60, 0.38, 1, 'Bad', 0, 0),
(23, '2025-10-06 15:13:48', 36, 61, 0.47, 1, 'Bad', 0, 0),
(24, '2025-10-06 15:13:52', 36, 61, 0.57, 1, 'Bad', 0, 0),
(25, '2025-10-06 15:13:55', 36, 61, 0.67, 1, 'Bad', 0, 0),
(26, '2025-10-06 15:14:02', 36, 61, 0.75, 1, 'Bad', 0, 0),
(27, '2025-10-06 15:14:07', 36, 61, 0.81, 1, 'Bad', 0, 0),
(28, '2025-10-06 15:14:11', 36, 61, 0.86, 1, 'Bad', 0, 0),
(29, '2025-10-06 15:14:14', 36, 61, 0.90, 1, 'Bad', 0, 0),
(30, '2025-10-06 15:14:18', 36, 61, 0.94, 1, 'Bad', 0, 0),
(31, '2025-10-06 15:14:21', 36, 61, 0.97, 1, 'Bad', 0, 0),
(32, '2025-10-06 15:14:27', 36, 61, 0.98, 1, 'Bad', 0, 0),
(33, '2025-10-06 15:14:31', 36, 61, 1.00, 1, 'Bad', 0, 0),
(34, '2025-10-06 15:14:37', 36, 61, 1.02, 1, 'Bad', 0, 0),
(35, '2025-10-06 15:14:41', 36, 61, 1.03, 1, 'Bad', 0, 0),
(36, '2025-10-06 15:14:46', 35, 61, 1.04, 1, 'Bad', 0, 0),
(37, '2025-10-06 15:14:52', 35, 61, 1.05, 1, 'Bad', 0, 0),
(38, '2025-10-06 15:14:55', 35, 61, 1.05, 1, 'Bad', 0, 0),
(39, '2025-10-06 15:15:00', 35, 61, 1.06, 1, 'Bad', 0, 0),
(40, '2025-10-06 15:15:04', 35, 61, 1.06, 1, 'Bad', 0, 0),
(41, '2025-10-06 15:15:07', 35, 61, 1.06, 1, 'Bad', 0, 0),
(42, '2025-10-06 15:15:13', 35, 61, 1.06, 1, 'Bad', 0, 0),
(43, '2025-10-06 15:15:16', 35, 61, 1.06, 1, 'Bad', 0, 0),
(44, '2025-10-06 15:15:20', 35, 61, 1.06, 1, 'Bad', 0, 0),
(45, '2025-10-06 15:15:23', 35, 61, 1.07, 1, 'Bad', 0, 0),
(46, '2025-10-06 15:15:28', 35, 61, 1.07, 1, 'Bad', 0, 0),
(47, '2025-10-06 15:15:31', 35, 61, 1.07, 1, 'Bad', 0, 0),
(48, '2025-10-06 15:15:37', 35, 61, 1.07, 1, 'Bad', 0, 0),
(49, '2025-10-06 15:15:40', 35, 61, 1.07, 1, 'Bad', 0, 0),
(50, '2025-10-06 15:15:44', 35, 61, 1.07, 1, 'Bad', 0, 0),
(51, '2025-10-06 15:15:47', 35, 61, 1.07, 1, 'Bad', 0, 0),
(52, '2025-10-06 15:15:52', 35, 61, 1.07, 1, 'Bad', 0, 0),
(53, '2025-10-06 15:15:55', 35, 61, 1.07, 1, 'Bad', 0, 0),
(54, '2025-10-06 15:15:59', 35, 61, 0.98, 1, 'Bad', 0, 0),
(55, '2025-10-06 15:16:02', 35, 61, 0.97, 1, 'Bad', 0, 0),
(56, '2025-10-06 15:16:56', 35, 61, 0.00, 0, 'Bad', 0, 0),
(57, '2025-10-06 15:16:59', 35, 62, 0.00, 0, 'Bad', 0, 0),
(58, '2025-10-06 15:17:03', 35, 62, 0.00, 0, 'Bad', 0, 0),
(59, '2025-10-06 15:17:06', 35, 62, 0.00, 0, 'Bad', 0, 0),
(60, '2025-10-06 15:17:10', 35, 62, -0.11, 0, 'Bad', 0, 0),
(61, '2025-10-06 15:17:16', 35, 62, 0.00, 0, 'Bad', 0, 0),
(62, '2025-10-06 15:17:22', 35, 62, 0.00, 0, 'Bad', 0, 0),
(63, '2025-10-06 15:17:25', 35, 62, 0.11, 0, 'Bad', 0, 0),
(64, '2025-10-06 15:17:31', 35, 62, 0.18, 0, 'Bad', 0, 0),
(65, '2025-10-06 15:17:38', 35, 62, 0.27, 0, 'Bad', 0, 0),
(66, '2025-10-06 15:17:44', 35, 62, 0.39, 0, 'Bad', 0, 0),
(67, '2025-10-06 15:17:48', 35, 62, 0.52, 0, 'Bad', 0, 0),
(68, '2025-10-06 15:17:51', 35, 62, 0.63, 0, 'Bad', 0, 0),
(69, '2025-10-06 15:17:54', 35, 62, 0.71, 0, 'Bad', 0, 0),
(70, '2025-10-06 15:17:58', 35, 62, 0.78, 0, 'Bad', 0, 0),
(71, '2025-10-06 15:18:02', 35, 62, 0.83, 0, 'Bad', 0, 0),
(72, '2025-10-06 15:18:05', 35, 62, 0.87, 0, 'Bad', 0, 0),
(73, '2025-10-06 15:18:08', 35, 62, 0.91, 0, 'Bad', 0, 0),
(74, '2025-10-06 15:18:13', 35, 62, 0.94, 0, 'Bad', 0, 0),
(75, '2025-10-06 15:18:16', 35, 62, 0.96, 0, 'Bad', 0, 0),
(76, '2025-10-06 15:18:19', 35, 62, 0.98, 0, 'Bad', 0, 0),
(77, '2025-10-06 15:18:25', 35, 62, 0.99, 0, 'Bad', 0, 0),
(78, '2025-10-06 15:18:29', 35, 62, 1.01, 0, 'Bad', 0, 0),
(79, '2025-10-06 15:18:32', 35, 63, 1.01, 0, 'Bad', 0, 0),
(80, '2025-10-06 15:18:36', 35, 63, 1.02, 0, 'Bad', 0, 0),
(81, '2025-10-06 15:18:39', 35, 62, 1.03, 0, 'Bad', 0, 0),
(82, '2025-10-06 15:18:42', 35, 63, 1.03, 0, 'Bad', 0, 0),
(83, '2025-10-06 15:18:46', 35, 63, 1.04, 0, 'Bad', 0, 0),
(84, '2025-10-06 15:18:49', 35, 63, 1.04, 0, 'Bad', 0, 0),
(85, '2025-10-06 15:18:53', 35, 63, 1.04, 0, 'Bad', 0, 0),
(86, '2025-10-06 15:18:56', 35, 63, 1.04, 0, 'Bad', 0, 0),
(87, '2025-10-06 15:19:01', 35, 63, 1.04, 0, 'Bad', 0, 0),
(88, '2025-10-06 15:19:07', 35, 63, 1.05, 0, 'Bad', 0, 0),
(89, '2025-10-06 15:19:10', 35, 63, 1.05, 0, 'Bad', 0, 0),
(90, '2025-10-06 15:19:13', 35, 63, 1.05, 0, 'Bad', 0, 0),
(91, '2025-10-06 15:19:17', 35, 63, 1.05, 0, 'Bad', 0, 0),
(92, '2025-10-06 15:19:21', 35, 63, 1.05, 0, 'Bad', 0, 0),
(93, '2025-10-06 15:19:24', 35, 63, 1.05, 0, 'Bad', 0, 0),
(94, '2025-10-06 15:19:28', 35, 63, 1.06, 0, 'Bad', 0, 0),
(95, '2025-10-06 15:19:31', 35, 63, 1.06, 0, 'Bad', 0, 0),
(96, '2025-10-06 15:19:34', 35, 63, 1.06, 0, 'Bad', 0, 0),
(97, '2025-10-06 15:19:38', 35, 63, 1.06, 0, 'Bad', 0, 0),
(98, '2025-10-06 15:19:41', 35, 63, 1.06, 0, 'Bad', 0, 0),
(99, '2025-10-06 15:19:46', 35, 63, 1.06, 0, 'Bad', 0, 0),
(100, '2025-10-06 15:19:49', 35, 63, 1.06, 0, 'Bad', 0, 0);

--
-- Triggers `beehive_readings`
--
DELIMITER $$
CREATE TRIGGER `set_beehive_status_before_insert` BEFORE INSERT ON `beehive_readings` FOR EACH ROW BEGIN
    IF (NEW.temperature BETWEEN 22.3 AND 25.9)
       AND (NEW.humidity BETWEEN 79.2 AND 86.4) THEN
        SET NEW.status = 'Good';
    ELSE
        SET NEW.status = 'Bad';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `set_beehive_status_before_update` BEFORE UPDATE ON `beehive_readings` FOR EACH ROW BEGIN
    IF (NEW.temperature BETWEEN 22.3 AND 25.9)
       AND (NEW.humidity BETWEEN 79.2 AND 86.4) THEN
        SET NEW.status = 'Good';
    ELSE
        SET NEW.status = 'Bad';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bee_feeding_schedule`
--

CREATE TABLE `bee_feeding_schedule` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `next_feed` datetime NOT NULL,
  `interval_minutes` int(11) NOT NULL,
  `last_fed` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `fed_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `fed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bee_feeding_schedule`
--

INSERT INTO `bee_feeding_schedule` (`id`, `user_id`, `next_feed`, `interval_minutes`, `last_fed`, `created_at`, `fed_by_user_id`, `fed_at`) VALUES
(1, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:31:54', '2025-10-03 15:57:56', 3, '2025-10-05 03:31:54'),
(2, 4, '2025-10-05 04:09:29', 1, '2025-10-05 04:08:29', '2025-10-04 06:41:30', 4, '2025-10-05 04:08:29'),
(3, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:36:48', '2025-10-04 19:36:48', 3, '2025-10-05 03:36:48'),
(4, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:36:50', '2025-10-04 19:36:50', 3, '2025-10-05 03:36:50'),
(5, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:36:51', '2025-10-04 19:36:51', 3, '2025-10-05 03:36:51'),
(6, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:36:51', '2025-10-04 19:36:51', 3, '2025-10-05 03:36:51'),
(7, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:36:51', '2025-10-04 19:36:51', 3, '2025-10-05 03:36:51'),
(8, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:36:51', '2025-10-04 19:36:51', 3, '2025-10-05 03:36:51'),
(9, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:36:51', '2025-10-04 19:36:51', 3, '2025-10-05 03:36:51'),
(10, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:36:53', '2025-10-04 19:36:53', 3, '2025-10-05 03:36:53'),
(11, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:36:53', '2025-10-04 19:36:53', 3, '2025-10-05 03:36:53'),
(12, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:36:53', '2025-10-04 19:36:53', 3, '2025-10-05 03:36:53'),
(13, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:36:55', '2025-10-04 19:36:55', 3, '2025-10-05 03:36:55'),
(14, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:38:47', '2025-10-04 19:38:47', 3, '2025-10-05 03:38:47'),
(15, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:38:52', '2025-10-04 19:38:52', 3, '2025-10-05 03:38:52'),
(16, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:38:53', '2025-10-04 19:38:53', 3, '2025-10-05 03:38:53'),
(17, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:38:53', '2025-10-04 19:38:53', 3, '2025-10-05 03:38:53'),
(18, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:44:02', '2025-10-04 19:44:02', 3, '2025-10-05 03:44:02'),
(19, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 03:45:05', '2025-10-04 19:45:05', 3, '2025-10-05 03:45:05'),
(20, 4, '2025-10-05 04:09:29', 1, '2025-10-05 04:08:29', '2025-10-04 19:45:30', 4, '2025-10-05 04:08:29'),
(21, 4, '2025-10-05 04:09:29', 1, '2025-10-05 04:08:29', '2025-10-04 19:52:56', 4, '2025-10-05 04:08:29'),
(22, 4, '2025-10-05 04:09:29', 1, '2025-10-05 04:08:29', '2025-10-04 19:56:56', 4, '2025-10-05 04:08:29'),
(23, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 04:03:37', '2025-10-04 20:03:37', 3, '2025-10-05 04:03:37'),
(24, 4, '2025-10-05 04:09:29', 1, '2025-10-05 04:08:29', '2025-10-04 20:06:16', 4, '2025-10-05 04:08:29'),
(25, 4, '2025-10-05 04:09:29', 1, '2025-10-05 04:08:29', '2025-10-04 20:07:57', 4, '2025-10-05 04:08:29'),
(26, 4, '2025-10-05 04:12:12', 1, '2025-10-05 04:11:12', '2025-10-04 20:11:12', 4, '2025-10-05 04:11:12'),
(27, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 04:11:36', '2025-10-04 20:11:36', 3, '2025-10-05 04:11:36'),
(28, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 04:12:39', '2025-10-04 20:12:39', 3, '2025-10-05 04:12:39'),
(29, 4, '2025-10-05 04:14:49', 1, '2025-10-05 04:13:49', '2025-10-04 20:13:49', 4, '2025-10-05 04:13:49'),
(30, 4, '2025-10-05 04:19:36', 1, '2025-10-05 04:18:36', '2025-10-04 20:18:36', 4, '2025-10-05 04:18:36'),
(31, 4, '2025-10-05 04:21:26', 1, '2025-10-05 04:20:26', '2025-10-04 20:20:26', 4, '2025-10-05 04:20:26'),
(32, 4, '2025-10-05 04:23:53', 1, '2025-10-05 04:22:53', '2025-10-04 20:22:53', 4, '2025-10-05 04:22:53'),
(33, 4, '2025-10-05 04:25:04', 1, '2025-10-05 04:24:04', '2025-10-04 20:24:04', 4, '2025-10-05 04:24:04'),
(34, 4, '2025-10-05 04:26:23', 1, '2025-10-05 04:25:23', '2025-10-04 20:25:23', 4, '2025-10-05 04:25:23'),
(35, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 04:28:17', '2025-10-04 20:28:17', 3, '2025-10-05 04:28:17'),
(36, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 04:32:41', '2025-10-04 20:32:41', 3, '2025-10-05 04:32:41'),
(37, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 14:19:55', '2025-10-05 06:19:55', 3, '2025-10-05 14:19:55'),
(38, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 14:29:51', '2025-10-05 06:29:51', 3, '2025-10-05 14:29:51'),
(39, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 14:36:59', '2025-10-05 06:36:59', 3, '2025-10-05 14:36:59'),
(40, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 14:39:38', '2025-10-05 06:39:38', 3, '2025-10-05 14:39:38'),
(41, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 14:46:56', '2025-10-05 06:46:56', 3, '2025-10-05 14:46:56'),
(42, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 15:12:22', '2025-10-05 07:12:22', 3, '2025-10-05 15:12:22'),
(43, 3, '2025-12-09 07:59:03', 1440, '2025-10-05 15:31:41', '2025-10-05 07:31:41', 3, '2025-10-05 15:31:41'),
(44, 3, '2025-12-09 07:59:03', 1440, '2025-10-06 12:18:22', '2025-10-06 04:18:22', 3, '2025-10-06 12:18:22'),
(45, 4, '2025-10-06 12:47:05', 1, '2025-10-06 12:46:05', '2025-10-06 04:46:05', 4, '2025-10-06 12:46:05'),
(46, 4, '2025-10-06 12:48:14', 1, '2025-10-06 12:47:14', '2025-10-06 04:47:14', 4, '2025-10-06 12:47:14'),
(47, 3, '2025-12-09 07:59:03', 1440, '2025-10-06 14:37:01', '2025-10-06 06:37:01', 3, '2025-10-06 14:37:01'),
(48, 4, '2025-10-06 14:39:14', 1, '2025-10-06 14:38:14', '2025-10-06 06:38:14', 4, '2025-10-06 14:38:14'),
(49, 3, '2025-12-09 07:59:03', 1440, '2025-12-08 14:57:06', '2025-12-08 06:57:06', 3, '2025-12-08 14:57:06');

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

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_by_admin_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `username`, `email`, `password_hash`, `birthday`, `address`, `contact_number`, `created_by_admin_id`, `created_at`, `status`, `reset_token`, `reset_expires`) VALUES
(3, 'Charlin Iverson', 'Dalisay', 'ive', 'charlinive@gmail.com', '$2y$10$dY8rTvt9mIkQhwZnv3mFNez36F3MJrNTqCy5XU4UhTVTBVVTvXGju', '2004-01-05', '1030 Cubul, Sapalbutad, Angeles City', '09462038385', 1, '2025-09-24 06:06:29', 'active', NULL, NULL),
(4, 'Aira', 'Tolentino', 'airajade', 'airajadetolentino26@gmail.com', '$2y$10$rdfPNzaKxaEBKf0srgBtg.kWmmeajYa1iqPAFSMJ0tEtMEtw91fz2', '2004-04-26', 'mawaque, mabalacat', '09462038385', 1, '2025-09-26 06:06:14', 'active', NULL, NULL),
(5, 'Laurenz', 'Timbol', 'lauhash', 'laurenztimbol@gmail.com', '$2y$10$wqENtLU40vWvjaaMXaEpJOjj6HuOQndvbXbJhhWrw4HjxGvjSmNGa', '2004-12-19', 'Cabio Bakal, Sapalibutad, Angeles City', '09462038385', 1, '2025-10-05 06:36:15', 'active', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `beehive_readings`
--
ALTER TABLE `beehive_readings`
  ADD PRIMARY KEY (`reading_id`);

--
-- Indexes for table `bee_feeding_schedule`
--
ALTER TABLE `bee_feeding_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_feeding` (`fed_by_user_id`);

--
-- Indexes for table `db_access`
--
ALTER TABLE `db_access`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `created_by_admin_id` (`created_by_admin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `beehive_readings`
--
ALTER TABLE `beehive_readings`
  MODIFY `reading_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `bee_feeding_schedule`
--
ALTER TABLE `bee_feeding_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `db_access`
--
ALTER TABLE `db_access`
  MODIFY `db_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bee_feeding_schedule`
--
ALTER TABLE `bee_feeding_schedule`
  ADD CONSTRAINT `fk_user_feeding` FOREIGN KEY (`fed_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`created_by_admin_id`) REFERENCES `admins` (`admin_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
