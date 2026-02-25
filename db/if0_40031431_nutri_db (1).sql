-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql309.infinityfree.com
-- Generation Time: Feb 23, 2026 at 10:58 PM
-- Server version: 11.4.10-MariaDB
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
-- Database: `if0_40031431_nutri_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
(1, 21, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-10 05:44:08'),
(2, 21, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-10 05:44:10'),
(3, 21, 'Logged out', 'Trusted Device', '2026-02-10 06:23:05'),
(4, 27, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-10 06:23:27'),
(5, 27, 'Logged out', 'Trusted Device', '2026-02-10 06:24:17'),
(6, 21, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-10 06:24:43'),
(7, 21, 'Report Added', 'Report ID: 1, Created for Barangay: Himaya, Year: 2025, Title: \'New Report: Barangay Himaya – February 10\'', '2026-02-10 06:38:09'),
(8, 21, 'Logged out', 'Trusted Device', '2026-02-10 06:41:47'),
(9, 17, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-10 06:42:05'),
(10, 17, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-10 07:08:27'),
(11, 17, 'Report Added', 'Report ID: 2, Created for Barangay: Amoros, Year: 2025, Title: \'New Report: Barangay Amoros – February 10\'', '2026-02-10 07:22:22'),
(12, 17, 'Logged out', 'Trusted Device', '2026-02-10 07:23:11'),
(13, 19, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-10 07:23:49'),
(14, 19, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-10 07:24:33'),
(15, 19, 'Report Added', 'Report ID: 3, Created for Barangay: Bolisong, Year: 2025, Title: \'New Report: Barangay Bolisong – February 10\'', '2026-02-10 07:41:07'),
(16, 19, 'Logged out', 'Trusted Device', '2026-02-10 07:41:22'),
(17, 18, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-10 07:41:47'),
(18, 18, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-10 07:41:57'),
(19, 18, 'Logged out', 'Trusted Device', '2026-02-10 07:53:20'),
(20, 30, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-10 08:00:28'),
(21, 30, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-10 08:00:55'),
(22, 30, 'Logged out', 'Trusted Device', '2026-02-10 08:02:55'),
(23, 20, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-10 08:03:17'),
(24, 20, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-10 08:03:27'),
(25, 20, 'Logged out', 'Trusted Device', '2026-02-10 08:20:06'),
(26, 23, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-10 08:20:21'),
(27, 23, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-10 08:20:29'),
(28, 23, 'Report Added', 'Report ID: 4, Created for Barangay: Kalabaylabay, Year: 2026, Title: \'New Report: Barangay Kalabaylabay – February 10\'', '2026-02-10 08:37:11'),
(29, 23, 'Logged out', 'Trusted Device', '2026-02-10 08:37:32'),
(30, 22, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-10 08:37:51'),
(31, 22, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-10 08:37:56'),
(32, 22, 'Report Added', 'Report ID: 5, Created for Barangay: Hinigdaan, Year: 2025, Title: \'New Report: Barangay Hinigdaan – February 10\'', '2026-02-10 08:52:24'),
(33, 22, 'Logged out', 'Trusted Device', '2026-02-10 08:52:59'),
(34, 30, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-11 07:34:24'),
(35, 30, 'Report Added', 'Report ID: 6, Created for Barangay: Calongonan, Year: 2025, Title: \'New Report: Barangay Calongonan – February 11\'', '2026-02-11 07:49:52'),
(36, 30, 'Logged out', 'Trusted Device', '2026-02-11 07:49:58'),
(37, 23, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-11 07:50:26'),
(38, 23, 'Logged out', 'Trusted Device', '2026-02-11 07:50:34'),
(39, 16, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-11 07:51:10'),
(40, 16, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-11 07:51:17'),
(41, 16, 'Report Added', 'Report ID: 7, Created for Barangay: Kibonbon, Year: 2025, Title: \'New Report: Barangay Kibonbon – February 11\'', '2026-02-11 08:03:12'),
(42, 16, 'Logged out', 'Trusted Device', '2026-02-11 08:03:27'),
(43, 20, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-11 08:03:40'),
(44, 20, 'Report Added', 'Report ID: 8, Created for Barangay: Cogon, Year: 2025, Title: \'New Report: Barangay Cogon – February 11\'', '2026-02-11 08:28:15'),
(45, 20, 'Logged out', 'Trusted Device', '2026-02-11 08:28:59'),
(46, 18, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-11 08:29:14'),
(47, 18, 'Report Added', 'Report ID: 9, Created for Barangay: Bolobolo, Year: 2025, Title: \'New Report: Barangay Bolobolo – February 11\'', '2026-02-11 08:52:48'),
(48, 14, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-12 00:31:01'),
(49, 14, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 00:31:09'),
(50, 14, 'Report Added', 'Report ID: 10, Created for Barangay: Ulaliman, Year: 2025, Title: \'New Report: Barangay Ulaliman – February 12\'', '2026-02-12 00:55:34'),
(51, 14, 'Logged out', 'Trusted Device', '2026-02-12 00:55:55'),
(52, 31, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-12 00:56:10'),
(53, 31, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 00:56:23'),
(54, 31, 'Report Added', 'Report ID: 11, Created for Barangay: Sinaloc, Year: 2025, Title: \'New Report: Barangay Sinaloc – February 12\'', '2026-02-12 01:08:24'),
(55, 31, 'Logged out', 'Trusted Device', '2026-02-12 01:08:41'),
(56, 25, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-12 01:08:56'),
(57, 25, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 01:09:01'),
(58, 25, 'Report Added', 'Report ID: 12, Created for Barangay: Taytay, Year: 2025, Title: \'New Report: Barangay Taytay – February 12\'', '2026-02-12 01:26:03'),
(59, 25, 'Logged out', 'Trusted Device', '2026-02-12 01:26:13'),
(60, 24, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-12 01:26:36'),
(61, 24, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 01:26:41'),
(62, 24, 'Report Added', 'Report ID: 13, Created for Barangay: Molugan, Year: 2025, Title: \'New Report: Barangay Molugan – February 12\'', '2026-02-12 01:43:09'),
(63, 24, 'Logged out', 'Trusted Device', '2026-02-12 01:43:33'),
(64, 25, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 01:43:48'),
(65, 25, 'Viewed report (ID: 12, Title: New Report: Barangay Taytay – February 12)', NULL, '2026-02-12 01:43:57'),
(66, 25, 'Updated report (cloned as Pending)', 'Old Report ID: 12 → New Report ID: 14', '2026-02-12 01:44:29'),
(67, 25, 'Updated report (cloned as Pending)', 'Old Report ID: 12 → New Report ID: 15', '2026-02-12 01:46:39'),
(68, 31, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 01:46:41'),
(69, 31, 'Viewed report (ID: 11, Title: New Report: Barangay Sinaloc – February 12)', NULL, '2026-02-12 02:02:13'),
(70, 31, 'Updated report (cloned as Pending)', 'Old Report ID: 11 → New Report ID: 16', '2026-02-12 02:09:48'),
(71, 31, 'Resubmitted report ID 16', NULL, '2026-02-12 02:09:59'),
(72, 31, 'Unsubmitted report ID 11', NULL, '2026-02-12 02:10:08'),
(73, 31, 'Logged out', 'Trusted Device', '2026-02-12 02:10:17'),
(74, 17, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 02:10:24'),
(75, 17, 'Viewed report (ID: 2, Title: New Report: Barangay Amoros – February 10)', NULL, '2026-02-12 02:10:39'),
(76, 17, 'Viewed report (ID: 2, Title: New Report: Barangay Amoros – February 10)', NULL, '2026-02-12 02:10:59'),
(77, 17, 'Updated report (cloned as Pending)', 'Old Report ID: 2 → New Report ID: 17', '2026-02-12 02:11:24'),
(78, 17, 'Resubmitted report ID 17', NULL, '2026-02-12 02:11:28'),
(79, 17, 'Unsubmitted report ID 2', NULL, '2026-02-12 02:11:31'),
(80, 17, 'Logged out', 'Trusted Device', '2026-02-12 02:11:37'),
(81, 19, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 02:11:47'),
(82, 19, 'Viewed report (ID: 3, Title: New Report: Barangay Bolisong – February 10)', NULL, '2026-02-12 02:11:55'),
(83, 19, 'Updated report (cloned as Pending)', 'Old Report ID: 3 → New Report ID: 18', '2026-02-12 02:12:19'),
(84, 19, 'Resubmitted report ID 18', NULL, '2026-02-12 02:12:23'),
(85, 19, 'Unsubmitted report ID 3', NULL, '2026-02-12 02:12:26'),
(86, 19, 'Logged out', 'Trusted Device', '2026-02-12 02:12:29'),
(87, 18, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 02:12:38'),
(88, 18, 'Viewed report (ID: 9, Title: New Report: Barangay Bolobolo – February 11)', NULL, '2026-02-12 02:12:41'),
(89, 18, 'Updated report (cloned as Pending)', 'Old Report ID: 9 → New Report ID: 19', '2026-02-12 02:15:06'),
(90, 18, 'Resubmitted report ID 19', NULL, '2026-02-12 02:15:10'),
(91, 18, 'Unsubmitted report ID 9', NULL, '2026-02-12 02:15:11'),
(92, 18, 'Logged out', 'Trusted Device', '2026-02-12 02:15:16'),
(93, 30, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 02:15:23'),
(94, 30, 'Viewed report (ID: 6, Title: New Report: Barangay Calongonan – February 11)', NULL, '2026-02-12 02:15:28'),
(95, 30, 'Updated report (cloned as Pending)', 'Old Report ID: 6 → New Report ID: 20', '2026-02-12 02:16:58'),
(96, 30, 'Resubmitted report ID 20', NULL, '2026-02-12 02:17:01'),
(97, 30, 'Unsubmitted report ID 6', NULL, '2026-02-12 02:17:03'),
(98, 30, 'Logged out', 'Trusted Device', '2026-02-12 02:17:07'),
(99, 20, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 02:17:16'),
(100, 20, 'Viewed report (ID: 8, Title: New Report: Barangay Cogon – February 11)', NULL, '2026-02-12 02:17:29'),
(101, 20, 'Updated report (cloned as Pending)', 'Old Report ID: 8 → New Report ID: 21', '2026-02-12 02:18:28'),
(102, 20, 'Resubmitted report ID 21', NULL, '2026-02-12 02:18:34'),
(103, 20, 'Unsubmitted report ID 8', NULL, '2026-02-12 02:18:35'),
(104, 20, 'Logged out', 'Trusted Device', '2026-02-12 02:18:49'),
(105, 21, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 02:18:57'),
(106, 21, 'Viewed report (ID: 1, Title: New Report: Barangay Himaya – February 10)', NULL, '2026-02-12 02:19:01'),
(107, 21, 'Updated report (cloned as Pending)', 'Old Report ID: 1 → New Report ID: 22', '2026-02-12 02:20:17'),
(108, 21, 'Resubmitted report ID 22', NULL, '2026-02-12 02:20:22'),
(109, 21, 'Unsubmitted report ID 1', NULL, '2026-02-12 02:20:23'),
(110, 21, 'Logged out', 'Trusted Device', '2026-02-12 02:20:26'),
(111, 22, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 02:20:34'),
(112, 22, 'Viewed report (ID: 5, Title: New Report: Barangay Hinigdaan – February 10)', NULL, '2026-02-12 02:20:46'),
(113, 22, 'Updated report (cloned as Pending)', 'Old Report ID: 5 → New Report ID: 23', '2026-02-12 02:21:48'),
(114, 22, 'Resubmitted report ID 23', NULL, '2026-02-12 02:21:52'),
(115, 22, 'Unsubmitted report ID 5', NULL, '2026-02-12 02:21:53'),
(116, 22, 'Logged out', 'Trusted Device', '2026-02-12 02:21:57'),
(117, 23, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 02:22:07'),
(118, 23, 'Viewed report (ID: 4, Title: New Report: Barangay Kalabaylabay – February 10)', NULL, '2026-02-12 02:22:14'),
(119, 23, 'Updated report (cloned as Pending)', 'Old Report ID: 4 → New Report ID: 24', '2026-02-12 02:22:45'),
(120, 23, 'Resubmitted report ID 24', NULL, '2026-02-12 02:22:49'),
(121, 23, 'Unsubmitted report ID 4', NULL, '2026-02-12 02:22:50'),
(122, 23, 'Logged out', 'Trusted Device', '2026-02-12 02:22:53'),
(123, 16, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 02:23:02'),
(124, 16, 'Viewed report (ID: 7, Title: New Report: Barangay Kibonbon – February 11)', NULL, '2026-02-12 02:23:09'),
(125, 16, 'Updated report (cloned as Pending)', 'Old Report ID: 7 → New Report ID: 25', '2026-02-12 02:24:05'),
(126, 16, 'Resubmitted report ID 25', NULL, '2026-02-12 02:24:11'),
(127, 16, 'Unsubmitted report ID 7', NULL, '2026-02-12 02:24:12'),
(128, 16, 'Logged out', 'Trusted Device', '2026-02-12 02:24:18'),
(129, 24, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 02:24:26'),
(130, 24, 'Viewed report (ID: 13, Title: New Report: Barangay Molugan – February 12)', NULL, '2026-02-12 02:26:39'),
(131, 24, 'Updated report (cloned as Pending)', 'Old Report ID: 13 → New Report ID: 26', '2026-02-12 02:27:40'),
(132, 24, 'Resubmitted report ID 26', NULL, '2026-02-12 02:27:50'),
(133, 24, 'Unsubmitted report ID 13', NULL, '2026-02-12 02:27:52'),
(134, 24, 'Logged out', 'Trusted Device', '2026-02-12 02:27:56'),
(135, 13, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-12 02:29:50'),
(136, 13, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 02:30:07'),
(137, 13, 'Report Added', 'Report ID: 27, Created for Barangay: Poblacion, Year: 2025, Title: \'New Report: Barangay Poblacion – February 12\'', '2026-02-12 02:45:58'),
(138, 13, 'Logged out', 'Trusted Device', '2026-02-12 02:47:00'),
(139, 12, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-12 02:47:15'),
(140, 12, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 02:47:17'),
(141, 12, 'Report Added', 'Report ID: 28, Created for Barangay: Sambulawan, Year: 2025, Title: \'New Report: Barangay Sambulawan – February 12\'', '2026-02-12 02:59:12'),
(142, 12, 'Logged out', 'Trusted Device', '2026-02-12 02:59:17'),
(143, 25, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 02:59:26'),
(144, 25, 'Viewed report (ID: 12, Title: New Report: Barangay Taytay – February 12)', NULL, '2026-02-12 02:59:30'),
(145, 25, 'Updated report (cloned as Pending)', 'Old Report ID: 12 → New Report ID: 29', '2026-02-12 03:00:28'),
(146, 25, 'Resubmitted report ID 29', NULL, '2026-02-12 03:00:37'),
(147, 25, 'Unsubmitted report ID 12', NULL, '2026-02-12 03:00:39'),
(148, 25, 'Logged out', 'Trusted Device', '2026-02-12 03:00:47'),
(149, 14, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 03:01:00'),
(150, 14, 'Viewed report (ID: 10, Title: New Report: Barangay Ulaliman – February 12)', NULL, '2026-02-12 03:01:03'),
(151, 14, 'Updated report (cloned as Pending)', 'Old Report ID: 10 → New Report ID: 30', '2026-02-12 03:01:43'),
(152, 14, 'Resubmitted report ID 30', NULL, '2026-02-12 03:01:49'),
(153, 14, 'Unsubmitted report ID 10', NULL, '2026-02-12 03:01:50'),
(154, 14, 'Logged out', 'Trusted Device', '2026-02-12 03:01:57'),
(155, 27, 'OTP sent for new device login', 'Device token: 2ebe0294fcc76a1b61431e22ca038f6b, IP: 143.44.192.116', '2026-02-12 03:02:05'),
(156, 27, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 03:02:12'),
(157, 27, 'Approved report ID 30', NULL, '2026-02-12 03:03:40'),
(158, 27, 'Approved report ID 29', NULL, '2026-02-12 03:03:44'),
(159, 27, 'Approved report ID 28', NULL, '2026-02-12 03:03:47'),
(160, 27, 'Approved report ID 27', NULL, '2026-02-12 03:03:48'),
(161, 27, 'Approved report ID 26', NULL, '2026-02-12 03:03:48'),
(162, 27, 'Approved report ID 25', NULL, '2026-02-12 03:03:49'),
(163, 27, 'Approved report ID 24', NULL, '2026-02-12 03:03:49'),
(164, 27, 'Approved report ID 23', NULL, '2026-02-12 03:03:50'),
(165, 27, 'Approved report ID 22', NULL, '2026-02-12 03:03:51'),
(166, 27, 'Approved report ID 22', NULL, '2026-02-12 03:03:51'),
(167, 27, 'Approved report ID 21', NULL, '2026-02-12 03:03:51'),
(168, 27, 'Approved report ID 20', NULL, '2026-02-12 03:03:52'),
(169, 27, 'Approved report ID 19', NULL, '2026-02-12 03:03:53'),
(170, 27, 'Approved report ID 18', NULL, '2026-02-12 03:03:54'),
(171, 27, 'Approved report ID 17', NULL, '2026-02-12 03:03:55'),
(172, 27, 'Approved report ID 16', NULL, '2026-02-12 03:03:57'),
(173, 27, 'Logged out', 'Trusted Device', '2026-02-12 03:13:47'),
(174, 23, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 03:13:56'),
(175, 23, 'Viewed report (ID: 24, Title: New Report: Barangay Kalabaylabay – February 10)', NULL, '2026-02-12 03:14:02'),
(176, 23, 'Updated report (cloned as Pending)', 'Old Report ID: 24 → New Report ID: 31', '2026-02-12 03:14:08'),
(177, 23, 'Logged out', 'Trusted Device', '2026-02-12 03:14:30'),
(178, 27, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 03:14:35'),
(179, 27, 'Logged out', 'Trusted Device', '2026-02-12 03:18:00'),
(180, 23, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 03:18:11'),
(181, 23, 'Viewed report (ID: 24, Title: New Report: Barangay Kalabaylabay – February 10)', NULL, '2026-02-12 03:18:25'),
(182, 23, 'Updated report (cloned as Pending)', 'Old Report ID: 24 → New Report ID: 32', '2026-02-12 03:18:31'),
(183, 23, 'Resubmitted report ID 32', NULL, '2026-02-12 03:18:41'),
(184, 23, 'Archived report (ID: 31) as BNS', NULL, '2026-02-12 03:18:46'),
(185, 23, 'Archived report (ID: 4) as BNS', NULL, '2026-02-12 03:18:48'),
(186, 23, 'Logged out', 'Trusted Device', '2026-02-12 03:18:52'),
(187, 27, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 03:18:57'),
(188, 27, 'Approved report ID 32', NULL, '2026-02-12 03:19:12'),
(189, 27, 'Logged out', 'Trusted Device', '2026-02-12 03:32:40'),
(190, 25, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 03:32:48'),
(191, 25, 'Viewed report (ID: 29, Title: New Report: Barangay Taytay – February 12)', NULL, '2026-02-12 03:32:56'),
(192, 25, 'Updated report (cloned as Pending)', 'Old Report ID: 29 → New Report ID: 33', '2026-02-12 03:32:59'),
(193, 25, 'Resubmitted report ID 33', NULL, '2026-02-12 03:33:21'),
(194, 25, 'Archived report (ID: 15) as BNS', NULL, '2026-02-12 03:33:26'),
(195, 25, 'Archived report (ID: 14) as BNS', NULL, '2026-02-12 03:33:27'),
(196, 25, 'Archived report (ID: 12) as BNS', NULL, '2026-02-12 03:33:29'),
(197, 25, 'Logged out', 'Trusted Device', '2026-02-12 03:33:33'),
(198, 27, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 03:33:38'),
(199, 27, 'Approved report ID 33', NULL, '2026-02-12 03:33:44'),
(200, 27, 'Logged out', 'Trusted Device', '2026-02-12 03:34:38'),
(201, 25, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 03:34:45'),
(202, 25, 'Viewed report (ID: 33, Title: New Report: Barangay Taytay – February 12)', NULL, '2026-02-12 03:34:53'),
(203, 25, 'Updated report (cloned as Pending)', 'Old Report ID: 33 → New Report ID: 34', '2026-02-12 03:34:56'),
(204, 25, 'Resubmitted report ID 34', NULL, '2026-02-12 03:35:02'),
(205, 25, 'Logged out', 'Trusted Device', '2026-02-12 03:35:06'),
(206, 27, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 03:35:11'),
(207, 27, 'Approved report ID 34', NULL, '2026-02-12 03:35:25'),
(208, 27, 'Logged out', 'Trusted Device', '2026-02-12 03:36:12'),
(209, 27, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 03:36:25'),
(210, 27, 'Logged out', 'Trusted Device', '2026-02-12 03:37:18'),
(211, 25, 'User logged in', 'Device token login from IP 143.44.192.116', '2026-02-12 03:37:26'),
(212, 25, 'Logged out', 'Trusted Device', '2026-02-12 03:38:02'),
(213, 17, 'User logged in', 'Device token login from IP 143.44.192.176', '2026-02-12 07:14:50'),
(214, 17, 'Logged out', 'Trusted Device', '2026-02-12 07:14:56'),
(215, 27, 'User logged in', 'Device token login from IP 143.44.192.176', '2026-02-12 07:15:08');

-- --------------------------------------------------------

--
-- Table structure for table `bns_reports`
--

CREATE TABLE `bns_reports` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `year` year(4) NOT NULL,
  `title` varchar(255) NOT NULL,
  `ind1` int(11) DEFAULT NULL,
  `ind_male` int(11) DEFAULT NULL,
  `ind_female` int(11) DEFAULT NULL,
  `ind2` int(11) DEFAULT NULL,
  `ind3` int(11) DEFAULT NULL,
  `ind4` int(11) DEFAULT NULL,
  `ind5` int(11) DEFAULT NULL,
  `ind6a` int(11) DEFAULT NULL,
  `ind6b` int(11) DEFAULT NULL,
  `ind7` int(11) DEFAULT NULL,
  `ind8` int(11) DEFAULT NULL,
  `ind9` int(11) DEFAULT NULL,
  `ind9a` decimal(5,2) DEFAULT NULL,
  `ind9b1_no` int(11) DEFAULT NULL,
  `ind9b1_pct` decimal(5,2) DEFAULT NULL,
  `ind9b2_no` int(11) DEFAULT NULL,
  `ind9b2_pct` decimal(5,2) DEFAULT NULL,
  `ind9b3_no` int(11) DEFAULT NULL,
  `ind9b3_pct` decimal(5,2) DEFAULT NULL,
  `ind9b4_no` int(11) DEFAULT NULL,
  `ind9b4_pct` decimal(5,2) DEFAULT NULL,
  `ind9b5_no` int(11) DEFAULT NULL,
  `ind9b5_pct` decimal(5,2) DEFAULT NULL,
  `ind9b6_no` int(11) DEFAULT NULL,
  `ind9b6_pct` decimal(5,2) DEFAULT NULL,
  `ind9b7_no` int(11) DEFAULT NULL,
  `ind9b7_pct` decimal(5,2) DEFAULT NULL,
  `ind9b8_no` int(11) DEFAULT NULL,
  `ind9b8_pct` decimal(5,2) DEFAULT NULL,
  `ind9b9_no` int(11) DEFAULT NULL,
  `ind9b9_pct` decimal(5,2) DEFAULT NULL,
  `ind10` int(11) DEFAULT NULL,
  `ind11` int(11) DEFAULT NULL,
  `ind12` int(11) DEFAULT NULL,
  `ind13` int(11) DEFAULT NULL,
  `ind14` int(11) DEFAULT NULL,
  `ind15` int(11) DEFAULT NULL,
  `ind16` int(11) DEFAULT NULL,
  `ind17a_public` int(11) DEFAULT NULL,
  `ind17a_private` int(11) DEFAULT NULL,
  `ind17b_public` int(11) DEFAULT NULL,
  `ind17b_private` int(11) DEFAULT NULL,
  `ind18` int(11) DEFAULT NULL,
  `ind19` int(11) DEFAULT NULL,
  `ind20` int(11) DEFAULT NULL,
  `ind21` decimal(5,2) DEFAULT NULL,
  `ind22a_no` int(11) DEFAULT NULL,
  `ind22a_pct` decimal(5,2) DEFAULT NULL,
  `ind22b_no` int(11) DEFAULT NULL,
  `ind22b_pct` decimal(5,2) DEFAULT NULL,
  `ind22c_no` int(11) DEFAULT NULL,
  `ind22c_pct` decimal(5,2) DEFAULT NULL,
  `ind22d_no` int(11) DEFAULT NULL,
  `ind22d_pct` decimal(5,2) DEFAULT NULL,
  `ind22e_no` int(11) DEFAULT NULL,
  `ind22e_pct` decimal(5,2) DEFAULT NULL,
  `ind22f_no` int(11) DEFAULT NULL,
  `ind22f_pct` decimal(5,2) DEFAULT NULL,
  `ind22g_no` int(11) DEFAULT NULL,
  `ind22g_pct` decimal(5,2) DEFAULT NULL,
  `ind23` int(11) DEFAULT NULL,
  `ind24` int(11) DEFAULT NULL,
  `ind25` int(11) DEFAULT NULL,
  `ind26` int(11) DEFAULT NULL,
  `ind27a_no` int(11) DEFAULT NULL,
  `ind27a_pct` decimal(5,2) DEFAULT NULL,
  `ind27b_no` int(11) DEFAULT NULL,
  `ind27b_pct` decimal(5,2) DEFAULT NULL,
  `ind27c_no` int(11) DEFAULT NULL,
  `ind27c_pct` decimal(5,2) DEFAULT NULL,
  `ind27d_no` int(11) DEFAULT NULL,
  `ind27d_pct` decimal(5,2) DEFAULT NULL,
  `ind27e_no` int(11) DEFAULT NULL,
  `ind27e_pct` decimal(5,2) DEFAULT NULL,
  `ind28a_no` int(11) DEFAULT NULL,
  `ind28a_pct` decimal(5,2) DEFAULT NULL,
  `ind28b_no` int(11) DEFAULT NULL,
  `ind28b_pct` decimal(5,2) DEFAULT NULL,
  `ind28c_no` int(11) DEFAULT NULL,
  `ind28c_pct` decimal(5,2) DEFAULT NULL,
  `ind28d_no` int(11) DEFAULT NULL,
  `ind28d_pct` decimal(5,2) DEFAULT NULL,
  `ind29a_no` int(11) DEFAULT NULL,
  `ind29a_pct` decimal(5,2) DEFAULT NULL,
  `ind29b_no` int(11) DEFAULT NULL,
  `ind29b_pct` decimal(5,2) DEFAULT NULL,
  `ind29c_no` int(11) DEFAULT NULL,
  `ind29c_pct` decimal(5,2) DEFAULT NULL,
  `ind29d_no` int(11) DEFAULT NULL,
  `ind29d_pct` decimal(5,2) DEFAULT NULL,
  `ind29e_no` int(11) DEFAULT NULL,
  `ind29e_pct` decimal(5,2) DEFAULT NULL,
  `ind29f_no` int(11) DEFAULT NULL,
  `ind29f_pct` decimal(5,2) DEFAULT NULL,
  `ind29g_no` int(11) DEFAULT NULL,
  `ind29g_pct` decimal(5,2) DEFAULT NULL,
  `ind30a_no` int(11) DEFAULT NULL,
  `ind30a_pct` decimal(5,2) DEFAULT NULL,
  `ind30b_no` int(11) DEFAULT NULL,
  `ind30b_pct` decimal(5,2) DEFAULT NULL,
  `ind30c_no` int(11) DEFAULT NULL,
  `ind30c_pct` decimal(5,2) DEFAULT NULL,
  `ind30d_no` int(11) DEFAULT NULL,
  `ind30d_pct` decimal(5,2) DEFAULT NULL,
  `ind31a_no` int(11) DEFAULT NULL,
  `ind31a_pct` decimal(5,2) DEFAULT NULL,
  `ind31b_no` int(11) DEFAULT NULL,
  `ind31b_pct` decimal(5,2) DEFAULT NULL,
  `ind31c_no` int(11) DEFAULT NULL,
  `ind31c_pct` decimal(5,2) DEFAULT NULL,
  `ind31d_no` int(11) DEFAULT NULL,
  `ind31d_pct` decimal(5,2) DEFAULT NULL,
  `ind31e_no` int(11) DEFAULT NULL,
  `ind31e_pct` decimal(5,2) DEFAULT NULL,
  `ind31f_no` int(11) DEFAULT NULL,
    `ind31f_pct` decimal(5,2) DEFAULT NULL,
  `ind32` int(11) DEFAULT NULL,
  `ind33` int(11) DEFAULT NULL,
  `ind34` int(11) DEFAULT NULL,
  `ind35` int(11) DEFAULT NULL,
  `ind36` int(11) DEFAULT NULL,
  `ind37a` int(11) DEFAULT NULL,
  `ind37b` int(11) DEFAULT NULL,
  `ind38` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bns_reports`
--

INSERT INTO `bns_reports` (`id`, `report_id`, `barangay`, `year`, `title`, `ind1`, `ind_male`, `ind_female`, `ind2`, `ind3`, `ind4`, `ind5`, `ind6a`, `ind6b`, `ind7`, `ind8`, `ind9`, `ind9a`, `ind9b1_no`, `ind9b1_pct`, `ind9b2_no`, `ind9b2_pct`, `ind9b3_no`, `ind9b3_pct`, `ind9b4_no`, `ind9b4_pct`, `ind9b5_no`, `ind9b5_pct`, `ind9b6_no`, `ind9b6_pct`, `ind9b7_no`, `ind9b7_pct`, `ind9b8_no`, `ind9b8_pct`, `ind9b9_no`, `ind9b9_pct`, `ind10`, `ind11`, `ind12`, `ind13`, `ind14`, `ind15`, `ind16`, `ind17a_public`, `ind17a_private`, `ind17b_public`, `ind17b_private`, `ind18`, `ind19`, `ind20`, `ind21`, `ind22a_no`, `ind22a_pct`, `ind22b_no`, `ind22b_pct`, `ind22c_no`, `ind22c_pct`, `ind22d_no`, `ind22d_pct`, `ind22e_no`, `ind22e_pct`, `ind22f_no`, `ind22f_pct`, `ind22g_no`, `ind22g_pct`, `ind23`, `ind24`, `ind25`, `ind26`, `ind27a_no`, `ind27a_pct`, `ind27b_no`, `ind27b_pct`, `ind27c_no`, `ind27c_pct`, `ind27d_no`, `ind27d_pct`, `ind27e_no`, `ind27e_pct`, `ind28a_no`, `ind28a_pct`, `ind28b_no`, `ind28b_pct`, `ind28c_no`, `ind28c_pct`, `ind28d_no`, `ind28d_pct`, `ind29a_no`, `ind29a_pct`, `ind29b_no`, `ind29b_pct`, `ind29c_no`, `ind29c_pct`, `ind29d_no`, `ind29d_pct`, `ind29e_no`, `ind29e_pct`, `ind29f_no`, `ind29f_pct`, `ind29g_no`, `ind29g_pct`, `ind30a_no`, `ind30a_pct`, `ind30b_no`, `ind30b_pct`, `ind30c_no`, `ind30c_pct`, `ind30d_no`, `ind30d_pct`, `ind31a_no`, `ind31a_pct`, `ind31b_no`, `ind31b_pct`, `ind31c_no`, `ind31c_pct`, `ind31d_no`, `ind31d_pct`, `ind31e_no`, `ind31e_pct`, `ind31f_no`, `ind31f_pct`, `ind32_no`, `ind32_pct`, `ind33_no`, `ind33_pct`, `ind34_no`, `ind34_pct`, `ind35_no`, `ind35_pct`, `ind36_no`, `ind36_pct`, `ind37a`, `ind37b`, `ind38`) VALUES
(1, 1, 'Himaya', 2025, 'New Report: Barangay Himaya – February 10', 2569, 1394, 1175, 616, 621, 429, 149, 43, 27, 166, 229, 182, '79.48', 1, '0.45', 6, '2.69', 217, '96.88', 0, '0.00', 3, '1.34', 3, '1.34', 1, '0.45', 1, '0.45', 7, '3.13', 19, 29, 74, 152, 108, 30, 5, 1, 0, 1, 0, 39, 298, 290, '86.05', 10, '3.35', 22, '7.38', 8, '2.68', 28, '9.39', 212, '71.14', 10, '3.35', 0, '0.00', 20, 30, 337, 11, 575, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 41, '7.14', 586, '95.12', 10, '1.62', 20, '3.24', 0, '0.00', 0, '0.00', 0, '0.00', 246, '39.93', 367, '59.57', 1, '0.16', 0, '0.00', 2, '3.27', 151, '3.40', 57, '9.25', 2, '0.16', 69, '11.20', 251, '40.74', 148, '24.02', 184, '29.89', 23, '3.73', 10, '1.62', 0, '0.00', 529, '0.00', 1, '0.16', 0, '0.00', 0, '0.00', 0, '0.00', 1, 15, 60),
(2, 2, 'Amoros', 2025, 'New Report: Barangay Amoros – February 10', 5534, 2795, 2739, 1335, 1532, 568, 767, 135, 60, 321, 450, 358, '63.60', 4, '0.80', 9, '1.80', 485, '96.42', 0, '0.00', 0, '0.00', 16, '3.18', 1, '0.20', 6, '1.19', 5, '0.99', 46, 32, 146, 271, 203, 45, 7, 5, 0, 1, 0, 82, 527, 553, '90.80', 21, '3.80', 24, '4.30', 30, '5.40', 104, '18.80', 488, '88.20', 16, '2.80', 4, '0.72', 33, 21, 669, 33, 1175, '88.00', 0, '0.00', 0, '0.00', 97, '7.20', 63, '4.70', 658, '49.20', 241, '18.00', 261, '19.50', 175, '13.10', 0, '0.00', 0, '0.00', 264, '22.00', 1071, '80.00', 2, '5.60', 0, '0.00', 0, '0.00', 143, '10.70', 576, '43.10', 0, '0.00', 616, '46.10', 429, '32.10', 207, '15.50', 584, '25.40', 155, '11.60', 0, '0.00', 0, '0.00', 265, '19.80', 5, '0.37', 135, '1.48', 135, '0.70', 2, '1.48', 1, 24, 185),
(3, 3, 'Bolisong', 2025, 'New Report: Barangay Bolisong – February 10', 2556, 1300, 1256, 580, 597, 480, 100, 38, 28, 131, 219, 155, '70.78', 0, '0.00', 2, '0.99', 202, '99.51', 0, '0.00', 0, '0.00', 1, '0.49', 0, '0.00', 2, '0.99', 1, '0.49', 24, 8, 58, 121, 95, 0, 7, 2, 0, 1, 0, 30, 233, 263, '100.00', 6, '2.28', 16, '6.08', 6, '2.28', 19, '7.22', 215, '81.74', 1, '0.38', 0, '0.00', 6, 0, 263, 9, 568, '97.93', 0, '0.00', 0, '0.00', 7, '0.12', 5, '0.86', 0, '0.00', 50, '0.86', 489, '0.84', 1, '0.01', 0, '0.00', 3, '0.05', 0, '0.00', 577, '99.48', 0, '0.00', 0, '0.00', 0, '0.00', 347, '59.82', 230, '39.65', 3, '0.05', 0, '0.00', 150, '25.86', 95, '16.37', 220, '37.93', 100, '17.24', 15, '0.25', 0, '0.00', 100, '18.51', 0, '0.00', 2, '0.37', 0, '0.00', 0, '0.00', 1, 13, 49),
(4, 4, 'Kalabaylabay', 2026, 'New Report: Barangay Kalabaylabay – February 10', 2508, 1308, 1200, 648, 753, 125, 523, 20, 25, 213, 219, 0, '88.30', 1, '0.38', 1, '0.38', 265, '97.79', 0, '0.00', 0, '0.00', 1, '0.37', 5, '1.85', 2, '0.74', 1, '0.37', 32, 23, 97, 156, 117, 25, 3, 2, 0, 1, 0, 58, 309, 367, '100.00', 31, '8.44', 41, '11.17', 46, '12.53', 17, '4.53', 241, '65.66', 26, '7.08', 2, '0.54', 22, 72, 404, 20, 533, '82.25', 0, '0.00', 0, '0.00', 88, '13.58', 27, '7.35', 450, '69.44', 30, '4.62', 168, '25.92', 0, '0.00', 0, '0.00', 0, '0.00', 63, '9.72', 577, '89.04', 0, '0.00', 8, '1.23', 0, '0.00', 113, '17.43', 190, '29.32', 6, '0.92', 130, '20.06', 206, '31.79', 191, '29.47', 251, '38.73', 0, '0.00', 0, '0.00', 0, '0.00', 520, '80.24', 2, '0.30', 10, '1.54', 0, '0.00', 0, '0.00', 1, 0, 77),
(5, 5, 'Hinigdaan', 2025, 'New Report: Barangay Hinigdaan – February 10', 2309, 1183, 1126, 593, 620, 473, 120, 25, 36, 203, 206, 0, '87.65', 0, '0.00', 5, '2.16', 230, '98.71', 0, '0.00', 1, '0.43', 1, '0.43', 1, '0.43', 1, '0.43', 5, '2.15', 20, 19, 67, 167, 139, 23, 5, 3, 0, 1, 0, 42, 280, 328, '100.00', 2, '0.60', 21, '6.40', 13, '3.96', 80, '24.39', 211, '64.32', 1, '0.30', 0, '0.00', 15, 2, 122, 0, 563, '94.94', 0, '0.00', 0, '0.00', 30, '5.05', 0, '0.00', 0, '0.00', 90, '15.17', 503, '84.82', 0, '0.00', 0, '0.00', 4, '0.67', 190, '32.04', 355, '59.86', 0, '0.00', 44, '7.41', 0, '0.00', 372, '62.73', 190, '32.04', 1, '0.16', 30, '5.05', 155, '26.13', 90, '15.17', 348, '58.68', 0, '0.00', 0, '0.00', 0, '0.00', 580, '97.80', 0, '0.00', 57, '9.61', 2, '0.33', 0, '0.00', 1, 11, 183),
(6, 6, 'Calongonan', 2025, 'New Report: Barangay Calongonan – February 11', 1698, 924, 774, 463, 480, 409, 54, 15, 9, 120, 123, 170, '72.35', 0, '0.00', 7, '3.66', 194, '99.49', 0, '0.00', 0, '0.00', 1, '0.51', 0, '0.00', 5, '2.56', 14, '7.18', 14, 5, 46, 104, 77, 35, 24, 4, 0, 1, 0, 37, 172, 209, '100.00', 0, '0.00', 0, '0.00', 5, '2.56', 20, '4.31', 112, '24.19', 1, '0.21', 0, '0.00', 14, 0, 227, 7, 415, '89.63', 0, '0.00', 5, '1.07', 0, '0.00', 43, '9.28', 24, '5.18', 0, '0.00', 439, '94.81', 0, '0.00', 0, '0.00', 63, '13.60', 0, '0.00', 400, '86.39', 0, '0.00', 0, '0.00', 0, '0.00', 458, '98.92', 0, '0.00', 5, '2.56', 0, '0.00', 21, '4.53', 63, '13.60', 247, '53.34', 132, '28.50', 0, '0.00', 0, '0.00', 50, '10.79', 1, '0.21', 25, '5.39', 0, '0.00', 0, '0.00', 1, 13, 155),
(7, 7, 'Kibonbon', 2025, 'New Report: Barangay Kibonbon – February 11', 2364, 1219, 1145, 653, 673, 490, 163, 15, 25, 131, 146, 192, '106.77', 2, '0.99', 2, '0.99', 198, '96.59', 0, '0.00', 3, '146.00', 4, '1.95', 0, '0.00', 1, '0.49', 5, '2.44', 12, 8, 44, 123, 121, 10, 1, 1, 0, 1, 0, 43, 226, 226, '100.00', 0, '0.00', 10, '0.04', 0, '0.00', 15, '0.06', 199, '88.05', 0, '0.00', 2, '0.08', 13, 0, 226, 4, 625, '95.70', 0, '0.00', 0, '0.00', 0, '0.00', 28, '0.04', 309, '47.32', 0, '0.00', 344, '52.67', 0, '0.00', 0, '0.00', 0, '0.00', 22, '0.33', 631, '96.60', 0, '0.00', 0, '0.00', 0, '0.00', 438, '67.70', 215, '32.90', 0, '0.00', 0, '0.00', 468, '71.66', 140, '21.43', 45, '68.90', 0, '0.00', 0, '0.00', 0, '0.00', 480, '73.50', 0, '0.00', 1, '1.50', 1, '1.50', 1, '1.50', 2, 18, 70),
(8, 8, 'Cogon', 2025, 'New Report: Barangay Cogon – February 11', 4345, 2233, 2112, 1000, 2111, 1036, 1075, 25, 37, 355, 364, 375, '97.08', 4, '1.10', 6, '1.60', 342, '98.46', 1, '0.30', 0, '0.00', 2, '0.50', 0, '0.00', 2, '1.20', 5, '1.40', 53, 31, 82, 280, 196, 0, 0, 2, 0, 1, 0, 72, 511, 511, '100.00', 4, '1.10', 6, '1.60', 1, '0.30', 5, '1.40', 2, '1.20', 0, '0.00', 0, '0.00', 35, 25, 392, 27, 940, '94.00', 0, '0.00', 0, '0.00', 55, '5.50', 5, '0.50', 506, '50.60', 11, '1.10', 483, '48.30', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 1000, '100.00', 0, '0.00', 0, '0.00', 0, '0.00', 151, '15.10', 189, '18.90', 2, '0.20', 0, '0.00', 516, '5.16', 359, '35.90', 112, '11.20', 13, '1.30', 0, '0.00', 0, '0.00', 251, '25.10', 10, '0.84', 11, '1.10', 2, '0.20', 2, '0.20', 1, 25, 169),
(9, 9, 'Bolobolo', 2025, 'New Report: Barangay Bolobolo – February 11', 5233, 2606, 2627, 1334, 1524, 350, 2277, 37, 20, 474, 474, 400, '85.65', 1, '0.25', 12, '3.03', 395, '97.29', 4, '0.99', 22, '5.42', 5, '1.23', 4, '0.99', 4, '0.99', 22, '5.42', 29, 30, 122, 297, 233, 34, 0, 2, 0, 1, 0, 52, 445, 497, '100.00', 6, '1.34', 39, '8.76', 1, '0.22', 33, '7.41', 321, '72.13', 32, '7.19', 13, '2.92', 22, 6, 445, 28, 1334, '100.00', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 833, '62.44', 0, '0.00', 501, '37.55', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 1332, '100.00', 2, '0.12', 0, '0.00', 0, '0.00', 515, '38.60', 654, '49.04', 1, '0.07', 0, '0.00', 773, '57.94', 490, '36.73', 71, '5.32', 0, '0.00', 0, '0.00', 0, '0.00', 784, '58.77', 15, '1.12', 8, '0.59', 2, '0.14', 1, '0.07', 1, 18, 94),
(10, 10, 'Ulaliman', 2025, 'New Report: Barangay Ulaliman – February 12', 2183, 1116, 1067, 595, 611, 525, 70, 32, 25, 134, 149, 146, '95.39', 1, '0.49', 12, '5.91', 200, '96.62', 0, '0.00', 2, '0.97', 2, '0.97', 3, '1.45', 3, '1.45', 18, '8.70', 14, 15, 48, 117, 98, 2, 24, 1, 0, 1, 0, 28, 270, 299, '100.34', 5, '1.67', 32, '10.70', 9, '3.01', 48, '16.05', 237, '79.26', 21, '7.02', 4, '1.34', 23, 5, 299, 15, 472, '79.32', 0, '0.00', 0, '0.00', 10, '1.68', 23, '3.86', 81, '13.61', 0, '0.00', 514, '86.38', 0, '0.00', 595, '100.00', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 172, '28.90', 80, '13.44', 0, '0.00', 123, '20.67', 101, '33.44', 31, '5.21', 463, '77.81', 0, '0.00', 0, '0.00', 0, '0.00', 595, '100.00', 1, '0.49', 50, '8.40', 0, '0.00', 0, '0.00', 1, 13, 0),
(11, 11, 'Sinaloc', 2025, 'New Report: Barangay Sinaloc – February 12', 6014, 3065, 2949, 1580, 1758, 1142, 438, 35, 19, 580, 510, 580, '89.80', 3, '0.58', 5, '0.96', 513, '98.46', 0, '0.00', 0, '0.00', 5, '0.96', 0, '0.00', 3, '0.58', 5, '0.96', 56, 42, 215, 412, 295, 0, 6, 2, 0, 1, 0, 68, 416, 416, '100.00', 3, '0.60', 9, '1.80', 7, '1.40', 52, '10.70', 403, '83.20', 26, '5.30', 9, '1.00', 56, 3, 567, 115, 1570, '99.36', 0, '0.00', 0, '0.00', 10, '0.63', 0, '0.00', 820, '51.89', 655, '41.45', 105, '6.64', 0, '0.00', 0, '0.00', 0, '0.00', 655, '41.45', 923, '58.41', 2, '0.12', 0, '0.00', 0, '0.00', 425, '26.89', 545, '34.49', 0, '0.00', 610, '38.60', 635, '40.18', 820, '51.89', 120, '7.56', 5, '0.31', 0, '0.00', 0, '0.00', 45, '2.48', 12, '0.75', 122, '7.72', 0, '0.00', 0, '0.00', 1, 22, 185),
(12, 12, 'Taytay', 2025, 'New Report: Barangay Taytay – February 12', 4529, 2134, 2395, 1036, 1052, 586, 450, 60, 86, 458, 500, 458, '95.40', 1, '0.21', 10, '2.12', 464, '97.27', 0, '0.00', 3, '0.63', 9, '1.89', 1, '0.21', 1, '0.21', 22, '4.61', 36, 30, 136, 252, 187, 3, 28, 2, 0, 1, 0, 67, 587, 609, '100.00', 3, '0.49', 11, '1.81', 4, '0.66', 9, '1.48', 519, '85.22', 64, '10.51', 11, '1.81', 54, 3, 598, 65, 778, '75.00', 0, '0.00', 0, '0.00', 187, '18.00', 71, '6.80', 180, '17.40', 0, '0.00', 856, '82.60', 0, '0.00', 0, '0.00', 0, '0.00', 183, '17.70', 851, '82.10', 2, '0.10', 0, '0.00', 0, '0.00', 532, '51.00', 95, '9.10', 0, '0.00', 0, '0.00', 512, '49.40', 293, '28.20', 220, '21.20', 10, '0.90', 1, '0.09', 0, '0.00', 354, '34.10', 12, '1.15', 46, '4.44', 46, '4.44', 2, '0.19', 1, 12, 190),
(13, 13, 'Molugan', 2025, 'New Report: Barangay Molugan – February 12', 13499, 6799, 6700, 3050, 4820, 0, 0, 90, 110, 929, 1544, 929, '84.30', 3, '0.23', 33, '2.58', 1286, '98.77', 0, '0.00', 0, '0.00', 7, '0.54', 6, '0.46', 16, '1.23', 46, '3.53', 135, 94, 409, 700, 520, 0, 93, 4, 0, 2, 0, 123, 1080, 1203, '100.00', 45, '3.38', 122, '9.17', 16, '1.20', 160, '12.03', 1141, '79.00', 58, '4.44', 19, '1.43', 135, 16, 1080, 69, 3050, '99.36', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 2850, '89.00', 0, '0.00', 200, '6.60', 0, '0.00', 3047, '99.90', 0, '0.00', 0, '0.00', 0, '0.00', 3, '0.23', 0, '0.00', 0, '0.00', 800, '76.22', 80, '2.62', 0, '0.00', 2170, '71.14', 1050, '34.42', 1000, '34.78', 950, '31.14', 50, '1.63', 0, '0.00', 0, '0.00', 2650, '86.88', 6, '0.19', 156, '5.11', 156, '5.11', 5, '0.16', 2, 45, 497),
(14, 14, 'Taytay', 2025, 'New Report: Barangay Taytay – February 12', 4529, 2134, 2395, 1036, 1052, 586, 450, 60, 86, 458, 0, 458, '95.40', 1, '0.21', 10, '2.12', 464, '97.27', 0, '0.00', 3, '0.63', 9, '1.89', 1, '0.21', 1, '0.21', 22, '4.61', 36, 30, 136, 252, 187, 3, 28, 2, 0, 1, 0, 67, 587, 609, '100.00', 3, '0.49', 11, '1.81', 4, '0.66', 9, '1.48', 519, '85.22', 64, '10.51', 11, '1.81', 54, 3, 598, 65, 778, '75.00', 0, '0.00', 0, '0.00', 187, '18.00', 71, '6.80', 180, '17.40', 0, '0.00', 856, '82.60', 0, '0.00', 0, '0.00', 0, '0.00', 183, '17.70', 851, '82.10', 2, '0.10', 0, '0.00', 0, '0.00', 532, '51.00', 95, '9.10', 0, '0.00', 0, '0.00', 512, '49.40', 293, '28.20', 220, '21.20', 10, '0.90', 1, '0.09', 0, '0.00', 354, '34.10', 12, '1.15', 46, '4.44', 46, '4.44', 2, '0.19', 1, 12, 190),
(15, 15, 'Taytay', 2025, 'New Report: Barangay Taytay – February 12', 4529, 2134, 2395, 1036, 1052, 586, 450, 60, 86, 458, 0, 458, '95.40', 1, '0.21', 10, '2.12', 464, '97.27', 0, '0.00', 3, '0.63', 9, '1.89', 1, '0.21', 1, '0.21', 22, '4.61', 36, 30, 136, 252, 187, 3, 28, 2, 0, 1, 0, 67, 587, 609, '100.00', 3, '0.49', 11, '1.81', 4, '0.66', 9, '1.48', 519, '85.22', 64, '10.51', 11, '1.81', 54, 3, 598, 65, 778, '75.00', 0, '0.00', 0, '0.00', 187, '18.00', 71, '6.80', 180, '17.40', 0, '0.00', 856, '82.60', 0, '0.00', 0, '0.00', 0, '0.00', 183, '17.70', 851, '82.10', 2, '0.10', 0, '0.00', 0, '0.00', 532, '51.00', 95, '9.10', 0, '0.00', 0, '0.00', 512, '49.40', 293, '28.20', 220, '21.20', 10, '0.90', 1, '0.09', 0, '0.00', 354, '34.10', 12, '1.15', 46, '4.44', 46, '4.44', 2, '0.19', 1, 12, 190),
(16, 16, 'Sinaloc', 2025, 'New Report: Barangay Sinaloc – February 12', 6014, 3065, 2949, 1580, 1758, 1142, 438, 35, 19, 468, 580, 521, '89.80', 3, '0.58', 5, '0.96', 513, '98.46', 0, '0.00', 0, '0.00', 5, '0.96', 0, '0.00', 3, '0.58', 5, '0.96', 56, 42, 215, 412, 295, 0, 6, 2, 0, 1, 0, 68, 416, 416, '100.00', 3, '0.60', 9, '1.80', 7, '1.40', 52, '10.70', 403, '83.20', 26, '5.30', 9, '1.00', 56, 3, 567, 115, 1570, '99.36', 0, '0.00', 0, '0.00', 10, '0.63', 0, '0.00', 820, '51.89', 655, '41.45', 105, '6.64', 0, '0.00', 0, '0.00', 0, '0.00', 655, '41.45', 923, '58.41', 2, '0.12', 0, '0.00', 0, '0.00', 425, '26.89', 545, '34.49', 0, '0.00', 610, '38.60', 635, '40.18', 820, '51.89', 120, '7.56', 5, '0.31', 0, '0.00', 0, '0.00', 45, '2.48', 12, '0.75', 122, '7.72', 0, '0.00', 0, '0.00', 1, 22, 185),
(17, 17, 'Amoros', 2025, 'New Report: Barangay Amoros – February 10', 5534, 2795, 2739, 1335, 1532, 568, 767, 135, 60, 464, 563, 503, '89.30', 4, '0.80', 9, '1.80', 485, '96.42', 0, '0.00', 0, '0.00', 16, '3.18', 1, '0.20', 6, '1.19', 5, '0.99', 46, 32, 146, 271, 203, 45, 7, 5, 0, 1, 0, 82, 527, 553, '90.80', 21, '3.80', 24, '4.30', 30, '5.40', 104, '18.80', 488, '88.20', 16, '2.80', 4, '0.72', 33, 21, 669, 33, 1175, '88.00', 0, '0.00', 0, '0.00', 97, '7.20', 63, '4.70', 658, '49.20', 241, '18.00', 261, '19.50', 175, '13.10', 0, '0.00', 0, '0.00', 264, '22.00', 1071, '80.00', 2, '5.60', 0, '0.00', 0, '0.00', 143, '10.70', 576, '43.10', 0, '0.00', 616, '46.10', 429, '32.10', 207, '15.50', 584, '25.40', 155, '11.60', 0, '0.00', 0, '0.00', 265, '19.80', 5, '0.37', 135, '1.48', 135, '0.70', 2, '1.48', 1, 24, 185),
(18, 18, 'Bolisong', 2025, 'New Report: Barangay Bolisong – February 10', 2556, 1300, 1256, 580, 597, 480, 100, 38, 28, 179, 219, 203, '92.70', 0, '0.00', 2, '0.99', 202, '99.51', 0, '0.00', 0, '0.00', 1, '0.49', 0, '0.00', 2, '0.99', 1, '0.49', 24, 8, 58, 121, 95, 0, 7, 2, 0, 1, 0, 30, 233, 263, '100.00', 6, '2.28', 16, '6.08', 6, '2.28', 19, '7.22', 215, '81.74', 1, '0.38', 0, '0.00', 6, 0, 263, 9, 568, '97.93', 0, '0.00', 0, '0.00', 7, '0.12', 5, '0.86', 0, '0.00', 50, '0.86', 489, '0.84', 1, '0.01', 0, '0.00', 3, '0.05', 0, '0.00', 577, '99.48', 0, '0.00', 0, '0.00', 0, '0.00', 347, '59.82', 230, '39.65', 3, '0.05', 0, '0.00', 150, '25.86', 95, '16.37', 220, '37.93', 100, '17.24', 15, '0.25', 0, '0.00', 100, '18.51', 0, '0.00', 2, '0.37', 0, '0.00', 0, '0.00', 1, 13, 49),
(19, 19, 'Bolobolo', 2025, 'New Report: Barangay Bolobolo – February 11', 5233, 2606, 2627, 1334, 1524, 350, 2277, 37, 20, 363, 474, 406, '85.70', 1, '0.25', 12, '3.03', 395, '97.29', 4, '0.99', 22, '5.42', 5, '1.23', 4, '0.99', 4, '0.99', 22, '5.42', 29, 30, 122, 297, 233, 34, 0, 2, 0, 1, 0, 52, 445, 497, '100.00', 6, '1.34', 39, '8.76', 1, '0.22', 33, '7.41', 321, '72.13', 32, '7.19', 13, '2.92', 22, 6, 445, 28, 1334, '100.00', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 833, '62.44', 0, '0.00', 501, '37.55', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 1332, '100.00', 2, '0.12', 0, '0.00', 0, '0.00', 515, '38.60', 654, '49.04', 1, '0.07', 0, '0.00', 773, '57.94', 490, '36.73', 71, '5.32', 0, '0.00', 0, '0.00', 0, '0.00', 784, '58.77', 15, '1.12', 8, '0.59', 2, '0.14', 1, '0.07', 1, 18, 94),
(20, 20, 'Calongonan', 2025, 'New Report: Barangay Calongonan – February 11', 1698, 924, 774, 463, 480, 409, 54, 15, 9, 186, 170, 195, '114.70', 0, '0.00', 7, '3.66', 194, '99.49', 0, '0.00', 0, '0.00', 1, '0.51', 0, '0.00', 5, '2.56', 14, '7.18', 14, 5, 46, 104, 77, 35, 24, 4, 0, 1, 0, 37, 172, 209, '100.00', 0, '0.00', 0, '0.00', 5, '2.56', 20, '4.31', 112, '24.19', 1, '0.21', 0, '0.00', 14, 0, 227, 7, 415, '89.63', 0, '0.00', 5, '1.07', 0, '0.00', 43, '9.28', 24, '5.18', 0, '0.00', 439, '94.81', 0, '0.00', 0, '0.00', 63, '13.60', 0, '0.00', 400, '86.39', 0, '0.00', 0, '0.00', 0, '0.00', 458, '98.92', 0, '0.00', 5, '2.56', 0, '0.00', 21, '4.53', 63, '13.60', 247, '53.34', 132, '28.50', 0, '0.00', 0, '0.00', 50, '10.79', 1, '0.21', 25, '5.39', 0, '0.00', 0, '0.00', 1, 13, 155),
(21, 21, 'Cogon', 2025, 'New Report: Barangay Cogon – February 11', 4345, 2233, 2112, 1000, 2111, 1036, 1075, 25, 37, 350, 411, 399, '97.10', 4, '1.10', 6, '1.60', 342, '98.46', 1, '0.30', 0, '0.00', 2, '0.50', 0, '0.00', 2, '1.20', 5, '1.40', 53, 31, 82, 280, 196, 0, 0, 2, 0, 1, 0, 72, 511, 511, '114.09', 4, '1.10', 6, '1.60', 1, '0.30', 5, '1.40', 2, '1.20', 0, '0.00', 0, '0.00', 35, 25, 392, 27, 940, '94.00', 0, '0.00', 0, '0.00', 55, '5.50', 5, '0.50', 506, '50.60', 11, '1.10', 483, '48.30', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 1000, '100.00', 0, '0.00', 0, '0.00', 0, '0.00', 151, '15.10', 189, '18.90', 2, '0.20', 0, '0.00', 516, '5.16', 359, '35.90', 112, '11.20', 13, '1.30', 0, '0.00', 0, '0.00', 251, '25.10', 10, '0.84', 11, '1.10', 2, '0.20', 2, '0.20', 1, 25, 169),
(22, 22, 'Himaya', 2025, 'New Report: Barangay Himaya – February 10', 2569, 1394, 1175, 616, 621, 429, 149, 43, 27, 207, 229, 224, '97.80', 1, '0.45', 6, '2.69', 217, '96.88', 0, '0.00', 3, '1.34', 3, '1.34', 1, '0.45', 1, '0.45', 7, '3.13', 19, 29, 74, 152, 108, 30, 5, 1, 0, 1, 0, 39, 298, 290, '86.05', 10, '3.35', 22, '7.38', 8, '2.68', 28, '9.39', 212, '71.14', 10, '3.35', 0, '0.00', 20, 30, 337, 11, 575, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 41, '7.14', 586, '95.12', 10, '1.62', 20, '3.24', 0, '0.00', 0, '0.00', 0, '0.00', 246, '39.93', 367, '59.57', 1, '0.16', 0, '0.00', 2, '3.27', 151, '3.40', 57, '9.25', 2, '0.16', 69, '11.20', 251, '40.74', 148, '24.02', 184, '29.89', 23, '3.73', 10, '1.62', 0, '0.00', 529, '0.00', 1, '0.16', 0, '0.00', 0, '0.00', 0, '0.00', 1, 15, 60),
(23, 23, 'Hinigdaan', 2025, 'New Report: Barangay Hinigdaan – February 10', 2309, 1183, 1126, 593, 620, 473, 120, 25, 36, 211, 235, 233, '99.10', 0, '0.00', 5, '2.16', 230, '98.71', 0, '0.00', 1, '0.43', 1, '0.43', 1, '0.43', 1, '0.43', 5, '2.15', 20, 19, 67, 167, 139, 23, 5, 3, 0, 1, 0, 42, 280, 328, '101.86', 2, '0.60', 21, '6.40', 13, '3.96', 80, '24.39', 211, '64.32', 1, '0.30', 0, '0.00', 15, 2, 122, 0, 563, '94.94', 0, '0.00', 0, '0.00', 30, '5.05', 0, '0.00', 0, '0.00', 90, '15.17', 503, '84.82', 0, '0.00', 0, '0.00', 4, '0.67', 190, '32.04', 355, '59.86', 0, '0.00', 44, '7.41', 0, '0.00', 372, '62.73', 190, '32.04', 1, '0.16', 30, '5.05', 155, '26.13', 90, '15.17', 348, '58.68', 0, '0.00', 0, '0.00', 0, '0.00', 580, '97.80', 0, '0.00', 57, '9.61', 2, '0.33', 0, '0.00', 1, 11, 183),
(24, 24, 'Kalabaylabay', 2026, 'New Report: Barangay Kalabaylabay – February 10', 2508, 1308, 1200, 648, 753, 125, 523, 20, 25, 246, 248, 271, '109.30', 1, '0.38', 1, '0.38', 265, '97.79', 0, '0.00', 0, '0.00', 1, '0.37', 5, '1.85', 2, '0.74', 1, '0.37', 32, 23, 97, 156, 117, 25, 3, 2, 0, 1, 0, 58, 309, 367, '100.00', 31, '8.44', 41, '11.17', 46, '12.53', 17, '4.53', 241, '65.66', 26, '7.08', 2, '0.54', 22, 72, 404, 20, 533, '82.25', 0, '0.00', 0, '0.00', 88, '13.58', 27, '7.35', 450, '69.44', 30, '4.62', 168, '25.92', 0, '0.00', 0, '0.00', 0, '0.00', 63, '9.72', 577, '89.04', 0, '0.00', 8, '1.23', 0, '0.00', 113, '17.43', 190, '29.32', 6, '0.92', 130, '20.06', 206, '31.79', 191, '29.47', 251, '38.73', 0, '0.00', 0, '0.00', 0, '0.00', 520, '80.24', 2, '0.30', 10, '1.54', 0, '0.00', 0, '0.00', 1, 0, 77),
(25, 25, 'Kibonbon', 2025, 'New Report: Barangay Kibonbon – February 11', 2364, 1219, 1145, 653, 673, 490, 163, 15, 25, 195, 192, 205, '106.80', 2, '0.99', 2, '0.99', 198, '96.59', 0, '0.00', 3, '146.00', 4, '1.95', 0, '0.00', 1, '0.49', 5, '2.44', 12, 8, 44, 123, 121, 10, 1, 1, 0, 1, 0, 43, 226, 226, '84.01', 0, '0.00', 10, '0.04', 0, '0.00', 15, '0.06', 199, '88.05', 0, '0.00', 2, '0.08', 13, 0, 226, 4, 625, '95.70', 0, '0.00', 0, '0.00', 0, '0.00', 28, '0.04', 309, '47.32', 0, '0.00', 344, '52.67', 0, '0.00', 0, '0.00', 0, '0.00', 22, '0.33', 631, '96.60', 0, '0.00', 0, '0.00', 0, '0.00', 438, '67.70', 215, '32.90', 0, '0.00', 0, '0.00', 468, '71.66', 140, '21.43', 45, '68.90', 0, '0.00', 0, '0.00', 0, '0.00', 480, '73.50', 0, '0.00', 1, '1.50', 1, '1.50', 1, '1.50', 2, 18, 70),
(26, 26, 'Molugan', 2025, 'New Report: Barangay Molugan – February 12', 13499, 6799, 6700, 3050, 4820, 1178, 1872, 90, 110, 1222, 1544, 1302, '84.30', 3, '0.23', 33, '2.58', 1286, '98.77', 0, '0.00', 0, '0.00', 7, '0.54', 6, '0.46', 16, '1.23', 46, '3.53', 135, 94, 409, 700, 520, 0, 93, 4, 0, 2, 0, 123, 1080, 1203, '104.33', 45, '3.38', 122, '9.17', 16, '1.20', 160, '12.03', 1141, '79.00', 58, '4.44', 19, '1.43', 135, 16, 1080, 69, 3050, '99.36', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 2850, '89.00', 0, '0.00', 200, '6.60', 0, '0.00', 3047, '99.90', 0, '0.00', 0, '0.00', 0, '0.00', 3, '0.23', 0, '0.00', 0, '0.00', 800, '76.22', 80, '2.62', 0, '0.00', 2170, '71.14', 1050, '34.42', 1000, '34.78', 950, '31.14', 50, '1.63', 0, '0.00', 0, '0.00', 2650, '86.88', 6, '0.19', 156, '5.11', 156, '5.11', 5, '0.16', 2, 45, 497),
(27, 27, 'Poblacion', 2025, 'New Report: Barangay Poblacion – February 12', 8624, 4092, 4532, 2046, 2145, 461, 1585, 112, 48, 699, 872, 762, '87.40', 6, '0.80', 15, '1.99', 728, '95.54', 9, '1.18', 3, '0.39', 17, '2.23', 5, '0.66', 35, '4.59', 5, '0.66', 119, 75, 348, 568, 414, 12, 42, 3, 0, 1, 3, 171, 1332, 1503, '100.00', 14, '0.90', 52, '3.50', 237, '15.80', 33, '2.20', 1031, '68.60', 99, '6.60', 37, '2.50', 18, 14, 1503, 195, 1928, '94.20', 2, '0.10', 101, '5.40', 0, '0.00', 6, '0.30', 1582, '77.30', 3, '0.10', 461, '22.50', 0, '0.00', 1965, '96.00', 78, '3.80', 3, '0.10', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 72, '3.50', 56, '2.70', 0, '0.00', 1902, '93.00', 874, '42.70', 491, '24.00', 668, '32.60', 13, '0.60', 0, '0.00', 0, '0.00', 1317, '0.00', 26, '0.00', 107, '38.31', 22, '20.56', 5, '100.00', 2, 20, 109),
(28, 28, 'Sambulawan', 2025, 'New Report: Barangay Sambulawan – February 12', 2070, 956, 1114, 566, 611, 146, 420, 32, 32, 184, 221, 201, '91.00', 0, '0.00', 7, '3.50', 195, '97.10', 0, '0.00', 0, '0.00', 5, '2.49', 1, '0.50', 3, '1.49', 16, '7.96', 24, 25, 85, 152, 116, 0, 19, 3, 0, 1, 0, 27, 231, 258, '100.00', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 258, '100.00', 0, '0.00', 0, '0.00', 10, 0, 258, 20, 535, '94.50', 0, '0.00', 31, '5.50', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 566, '100.00', 0, '0.00', 525, '92.80', 41, '7.20', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 421, '74.40', 67, '11.80', 5, '0.90', 73, '12.90', 142, '25.10', 89, '15.70', 335, '59.20', 0, '0.00', 0, '0.00', 0, '0.00', 459, '0.00', 0, '0.00', 41, '100.00', 0, '0.00', 0, '0.00', 1, 18, 192),
(29, 29, 'Taytay', 2025, 'New Report: Barangay Taytay – February 12', 4529, 2134, 2395, 1036, 1052, 586, 450, 60, 86, 451, 500, 477, '95.40', 1, '0.21', 10, '2.12', 464, '97.27', 0, '0.00', 3, '0.63', 9, '1.89', 1, '0.21', 1, '0.21', 22, '4.61', 36, 30, 136, 252, 187, 3, 28, 2, 0, 1, 0, 67, 587, 609, '93.11', 3, '0.49', 11, '1.81', 4, '0.66', 9, '1.48', 519, '85.22', 64, '10.51', 11, '1.81', 54, 3, 598, 65, 778, '75.00', 0, '0.00', 0, '0.00', 187, '18.00', 71, '6.80', 180, '17.40', 0, '0.00', 856, '82.60', 0, '0.00', 0, '0.00', 0, '0.00', 183, '17.70', 851, '82.10', 2, '0.10', 0, '0.00', 0, '0.00', 532, '51.00', 95, '9.10', 0, '0.00', 0, '0.00', 512, '49.40', 293, '28.20', 220, '21.20', 10, '0.90', 1, '0.09', 0, '0.00', 354, '34.10', 12, '1.15', 46, '4.44', 46, '4.44', 2, '0.19', 1, 12, 190),
(30, 30, 'Ulaliman', 2025, 'New Report: Barangay Ulaliman – February 12', 2183, 1116, 1067, 595, 611, 525, 70, 32, 25, 189, 217, 207, '97.40', 1, '0.49', 12, '5.91', 200, '96.62', 0, '0.00', 2, '0.97', 2, '0.97', 3, '1.45', 3, '1.45', 18, '8.70', 14, 15, 48, 117, 98, 2, 24, 1, 0, 1, 0, 28, 270, 299, '100.34', 5, '1.67', 32, '10.70', 9, '3.01', 48, '16.05', 237, '79.26', 21, '7.02', 4, '1.34', 23, 5, 299, 15, 472, '79.32', 0, '0.00', 0, '0.00', 10, '1.68', 23, '3.86', 81, '13.61', 0, '0.00', 514, '86.38', 0, '0.00', 595, '100.00', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 0, '0.00', 172, '28.90', 80, '13.44', 0, '0.00', 123, '20.67', 101, '33.44', 31, '5.21', 463, '77.81', 0, '0.00', 0, '0.00', 0, '0.00', 595, '100.00', 1, '0.49', 50, '8.40', 0, '0.00', 0, '0.00', 1, 13, 0),
(31, 31, 'Kalabaylabay', 2025, 'New Report: Barangay Kalabaylabay – February 10', 2508, 1308, 1200, 648, 753, 125, 523, 20, 25, 246, 248, 271, '109.30', 1, '0.38', 1, '0.38', 265, '97.79', 0, '0.00', 0, '0.00', 1, '0.37', 5, '1.85', 2, '0.74', 1, '0.37', 32, 23, 97, 156, 117, 25, 3, 2, 0, 1, 0, 58, 309, 367, '100.00', 31, '8.44', 41, '11.17', 46, '12.53', 17, '4.53', 241, '65.66', 26, '7.08', 2, '0.54', 22, 72, 404, 20, 533, '82.25', 0, '0.00', 0, '0.00', 88, '13.58', 27, '7.35', 450, '69.44', 30, '4.62', 168, '25.92', 0, '0.00', 0, '0.00', 0, '0.00', 63, '9.72', 577, '89.04', 0, '0.00', 8, '1.23', 0, '0.00', 113, '17.43', 190, '29.32', 6, '0.92', 130, '20.06', 206, '31.79', 191, '29.47', 251, '38.73', 0, '0.00', 0, '0.00', 0, '0.00', 520, '80.24', 2, '0.30', 10, '1.54', 0, '0.00', 0, '0.00', 1, 0, 77),
(32, 32, 'Kalabaylabay', 2025, 'New Report: Barangay Kalabaylabay – February 10', 2508, 1308, 1200, 648, 753, 125, 523, 20, 25, 246, 248, 271, '109.30', 1, '0.38', 1, '0.38', 265, '97.79', 0, '0.00', 0, '0.00', 1, '0.37', 5, '1.85', 2, '0.74', 1, '0.37', 32, 23, 97, 156, 117, 25, 3, 2, 0, 1, 0, 58, 309, 367, '100.00', 31, '8.44', 41, '11.17', 46, '12.53', 17, '4.53', 241, '65.66', 26, '7.08', 2, '0.54', 22, 72, 404, 20, 533, '82.25', 0, '0.00', 0, '0.00', 88, '13.58', 27, '7.35', 450, '69.44', 30, '4.62', 168, '25.92', 0, '0.00', 0, '0.00', 0, '0.00', 63, '9.72', 577, '89.04', 0, '0.00', 8, '1.23', 0, '0.00', 113, '17.43', 190, '29.32', 6, '0.92', 130, '20.06', 206, '31.79', 191, '29.47', 251, '38.73', 0, '0.00', 0, '0.00', 0, '0.00', 520, '80.24', 2, '0.30', 10, '1.54', 0, '0.00', 0, '0.00', 1, 0, 77),
(33, 33, 'Taytay', 2025, 'New Report: Barangay Taytay – February 12', 4529, 2134, 2395, 1036, 1052, 586, 450, 60, 86, 451, 500, 477, '95.40', 1, '0.21', 10, '2.12', 464, '97.27', 0, '0.00', 3, '0.63', 9, '1.89', 1, '0.21', 1, '0.21', 22, '4.61', 36, 30, 136, 252, 187, 3, 28, 2, 0, 1, 0, 67, 587, 609, '93.11', 3, '0.49', 11, '1.81', 4, '0.66', 9, '1.48', 519, '85.22', 64, '10.51', 11, '1.81', 54, 3, 598, 65, 778, '75.00', 0, '0.00', 0, '0.00', 187, '18.00', 71, '6.80', 180, '17.40', 0, '0.00', 856, '82.60', 0, '0.00', 0, '0.00', 0, '0.00', 183, '17.70', 851, '82.10', 2, '0.10', 0, '0.00', 0, '0.00', 532, '51.00', 95, '9.10', 0, '0.00', 0, '0.00', 512, '49.40', 293, '28.20', 220, '21.20', 10, '0.90', 1, '0.09', 0, '0.00', 354, '34.10', 12, '1.15', 46, '4.44', 46, '4.44', 2, '0.19', 1, 12, 190),
(34, 34, 'Taytay', 2025, 'New Report: Barangay Taytay – February 12', 4529, 2134, 2395, 1036, 1052, 586, 450, 60, 86, 451, 500, 477, '95.40', 1, '0.21', 10, '2.12', 464, '97.27', 0, '0.00', 3, '0.63', 9, '1.89', 1, '0.21', 1, '0.21', 22, '4.61', 36, 30, 136, 252, 187, 3, 28, 2, 0, 1, 0, 67, 587, 609, '93.11', 3, '0.49', 11, '1.81', 4, '0.66', 9, '1.48', 519, '85.22', 64, '10.51', 11, '1.81', 54, 3, 598, 65, 778, '75.00', 0, '0.00', 0, '0.00', 187, '18.00', 71, '6.80', 180, '17.40', 0, '0.00', 856, '82.60', 0, '0.00', 0, '0.00', 0, '0.00', 183, '17.70', 851, '82.10', 2, '0.10', 0, '0.00', 0, '0.00', 532, '51.00', 95, '9.10', 0, '0.00', 0, '0.00', 512, '49.40', 293, '28.20', 220, '21.20', 10, '0.90', 1, '0.09', 0, '0.00', 354, '34.10', 12, '1.15', 46, '4.44', 46, '4.44', 2, '0.19', 1, 12, 190);

-- --------------------------------------------------------

--
-- Table structure for table `login_history`
--

CREATE TABLE `login_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `browser` varchar(100) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout_time` timestamp NULL DEFAULT NULL,
  `device_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_history`
--

INSERT INTO `login_history` (`id`, `user_id`, `session_id`, `browser`, `ip_address`, `login_time`, `logout_time`, `device_token`) VALUES
(1, 21, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 02:18:57', NULL, '2ebe0294fcc76a1b61431e22ca038f6b'),
(2, 27, '60aa3a8c8dc199645abb3e411c459459', 'Chrome on Windows', '143.44.192.116', '2026-02-10 06:23:43', NULL, 'e8dd763d4219ef701cfe31eb32ad7808'),
(3, 17, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 07:14:50', NULL, '2ebe0294fcc76a1b61431e22ca038f6b'),
(4, 19, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 02:11:47', NULL, '2ebe0294fcc76a1b61431e22ca038f6b'),
(5, 18, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 02:12:38', NULL, '2ebe0294fcc76a1b61431e22ca038f6b'),
(6, 30, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 02:15:23', NULL, '2ebe0294fcc76a1b61431e22ca038f6b'),
(7, 20, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 02:17:16', NULL, '2ebe0294fcc76a1b61431e22ca038f6b'),
(8, 23, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 03:18:11', NULL, '2ebe0294fcc76a1b61431e22ca038f6b'),
(9, 22, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 02:20:34', NULL, '2ebe0294fcc76a1b61431e22ca038f6b'),
(10, 16, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 02:23:02', NULL, '2ebe0294fcc76a1b61431e22ca038f6b'),
(11, 14, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 03:01:00', NULL, '2ebe0294fcc76a1b61431e22ca038f6b'),
(12, 31, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 01:46:41', NULL, '2ebe0294fcc76a1b61431e22ca038f6b'),
(13, 25, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 03:37:26', NULL, '2ebe0294fcc76a1b61431e22ca038f6b'),
(14, 24, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 02:24:26', NULL, '2ebe0294fcc76a1b61431e22ca038f6b'),
(15, 13, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 02:30:07', NULL, '2ebe0294fcc76a1b61431e22ca038f6b'),
(16, 12, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 02:47:17', NULL, '2ebe0294fcc76a1b61431e22ca038f6b'),
(17, 27, 'ad262718bc012ab03dc1b1c998645570', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Sa', '143.44.192.116', '2026-02-12 07:15:08', NULL, '2ebe0294fcc76a1b61431e22ca038f6b');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `message` varchar(255) NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `read_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `sender_id`, `message`, `date`, `read_status`) VALUES
(1, 8, 21, 'A new report has been submitted by Himaya.', '2026-02-09 22:38:09', 0),
(2, 27, 21, 'A new report has been submitted by Himaya.', '2026-02-09 22:38:09', 0),
(3, 29, 21, 'A new report has been submitted by Himaya.', '2026-02-09 22:38:09', 0),
(4, 8, 17, 'A new report has been submitted by Amoros.', '2026-02-09 23:22:22', 0),
(5, 27, 17, 'A new report has been submitted by Amoros.', '2026-02-09 23:22:22', 0),
(6, 29, 17, 'A new report has been submitted by Amoros.', '2026-02-09 23:22:22', 0),
(7, 8, 19, 'A new report has been submitted by Bolisong.', '2026-02-09 23:41:07', 0),
(8, 27, 19, 'A new report has been submitted by Bolisong.', '2026-02-09 23:41:07', 0),
(9, 29, 19, 'A new report has been submitted by Bolisong.', '2026-02-09 23:41:07', 0),
(10, 8, 23, 'A new report has been submitted by Kalabaylabay.', '2026-02-10 00:37:11', 0),
(11, 27, 23, 'A new report has been submitted by Kalabaylabay.', '2026-02-10 00:37:11', 0),
(12, 29, 23, 'A new report has been submitted by Kalabaylabay.', '2026-02-10 00:37:11', 0),
(13, 8, 22, 'A new report has been submitted by Hinigdaan.', '2026-02-10 00:52:24', 0),
(14, 27, 22, 'A new report has been submitted by Hinigdaan.', '2026-02-10 00:52:24', 0),
(15, 29, 22, 'A new report has been submitted by Hinigdaan.', '2026-02-10 00:52:24', 0),
(16, 8, 30, 'A new report has been submitted by Calongonan.', '2026-02-10 23:49:52', 0),
(17, 27, 30, 'A new report has been submitted by Calongonan.', '2026-02-10 23:49:52', 0),
(18, 29, 30, 'A new report has been submitted by Calongonan.', '2026-02-10 23:49:52', 0),
(19, 8, 16, 'A new report has been submitted by Kibonbon.', '2026-02-11 00:03:12', 0),
(20, 27, 16, 'A new report has been submitted by Kibonbon.', '2026-02-11 00:03:12', 0),
(21, 29, 16, 'A new report has been submitted by Kibonbon.', '2026-02-11 00:03:12', 0),
(22, 8, 20, 'A new report has been submitted by Cogon.', '2026-02-11 00:28:15', 0),
(23, 27, 20, 'A new report has been submitted by Cogon.', '2026-02-11 00:28:15', 0),
(24, 29, 20, 'A new report has been submitted by Cogon.', '2026-02-11 00:28:15', 0),
(25, 8, 18, 'A new report has been submitted by Bolobolo.', '2026-02-11 00:52:48', 0),
(26, 27, 18, 'A new report has been submitted by Bolobolo.', '2026-02-11 00:52:48', 0),
(27, 29, 18, 'A new report has been submitted by Bolobolo.', '2026-02-11 00:52:48', 0),
(28, 8, 14, 'A new report has been submitted by Ulaliman.', '2026-02-11 16:55:34', 0),
(29, 27, 14, 'A new report has been submitted by Ulaliman.', '2026-02-11 16:55:34', 0),
(30, 29, 14, 'A new report has been submitted by Ulaliman.', '2026-02-11 16:55:34', 0),
(31, 8, 31, 'A new report has been submitted by Sinaloc.', '2026-02-11 17:08:24', 0),
(32, 27, 31, 'A new report has been submitted by Sinaloc.', '2026-02-11 17:08:24', 0),
(33, 29, 31, 'A new report has been submitted by Sinaloc.', '2026-02-11 17:08:24', 0),
(34, 8, 25, 'A new report has been submitted by Taytay.', '2026-02-11 17:26:03', 0),
(35, 27, 25, 'A new report has been submitted by Taytay.', '2026-02-11 17:26:03', 0),
(36, 29, 25, 'A new report has been submitted by Taytay.', '2026-02-11 17:26:03', 0),
(37, 8, 24, 'A new report has been submitted by Molugan.', '2026-02-11 17:43:09', 0),
(38, 27, 24, 'A new report has been submitted by Molugan.', '2026-02-11 17:43:09', 0),
(39, 29, 24, 'A new report has been submitted by Molugan.', '2026-02-11 17:43:09', 0),
(40, 8, 13, 'A new report has been submitted by Poblacion.', '2026-02-11 18:45:58', 0),
(41, 27, 13, 'A new report has been submitted by Poblacion.', '2026-02-11 18:45:58', 0),
(42, 29, 13, 'A new report has been submitted by Poblacion.', '2026-02-11 18:45:58', 0),
(43, 8, 12, 'A new report has been submitted by Sambulawan.', '2026-02-11 18:59:12', 0),
(44, 27, 12, 'A new report has been submitted by Sambulawan.', '2026-02-11 18:59:12', 0),
(45, 29, 12, 'A new report has been submitted by Sambulawan.', '2026-02-11 18:59:12', 0),
(46, 14, NULL, 'Your report has been approved!', '2026-02-11 19:03:40', 0),
(47, 25, NULL, 'Your report has been approved!', '2026-02-11 19:03:44', 0),
(48, 12, NULL, 'Your report has been approved!', '2026-02-11 19:03:47', 0),
(49, 13, NULL, 'Your report has been approved!', '2026-02-11 19:03:48', 0),
(50, 24, NULL, 'Your report has been approved!', '2026-02-11 19:03:48', 0),
(51, 16, NULL, 'Your report has been approved!', '2026-02-11 19:03:49', 0),
(52, 23, NULL, 'Your report has been approved!', '2026-02-11 19:03:49', 0),
(53, 22, NULL, 'Your report has been approved!', '2026-02-11 19:03:50', 0),
(54, 21, NULL, 'Your report has been approved!', '2026-02-11 19:03:51', 0),
(55, 21, NULL, 'Your report has been approved!', '2026-02-11 19:03:51', 0),
(56, 20, NULL, 'Your report has been approved!', '2026-02-11 19:03:51', 0),
(57, 30, NULL, 'Your report has been approved!', '2026-02-11 19:03:52', 0),
(58, 18, NULL, 'Your report has been approved!', '2026-02-11 19:03:53', 0),
(59, 19, NULL, 'Your report has been approved!', '2026-02-11 19:03:54', 0),
(60, 17, NULL, 'Your report has been approved!', '2026-02-11 19:03:55', 0),
(61, 31, NULL, 'Your report has been approved!', '2026-02-11 19:03:57', 0),
(62, 23, NULL, 'Your report has been approved!', '2026-02-11 19:19:12', 0),
(63, 25, NULL, 'Your report has been approved!', '2026-02-11 19:33:44', 0),
(64, 25, NULL, 'Your report has been approved!', '2026-02-11 19:35:25', 0);

-- --------------------------------------------------------

--
-- Table structure for table `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `user_id`, `otp_code`, `created_at`, `expires_at`) VALUES
(1, 21, '199558', '2026-02-10 05:44:08', '2026-02-10 13:49:08'),
(2, 27, '512541', '2026-02-10 06:23:27', '2026-02-10 14:28:27'),
(3, 17, '356573', '2026-02-10 06:42:05', '2026-02-10 14:47:05'),
(4, 17, '151713', '2026-02-10 07:07:27', '2026-02-10 15:12:27'),
(5, 19, '629926', '2026-02-10 07:23:49', '2026-02-10 15:28:49'),
(6, 19, '811248', '2026-02-10 07:24:17', '2026-02-10 15:29:17'),
(7, 18, '445301', '2026-02-10 07:41:47', '2026-02-10 15:46:47'),
(8, 30, '435767', '2026-02-10 08:00:28', '2026-02-10 16:05:28'),
(9, 20, '403873', '2026-02-10 08:03:17', '2026-02-10 16:08:17'),
(10, 23, '549920', '2026-02-10 08:20:21', '2026-02-10 16:25:21'),
(11, 22, '217548', '2026-02-10 08:37:51', '2026-02-10 16:42:51'),
(12, 16, '509361', '2026-02-11 07:51:10', '2026-02-11 15:56:10'),
(13, 14, '399315', '2026-02-12 00:31:01', '2026-02-12 08:36:01'),
(14, 31, '584541', '2026-02-12 00:56:10', '2026-02-12 09:01:10'),
(15, 25, '450116', '2026-02-12 01:08:56', '2026-02-12 09:13:56'),
(16, 24, '929927', '2026-02-12 01:26:36', '2026-02-12 09:31:36'),
(17, 13, '239432', '2026-02-12 02:29:50', '2026-02-12 10:34:50'),
(18, 12, '523201', '2026-02-12 02:47:15', '2026-02-12 10:52:15'),
(19, 27, '549065', '2026-02-12 03:02:05', '2026-02-12 11:07:05');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `report_time` time NOT NULL,
  `report_date` date NOT NULL,
  `status` enum('Pending','Approved','Rejected','Archived') DEFAULT 'Pending',
  `prev_status` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_submitted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `report_time`, `report_date`, `status`, `prev_status`, `created_at`, `is_submitted`) VALUES
(1, 21, '14:38:09', '2026-02-10', 'Pending', NULL, '2026-02-09 22:38:09', 0),
(2, 17, '15:22:22', '2026-02-10', 'Pending', NULL, '2026-02-09 23:22:22', 0),
(3, 19, '15:41:07', '2026-02-10', 'Pending', NULL, '2026-02-09 23:41:07', 0),
(4, 23, '16:37:11', '2026-02-10', 'Pending', NULL, '2026-02-10 00:37:11', 0),
(5, 22, '16:52:24', '2026-02-10', 'Pending', NULL, '2026-02-10 00:52:24', 0),
(6, 30, '15:49:52', '2026-02-11', 'Pending', NULL, '2026-02-10 23:49:52', 0),
(7, 16, '16:03:12', '2026-02-11', 'Pending', NULL, '2026-02-11 00:03:12', 0),
(8, 20, '16:28:15', '2026-02-11', 'Pending', NULL, '2026-02-11 00:28:15', 0),
(9, 18, '16:52:48', '2026-02-11', 'Pending', NULL, '2026-02-11 00:52:48', 0),
(10, 14, '08:55:34', '2026-02-12', 'Pending', NULL, '2026-02-11 16:55:34', 0),
(11, 31, '09:08:24', '2026-02-12', 'Pending', NULL, '2026-02-11 17:08:24', 0),
(12, 25, '09:26:03', '2026-02-12', 'Pending', NULL, '2026-02-11 17:26:03', 0),
(13, 24, '09:43:09', '2026-02-12', 'Pending', NULL, '2026-02-11 17:43:09', 0),
(14, 25, '09:44:29', '2026-02-12', 'Pending', NULL, '2026-02-11 17:44:29', 0),
(15, 25, '09:46:39', '2026-02-12', 'Pending', NULL, '2026-02-11 17:46:39', 0),
(16, 31, '10:09:48', '2026-02-12', 'Approved', NULL, '2026-02-11 18:09:48', 1),
(17, 17, '10:11:24', '2026-02-12', 'Approved', NULL, '2026-02-11 18:11:24', 1),
(18, 19, '10:12:19', '2026-02-12', 'Approved', NULL, '2026-02-11 18:12:19', 1),
(19, 18, '10:15:06', '2026-02-12', 'Approved', NULL, '2026-02-11 18:15:06', 1),
(20, 30, '10:16:58', '2026-02-12', 'Approved', NULL, '2026-02-11 18:16:58', 1),
(21, 20, '10:18:28', '2026-02-12', 'Approved', NULL, '2026-02-11 18:18:28', 1),
(22, 21, '10:20:17', '2026-02-12', 'Approved', NULL, '2026-02-11 18:20:17', 1),
(23, 22, '10:21:48', '2026-02-12', 'Approved', NULL, '2026-02-11 18:21:48', 1),
(24, 23, '10:22:45', '2026-02-12', 'Approved', NULL, '2026-02-11 18:22:45', 1),
(25, 16, '10:24:05', '2026-02-12', 'Approved', NULL, '2026-02-11 18:24:05', 1),
(26, 24, '10:27:40', '2026-02-12', 'Approved', NULL, '2026-02-11 18:27:40', 1),
(27, 13, '10:45:58', '2026-02-12', 'Approved', NULL, '2026-02-11 18:45:58', 1),
(28, 12, '10:59:12', '2026-02-12', 'Approved', NULL, '2026-02-11 18:59:12', 1),
(29, 25, '11:00:28', '2026-02-12', 'Approved', NULL, '2026-02-11 19:00:28', 1),
(30, 14, '11:01:43', '2026-02-12', 'Approved', NULL, '2026-02-11 19:01:43', 1),
(31, 23, '11:14:07', '2026-02-12', 'Pending', NULL, '2026-02-11 19:14:08', 0),
(32, 23, '11:18:31', '2026-02-12', 'Approved', NULL, '2026-02-11 19:18:31', 1),
(33, 25, '11:32:59', '2026-02-12', 'Approved', NULL, '2026-02-11 19:32:59', 1),
(34, 25, '11:34:56', '2026-02-12', 'Approved', NULL, '2026-02-11 19:34:56', 1);

-- --------------------------------------------------------

--
-- Table structure for table `report_archives`
--

CREATE TABLE `report_archives` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('BNS','CNO') NOT NULL,
  `is_archived` tinyint(1) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0,
  `archived_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report_archives`
--

INSERT INTO `report_archives` (`id`, `report_id`, `user_id`, `user_type`, `is_archived`, `is_deleted`, `archived_at`, `deleted_at`) VALUES
(1, 24, 27, 'CNO', 1, 0, '2026-02-12 03:16:31', NULL),
(2, 31, 23, 'BNS', 1, 0, '2026-02-12 03:18:46', NULL),
(3, 4, 23, 'BNS', 1, 0, '2026-02-12 03:18:48', NULL),
(4, 15, 25, 'BNS', 1, 0, '2026-02-12 03:33:26', NULL),
(5, 14, 25, 'BNS', 1, 0, '2026-02-12 03:33:27', NULL),
(6, 12, 25, 'BNS', 1, 0, '2026-02-12 03:33:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `address` varchar(255) NOT NULL,
  `barangay` enum('CNO','Amoros','Bolisong','Cogon','Himaya','Hinigdaan','Kalabaylabay','Molugan','Bolobolo','Poblacion','Kibonbon','Sambulawan','Calongonan','Sinaloc','Taytay','Ulaliman') NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `user_type` enum('BNS','CNO') NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `current_session` varchar(128) DEFAULT NULL,
  `password_changed` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `phone_number`, `email`, `address`, `barangay`, `profile_pic`, `user_type`, `password_hash`, `created_at`, `current_session`, `password_changed`, `status`) VALUES
(8, 'CNO', 'ADMIN', 'CNO', '09264686830', 'louizkylaspona@gmail.com', 'Mangima Tankulan', 'CNO', '1758540105_CCS_Logo_2.png', 'CNO', '$2y$10$Xm/8kdPuxuROCeJfzlcU6.rrR0CizxiD3R7CCAy/lKlwIaZCGFJJq', '2025-09-21 13:06:17', '16a84454c150b76ac6aeb73fa7f6ff2e', 0, 'Active'),
(10, 'BNS', 'Brgy', 'bns', '09264686830', 'loki072002@gmail.com', 'Mangima Tankulan', 'Calongonan', '1758514208_2.png', 'BNS', '$2y$10$zO97F06lMGhmM.Cap0dhu.P4bn/7GyR4zMNMFnd3YS/zOK0WDjEoa', '2025-09-21 13:16:32', 'ee338fc63318bcced7d95d509c9d3908', 0, 'Inactive'),
(12, 'Brgy', 'Sambulawan', 'Sambulawan', '09xxxxxxxxx', 'sambulawan@gmail.com', 'El Salvador', 'Sambulawan', '1758540182_image_1.jpg', 'BNS', '$2y$10$XKrEFbJZtEkhhDpDq9vOsOQoQZ1H2ubMfaN2oEPSE8crsIW2OYoMW', '2025-09-21 13:18:02', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(13, 'Brgy', 'Poblacion', 'Poblcaion', '09xxxxxxxxx', 'poblacion@gmail.com', 'El Salvador', 'Poblacion', '1759897044_4.png', 'BNS', '$2y$10$PLmDMPp197hXc2ykxyA9XuaGKCCy47YQXDP4XgSDvNZ3K8yQYEw2q', '2025-10-06 11:12:40', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(14, 'Brgy', 'Ulaliman', 'Ulaliman', '09xxxxxxxxx', 'ulaliman@gmail.com', 'El Salvador', 'Ulaliman', '1759896854_image_1.jpg', 'BNS', '$2y$10$GeC6UNg6Iy9dW9q3ZO9S7.Fa/xksv4S9niLPpJzTYOYmpmmTxknKK', '2025-10-06 11:20:42', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(15, 'Chucks', 'Glee', 'Chucks07', '2121', 'cleezypanda1@gmail.com', 'Mangima Tankulan', 'Sinaloc', '1759896087_1000711103.png', 'BNS', '$2y$10$0s4QzVmNAF4qnhxDB6ak2OvTAcdYuzHwzZYKSucGUDIuLtL1sH4a.', '2025-10-06 11:27:13', 'a9e8c9e4d893aa7e8970751f21a0564a', 0, 'Inactive'),
(16, 'Floyd', 'Botandes', 'Floyd', '21', 'kibonbon@gmail.com', 'El Salvador', 'Kibonbon', NULL, 'BNS', '$2y$10$MOymOOv6PxvXD7Q9XzDAdObjGWP.V3JF/g.5tf7MN4XN5U.LrCTgW', '2025-10-08 04:19:37', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(17, 'Amor', 'Sat', 'Amoros', '212', 'amoros@gmail.com', 'El Salvador', 'Amoros', '1759897386_4.png', 'BNS', '$2y$10$Iw99Cp1j5gp7icPqGvxwI.uE6RLUFT42w5/vyFi9Sar5HHp7JD5AS', '2025-10-08 04:22:12', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(18, 'Bolo', 'Bolo', 'Bolobolo', '212121', 'bolobolo@gmail.com', 'El Salvador', 'Bolobolo', '1768189340_301734213_177980474758485_6598622755302204059_n.jpg', 'BNS', '$2y$10$3pFvY2Dve2EQYgV75HbMg.hM7enVbp2Bmc4PisuofwihvlhJuT2hK', '2025-10-08 04:24:40', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(19, 'Jus', 'Tine', 'Bolisong', '12123', 'bolisong@gmail.com', 'El Salvador', 'Bolisong', '1759897672_2.png', 'BNS', '$2y$10$wLeTAmws0javLtCeuQ2mwOg3xO/gGRkcxdBhBoWusPgl2SpYB/l2G', '2025-10-08 04:27:07', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(20, 'Hes', 'Des', 'Cogon', '12121', 'cogon@gmail.com', 'El Salvador', 'Cogon', '1759897859_2.png', 'BNS', '$2y$10$WoT8Z/FyQSQqUHIk0hlo2uLMPT6jMuqW4Pwg.tl8LWi1vHGkKEP2m', '2025-10-08 04:29:18', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(21, 'Barangay', 'Himaya', 'Himaya', '1231', 'himaya@gmail.com', 'El Salvador', 'Himaya', '1768189739_445400397_122097675662350214_5170896279758019837_n.jpg', 'BNS', '$2y$10$QPJMf0YoLJERsLpqpMjETOK9IdMf61Uen8441xq1QZlJciFkEifN6', '2025-10-08 04:32:15', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(22, 'Brgy', 'Hinigdaan', 'Hinigdaan', '09539124087', 'hinigdaan@gmail.com', 'El Salvador', 'Hinigdaan', '1768185526_LOGO- BRGY.jpg', 'BNS', '$2y$10$o9ITZy8WYSgmynOFjbhgy.q.txa4.BUYdBbI..rpN0Z8JBzekkAXC', '2025-10-08 04:34:23', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(23, 'Brgy', 'Kalabaylabay', 'Kalabaylabay', '21', 'kalabaylabay@gmail.com', 'El Salvador', 'Kalabaylabay', '1759898363_image_1.jpg', 'BNS', '$2y$10$9Lj3RL5o260AXLv8OSgxPeMVtYGTHPvFt/Sx2BRMROgIGIPxGXkMS', '2025-10-08 04:37:51', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(24, 'Brgy', 'Molugan', 'Molugan', '21', 'molugan@gmail.com', 'El Salvador', 'Molugan', '1759898482_2.png', 'BNS', '$2y$10$9bIufpiowtibsueJewYntu5csGk.3qMKaehJhMjdvK/L2YVVGI2tK', '2025-10-08 04:40:33', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(25, 'Brgy', 'Taytay', 'Taytay', '22', 'taytay@gmail.com', 'El Salvador', 'Taytay', '1759898598_4.png', 'BNS', '$2y$10$1vQVabql7YkR6pLlOiFggOIlI0tgqovflXTlc54qsoN74hu9iCq3m', '2025-10-08 04:42:34', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(26, 'Antonio', 'Parane', 'Antonio', '09xxxxxx', 'anthon2712@gmail.com', 'Tankulan', 'Calongonan', '1763487887_Logo.png', 'BNS', '$2y$10$.SS4E2dgWwhGGT.Ing6ls.lYN12IUHAibgqfTudRObDnQsqjZF8q6', '2025-10-15 07:24:32', '44a706b018929caf9f92fadec11024b6', 0, 'Inactive'),
(27, 'Karen Jay', 'Langala', 'Admin', '099999999', 'citynutritionoffice@elsalvadorcity.gov.ph', 'El Salvador', 'CNO', NULL, 'CNO', '$2y$10$fNr76vClgbI0eti/iDgdOO2XCgmC9xMvt95yisqY07wXBW8sh0FgK', '2025-11-10 10:57:09', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(29, 'Mac', 'Mac', 'Macky', '09xxxxxxxxx', 'danmarkjavier123@gmail.com', 'El Salvador', 'CNO', NULL, 'CNO', '$2y$10$oqoqAbwSyA00HuQkQbAlb.jbzz8I.bI/9gJu5SqcNEjaLPqrqbl2m', '2025-11-15 06:53:28', '93f3b568ddd9227f8f4beba0f1829a85', 0, 'Inactive'),
(30, 'Brgy', 'Calongonan', 'Calongonan1', '09xxxxxxxxx', 'calongonan@gmail.com', 'El Salvador', 'Calongonan', NULL, 'BNS', '$2y$10$5dQcljjGUuzYDIeTupvxSev7PPdpywHGoYVeONSLguaUgoexuttXe', '2025-11-17 00:19:49', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(31, 'Brgy', 'Sinaloc', 'Sinaloc1', '21', 'sinaloc@gmail.com', 'El Salvador', 'Sinaloc', NULL, 'BNS', '$2y$10$aAsdyNzgK0m8qFg/IT80auJbEZQ9rBYrTUP.Mg0.jPw5H2VVFyNh.', '2025-11-17 02:50:33', 'ad262718bc012ab03dc1b1c998645570', 0, 'Active'),
(32, 'Sample', 'Sample', 'Samp', '09758765987', 'sample@gmail.com', 'El Salvador', 'Calongonan', NULL, 'BNS', '$2y$10$ss97Ls3AeLgkZiiqCjXQ3uRulZXL2I.JvGmThfSGMtHcKpGpsw.3S', '2025-11-19 06:30:45', NULL, 0, 'Inactive');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bns_reports`
--
ALTER TABLE `bns_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bns_reports_report_fk` (`report_id`);

--
-- Indexes for table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_history_ibfk_1` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_sender` (`sender_id`);

--
-- Indexes for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_ibfk_1` (`user_id`);

--
-- Indexes for table `report_archives`
--
ALTER TABLE `report_archives`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_report` (`report_id`,`user_id`,`user_type`);

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
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

--
-- AUTO_INCREMENT for table `bns_reports`
--
ALTER TABLE `bns_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `report_archives`
--
ALTER TABLE `report_archives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bns_reports`
--
ALTER TABLE `bns_reports`
  ADD CONSTRAINT `bns_reports_report_fk` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login_history`
--
ALTER TABLE `login_history`
  ADD CONSTRAINT `login_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
