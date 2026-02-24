-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 04, 2025 at 07:01 PM
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
-- Database: `nutri_map`
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
(21, 8, 'Logged out', 'Trusted Device', '2025-10-01 16:59:01'),
(22, 8, 'User logged in', 'Trusted device login from IP ::1', '2025-10-01 16:59:05'),
(23, 12, 'User logged in', 'Trusted device login from IP ::1', '2025-10-01 17:36:25'),
(24, 10, 'User logged in', 'Trusted device login from IP ::1', '2025-10-01 17:36:33'),
(25, 8, 'Logged out', 'Trusted Device', '2025-10-01 17:36:51'),
(26, 8, 'User logged in', 'Trusted device login from IP ::1', '2025-10-01 17:36:57'),
(27, 12, 'User logged in', 'Trusted device login from IP ::1', '2025-10-01 17:37:32'),
(28, 10, 'User logged in', 'Trusted device login from IP ::1', '2025-10-01 17:37:53'),
(29, 8, 'User logged in', 'Trusted device login from IP ::1', '2025-10-01 17:38:01'),
(30, 10, 'User logged in', 'Trusted device login from IP ::1', '2025-10-01 18:05:24'),
(31, 10, 'User logged in', 'Trusted device login from IP ::1', '2025-10-01 18:05:52'),
(32, 8, 'User logged in', 'Trusted device login from IP ::1', '2025-10-01 18:06:07'),
(33, 8, 'Logged out', 'Trusted Device', '2025-10-01 18:06:11'),
(34, 8, 'User logged in', 'Trusted device login from IP ::1', '2025-10-01 18:06:17'),
(35, 12, 'User logged in', 'Trusted device login from IP ::1', '2025-10-01 18:10:10'),
(36, 12, 'Logged out', 'Trusted Device', '2025-10-01 18:10:12'),
(37, 10, 'User logged in', 'Trusted device login from IP ::1', '2025-10-01 18:17:51'),
(38, 10, 'User logged in', 'Trusted device login from IP ::1', '2025-10-01 18:18:15'),
(39, 10, 'Report Added', 'Report ID 21 created for Barangay Calongonan, Year 2025 with title \'new report\'', '2025-10-01 18:19:18'),
(40, 8, 'User logged in', 'Trusted device login from IP ::1', '2025-10-01 18:19:26'),
(41, 10, 'User logged in', 'Trusted device login from IP ::1', '2025-10-04 16:52:39');

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
  `ind2` int(11) DEFAULT NULL,
  `ind3` int(11) DEFAULT NULL,
  `ind4a` int(11) DEFAULT NULL,
  `ind4b` int(11) DEFAULT NULL,
  `ind5` int(11) DEFAULT NULL,
  `ind6` int(11) DEFAULT NULL,
  `ind7a` decimal(5,2) DEFAULT NULL,
  `ind7b1_no` int(11) DEFAULT NULL,
  `ind7b1_pct` decimal(5,2) DEFAULT NULL,
  `ind7b2_no` int(11) DEFAULT NULL,
  `ind7b2_pct` decimal(5,2) DEFAULT NULL,
  `ind7b3_no` int(11) DEFAULT NULL,
  `ind7b3_pct` decimal(5,2) DEFAULT NULL,
  `ind7b4_no` int(11) DEFAULT NULL,
  `ind7b4_pct` decimal(5,2) DEFAULT NULL,
  `ind7b5_no` int(11) DEFAULT NULL,
  `ind7b5_pct` decimal(5,2) DEFAULT NULL,
  `ind7b6_no` int(11) DEFAULT NULL,
  `ind7b6_pct` decimal(5,2) DEFAULT NULL,
  `ind7b7_no` int(11) DEFAULT NULL,
  `ind7b7_pct` decimal(5,2) DEFAULT NULL,
  `ind7b8_no` int(11) DEFAULT NULL,
  `ind7b8_pct` decimal(5,2) DEFAULT NULL,
  `ind7b9_no` int(11) DEFAULT NULL,
  `ind7b9_pct` decimal(5,2) DEFAULT NULL,
  `ind8` int(11) DEFAULT NULL,
  `ind9` int(11) DEFAULT NULL,
  `ind10` int(11) DEFAULT NULL,
  `ind11` int(11) DEFAULT NULL,
  `ind12` int(11) DEFAULT NULL,
  `ind13` int(11) DEFAULT NULL,
  `ind14` int(11) DEFAULT NULL,
  `ind15a_public` int(11) DEFAULT NULL,
  `ind15a_private` int(11) DEFAULT NULL,
  `ind15b_public` int(11) DEFAULT NULL,
  `ind15b_private` int(11) DEFAULT NULL,
  `ind16` int(11) DEFAULT NULL,
  `ind17` int(11) DEFAULT NULL,
  `ind18` int(11) DEFAULT NULL,
  `ind19` decimal(5,2) DEFAULT NULL,
  `ind20a_no` int(11) DEFAULT NULL,
  `ind20a_pct` decimal(5,2) DEFAULT NULL,
  `ind20b_no` int(11) DEFAULT NULL,
  `ind20b_pct` decimal(5,2) DEFAULT NULL,
  `ind20c_no` int(11) DEFAULT NULL,
  `ind20c_pct` decimal(5,2) DEFAULT NULL,
  `ind20d_no` int(11) DEFAULT NULL,
  `ind20d_pct` decimal(5,2) DEFAULT NULL,
  `ind20e_no` int(11) DEFAULT NULL,
  `ind20e_pct` decimal(5,2) DEFAULT NULL,
  `ind21` int(11) DEFAULT NULL,
  `ind22` int(11) DEFAULT NULL,
  `ind23` int(11) DEFAULT NULL,
  `ind24` int(11) DEFAULT NULL,
  `ind25` int(11) DEFAULT NULL,
  `ind26a_no` int(11) DEFAULT NULL,
  `ind26a_pct` decimal(5,2) DEFAULT NULL,
  `ind26b_no` int(11) DEFAULT NULL,
  `ind26b_pct` decimal(5,2) DEFAULT NULL,
  `ind26c_no` int(11) DEFAULT NULL,
  `ind26c_pct` decimal(5,2) DEFAULT NULL,
  `ind26d_no` int(11) DEFAULT NULL,
  `ind26d_pct` decimal(5,2) DEFAULT NULL,
  `ind27a_no` int(11) DEFAULT NULL,
  `ind27a_pct` decimal(5,2) DEFAULT NULL,
  `ind27b_no` int(11) DEFAULT NULL,
  `ind27b_pct` decimal(5,2) DEFAULT NULL,
  `ind27c_no` int(11) DEFAULT NULL,
  `ind27c_pct` decimal(5,2) DEFAULT NULL,
  `ind27d_no` int(11) DEFAULT NULL,
  `ind27d_pct` decimal(5,2) DEFAULT NULL,
  `ind28a_no` int(11) DEFAULT NULL,
  `ind28a_pct` decimal(5,2) DEFAULT NULL,
  `ind28b_no` int(11) DEFAULT NULL,
  `ind28b_pct` decimal(5,2) DEFAULT NULL,
  `ind28c_no` int(11) DEFAULT NULL,
  `ind28c_pct` decimal(5,2) DEFAULT NULL,
  `ind28d_no` int(11) DEFAULT NULL,
  `ind28d_pct` decimal(5,2) DEFAULT NULL,
  `ind28e_no` int(11) DEFAULT NULL,
  `ind28e_pct` decimal(5,2) DEFAULT NULL,
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
  `ind30a_no` int(11) DEFAULT NULL,
  `ind30a_pct` decimal(5,2) DEFAULT NULL,
  `ind30b_no` int(11) DEFAULT NULL,
  `ind30b_pct` decimal(5,2) DEFAULT NULL,
  `ind30c_no` int(11) DEFAULT NULL,
  `ind30c_pct` decimal(5,2) DEFAULT NULL,
  `ind30d_no` int(11) DEFAULT NULL,
  `ind30d_pct` decimal(5,2) DEFAULT NULL,
  `ind30e_no` int(11) DEFAULT NULL,
  `ind30e_pct` decimal(5,2) DEFAULT NULL,
  `ind31` int(11) DEFAULT NULL,
  `ind32` int(11) DEFAULT NULL,
  `ind33` int(11) DEFAULT NULL,
  `ind34` int(11) DEFAULT NULL,
  `ind35a` int(11) DEFAULT NULL,
  `ind35b` int(11) DEFAULT NULL,
  `ind36` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bns_reports`
--

INSERT INTO `bns_reports` (`id`, `report_id`, `barangay`, `year`, `title`, `ind1`, `ind2`, `ind3`, `ind4a`, `ind4b`, `ind5`, `ind6`, `ind7a`, `ind7b1_no`, `ind7b1_pct`, `ind7b2_no`, `ind7b2_pct`, `ind7b3_no`, `ind7b3_pct`, `ind7b4_no`, `ind7b4_pct`, `ind7b5_no`, `ind7b5_pct`, `ind7b6_no`, `ind7b6_pct`, `ind7b7_no`, `ind7b7_pct`, `ind7b8_no`, `ind7b8_pct`, `ind7b9_no`, `ind7b9_pct`, `ind8`, `ind9`, `ind10`, `ind11`, `ind12`, `ind13`, `ind14`, `ind15a_public`, `ind15a_private`, `ind15b_public`, `ind15b_private`, `ind16`, `ind17`, `ind18`, `ind19`, `ind20a_no`, `ind20a_pct`, `ind20b_no`, `ind20b_pct`, `ind20c_no`, `ind20c_pct`, `ind20d_no`, `ind20d_pct`, `ind20e_no`, `ind20e_pct`, `ind21`, `ind22`, `ind23`, `ind24`, `ind25`, `ind26a_no`, `ind26a_pct`, `ind26b_no`, `ind26b_pct`, `ind26c_no`, `ind26c_pct`, `ind26d_no`, `ind26d_pct`, `ind27a_no`, `ind27a_pct`, `ind27b_no`, `ind27b_pct`, `ind27c_no`, `ind27c_pct`, `ind27d_no`, `ind27d_pct`, `ind28a_no`, `ind28a_pct`, `ind28b_no`, `ind28b_pct`, `ind28c_no`, `ind28c_pct`, `ind28d_no`, `ind28d_pct`, `ind28e_no`, `ind28e_pct`, `ind29a_no`, `ind29a_pct`, `ind29b_no`, `ind29b_pct`, `ind29c_no`, `ind29c_pct`, `ind29d_no`, `ind29d_pct`, `ind29e_no`, `ind29e_pct`, `ind30a_no`, `ind30a_pct`, `ind30b_no`, `ind30b_pct`, `ind30c_no`, `ind30c_pct`, `ind30d_no`, `ind30d_pct`, `ind30e_no`, `ind30e_pct`, `ind31`, `ind32`, `ind33`, `ind34`, `ind35a`, `ind35b`, `ind36`) VALUES
(10, 12, '', '2025', 'TheF', 12313, 213, 0, 0, 0, 0, 0, 0.00, 0, 100.00, 0, 100.00, 0, 100.00, 0, 100.00, 0, 100.00, 0, 100.00, 0, 100.00, 0, 100.00, 0, 100.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0),
(11, 13, '', '2025', 'Broda', 12, 21, 213, 0, 0, 0, 0, 0.00, 0, 51.00, 0, 51.00, 0, 51.00, 0, 51.00, 0, 51.00, 0, 51.00, 0, 15.00, 0, 51.00, 0, 51.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0),
(14, 14, '', '2025', '412', 21, 0, 0, 0, 0, 0, 0, 0.00, 0, 100.00, 0, 100.00, 0, 41.00, 0, 412.00, 0, 14.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0),
(15, 15, 'Sambulawan', '2025', 'GOODSH*T', 9999, 999, 999, 999, 9999, 999, 999, 99.00, 0, 100.00, 0, 100.00, 0, 100.00, 0, 99.00, 0, 99.00, 0, 99.00, 0, 99.00, 0, 99.00, 0, 99.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0),
(16, 16, 'Sambulawan', '2025', '21', 12, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0),
(17, 17, 'Sambulawan', '2025', '2', 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0),
(18, 18, 'Calongonan', '2025', 'dqq', 1231, 131, 1313, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 123.00, 0, 0.00, 0, 123.00, 0, 22.00, 0, 22.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0),
(19, 19, 'Calongonan', '2025', 'daaad', 231, 0, 0, 0, 0, 0, 0, 0.00, 0, 1.00, 0, 1.00, 0, 1.00, 0, 1.00, 0, 1.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0),
(20, 20, 'Calongonan', '2025', 'dasd', 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 1, 2, 3, 4, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0),
(21, 21, 'Calongonan', '2025', 'new report', 1232313, 1441, 0, 0, 0, 0, 0, 0.00, 0, 12.00, 0, 2.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `bns_reports_archive`
--

CREATE TABLE `bns_reports_archive` (
  `archived_id` int(11) NOT NULL,
  `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
  `archived_by` int(11) DEFAULT NULL,
  `report_id` int(11) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `year` year(4) NOT NULL,
  `title` varchar(255) NOT NULL,
  `ind1` int(11) DEFAULT NULL,
  `ind2` int(11) DEFAULT NULL,
  `ind3` int(11) DEFAULT NULL,
  `ind4a` int(11) DEFAULT NULL,
  `ind4b` int(11) DEFAULT NULL,
  `ind5` int(11) DEFAULT NULL,
  `ind6` int(11) DEFAULT NULL,
  `ind7a` decimal(5,2) DEFAULT NULL,
  `ind7b1_no` int(11) DEFAULT NULL,
  `ind7b1_pct` decimal(5,2) DEFAULT NULL,
  `ind7b2_no` int(11) DEFAULT NULL,
  `ind7b2_pct` decimal(5,2) DEFAULT NULL,
  `ind7b3_no` int(11) DEFAULT NULL,
  `ind7b3_pct` decimal(5,2) DEFAULT NULL,
  `ind7b4_no` int(11) DEFAULT NULL,
  `ind7b4_pct` decimal(5,2) DEFAULT NULL,
  `ind7b5_no` int(11) DEFAULT NULL,
  `ind7b5_pct` decimal(5,2) DEFAULT NULL,
  `ind7b6_no` int(11) DEFAULT NULL,
  `ind7b6_pct` decimal(5,2) DEFAULT NULL,
  `ind7b7_no` int(11) DEFAULT NULL,
  `ind7b7_pct` decimal(5,2) DEFAULT NULL,
  `ind7b8_no` int(11) DEFAULT NULL,
  `ind7b8_pct` decimal(5,2) DEFAULT NULL,
  `ind7b9_no` int(11) DEFAULT NULL,
  `ind7b9_pct` decimal(5,2) DEFAULT NULL,
  `ind8` int(11) DEFAULT NULL,
  `ind9` int(11) DEFAULT NULL,
  `ind10` int(11) DEFAULT NULL,
  `ind11` int(11) DEFAULT NULL,
  `ind12` int(11) DEFAULT NULL,
  `ind13` int(11) DEFAULT NULL,
  `ind14` int(11) DEFAULT NULL,
  `ind15a_public` int(11) DEFAULT NULL,
  `ind15a_private` int(11) DEFAULT NULL,
  `ind15b_public` int(11) DEFAULT NULL,
  `ind15b_private` int(11) DEFAULT NULL,
  `ind16` int(11) DEFAULT NULL,
  `ind17` int(11) DEFAULT NULL,
  `ind18` int(11) DEFAULT NULL,
  `ind19` decimal(5,2) DEFAULT NULL,
  `ind20a_no` int(11) DEFAULT NULL,
  `ind20a_pct` decimal(5,2) DEFAULT NULL,
  `ind20b_no` int(11) DEFAULT NULL,
  `ind20b_pct` decimal(5,2) DEFAULT NULL,
  `ind20c_no` int(11) DEFAULT NULL,
  `ind20c_pct` decimal(5,2) DEFAULT NULL,
  `ind20d_no` int(11) DEFAULT NULL,
  `ind20d_pct` decimal(5,2) DEFAULT NULL,
  `ind20e_no` int(11) DEFAULT NULL,
  `ind20e_pct` decimal(5,2) DEFAULT NULL,
  `ind21` int(11) DEFAULT NULL,
  `ind22` int(11) DEFAULT NULL,
  `ind23` int(11) DEFAULT NULL,
  `ind24` int(11) DEFAULT NULL,
  `ind25` int(11) DEFAULT NULL,
  `ind26a_no` int(11) DEFAULT NULL,
  `ind26a_pct` decimal(5,2) DEFAULT NULL,
  `ind26b_no` int(11) DEFAULT NULL,
  `ind26b_pct` decimal(5,2) DEFAULT NULL,
  `ind26c_no` int(11) DEFAULT NULL,
  `ind26c_pct` decimal(5,2) DEFAULT NULL,
  `ind26d_no` int(11) DEFAULT NULL,
  `ind26d_pct` decimal(5,2) DEFAULT NULL,
  `ind27a_no` int(11) DEFAULT NULL,
  `ind27a_pct` decimal(5,2) DEFAULT NULL,
  `ind27b_no` int(11) DEFAULT NULL,
  `ind27b_pct` decimal(5,2) DEFAULT NULL,
  `ind27c_no` int(11) DEFAULT NULL,
  `ind27c_pct` decimal(5,2) DEFAULT NULL,
  `ind27d_no` int(11) DEFAULT NULL,
  `ind27d_pct` decimal(5,2) DEFAULT NULL,
  `ind28a_no` int(11) DEFAULT NULL,
  `ind28a_pct` decimal(5,2) DEFAULT NULL,
  `ind28b_no` int(11) DEFAULT NULL,
  `ind28b_pct` decimal(5,2) DEFAULT NULL,
  `ind28c_no` int(11) DEFAULT NULL,
  `ind28c_pct` decimal(5,2) DEFAULT NULL,
  `ind28d_no` int(11) DEFAULT NULL,
  `ind28d_pct` decimal(5,2) DEFAULT NULL,
  `ind28e_no` int(11) DEFAULT NULL,
  `ind28e_pct` decimal(5,2) DEFAULT NULL,
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
  `ind30a_no` int(11) DEFAULT NULL,
  `ind30a_pct` decimal(5,2) DEFAULT NULL,
  `ind30b_no` int(11) DEFAULT NULL,
  `ind30b_pct` decimal(5,2) DEFAULT NULL,
  `ind30c_no` int(11) DEFAULT NULL,
  `ind30c_pct` decimal(5,2) DEFAULT NULL,
  `ind30d_no` int(11) DEFAULT NULL,
  `ind30d_pct` decimal(5,2) DEFAULT NULL,
  `ind30e_no` int(11) DEFAULT NULL,
  `ind30e_pct` decimal(5,2) DEFAULT NULL,
  `ind31` int(11) DEFAULT NULL,
  `ind32` int(11) DEFAULT NULL,
  `ind33` int(11) DEFAULT NULL,
  `ind34` int(11) DEFAULT NULL,
  `ind35a` int(11) DEFAULT NULL,
  `ind35b` int(11) DEFAULT NULL,
  `ind36` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 10, '04hk3r65n68a8bi0qb382p2fsp', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Sa', '::1', '2025-10-04 16:52:39', NULL, 'eb917e6f5f80b52a73e8e69c1a03a18e'),
(2, 10, '2hauhvi1j0u8lcgslp2m8ovncv', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Sa', '::1', '2025-09-22 03:56:44', NULL, NULL),
(3, 8, 'ph8g5jk94suiavfbon58b31hf4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Sa', '::1', '2025-10-01 18:19:26', NULL, 'eb917e6f5f80b52a73e8e69c1a03a18e'),
(4, 8, '2hauhvi1j0u8lcgslp2m8ovncv', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Sa', '::1', '2025-09-22 04:12:29', NULL, NULL),
(5, 12, 'o86fu9nin7o5i9rt5hsrvf09q9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Sa', '::1', '2025-10-01 18:10:10', NULL, 'a067af488dacc36b3d01fdb803f029e3'),
(6, 12, '89jbjpmh218tvv08dbf3v77i0p', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Sa', '::1', '2025-09-22 11:22:48', NULL, NULL),
(7, 10, 'o86fu9nin7o5i9rt5hsrvf09q9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Sa', '::1', '2025-10-01 18:18:15', NULL, 'a067af488dacc36b3d01fdb803f029e3'),
(8, 10, '89jbjpmh218tvv08dbf3v77i0p', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Sa', '::1', '2025-09-22 11:26:57', NULL, NULL),
(9, 12, 'ph8g5jk94suiavfbon58b31hf4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Sa', '::1', '2025-10-01 16:20:25', NULL, 'eb917e6f5f80b52a73e8e69c1a03a18e'),
(10, 12, '8vsueecltnic705kk3f66fgdh4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Sa', '::1', '2025-09-26 03:18:56', NULL, NULL),
(11, 8, 'rr16nfgqk577b4jnm0qam3lkv0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Sa', '::1', '2025-09-29 13:39:29', NULL, 'a067af488dacc36b3d01fdb803f029e3'),
(12, 8, 'rr16nfgqk577b4jnm0qam3lkv0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Sa', '::1', '2025-09-29 11:35:51', NULL, NULL);

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
(26, 10, '947964', '2025-09-21 13:18:32', '2025-09-21 15:23:32'),
(27, 12, '591477', '2025-09-21 13:20:37', '2025-09-21 15:25:37'),
(28, 10, '780619', '2025-09-21 15:00:21', '2025-09-21 17:05:21'),
(29, 8, '751915', '2025-09-21 15:11:58', '2025-09-21 17:16:58'),
(30, 8, '352666', '2025-09-21 15:12:02', '2025-09-21 17:17:02'),
(31, 10, '216510', '2025-09-22 03:56:26', '2025-09-22 06:01:26'),
(32, 8, '430813', '2025-09-22 04:12:00', '2025-09-22 06:17:00'),
(33, 12, '915745', '2025-09-22 11:22:08', '2025-09-22 13:27:08'),
(34, 10, '201614', '2025-09-22 11:26:27', '2025-09-22 13:31:27'),
(35, 12, '698628', '2025-09-26 03:18:17', '2025-09-26 05:23:17'),
(36, 8, '904771', '2025-09-29 11:35:23', '2025-09-29 13:40:23');

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
  `prev_status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `report_time`, `report_date`, `status`, `prev_status`) VALUES
(12, 10, '15:19:58', '2025-09-21', 'Approved', NULL),
(13, 12, '15:21:49', '2025-09-21', 'Approved', NULL),
(14, 12, '06:08:47', '2025-09-26', 'Approved', NULL),
(15, 12, '08:54:32', '2025-09-26', 'Approved', NULL),
(16, 12, '09:12:13', '2025-09-26', 'Rejected', NULL),
(17, 12, '09:18:02', '2025-09-26', 'Rejected', NULL),
(18, 10, '12:54:05', '2025-09-29', 'Pending', NULL),
(19, 10, '13:13:07', '2025-09-29', 'Pending', NULL),
(20, 10, '15:14:33', '2025-09-29', 'Approved', NULL),
(21, 10, '20:19:18', '2025-10-01', 'Pending', NULL);

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
(8, 'CNO', 'ADMIN', 'CNO', '09264686830', 'louizkylaspona@gmail.com', 'Mangima Tankulan', 'CNO', '1758540105_CCS_Logo_2.png', 'CNO', '$2y$10$Xm/8kdPuxuROCeJfzlcU6.rrR0CizxiD3R7CCAy/lKlwIaZCGFJJq', '2025-09-21 13:06:17', 'ph8g5jk94suiavfbon58b31hf4', 0, 'Active'),
(10, 'BNS', 'Brgy', 'bns', '09264686830', 'loki072002@gmail.com', 'Mangima Tankulan', 'Calongonan', '1758514208_2.png', 'BNS', '$2y$10$zO97F06lMGhmM.Cap0dhu.P4bn/7GyR4zMNMFnd3YS/zOK0WDjEoa', '2025-09-21 13:16:32', '04hk3r65n68a8bi0qb382p2fsp', 0, 'Active'),
(12, 'we', 'we', 'we', '21', 'goodies34512@gmail.com', 'Mangima Tankulan', 'Sambulawan', '1758540182_image_1.jpg', 'BNS', '$2y$10$XKrEFbJZtEkhhDpDq9vOsOQoQZ1H2ubMfaN2oEPSE8crsIW2OYoMW', '2025-09-21 13:18:02', 'o86fu9nin7o5i9rt5hsrvf09q9', 0, 'Active');

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
  ADD KEY `report_id` (`report_id`);

--
-- Indexes for table `bns_reports_archive`
--
ALTER TABLE `bns_reports_archive`
  ADD PRIMARY KEY (`archived_id`);

--
-- Indexes for table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_history_ibfk_1` (`user_id`);

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
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `bns_reports`
--
ALTER TABLE `bns_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `bns_reports_archive`
--
ALTER TABLE `bns_reports_archive`
  MODIFY `archived_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
  ADD CONSTRAINT `bns_reports_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE;

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
