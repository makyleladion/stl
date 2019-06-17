-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 28, 2019 at 09:50 AM
-- Server version: 5.5.62-cll
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stl_v1`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_log`
--

CREATE TABLE `api_log` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `request` varchar(5000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bets`
--

CREATE TABLE `bets` (
  `id` int(10) UNSIGNED NOT NULL,
  `outlet_id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `ticket_id` int(10) UNSIGNED NOT NULL,
  `amount` int(10) NOT NULL,
  `type` varchar(191) NOT NULL,
  `number` varchar(191) NOT NULL,
  `game` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bet_prices`
--

CREATE TABLE `bet_prices` (
  `id` int(10) UNSIGNED NOT NULL,
  `outlet_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(191) NOT NULL,
  `price_per_bet_count` int(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `config_global`
--

CREATE TABLE `config_global` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `type` varchar(10) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `config_global`
--

INSERT INTO `config_global` (`id`, `name`, `type`, `value`, `created_at`, `updated_at`) VALUES
(1, 'HIDE_OUTLET_USHERS_ASSIGNMENT', 'boolean', 'true', '2018-12-08 05:00:00', '2018-12-08 05:00:00'),
(2, 'ENABLE_BETTING', 'boolean', 'true', '2019-01-16 05:00:00', '2019-01-16 05:00:00'),
(3, 'COMMISSION_PERCENTAGE_DECIMAL', 'number', '0.07', '2019-01-23 05:00:00', '2019-01-23 05:00:00'),
(4, 'NOTIFICATION_EMAILS', 'json', '[\"joshuapaylaga@gmail.com\",\"daveanthonyaduenas@gmail.com\"]', '2019-01-24 05:00:00', '2019-01-24 05:00:00'),
(5, 'NOTIFICATION_EMAILS_ENABLED', 'boolean', 'false', '2019-03-05 05:00:00', '2019-03-05 05:00:00'),
(6, 'GLOBAL_CUTOFF_TIME', 'number', '30', '2019-04-03 04:00:00', '2019-04-03 04:00:00'),
(7, 'GLOBAL_PREPARE_TO_CUTOFF_TIME', 'number', '20', '2019-04-03 04:00:00', '2019-04-03 04:00:00'),
(8, 'REMITTANCE_PERCENTAGE_DECIMAL', 'number', '0.07', '2019-04-03 04:00:00', '2019-04-03 04:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `default_outlets`
--

CREATE TABLE `default_outlets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `outlet_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `default_outlets`
--

INSERT INTO `default_outlets` (`id`, `user_id`, `outlet_id`, `created_at`, `updated_at`) VALUES
(1, 5, 51, '2017-11-25 00:22:33', '2018-04-30 13:02:51'),
(2, 6, 3, '2017-11-25 00:24:51', '2017-11-25 00:24:51'),
(3, 7, 4, '2017-11-25 00:27:06', '2017-11-25 00:27:06'),
(4, 8, 5, '2017-11-25 00:29:54', '2017-11-25 00:29:54'),
(5, 9, 9, '2017-11-25 00:40:32', '2017-11-25 00:40:32'),
(6, 10, 10, '2017-11-25 02:22:49', '2017-11-25 02:22:49'),
(7, 11, 10, '2017-11-25 02:23:31', '2017-11-25 02:23:31'),
(8, 12, 11, '2017-11-25 02:26:26', '2017-11-25 02:26:26'),
(9, 13, 50, '2017-11-25 02:29:46', '2018-04-30 13:00:26'),
(10, 14, 28, '2017-11-25 02:31:55', '2018-09-07 03:11:16'),
(11, 15, 14, '2017-11-25 02:33:59', '2017-11-25 02:33:59'),
(12, 16, 6, '2017-11-25 02:37:34', '2017-11-25 02:37:34'),
(13, 17, 15, '2017-11-25 02:40:19', '2017-11-25 02:40:19'),
(14, 18, 16, '2017-11-25 02:42:21', '2017-11-25 02:42:21'),
(15, 19, 17, '2017-11-25 02:45:20', '2017-11-25 02:45:20'),
(16, 20, 41, '2017-11-25 02:48:00', '2018-04-04 13:40:12'),
(17, 21, 7, '2017-11-25 02:51:18', '2017-11-25 02:51:18'),
(18, 23, 24, '2017-11-25 10:58:31', '2018-01-19 03:57:19'),
(19, 24, 12, '2017-11-25 11:43:56', '2018-01-19 03:59:56'),
(20, 25, 8, '2017-11-25 15:10:15', '2017-11-25 15:10:15'),
(21, 26, 5, '2017-11-25 15:40:53', '2017-11-25 15:40:53'),
(22, 28, 19, '2017-12-02 05:21:57', '2017-12-02 05:21:57'),
(23, 29, 20, '2017-12-03 00:33:19', '2017-12-03 00:33:19'),
(24, 30, 21, '2017-12-03 04:12:54', '2017-12-03 04:12:54'),
(25, 31, 22, '2017-12-03 04:17:39', '2017-12-03 04:17:39'),
(26, 33, 23, '2017-12-03 12:25:35', '2018-01-20 02:21:47'),
(27, 34, 24, '2017-12-04 02:48:21', '2017-12-04 02:48:21'),
(28, 35, 25, '2017-12-04 02:50:55', '2017-12-04 02:50:55'),
(29, 36, 26, '2017-12-11 14:09:28', '2017-12-11 14:09:28'),
(30, 37, 21, '2017-12-29 00:34:40', '2017-12-29 00:34:40'),
(31, 38, 27, '2018-01-05 04:27:17', '2018-01-05 04:27:17'),
(32, 39, 28, '2018-01-05 04:29:34', '2018-01-05 04:29:34'),
(33, 40, 29, '2018-01-05 04:32:04', '2018-01-05 04:32:04'),
(34, 41, 30, '2018-01-05 04:34:51', '2018-01-05 04:34:51'),
(35, 42, 31, '2018-01-05 04:38:50', '2018-01-05 04:38:50'),
(36, 43, 23, '2018-01-19 04:23:11', '2018-01-30 12:34:54'),
(37, 45, 50, '2018-02-02 23:52:56', '2018-11-15 11:27:56'),
(38, 46, 37, '2018-02-28 14:36:27', '2018-03-08 15:18:04'),
(39, 47, 33, '2018-03-01 16:52:24', '2018-03-01 16:52:24'),
(40, 48, 34, '2018-03-03 19:21:24', '2018-03-03 19:21:24'),
(41, 49, 35, '2018-03-03 20:43:14', '2018-03-03 20:43:14'),
(42, 50, 36, '2018-03-07 14:35:28', '2018-03-07 14:35:28'),
(43, 51, 38, '2018-03-09 01:09:05', '2018-03-09 01:09:05'),
(44, 52, 39, '2018-03-09 13:42:10', '2018-03-09 13:42:10'),
(45, 53, 29, '2018-03-09 20:03:16', '2018-03-09 20:03:16'),
(46, 54, 40, '2018-03-11 15:55:28', '2018-03-11 15:55:28'),
(47, 55, 42, '2018-04-26 18:45:15', '2018-04-26 18:45:15'),
(48, 56, 43, '2018-04-26 20:19:10', '2018-04-26 20:19:10'),
(49, 57, 44, '2018-04-27 22:39:03', '2018-04-27 22:39:03'),
(50, 58, 45, '2018-04-28 11:09:31', '2018-04-28 11:09:31'),
(51, 59, 46, '2018-04-28 15:39:58', '2018-04-28 15:39:58'),
(52, 64, 52, '2018-05-07 20:31:09', '2018-05-07 20:31:09'),
(53, 65, 48, '2018-05-07 22:10:37', '2018-05-07 22:10:37'),
(54, 67, 47, '2018-05-23 00:31:14', '2018-05-23 00:31:14'),
(55, 69, 53, '2018-05-24 15:16:59', '2018-05-24 15:16:59'),
(56, 70, 54, '2018-05-24 15:31:05', '2018-05-24 15:31:05'),
(57, 73, 55, '2018-06-05 12:31:30', '2018-06-05 12:31:30'),
(58, 78, 39, '2018-09-03 19:33:32', '2018-09-03 19:33:32'),
(59, 79, 13, '2018-09-07 03:10:37', '2018-09-07 14:20:23'),
(60, 80, 53, '2018-09-12 11:57:21', '2018-09-12 11:57:21'),
(61, 81, 39, '2018-09-12 12:39:30', '2018-09-12 12:39:30'),
(62, 82, 11, '2018-09-12 18:39:55', '2018-09-12 18:39:55'),
(63, 83, 19, '2018-09-13 15:34:06', '2018-09-13 15:34:06'),
(64, 84, 17, '2018-09-14 19:09:01', '2018-09-14 19:09:01'),
(65, 85, 35, '2018-09-15 15:49:12', '2018-09-15 15:49:12'),
(66, 86, 33, '2018-10-03 18:11:50', '2018-10-03 18:11:50'),
(67, 87, 56, '2018-10-04 12:10:10', '2018-10-04 12:10:10'),
(68, 88, 57, '2018-10-04 14:14:35', '2018-10-04 14:14:35'),
(69, 89, 42, '2018-10-06 12:23:46', '2018-10-06 12:23:46'),
(70, 90, 50, '2018-11-08 22:57:42', '2018-11-08 22:57:42'),
(71, 91, 50, '2018-11-08 22:58:20', '2018-11-08 22:58:20'),
(72, 92, 50, '2018-11-08 23:37:57', '2018-11-08 23:37:57'),
(73, 93, 50, '2018-11-08 23:42:35', '2018-11-08 23:42:35'),
(74, 94, 50, '2018-11-08 23:42:57', '2018-11-08 23:42:57'),
(75, 95, 50, '2018-11-12 17:50:15', '2018-11-12 17:50:15'),
(76, 96, 39, '2018-11-16 14:08:26', '2018-11-16 14:08:26'),
(77, 97, 55, '2018-11-16 16:02:39', '2018-11-16 16:02:39'),
(78, 98, 50, '2018-11-16 17:38:43', '2018-11-16 17:38:43'),
(79, 99, 50, '2018-11-16 17:39:32', '2018-11-16 17:39:32'),
(80, 100, 50, '2018-11-16 18:42:20', '2018-11-16 18:42:20'),
(81, 101, 50, '2018-11-16 18:43:11', '2018-11-16 18:43:11'),
(82, 102, 15, '2018-11-16 19:12:36', '2018-11-16 19:12:36'),
(83, 103, 55, '2018-11-16 20:27:11', '2018-11-16 20:27:11'),
(84, 104, 50, '2018-11-16 21:34:27', '2018-11-16 21:34:27'),
(85, 105, 50, '2018-11-16 21:35:25', '2018-11-16 21:35:25'),
(86, 106, 50, '2018-11-17 16:54:04', '2018-11-17 16:54:04'),
(87, 107, 16, '2018-11-17 19:08:32', '2018-11-17 19:08:32'),
(88, 108, 16, '2018-11-17 19:23:24', '2018-11-17 19:23:24'),
(89, 109, 50, '2018-11-30 20:03:05', '2018-11-30 20:03:05'),
(90, 110, 16, '2018-11-30 21:21:17', '2018-11-30 21:21:17'),
(91, 111, 50, '2018-12-05 20:01:26', '2018-12-05 20:01:26'),
(92, 112, 58, '2018-12-11 14:47:39', '2018-12-11 14:47:39'),
(93, 113, 16, '2018-12-12 15:25:51', '2018-12-12 15:25:51'),
(94, 114, 16, '2018-12-13 16:35:49', '2018-12-13 16:35:49'),
(95, 115, 11, '2018-12-30 14:37:41', '2018-12-30 14:37:41'),
(96, 116, 16, '2019-01-19 13:45:27', '2019-01-19 13:45:27'),
(97, 117, 40, '2019-01-21 13:37:59', '2019-01-21 13:37:59'),
(98, 118, 40, '2019-01-21 18:34:26', '2019-01-21 18:34:26'),
(99, 119, 50, '2019-01-26 17:25:47', '2019-01-26 17:25:47'),
(100, 121, 50, '2019-03-16 00:22:23', '2019-03-16 00:22:23'),
(101, 124, 22, '2019-03-25 17:42:26', '2019-03-25 17:42:26'),
(102, 125, 30, '2019-04-03 17:46:26', '2019-04-03 17:46:26');

-- --------------------------------------------------------

--
-- Table structure for table `disable_outlet_records`
--

CREATE TABLE `disable_outlet_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `outlet_id` int(10) UNSIGNED NOT NULL,
  `disable_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `disable_outlet_records`
--

INSERT INTO `disable_outlet_records` (`id`, `outlet_id`, `disable_timestamp`) VALUES
(1, 12, '2019-01-04 01:14:54'),
(2, 24, '2019-01-04 01:16:03'),
(3, 22, '2019-01-04 01:28:06'),
(4, 33, '2019-01-04 01:29:07'),
(5, 39, '2019-01-04 01:29:51'),
(6, 33, '2019-01-04 01:30:06'),
(7, 19, '2019-01-04 01:30:39'),
(8, 44, '2019-01-04 01:31:01'),
(9, 5, '2019-01-04 01:31:05'),
(10, 53, '2019-01-04 01:31:29'),
(11, 54, '2019-01-04 01:32:06'),
(12, 47, '2019-01-04 01:33:34'),
(13, 20, '2019-01-04 01:33:51'),
(14, 47, '2019-01-04 01:34:02'),
(15, 16, '2019-01-04 01:39:29'),
(16, 36, '2019-01-04 01:44:13'),
(17, 50, '2019-01-04 02:17:38'),
(18, 12, '2019-01-05 01:18:14'),
(19, 24, '2019-01-05 01:19:41'),
(20, 22, '2019-01-05 01:23:36'),
(21, 20, '2019-01-05 01:28:53'),
(22, 19, '2019-01-05 01:30:08'),
(23, 50, '2019-01-05 01:30:14'),
(24, 5, '2019-01-05 01:30:25'),
(25, 33, '2019-01-05 01:30:44'),
(26, 36, '2019-01-05 01:31:03'),
(27, 19, '2019-01-05 01:31:04'),
(28, 36, '2019-01-05 01:31:11'),
(29, 54, '2019-01-05 01:31:14'),
(30, 44, '2019-01-05 01:32:01'),
(31, 47, '2019-01-05 01:32:21'),
(32, 53, '2019-01-05 01:36:17'),
(33, 50, '2019-01-05 02:41:27'),
(34, 12, '2019-01-06 01:15:27'),
(35, 5, '2019-01-06 01:22:53'),
(36, 5, '2019-01-06 01:25:16'),
(37, 24, '2019-01-06 01:25:31'),
(38, 54, '2019-01-06 01:30:14'),
(39, 39, '2019-01-06 01:30:27'),
(40, 39, '2019-01-06 01:30:37'),
(41, 33, '2019-01-06 01:30:37'),
(42, 20, '2019-01-06 01:30:40'),
(43, 35, '2019-01-06 01:31:22'),
(44, 22, '2019-01-06 01:31:37'),
(45, 44, '2019-01-06 01:31:53'),
(46, 50, '2019-01-06 01:32:19'),
(47, 19, '2019-01-06 01:32:50'),
(48, 47, '2019-01-06 01:32:53'),
(49, 53, '2019-01-06 01:33:16'),
(50, 36, '2019-01-06 01:42:56'),
(51, 12, '2019-01-07 01:15:21'),
(52, 50, '2019-01-07 01:18:26'),
(53, 24, '2019-01-07 01:27:46'),
(54, 20, '2019-01-07 01:27:54'),
(55, 22, '2019-01-07 01:28:22'),
(56, 5, '2019-01-07 01:28:23'),
(57, 53, '2019-01-07 01:30:14'),
(58, 19, '2019-01-07 01:30:59'),
(59, 39, '2019-01-07 01:31:05'),
(60, 47, '2019-01-07 01:31:32'),
(61, 54, '2019-01-07 01:31:59'),
(62, 33, '2019-01-07 01:36:12'),
(63, 50, '2019-01-07 01:43:46'),
(64, 36, '2019-01-07 01:45:37'),
(65, 44, '2019-01-07 01:53:56'),
(66, 12, '2019-01-08 01:08:40'),
(67, 5, '2019-01-08 01:24:33'),
(68, 24, '2019-01-08 01:27:49'),
(69, 22, '2019-01-08 01:28:04'),
(70, 50, '2019-01-08 01:29:04'),
(71, 54, '2019-01-08 01:30:12'),
(72, 33, '2019-01-08 01:30:19'),
(73, 20, '2019-01-08 01:30:30'),
(74, 53, '2019-01-08 01:31:13'),
(75, 19, '2019-01-08 01:31:23'),
(76, 39, '2019-01-08 01:32:38'),
(77, 36, '2019-01-08 01:43:13'),
(78, 44, '2019-01-08 01:43:48'),
(79, 50, '2019-01-08 02:34:28'),
(80, 35, '2019-01-08 03:02:02'),
(81, 50, '2019-01-09 01:10:44'),
(82, 21, '2019-01-09 01:10:54'),
(83, 12, '2019-01-09 01:20:51'),
(84, 24, '2019-01-09 01:22:38'),
(85, 37, '2019-01-09 01:23:53'),
(86, 33, '2019-01-09 01:27:09'),
(87, 20, '2019-01-09 01:30:07'),
(88, 54, '2019-01-09 01:30:13'),
(89, 53, '2019-01-09 01:30:26'),
(90, 44, '2019-01-09 01:30:31'),
(91, 44, '2019-01-09 01:30:47'),
(92, 39, '2019-01-09 01:30:51'),
(93, 22, '2019-01-09 01:31:15'),
(94, 3, '2019-01-09 01:31:24'),
(95, 5, '2019-01-09 01:32:19'),
(96, 36, '2019-01-09 01:38:48'),
(97, 50, '2019-01-09 01:41:12'),
(98, 50, '2019-01-09 02:39:03'),
(99, 22, '2019-01-10 01:12:38'),
(100, 37, '2019-01-10 01:19:53'),
(101, 39, '2019-01-10 01:27:11'),
(102, 5, '2019-01-10 01:27:47'),
(103, 33, '2019-01-10 01:30:21'),
(104, 53, '2019-01-10 01:30:25'),
(105, 24, '2019-01-10 01:30:43'),
(106, 44, '2019-01-10 01:30:45'),
(107, 19, '2019-01-10 01:30:48'),
(108, 54, '2019-01-10 01:31:01'),
(109, 50, '2019-01-10 01:32:20'),
(110, 20, '2019-01-10 01:35:32'),
(111, 36, '2019-01-10 01:54:43'),
(112, 50, '2019-01-10 03:08:11'),
(113, 50, '2019-01-11 01:21:39'),
(114, 24, '2019-01-11 01:24:53'),
(115, 37, '2019-01-11 01:26:07'),
(116, 33, '2019-01-11 01:27:17'),
(117, 5, '2019-01-11 01:27:52'),
(118, 22, '2019-01-11 01:30:17'),
(119, 53, '2019-01-11 01:30:23'),
(120, 54, '2019-01-11 01:30:26'),
(121, 19, '2019-01-11 01:30:38'),
(122, 44, '2019-01-11 01:30:39'),
(123, 20, '2019-01-11 01:31:04'),
(124, 22, '2019-01-11 01:31:15'),
(125, 39, '2019-01-11 01:33:25'),
(126, 50, '2019-01-11 01:37:51'),
(127, 35, '2019-01-11 01:39:00'),
(128, 36, '2019-01-11 01:46:28'),
(129, 50, '2019-01-11 02:50:41'),
(130, 24, '2019-01-12 01:19:17'),
(131, 5, '2019-01-12 01:26:41'),
(132, 37, '2019-01-12 01:27:04'),
(133, 33, '2019-01-12 01:27:08'),
(134, 50, '2019-01-12 01:27:55'),
(135, 22, '2019-01-12 01:29:15'),
(136, 39, '2019-01-12 01:29:58'),
(137, 19, '2019-01-12 01:30:49'),
(138, 20, '2019-01-12 01:30:51'),
(139, 54, '2019-01-12 01:31:06'),
(140, 54, '2019-01-12 01:31:07'),
(141, 53, '2019-01-12 01:32:15'),
(142, 35, '2019-01-12 02:03:42'),
(143, 44, '2019-01-12 02:27:22'),
(144, 50, '2019-01-13 01:10:51'),
(145, 24, '2019-01-13 01:13:41'),
(146, 22, '2019-01-13 01:25:58'),
(147, 20, '2019-01-13 01:28:09'),
(148, 5, '2019-01-13 01:28:30'),
(149, 50, '2019-01-13 01:29:10'),
(150, 33, '2019-01-13 01:30:22'),
(151, 33, '2019-01-13 01:30:31'),
(152, 44, '2019-01-13 01:30:57'),
(153, 39, '2019-01-13 01:30:58'),
(154, 19, '2019-01-13 01:32:09'),
(155, 36, '2019-01-13 01:45:17'),
(156, 50, '2019-01-13 04:01:06'),
(157, 37, '2019-01-14 01:20:22'),
(158, 5, '2019-01-14 01:24:51'),
(159, 22, '2019-01-14 01:27:16'),
(160, 50, '2019-01-14 01:29:31'),
(161, 33, '2019-01-14 01:30:27'),
(162, 20, '2019-01-14 01:30:49'),
(163, 39, '2019-01-14 01:31:57'),
(164, 24, '2019-01-14 01:32:20'),
(165, 22, '2019-01-14 01:35:55'),
(166, 44, '2019-01-14 01:37:31'),
(167, 36, '2019-01-14 01:53:11'),
(168, 50, '2019-01-14 02:47:34'),
(169, 24, '2019-01-15 01:12:00'),
(170, 5, '2019-01-15 01:21:38'),
(171, 37, '2019-01-15 01:26:36'),
(172, 20, '2019-01-15 01:28:50'),
(173, 22, '2019-01-15 01:30:06'),
(174, 39, '2019-01-15 01:30:30'),
(175, 44, '2019-01-15 01:30:31'),
(176, 33, '2019-01-15 01:32:08'),
(177, 19, '2019-01-15 01:32:15'),
(178, 50, '2019-01-15 01:37:12'),
(179, 36, '2019-01-15 01:43:01'),
(180, 50, '2019-01-15 02:20:47'),
(181, 50, '2019-01-15 16:34:46'),
(182, 37, '2019-01-16 01:15:14'),
(183, 24, '2019-01-16 01:19:18'),
(184, 5, '2019-01-16 01:22:30'),
(185, 36, '2019-01-16 01:27:05'),
(186, 22, '2019-01-16 01:30:25'),
(187, 33, '2019-01-16 01:30:28'),
(188, 44, '2019-01-16 01:30:39'),
(189, 19, '2019-01-16 01:30:47'),
(190, 39, '2019-01-16 01:31:35'),
(191, 20, '2019-01-16 01:32:16'),
(192, 50, '2019-01-16 01:33:25'),
(193, 50, '2019-01-16 01:33:35'),
(194, 5, '2019-01-16 22:06:45'),
(195, 24, '2019-01-17 01:13:05'),
(196, 33, '2019-01-17 01:24:51'),
(197, 37, '2019-01-17 01:25:05'),
(198, 39, '2019-01-17 01:29:15'),
(199, 22, '2019-01-17 01:29:58'),
(200, 20, '2019-01-17 01:30:43'),
(201, 44, '2019-01-17 01:31:49'),
(202, 19, '2019-01-17 01:32:39'),
(203, 50, '2019-01-17 01:33:29'),
(204, 36, '2019-01-17 01:40:10'),
(205, 37, '2019-01-18 01:24:33'),
(206, 39, '2019-01-18 01:29:25'),
(207, 50, '2019-01-18 01:35:12'),
(208, 37, '2019-01-19 01:26:10'),
(209, 39, '2019-01-19 01:30:36'),
(210, 50, '2019-01-19 13:02:10'),
(211, 37, '2019-01-20 01:28:12'),
(212, 37, '2019-01-21 01:26:03'),
(213, 39, '2019-01-21 01:31:38'),
(214, 37, '2019-01-21 14:17:54'),
(215, 39, '2019-01-22 01:37:03'),
(216, 39, '2019-01-23 01:30:15'),
(217, 3, '2019-01-23 01:31:27'),
(218, 39, '2019-01-24 01:31:08'),
(219, 39, '2019-01-25 01:32:06'),
(220, 35, '2019-01-25 01:40:02'),
(221, 39, '2019-01-26 01:31:27'),
(222, 3, '2019-01-27 01:30:17'),
(223, 39, '2019-01-27 01:35:23'),
(224, 35, '2019-01-27 02:17:21'),
(225, 39, '2019-01-28 01:30:07'),
(226, 39, '2019-01-29 01:32:35'),
(227, 39, '2019-01-30 01:29:46'),
(228, 39, '2019-01-31 01:32:54'),
(229, 39, '2019-02-01 01:29:58'),
(230, 3, '2019-02-03 00:24:59'),
(231, 3, '2019-02-12 01:30:28'),
(232, 3, '2019-03-25 00:30:05'),
(233, 3, '2019-03-25 00:30:27'),
(234, 3, '2019-04-01 00:30:48'),
(235, 39, '2019-04-15 18:15:29'),
(236, 39, '2019-04-15 18:15:50'),
(237, 3, '2019-04-30 00:30:34'),
(238, 15, '2019-05-27 10:36:24'),
(239, 15, '2019-05-28 12:16:47');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hierarchy`
--

CREATE TABLE `hierarchy` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_superior_id` int(10) UNSIGNED NOT NULL,
  `user_subordinate_id` int(10) UNSIGNED NOT NULL,
  `relationship_label` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hotnumber_searches`
--

CREATE TABLE `hotnumber_searches` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `result_date` date NOT NULL,
  `schedule_key` varchar(191) DEFAULT NULL,
  `keyword` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `invalid_tickets`
--

CREATE TABLE `invalid_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `outlet_id` int(11) NOT NULL,
  `transaction_code` varchar(191) DEFAULT NULL,
  `ticket_number` varchar(191) NOT NULL,
  `result_date` date NOT NULL,
  `schedule_key` varchar(191) DEFAULT NULL,
  `amount` int(10) NOT NULL DEFAULT '0',
  `error` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `source` varchar(191) NOT NULL DEFAULT 'unknown'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `memos`
--

CREATE TABLE `memos` (
  `id` int(10) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `message` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_numbers`
--

CREATE TABLE `mobile_numbers` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `mobile_number` varchar(13) NOT NULL,
  `do_not_send` tinyint(1) NOT NULL DEFAULT '0',
  `role` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` int(10) UNSIGNED NOT NULL,
  `notifiable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offline_sync_logs`
--

CREATE TABLE `offline_sync_logs` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `sync_time` datetime NOT NULL,
  `result` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `outlets`
--

CREATE TABLE `outlets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'The owner of the outlet',
  `user_creator_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(191) NOT NULL,
  `address` tinytext NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'active',
  `is_affiliated` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `outlets`
--

INSERT INTO `outlets` (`id`, `user_id`, `user_creator_id`, `name`, `address`, `status`, `is_affiliated`, `created_at`, `updated_at`) VALUES
(1, 4, 0, '(STL-01) GGG OFFICE', 'Green Village Road, Hinaplanon, Iligan City', 'closed', 0, '2017-11-25 00:16:31', '2018-04-28 02:25:16'),
(2, 4, 0, '(STL-02) Esther Gabutan', 'Zone Begonia, Suarez, Iligan City', 'closed', 0, '2017-11-25 00:19:27', '2018-04-30 12:05:00'),
(3, 4, 22, '(STL-03) Sulfecio Garces', 'Zone 8 Pieces, Suarez, Iligan City', 'active', 0, '2017-11-25 00:23:44', '2019-04-30 00:30:34'),
(4, 4, 22, '(STL-04) Rhea Monteros', 'Purok 6, Dalipuga, Iligan City', 'active', 0, '2017-11-25 00:26:08', '2019-01-02 17:45:06'),
(5, 4, 22, '(STL-05) Grace Bataller', 'Purok 10, Dalipuga, Iligan City', 'active', 0, '2017-11-25 00:28:31', '2019-01-16 22:30:39'),
(6, 4, 22, '(STL-06) Severo Valendez', 'Purok 9, Kiwalan, Iligan City', 'active', 0, '2017-11-25 00:30:58', '2019-01-02 17:45:09'),
(7, 4, 22, '(STL-07) Anna Laput', 'Purok 8, Tag-ibo, Kiwalan, Iligan City', 'active', 0, '2017-11-25 00:37:06', '2019-01-02 17:45:10'),
(8, 4, 22, '(STL-08) Eduardo Laude', 'Purok 9A, Kiwalan, Iligan City', 'active', 0, '2017-11-25 00:38:26', '2019-01-02 17:45:11'),
(9, 4, 22, '(STL-09) Nenita Perolino', 'Purok 9, Kiwalan, Iligan City', 'active', 0, '2017-11-25 00:39:30', '2019-01-02 17:45:12'),
(10, 4, 22, '(STL-12) Jonathan Layug', 'Purok 10, Tambacan, Iligan City', 'active', 0, '2017-11-25 02:21:52', '2019-01-02 17:45:16'),
(11, 4, 22, '(STL-14) Estrella la Cerna', 'Purok Adelfa, Ubaldo Laya St., Mahayahay, Iligan City', 'active', 0, '2017-11-25 02:25:32', '2019-01-02 17:45:19'),
(12, 4, 22, '(STL-15) Emelia Erederos', 'Purok 6, Sablaon, Saray, Tibanga Iligan City', 'active', 0, '2017-11-25 02:28:57', '2019-01-09 01:20:51'),
(13, 4, 22, '(STL-16) GGG-Cams', 'Purok 8,  Zone 3, Fuentes, Brgy. Ma. Cristina, Iligan City', 'active', 0, '2017-11-25 02:30:58', '2019-01-02 17:45:22'),
(14, 4, 22, '(STL-17) P16 Fuentes', 'Purok 16, Fuentes, Brgy. Ma. Cristina, Iligan City', 'active', 0, '2017-11-25 02:33:11', '2019-01-26 17:52:05'),
(15, 4, 22, '(STL-18) Prescilla Uban', 'Purok 1B, Brgy. Buru.un, Iligan City', 'active', 0, '2017-11-25 02:39:02', '2019-05-28 19:03:19'),
(16, 4, 22, '(STL-19) Mediya Moyco', 'Purok 10A, Buru.un, Iligan City', 'active', 0, '2017-11-25 02:41:18', '2019-01-04 01:39:29'),
(17, 4, 22, '(STL-20) Ricardo Gallardo', 'Purok 14, Zone 5 Fuentes, Ma. Christina, Iligan City', 'active', 0, '2017-11-25 02:43:59', '2019-01-02 17:45:25'),
(18, 4, 0, '(STL-21) Dulcesima Dumaguing', 'Zone Army Village, Suarez, Iligan City', 'closed', 0, '2017-11-25 02:46:43', '2018-04-13 15:21:48'),
(19, 4, 22, '(STL-22) Marilyn Campugan', 'Purok 3, Buru.on, Iligan City', 'active', 0, '2017-11-25 02:49:33', '2019-01-17 01:32:39'),
(20, 4, 22, '(STL-10) Leoben Restauro', 'Zone Roadside, Suarez, Iligan City', 'active', 0, '2017-12-03 00:31:39', '2019-01-17 01:30:43'),
(21, 4, 22, '(STL-32) Zaragosa Store', 'Tag-ibo, Dalipuga Iligan CIty', 'active', 0, '2017-12-03 04:11:34', '2019-01-09 01:10:54'),
(22, 4, 22, '(STL-23) Gloria Payla', 'Suarez, Iligan City', 'active', 0, '2017-12-03 04:16:42', '2019-01-17 01:29:58'),
(23, 4, 22, '(STL-11) Juditho Echavez', 'Purok 3B, Gumamela St., San Roque, Iligan City', 'active', 0, '2017-12-03 12:09:49', '2019-01-02 17:45:14'),
(24, 4, 22, '(STL-26) Rada\'s Residence', 'Brgy. Bagong Silang, Iligan City', 'active', 0, '2017-12-04 02:47:23', '2019-01-17 01:13:05'),
(25, 4, 0, '(STL-27) Eric Enriquez', 'Purok 1B, Brgy. Buru-un, Iligan City', 'closed', 0, '2017-12-04 02:49:55', '2018-09-28 09:45:29'),
(26, 4, 22, '(STL-28) Maricel Saren', 'Zone 8, Brgy. Santo Rosario, Iligan City', 'active', 0, '2017-12-11 13:57:01', '2019-01-02 17:45:33'),
(27, 4, 0, '(STL-24) Rolando Saycon', 'Purok Adelfa, Brgy. Suarez, Iligan City', 'closed', 0, '2018-01-05 04:25:31', '2018-09-28 09:45:24'),
(28, 4, 22, '(STL-30) Remy Sepe', 'Purok 8, Lansones, Brgy. Fuentes, Iligan City', 'active', 0, '2018-01-05 04:28:35', '2019-01-02 17:45:35'),
(29, 4, 0, '(STL-39) Myrna Bariga', 'Zone Riverside, Suarez, Iligan City', 'closed', 0, '2018-01-05 04:30:37', '2018-09-28 09:45:53'),
(30, 4, 22, '(STL-13) Simplecio Aranas', 'Purok 11, Tambacan, Iligan City', 'active', 0, '2018-01-05 04:33:53', '2019-01-02 17:45:18'),
(31, 4, 22, '(STL-25) Julieta Ranara', 'Purok 13, Sitio Timoga, Iligan City', 'active', 0, '2018-01-05 04:37:53', '2019-01-02 17:45:30'),
(32, 44, 0, '(STL) GGG OFFICE ADMIN', 'Suarez,  Iligan City', 'closed', 0, '2018-02-02 23:49:21', '2018-04-13 15:21:17'),
(33, 4, 22, '(STL-31) Ronnie Albastro', 'Zone 6, Bagong Silang, Iligan City', 'active', 0, '2018-03-01 16:49:39', '2019-01-17 01:24:51'),
(34, 4, 0, '(STL-34) Naomi T. Balanay', 'Zone 4, Del Carmen, Iligan City', 'closed', 0, '2018-03-03 19:19:10', '2018-09-28 09:45:43'),
(35, 4, 22, '(STL-35) Mark Pagente', 'Lambaguhon, San Roque, Iligan City', 'active', 0, '2018-03-03 20:42:33', '2019-01-27 02:17:21'),
(36, 4, 22, '(STL-36) Rose V. Sarip', 'Phase 1, Nonucan, Ma. Cristina, Iligan City', 'active', 0, '2018-03-07 14:34:30', '2019-01-17 01:40:10'),
(37, 4, 22, '(STL-29) Wilfreda Sebaga', 'Purok 10, Abandon Road Dalipuga, Iligan City', 'active', 0, '2018-03-08 15:17:03', '2019-01-21 14:27:11'),
(38, 4, 0, '(STL-37)  Joy Radoc', 'Duranta B, Santa Felomena Iligan City', 'closed', 0, '2018-03-09 01:08:02', '2018-09-28 09:45:49'),
(39, 4, 22, '(STL-38) Adela Cuadero', 'Phase 7 Orchieds, Lambaguhon, San Roque, Iligan City', 'active', 0, '2018-03-09 13:40:57', '2019-04-15 18:15:52'),
(40, 4, 22, '(STL-33) Jerelyn Echavez', 'Purok 2B, Santan, San Roque, Iligan City', 'active', 0, '2018-03-11 15:54:46', '2019-01-02 17:45:42'),
(41, 4, 22, '(STL-21) Dulcesima Dumaguing', 'Zone Army Village, Suarez, Iligan City', 'active', 0, '2018-04-04 13:34:35', '2019-01-02 17:45:26'),
(42, 4, 22, '(STL-40) Archem Malonhao', 'Purok-3, Luinab, Iligan City', 'active', 0, '2018-04-26 18:42:07', '2019-01-02 17:45:46'),
(43, 4, 22, '(STL-41) Vicenta Mariquit', 'Purok-2b, Katipunan, Hinaplanon, Iligan City', 'active', 0, '2018-04-26 20:17:34', '2019-01-02 17:45:49'),
(44, 4, 22, '(STL-42) Isaias Gacutan', 'Blk 27 Lot 2, Bayanihan Villages, Sta. Elena, Iligan City', 'active', 0, '2018-04-27 22:37:09', '2019-01-17 01:31:49'),
(45, 4, 22, '(STL-43) GGG MAGSAYO', 'Camague, Highway, Iligan City', 'active', 0, '2018-04-28 01:36:06', '2019-01-02 17:45:53'),
(46, 4, 22, '(STL-44) Eleuterio Arnoco', 'Block 2 Lot 5, Zone 12, Bayanihan Village', 'active', 0, '2018-04-28 01:37:46', '2019-01-02 17:45:58'),
(47, 4, 22, '(STL-47) Galileo Velado', 'Rosario Heights, Tubod, Iligan City', 'active', 0, '2018-04-28 01:38:47', '2019-01-07 01:31:32'),
(48, 4, 22, '(STL-46) Felicitas Ajeto', 'Greenhills, Suarez, Iligan City', 'active', 0, '2018-04-28 01:40:25', '2019-01-02 17:46:04'),
(49, 4, 0, '(STL-01) GGG Office', 'Hinaplanon, Iligan City', 'closed', 0, '2018-04-28 11:07:30', '2018-04-30 12:04:46'),
(50, 4, 22, '(STL-01) GGG Office', 'Hinaplanon, Iligan City', 'active', 0, '2018-04-30 12:59:56', '2019-01-19 13:03:39'),
(51, 4, 22, '(STL-02) Esther Gabutan', 'Suarez, Iligan City', 'active', 0, '2018-04-30 13:02:30', '2019-01-02 17:45:03'),
(52, 4, 22, '(STL-45) Antonio Pasay', 'Block 11 Lot 2, Sta. Elena, Steel Town, Iligan City', 'active', 0, '2018-05-07 20:28:54', '2019-01-02 17:46:02'),
(53, 4, 22, '(STL-48) Roland De la Cruz', 'Macapagal Ave., Tubod, Iligan City', 'active', 0, '2018-05-24 15:15:27', '2019-01-12 01:32:15'),
(54, 4, 22, '(STL-49) Rogelio Englatira', 'Baraas, Tubod, Iligan City', 'active', 0, '2018-05-24 15:30:10', '2019-01-12 01:31:06'),
(55, 1, 22, '(STL-50) GGG-Alfs', '1st East Ext., Tubod, Iligan City', 'active', 0, '2018-06-05 12:23:13', '2019-01-02 17:46:08'),
(56, 4, 22, '(STL-46) Carol Pates', 'Purok 13 Tominobo, Iligan City', 'active', 0, '2018-10-04 12:05:06', '2019-01-02 17:46:03'),
(57, 4, 22, '(STL-51) Leofe C. Julito', 'Purok Dahlia,Acmac Iligan City', 'active', 0, '2018-10-04 14:12:52', '2019-01-02 17:46:08'),
(58, 4, 0, '(STL-52) Riemar Gravador', 'Sta. Felonies, Acmac, IC', 'active', 0, '2018-12-11 14:45:13', '2019-01-02 17:46:09');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payouts`
--

CREATE TABLE `payouts` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `outlet_id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `ticket_id` int(10) UNSIGNED NOT NULL,
  `bet_id` int(10) UNSIGNED NOT NULL,
  `winning_result_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `outlet_id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `ticket_number` varchar(191) NOT NULL,
  `result_date` date NOT NULL,
  `schedule_key` varchar(191) NOT NULL,
  `print_count` int(11) NOT NULL,
  `is_cancelled` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_cancellations`
--

CREATE TABLE `ticket_cancellations` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ticket_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'The user that made the transaction.',
  `outlet_id` int(10) UNSIGNED NOT NULL,
  `transaction_code` varchar(191) NOT NULL,
  `customer_name` varchar(191) DEFAULT NULL,
  `sync` tinyint(2) NOT NULL DEFAULT '0',
  `origin` varchar(191) NOT NULL DEFAULT 'outlet',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_admin` tinyint(4) NOT NULL DEFAULT '0',
  `is_superadmin` tinyint(1) NOT NULL DEFAULT '0',
  `is_read_only` tinyint(1) NOT NULL DEFAULT '0',
  `is_usher` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_password_updated` tinyint(1) NOT NULL DEFAULT '0',
  `is_betting_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `api_token` char(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `status`, `is_admin`, `is_superadmin`, `is_read_only`, `is_usher`, `remember_token`, `is_password_updated`, `is_betting_enabled`, `api_token`, `created_at`, `updated_at`) VALUES
(1, 'Joshua Paylaga', 'joshua@stl.ph', '$2y$10$EFD0dwaWcj14HP3LxX.N3ej/JFVC0dDaMeyGif6Rz4zSGrq8D3NDa', 'active', 1, 1, 0, 0, '0hxmqN5OzkoPzKq0AVKq4b4kYrZy0oOmW2nyvUN3WkAAzxgNp0Redi9eiMMX', 1, 1, 'NMilM9Nk5t6dVtba4v5EmpDzjBVKiHkMvoqeAR7LqQj5QC0SwSOUqRkm4q8z', '2017-11-24 23:40:45', '2018-05-15 00:04:36'),
(2, 'Richard Santiago Illescas', 'richard@stl.ph', '$2y$10$hxmMYyOT8p7VUgpg1VTsmuUxIB/jLvylQhYPfdVbtjYfHdns1ps7e', 'inactive', 1, 0, 0, 0, NULL, 0, 1, 'ZFvRmweRZpnNKxuGIozZinFY7ToOjAp2BN4AfBpWz2omctR2DiilFK1SqZsd', '2017-11-24 23:42:12', '2018-05-15 00:04:36'),
(3, 'Robin Abas', 'robin@stl.ph', '$2y$10$/3tgs.5O/Gxbi3.a7BPeo.FsBaXlpCpTw9DBk9jf7tBB6oXjuk9w.', 'inactive', 1, 0, 0, 0, NULL, 0, 1, 'fAuzbRb3zJy3cVytAjGF5rfS6p7gSbON9WbppQPz2tgxCmGpoiSMGjrzZLvA', '2017-11-24 23:44:01', '2018-05-15 00:04:36'),
(4, 'GGG OFFICE ADMIN', 'gggofficeadmin@stl.ph', '$2y$10$7W2uYXcbbP60q8TWFDQFruTTGgGrgu2LX7HR/PNQU74lPkHvcBXkK', 'active', 1, 0, 1, 0, 'gBnzz7gB3bEjKefS1gx8HCGCPjhJtjX5zHTs461EkzyivOcLQsmvkS9lyLOk', 1, 1, 'nh3SrOUFh6fTsON1fy2EBHaYDCeghyLOX181xEOLYhzICGOGwlXG5QXrinpD', '2017-11-25 00:12:52', '2019-01-12 13:58:20'),
(5, 'Lea Bentillo', 'jnelmida@stl.ph', '$2y$10$Cz0n5xmAHkFJebePpxG28ee2a83hCMtxaDe9NA1D5nhOKx493A2Sa', 'active', 0, 0, 0, 0, 'a67leZHqCeRFpZfmg9YoslZQnQzwQLFACYAhqaCNSjNxqZH7OAqOrCwNqqep', 1, 1, 'ibPm9tEoJiiKl8upSl5IB3K0CnftZSAy9luVQiWbO3IbEI0siqCav9OazX2l', '2017-11-25 00:22:33', '2019-05-27 00:16:41'),
(6, 'Arlene Liganan', 'tpaculba@stl.ph', '$2y$10$UVNLZaZ/mpvAeHnBQHa2kefsE3y6W8iYXzYVH7A.HCz2It7g2AlD2', 'active', 0, 0, 0, 0, 'lNerzwgbKKEVE3KH0iH0yxdXJqStPReRWOkEyeQcdq2XzVi4dTOegKFQwxQ5', 1, 1, 'mrmjZCi2DMfa1fqRSkKLAsCJM06t8EY9ZeDLvQeHoP6bOEVhi8JinE0TSN5R', '2017-11-25 00:24:51', '2019-05-13 17:37:11'),
(7, 'Florimae Majorenos', 'fmajorenos@stl.ph', '$2y$10$rQpAIOjYEa0nX8dqXoL8tOAH/fcdF7jM3npsb1t8kClyFAeGtLMuK', 'active', 0, 0, 0, 0, 'Kl0y8ZtY8KUw42eAK5dqIYOnCYynR3nVnt7tXyFtY4CwfPU6vBCVqdqLUcAw', 1, 1, 'MXLhqb4cS39f4vSXZPogNoTS8OvMbHVTy1AFW0fDwInWWoGEjz3xs3W2kgE2', '2017-11-25 00:27:06', '2019-05-13 17:37:22'),
(8, 'Shaiky Secula', 'ssecula@stl.ph', '$2y$10$671tsfBzMw1I8QYoh1TMcOPj.5veTXOJRykasYDH3f86CFaWuNZ5.', 'inactive', 0, 0, 0, 0, NULL, 0, 1, 'zDlGaDFRamjQ6i19ZmcnogLAgajLiUJ9LKSwmF88LJjOFxZISfjJu668mH1M', '2017-11-25 00:29:54', '2018-05-15 00:04:36'),
(9, 'Rexlyn Cueva', 'aperolino@stl.ph', '$2y$10$ZCLE.vdPeg3bOmxun1ssqOvvOi9HL3OhzJnA47MgXRS8AgYTq/rlW', 'active', 0, 0, 0, 0, 'yAqlSHmTxEZjnmcr9EJNiacobbaxbnZpH8nxoWslFIlvWMCgkhona3Ms8wD6', 1, 1, 'jrt9eS3ai6FBd0Pcb0TjJJz19DWY6UJiPh2QbhUVY93dxVHjsIBtvnenk2Rd', '2017-11-25 00:40:32', '2019-05-21 00:00:31'),
(10, 'Julie E. Buhisan', 'jbuhisan@stl.ph', '$2y$10$OB9DwGa3SnMOf58GZ69dnOhTo/AdIaZ.cfIR52gk8tKzUYzwiclzm', 'inactive', 0, 0, 0, 0, 'wDfpkOtfqbc6RMXtwMC1E8M3LDM2PD3ehMyzlRKFRLjJqAC2CPCUmY6IMBpD', 0, 1, 'QY0yQ4DwfIeGoOYqDJZyXZziJv8yI3a0ZC1qMnreNNStoFbQkFASFlM9OGmk', '2017-11-25 02:22:49', '2018-05-15 00:04:36'),
(11, 'Julie E. Buhisan', 'jengracia@stl.ph', '$2y$10$1.CXb8QI/FtTJElknvb8VessiVzFGavguwPqlg6LYfd8HHKG0DQZq', 'active', 0, 0, 0, 0, 'YdhhEa0D0xpe2X2nG29ikWeFQRPHn2co9eII5cmTXD4Z72Db2hlnOusuacTN', 0, 0, '7yR7COUjpbQYtnOK5LT1JX7rRKob7qVHgIJEnFzbwKXGZfRSGcEsE1WcGeHS', '2017-11-25 02:23:31', '2019-05-29 00:24:40'),
(12, 'Emilynne Casas', 'mcanedo@stl.ph', '$2y$10$VGuzAyR/nLJOBuSYw3hmCeUqWf3anP.0EsjpA0UeP30Di9fdVGYt2', 'active', 0, 0, 0, 0, '5MXP75bddcDr3fW6VbgNAlTFdf0YyTTNr2cxVfzCPv4gVJjClWYSk6d8ViIt', 1, 0, '1z4hZixzS1vh0Tn45tdiVdrHfc8wNS0vwUyh3fFcZHJV9DfA60t0u2cuPW8D', '2017-11-25 02:26:26', '2019-05-29 00:32:36'),
(13, 'Mary Je Batara', 'elagrosas@stl.ph', '$2y$10$LEsWyR7tkPd/.qG0jGkJyOowEzs2p4kjnneAtjujSHrNgeHiE8WNm', 'active', 0, 0, 0, 0, 'k4OIx5lB3EfLIl8uTslQHv8M4oTMts3jQ2xPzqscvuCTkNl8ur5AgwPaznMl', 1, 0, 'xQrxXOZsUj9J1iWqNlT2x5rcnDsqzvDD9lc1KzXSMgtHVTH7lo8j65vGJ9Be', '2017-11-25 02:29:46', '2019-05-29 00:32:15'),
(14, 'Maria Fe Tanajura', 'mftanajura@stl.ph', '$2y$10$70NAZwPYD265kXz57HNLY.fTnC8qKWSglttB.7oEEUyQsWqqPYL2W', 'active', 0, 0, 0, 0, 'ZFVl92kXqneKN9QPSCuDRtClrG64uTSE9MAN7gjmXSZr8lIrRDZtGCK7YOin', 1, 1, 'UCdcHjSi2AhL13Uw56NNMXLBSExOqsGVNrVUyr3g2kosxiR3rMWgKCdgJfSt', '2017-11-25 02:31:55', '2019-05-13 17:37:48'),
(15, 'Elaine Moreno', 'squitat@stl.ph', '$2y$10$Ze/5uVzIStSUQbedKkfIu.tA3So/hjZ1HeybW/4m3FMtQuBiG0ZP.', 'active', 0, 0, 0, 0, 'Y6zz9uFNmO3FkUWyaUD4pdQGyyDcQjghxiZfQqwbeIL1w0LYM1Bpkx1L15my', 1, 0, 'LII2liws0uv0LaQ9RECe3x8hWanWPlkFgbJYw6apMkiTcRQsJvvmYO3lYdQJ', '2017-11-25 02:33:59', '2019-05-29 00:32:06'),
(16, 'JOAN TANGIAN', 'joan@stl.ph', '$2y$10$7VR2cR6VPVdkgtLrzLcFteaeolS/saZtgR8VoP7FBch78Pb7zgPK2', 'active', 0, 0, 0, 0, 'jT7Qmuhq6rOdGOJUgOaMMhXBDHfWkWCVFTCT7JIR9sOIR6PasDf6Ihie5oYL', 1, 0, 'fIL8cmKGg7KSIRns4QOpojhwZzKfhtMGUP6ZYN06YSL9wigK3Yf6SIlZvNo3', '2017-11-25 02:37:34', '2019-05-29 00:07:48'),
(17, 'April Love Uban', 'auban@stl.ph', '$2y$10$4ijbd0qtWkvXImhshLQAauKil3Xj9wmPLKPng1iy28vqdocAbeJFy', 'active', 0, 0, 0, 0, '3K4X3NquSPpXC6NLZshUoVOIovN1ocxTABqo2XNw6wmWtJyx1euuI73RIIGk', 1, 0, 'Mic511FLucyfI1C1ZajeaAvtOFoo9sKcC2yL4KrtGJCMVPIMU2LSKQOWuHlq', '2017-11-25 02:40:19', '2019-05-29 00:37:22'),
(18, 'Geraldine Uy', 'bmoyco@stl.ph', '$2y$10$IJF3Y4/D2KD5oVuppyvJPeK9loLmW9bticoUoWjjH9H3zJfqrl77m', 'active', 0, 0, 0, 0, 'BPgPsYPIDrHK0x5jkP5gShYPRTCchB4yuiZ3pb0KrP5NknLciIEMjPmCPExb', 1, 0, 'OypiWzPQ6rGXUNghE61V0Sb9UZtK8rwVn4FNTHG0AG7kB9aid1oVaZZMgQP9', '2017-11-25 02:42:21', '2019-05-29 00:32:22'),
(19, 'Audrey Rose Gallardo', 'agallardo@stl.ph', '$2y$10$Zagqx.l9DU8GLY26AwpxdeRA6LLygZ4U3AVMA4h9cLkAthF/4XHqC', 'active', 0, 0, 0, 0, 'dCg2TWW5RvPZDsFWy8JgLRYXr5MmL3w7zEYZPzMdpOFxT0PJjSAqMjqBUcuk', 1, 0, 'SHTKI3IrMI9190gyBlTkV1u2gKoVWAoX0qQVBODMtVZbE2A3jYfgFseSkYam', '2017-11-25 02:45:20', '2019-05-29 00:37:07'),
(20, 'Ralph Ryan Dumaguing', 'mromero@stl.ph', '$2y$10$KA1Ty3/fv.hcxIp13ZIQ5OUVEwQBjsLb1j/qMCw.yUqrzNx44iWxG', 'active', 0, 0, 0, 0, 'UgeSlMTWvXCXbV6Ky1OuHABXJ73BQgqYVhy5j8aXrp6s8luX9RhKkk6wKH3f', 1, 1, 'DtUfso3QI1vovBvkyxdJ9uzRFtdo05IQXiYFLpjy7z8QpcqHBVVVajO0rHWg', '2017-11-25 02:48:00', '2019-05-25 23:44:09'),
(21, 'Lealyn Alvarez', 'bllanes@stl.ph', '$2y$10$rxOi1BoolWf.2jPGE3DWG.99G.Fyk5KEH7V75j2UPhaQhenQyju0W', 'active', 0, 0, 0, 0, '7jp8yZt23hzNLi7UpFuYgeq7OUy0HnSnDIOOjN6KOvgPdo5pgo1tjQQMzkW3', 1, 0, 'F8BLBP1lQzHjAsVnIutoe09uTxa7erW9hqOlhBgL204h4Ro5W6eh2VlzC0nV', '2017-11-25 02:51:17', '2019-05-29 00:25:42'),
(22, '3A8 Gaming and Entertainment Inc.', '3a8gaming@stl.ph', '$2y$10$K/D2flrR5ykftfxqeG5/wu01CTO7Pjuc13TkXPGXkKXf6C/XP9b5u', 'active', 1, 0, 1, 0, 'o95k8Rp7gBLCBKivTW2gevh97CXfGuMgykwB25WcecpeXjyWPvPqn9seZ4zT', 1, 1, '16TqhsACNPl3mqQ6iTzaz7cDBHXQKVOxWF5NkZNI8iI6S2ceuLFMfqlRg2Z8', '2017-11-25 04:38:54', '2018-09-01 18:54:43'),
(23, 'Joylyn Tayros', 'jsinagpulo@stl.ph', '$2y$10$DFKqbi9t1cDblcG7QdBv8uVLMvEekqZZ8vZP8bfxuq3/YJ44PcIQS', 'active', 0, 0, 0, 0, 'dPwA8hh5z7xNHSv2qhQiZn8roavk5ausa3eQRny67NEoqiJZVWXQIfwUwVga', 1, 1, 'babGlLRBroZegPt1ek1DD0iJkNw21XPq2ChIgWxmMeqjPOK06ihc2fIzP5f8', '2017-11-25 10:58:31', '2019-05-26 12:07:10'),
(24, 'Marlon Magsoling', 'mmagsoling@stl.ph', '$2y$10$WU.QQ.cP4K.nJPyvFU9rEOhzRJVNYML5TmCKcKn.JiHj0d0MJnZs.', 'active', 0, 0, 0, 0, 'fa3ySA4qbf36Dn8kSCBwhb6qoXoVhcLlxn1yNuusHzBhubchTY7MXEs63omV', 1, 0, 'BH26nEccvMk7PSf31K0Dq75frBBIqvzBeDvAGn0LfSew8Gn8zvkm82QwYWiP', '2017-11-25 11:43:56', '2019-05-29 00:17:10'),
(25, 'Joan Partulan', 'rfamacion@stl.ph', '$2y$10$T5UCH9rIAIWcwc5xLixVmeIX4x5DhlounJhtOPjeHvceWQZ2oA/oi', 'active', 0, 0, 0, 0, 'yh5MOdCLQFx9V5N5Nbrw3fFXOWemRxgSH9VH7wkSuqfH1YAtvBqa5QCyR8vB', 1, 1, '8IT33mgF4TgtbcYze4nd5UtBUq6p872KnvSQGfqADv5R79xJ8vfqdA6UKJ2k', '2017-11-25 15:10:15', '2019-05-27 00:04:32'),
(26, 'Angie Anoos', 'rvina@stl.ph', '$2y$10$yvuVZc7rOpv6jvygfZz2p.Fs5Y21Dx9MsWdwUdMyOWHzazQVC.KJ6', 'active', 0, 0, 0, 0, 'bi2Ncb14YGXnNCbB4NIzrP3BzhtvQiiJFZiOnrvuF8ddFQUlJTw0QCVOIbUq', 1, 0, 'IZa1aZGdlGpNdmNGAiYTp4DqGMvRCcZV79FTIZINEV6YwtNXtTg6QrNIpTZY', '2017-11-25 15:40:53', '2019-05-29 00:24:57'),
(27, 'Rey Marban', 'rmarban@stl.ph', '$2y$10$MBHChGzf7PPA6AqrzdXt3.eAq4JWfSL2CWKpF6W7uuQal3RpaySCW', 'inactive', 1, 0, 0, 0, NULL, 0, 1, 'Di9riurEM5dSBVJTWZexaiGhGNZLRYnN2aDztESYDd4fN3KINyZWySbiccIl', '2017-11-26 00:04:43', '2018-05-15 00:04:36'),
(28, 'Arcenas Girlie', 'btanan@stl.ph', '$2y$10$jH7Uo/CO5F9Qd65xhu/T6.jrQnm41L.dbSySCkNgH6IpXRRPnzRI6', 'active', 0, 0, 0, 0, 'g4cWelSP63SB17a6nZzCdRjqV6YAVZb92gVw1DOqmpERVLLxhQSxvUGX2ohn', 1, 0, '1CfeTeZitAC39vFITmwlVkOQsiu6kKW2236NhoPw2znTUhuC5GoGWUbsSXdN', '2017-12-02 05:21:57', '2019-05-29 00:30:56'),
(29, 'Christian Restauro', 'crestauro@stl.ph', '$2y$10$Qeu.1NX1PlMmV.hsy2sHH.lLmJy5cQ2RPMi/NVgmOOP3T5pLY2o0a', 'active', 0, 0, 0, 0, 'QxdHkkp6f3urxkMgzc2NTvrSZpGRlcojcDiFYnWHrBbasqxQcF5wNvm86r59', 1, 0, 'K3HubQ6Uookkb2RZN4iUJFGTEHJAY1XDM6lc2XG2cEba4810on1Tf1sP6dOH', '2017-12-03 00:33:19', '2019-05-29 00:35:48'),
(30, 'Lovely Mae DelaCruz', 'ldelacruz@stl.ph', '$2y$10$w.PGDbAPXW0ct5wKUA8B5e63K3iBqwb6wF5OmfdihWUnkfVaVhXiK', 'inactive', 0, 0, 0, 0, '406j0e08tveUbkPm1H7CflF4UwbnzwucKXDSEKlVuzi6zJu2lWi3RZ6uNu5C', 0, 1, 'fxrmZzFmRb7T0YLy4GsqKdrsUBLe1HqUtjH9k9yO221PWADn6UvWtSqmjNhp', '2017-12-03 04:12:54', '2018-05-15 00:04:36'),
(31, 'Rold Xavier Tugahan', 'rtugahan@stl.ph', '$2y$10$VxhGutdRJR5NziaPI1sA1et67nTAjPHJxXQ6yd.Aaotui7CEbmB8K', 'active', 0, 0, 0, 0, '88IT8Cioghi6AbGhwKVdu3QFnsBkVoRWqwXMnRmovlL5J6UJujmCwsXrvJCJ', 1, 0, 'ar7krGvyEthd2yvAEB9aDutnNMy8Hb4DTFh0kDTsYPGzJDeoEd5M2HkNfwvF', '2017-12-03 04:17:39', '2019-05-29 00:30:33'),
(32, 'Zaragosa Store', 'Zaragosa@stl.ph', '$2y$10$JslAM5qV/AKTjtvz6gbM.evsAENDP2nxf3BM7ZIJbJiOx5i62kh1W', 'inactive', 0, 0, 0, 0, NULL, 0, 1, 'lTEEf7I7CXzuMYAQdKhteO35aGuCWH54zZxOJLBZhVvor3PJplFYnfHPbWzr', '2017-12-03 12:09:49', '2018-05-15 00:04:36'),
(33, 'Gissile Lacapag', 'glacapag@stl.ph', '$2y$10$9qlOv0kl3cRWkjFb8Nfblu8TSl40Tz6YPG1rFLZk9Lep4n7VYZ63e', 'active', 0, 0, 0, 0, 'nyKp5DOug8ycRVkWO3hKySMlFXzbmYCEPRSyWTYA5QH9iSw7bbpuTj3phpjn', 1, 0, '4DChFQvqOTw72Jr2ggw0cRA9zUIYmqmCquTegm11ENlC5MoEeNfJ2jYKwVvc', '2017-12-03 12:25:35', '2019-05-29 00:01:19'),
(34, 'Hannah Faye Bracuso', 'hbracuso@stl.ph', '$2y$10$rXM.fAqmR5Qh/ye4DNXVDu1SVFyEOL05Xv6O4PGjGDLNIyoeVxUr2', 'inactive', 0, 0, 0, 0, '4KdAMYpNuXaqRcJpfMdwRDGd5uqj2Y5CjMGTBGE762nVSPkKWINSRMayhbZ3', 0, 1, '8g2hpiFyg8RTjCU8vdnaum3xQCJaj46JCmw0ckjYmfSmYcGARrI8JsOMm7Lh', '2017-12-04 02:48:21', '2018-05-15 00:04:36'),
(35, 'n/a', 'ebaje@stl.ph', '$2y$10$fbmbhKr0ciAoJ9yXOZJz1.j7XhW3adLYp7tPE.ak/VFPbgJejFUnm', 'active', 0, 0, 0, 0, 'ib8WsIpXYDmINtB996P0RBnpSx3KHGb9smCvDcP5KAEvDAl5Lcy3XsyUQEJA', 0, 1, 'VJCGeUapfnNFkf8OekZMziBZF70F9FwosiYCWypaQz7eScylDcP1ws9SlRhl', '2017-12-04 02:50:55', '2019-05-13 17:38:11'),
(36, 'Maricel Saren', 'msaren@stl.ph', '$2y$10$CKraMOMWk4M8Km/UxIYKwOX6X8/9RAU8cuJH0gL00IgHOB0Wef0PW', 'active', 0, 0, 0, 0, 'xI0CiVtOnvhZT2he0HMEIsgufpRXDYhYMC1gANKKtHzjeoBl1tnUgHmTkuuv', 1, 1, 'MUG9zzU7AHFl1dre2ZO6z9kjGRif0XAgZrq8e5vpZDWRQeOylVzhB005y235', '2017-12-11 14:09:28', '2019-05-13 17:37:51'),
(37, 'Annalen Apa-on', 'bpatra@stl.ph', '$2y$10$5ZtMW3ZQFCKe0LK1ANsahezjS1EmALmZ.Qn9wq2HmU9KY3vAq4C2G', 'active', 0, 0, 0, 0, 'M217R6UvqzU0D8alRSScGWLDtDBQxfwAsXEdsVFRReNtJWGQo3oJgnyizIkO', 1, 1, 'a5g0sd1UXHf2cH3866USFIHOJpI0aayqgLB5FgQTrsc0DoIMoxus9b8jJLhw', '2017-12-29 00:34:40', '2019-05-13 17:37:08'),
(38, 'April Jane I. Gonzales', 'agonzales@stl.ph', '$2y$10$3nQlU/JBmZjBGaOJq.wApuUipVhR4L7euEMTTeVeZyPbIiVDPWZoi', 'inactive', 0, 0, 0, 0, NULL, 0, 1, 'oeh6gIM8vuP4HCXq0tZALysCVJxFZYSDA37im0wdDTIZAKVNG8dJs3J5m7b6', '2018-01-05 04:27:17', '2018-05-15 00:04:36'),
(39, 'Rachel C.  Guinar', 'pcarino@stl.ph', '$2y$10$FYPeTe8HjJYVwIHwH7Lo4OQbhpMVTdjOyJdu7BtP2EZ38GBwemNrC', 'active', 0, 0, 0, 0, '8gOgfgKjN7YXfJxNFAUQEpUExDUMTkHosIvltXzMrmeIkQIRHGGgyxNJdLhV', 1, 1, 'JAYcFb8h7yvwy2x5wfW2kBYMzz2wxCrdJpP9OW06eCZ2jivfmH9xWWsCVIlB', '2018-01-05 04:29:34', '2019-05-28 00:29:33'),
(40, 'Quennelyn Famacion', 'qfamacion@stl.ph', '$2y$10$y2KFXKhWHquhtHYtyZ9g/eh2kaY0bOAjseNHuQ9BRdaxP7hWoOThu', 'inactive', 0, 0, 0, 0, NULL, 0, 1, 'QxG5oUBJy3CkxnKCnBzDs6E0cInDW5v7E5th1SM2xi4EDTF2O0xXlhTiz5EU', '2018-01-05 04:32:04', '2018-05-15 00:04:36'),
(41, 'Michelle Ybas', 'gsumile@stl.ph', '$2y$10$4202hrva04MyUJWA3snF7OGMa12jrHi0ynKzm7vBVlX7kVUgQwV7a', 'active', 0, 0, 0, 0, '5QzhnCR1l9qgU4M70tptm8i5LQFUE2oeKCiKAZAxlyrHhV4pyGLpyLXTrCDb', 1, 0, 'U9FJPj7GiWysFfOrMlQluaHzScrvynrNWHKL87u8k2GxfwESezbBogP5599R', '2018-01-05 04:34:51', '2019-05-29 00:29:09'),
(42, 'Avon Secretaria', 'jranara@stl.ph', '$2y$10$OvvlEY5IsYMLBe2UPGdIp.hMvpWPYQioeoqUDMu4nt895WNcqO.nK', 'active', 0, 0, 0, 0, 'I1bZYG63NeyrbOlGJccPHFqvoNeVV6F55TDydaJC6EUkB4W6unVzzI6NHOXD', 1, 0, 'Kca5CI1XerFCVtqGiCGpGp9D0fMIBR2J062NRNe5Jr6HfNtXpD6QFeB4bGSM', '2018-01-05 04:38:50', '2019-05-29 00:34:42'),
(43, 'Ronald P. Alinsonorin', 'ralinsonorin@stl.ph', '$2y$10$Cj598kRxpz/v/33H7IDkBO.yOcPy5USiYu36V8nWca/0OsYEanp9W', 'inactive', 0, 0, 0, 0, 'kpXJeAuJ1XAqwjhhM9tpLb4LrJeuxs3EQFDgrFQ25FKpbNHIodOnKf6Lgg01', 0, 1, 'mw99ip43dJoSFixkNOcC8bqQ3fSFKbvYnwuHlRwZL7pYEdwbLrQBqGjjKDSl', '2018-01-19 04:23:11', '2018-05-15 00:04:36'),
(44, 'Daniela Saycon', 'dsaycon@stl.ph', '$2y$10$dnF3OADCMEYRirTZu/Vm6.jLx6sKIImlQ8rjCvMxI12CUZrPgY2na', 'inactive', 0, 0, 0, 0, NULL, 0, 1, 'cNahGU0jJBNxHF9dXxON0z8fAnwMuHmmp3uzD7bur52bPDmbuXJxodtLvcAi', '2018-02-02 23:49:21', '2018-05-15 00:04:36'),
(45, 'Test Account', 'djsaycon@stl.ph', '$2y$10$CUpdzM7uaXEjtqpuum2zJeqiO.cVFP3w4Q76PvrhQvgqw2QknbyEm', 'inactive', 0, 0, 0, 1, 'OtG963Nflxi9ccZ1ysjrSJgMfaWhiEVTB6GudE5LppxygUtz0lK4RWy2TAVq', 1, 1, 'Smhz7Egzf9HUSy3lXVsjyP3ow0dm82zvdEkpUzBrWMNMi2aWUlKAn22UK1ZP', '2018-02-02 23:52:56', '2018-11-16 18:21:02'),
(46, 'Abigail Bacsarpa', 'hayodtod@stl.ph', '$2y$10$B/9szLLCdCBE6g3RhDpDVu6PU4ULo9eiaoISvCP4JuQ7wf8ZtDtvq', 'active', 0, 0, 0, 0, '5aELY1yTfrIWqtuy5gJFrjNm6oR8oDOSx1MhjFbCoEMyvApFD9WMRT2mJxzG', 1, 0, 'qhqivC8GwXmzICEKTyZGRqn1SsCduQguMuJxNGHg3NJWx4JgE3JpDS7mAFVV', '2018-02-28 14:36:27', '2019-05-29 00:10:37'),
(47, 'Irene Albastro', 'ialbastro@stl.ph', '$2y$10$6uJ.bLY3p0hWtYPeBwu4COyoUJy26lMRMRtVAFPwtSXY6wvfNFgi.', 'active', 0, 0, 0, 0, 'ZRIjPKLHSRcWVjUpgJejNRXdSiGA0BzHhYqeM6MC3YCI3YBsfKZ0AGMDhh33', 1, 0, 'pnGCybUWe4WscxIViklgIRgsOLGFKCmkf0yJaUVlDrwPjMmePhRE4YOG08Vu', '2018-03-01 16:52:24', '2019-05-29 00:30:14'),
(48, 'Naomi Balanay', 'lguirre@stl.ph', '$2y$10$pZLGH329lwVi0Fb2354DRuI5Qn.kiDnzsR4UzZHNB7T3CztoN4To6', 'active', 0, 0, 0, 0, '77qVvJiQniGQiKwTpA6iLZrowWSkyy0Pd6eHh5OxmexPdpGFXrpg3VR5tGF2', 1, 1, '7rgsWWhMiYBKm77g2Ij1xLWg4oGoFXi4E6PPi4FNLij5KpPvb5CGA2AI66V4', '2018-03-03 19:21:24', '2019-05-13 17:38:12'),
(49, 'Mark Pagente', 'mpagente@stl.ph', '$2y$10$1Q0SioN1oB0apxJDBrxJU.kIiyMg/Vso0rgO.FIIbGiab9VxqVsjK', 'active', 0, 0, 0, 0, '6tje665QEraicZxQtQIs5cTvgYnkhp8TK3LQFn3zyNO7fuBg4yaeAgCOJush', 1, 1, 'YOsL0gplU8WI9DTAGVH2PYgs2wHf30kkccHPrXigbh2xsfXieyp7wWzYSHoj', '2018-03-03 20:43:14', '2019-05-20 00:25:11'),
(50, 'Rose Mae S. Daanoy', 'rdaanoy@stl.ph', '$2y$10$Ac7/i4ocEkIc7qBkLLGIIuq.2QDBbIa6Gx7uNzrzp34b4PYzZDXT.', 'active', 0, 0, 0, 0, 'MND4Xr2hWKoa6I7wtjNwvRH1Q8BmTqox1zL1znPLjPlUiCH1gKm3hNcG7Vuo', 1, 0, 'X98tU6zMXAmaP7nN6AgvIjQ5smro85MNsikxP10FDdqUbXU6zzp9CuJedfH1', '2018-03-07 14:35:28', '2019-05-29 00:43:33'),
(51, 'Josie M. Lambeth', 'jlambeth@stl.ph', '$2y$10$CtgKlAXr3IZMC1210z2xgOorjmpJEYj7v/4JuwpJlhfKA8zgqIPEO', 'active', 0, 0, 0, 0, 'sqZMQBW4OLDOQF9sOKtktn370CCPbctKlsbT30VS20FdD1vrp1BSLAMM07ei', 1, 1, 'KH3M1NLmlTNzTywRn8XWkrWQbmP4sIhaQuEux8ANd4ARDEd1opzC6JmFVSc9', '2018-03-09 01:09:05', '2019-05-13 17:37:39'),
(52, 'Cherie Mae E. Cuadero', 'ccuadero@stl.ph', '$2y$10$RX.VfKIUrBOHLX9e0aS4N.98TFb/e1w5W1Sby7nhFBUQbgI1m1EPy', 'active', 0, 0, 0, 0, 'J3nvkMNT5zME8MehXALpaAzFbIribFpEWAO25oplIG3NdVliXR4rsrrtwZad', 1, 0, 'FnTll9kbtVSTe4vsj1W3Z3WhMdSZYzYkBHlZaO9ZA7QJfPABZUx6cp8oEmpX', '2018-03-09 13:42:10', '2019-05-29 00:32:16'),
(53, 'Crystal Mae Ponce', 'lbentillo@stl.ph', '$2y$10$D20V37gAVctcaP2zJ3OxJOcauMAVT2ZoHSoTK3VzfuYXjD9Yz/Z.u', 'active', 0, 0, 0, 0, 'moXXuNW4zfduU8AW2iRolH0uQ3YCnx55M4k4HwUaFDxk4EtG7nYShYxoPtBB', 1, 1, 'hg2nfuilxkyFBre4r0PL1YuSXvgXgMNsFFUUZmdVVR8UB3NtuZqnL0Fan6ZT', '2018-03-09 20:03:16', '2019-05-13 17:37:18'),
(54, 'Mathirine Nagba', 'jechavez@stl.ph', '$2y$10$MlVutpgaD1HhM0QRIyUBSelFNEvUjTGoufl0KmS9G1/Zzr3O9GCw2', 'inactive', 0, 0, 0, 0, 'oxEkyHliIsiDK6yLCh7DUuIensFfaWeVTUri6VcqRPz64PZPRAy1NEsBZ2dD', 1, 1, 'V6ZhVtKobrTKVAlHPD5w868cYp0DH0eFwbJPXmNrMTVsY0w4HnpXZz9mT3bN', '2018-03-11 15:55:28', '2019-05-05 00:30:35'),
(55, 'Rosel G. Balucan', 'rbalucan@stl.ph', '$2y$10$custempYpq.aSbDuNnofw.pNrjoVQssLInI.2o1WxTG89iZ4pxdvy', 'active', 0, 0, 0, 0, 'O3ZNZ4IG7jPRca0SzrSQtWU8TzRCpaQuGokVcqT79GNSBsEFlpsLGxul5VUy', 1, 0, 'sagXRFUdcXrgzETUDQ9GG5gyPlxi4qDlai1gjP6FD4fpr7yUCQwuNDifVYw6', '2018-04-26 18:45:15', '2019-05-29 00:27:08'),
(56, 'Rolan Caro', 'jporras@stl.ph', '$2y$10$AuIyS/XScgEdjGdPsYVcm.voTY1XqmLsdMeZutPfo.ASxVj.uZrNe', 'active', 0, 0, 0, 0, '00UVPugv4svUL0ZiWmUN2YiFFMqGUphxMl903jkK4OTT2ZndKR0GACNIRD0W', 1, 1, 'l1t2mP7dZzineyRtRi3iw5yZ7RElkjExtsE2y5VVFhI933u0sMJjbWwF8O4C', '2018-04-26 20:19:10', '2019-05-13 17:38:27'),
(57, 'Lani S. Talon', 'ltalon@stl.ph', '$2y$10$sCa1h6HgkxcjD6OXauHGv.v8t.I6QC3/b9BYfULHssS/KaSeLXJwi', 'active', 0, 0, 0, 0, 'laNxOFWp2bg6L5F3GFWCowDTrYUYE08FNqjJ4bEZ89Af7qhNCD9cMpifJ9cI', 1, 0, '2xgdKqbRWDvBsoG1bjkkVlXqSIhqSyeuvHzHOdXTz4Yt0kuAnDrESJYAniAx', '2018-04-27 22:39:03', '2019-05-29 01:07:15'),
(58, 'Rosalie Cajotor', 'rugapay@stl.ph', '$2y$10$qLEdCckRzSs8mzoU9LQsW.PHpV7fCZ94eDx1p8Q4UBYb2yEBQMMsu', 'active', 0, 0, 0, 0, 'WyDoasoLWlBxxdpYysRJkMtcCm1SGhqOefJ9zRu4KMDbghW3tmtbXJbMkqrs', 1, 1, 'yiwQRe0z89EfSQNki7oFZa0dfKF2ug0PYMlEN7Skq9NjZLdKQA1gk5YjOkUl', '2018-04-28 11:09:31', '2019-05-27 00:36:43'),
(59, 'Maryzell Mosqueda', 'mmosqueda@stl.ph', '$2y$10$TcCb0QBeGxs/YBATqUR1ju1oj/ClEILpkubhB4wwJiKRM4QRBtkKm', 'active', 0, 0, 0, 0, 'dwKyhwpAgT2RImNizDtCrHz3i50QW6k8BMvs4cw5mVrmH01iOT5TmFmuBIFR', 1, 0, 'n6yoKR3HXLtvdlTDEki45sVXiMmHsh7UdvYfNHUsDsGbl8sIwNpN6NC43XkU', '2018-04-28 15:39:58', '2019-05-29 00:32:55'),
(60, 'Roque Marban', 'colroquemarban@stl.ph', '$2y$10$WSxs.tzUWoRt1C42ZNbPDur/t0RnLxpZOkeC0WQzz3YE8PDatiwXe', 'active', 1, 1, 0, 0, 'rIokcajWWVKD7t13utD1KZI1S8qmH1rsoa1VKjKE8RfXPMZq8bntKGop1Cxp', 1, 1, 'fTrcXWWjrsiMAyxHmK33ExJ4gkudS2NNCYvLEyL1zJBJOUBEHBb3vA5kLweY', '2018-05-07 12:12:35', '2018-06-11 00:43:56'),
(61, 'Astrid Bana', 'astridbana@stl.ph', '$2y$10$U7DsmgiU29s0uj6gZFGXWuqk02JWrcW43KaUdbLXfl.75wQBaLCYG', 'active', 1, 1, 0, 0, '0Sni5A2mCXUfTQGZgJ12fVC1W4ygpmBEwNGkfBTK04s9HtjVtL8xLJHRwxfQ', 1, 1, 'QuESqnJjG8hjSCs6pMP9TTOoG01cqyOof1NEMjtVhOftuILwz10nZyP8M5G2', '2018-05-07 14:32:52', '2018-05-15 00:04:36'),
(62, 'Mary Ann Rontal', 'mrontal@stl.ph', '$2y$10$Wx0BuDZC5Ye0HqcC6LP8Ge8.1CogMhxnTLplNCxrys0lgzO5z8P8a', 'inactive', 1, 0, 0, 0, 'PNMXGMKtIdOzdeP3thN3BJWXM50NXzd5IOlytV7DFTlhLGE1ThDh7xvojgHL', 1, 1, 'SikFu6V5qd3yfF1JY6RylIZTRr8WJBmChsh5ywl7zIotH356STU1yAINz8aR', '2018-05-07 14:37:10', '2018-07-07 22:25:46'),
(63, 'Maridel Beborde', 'mbeborde@stl.ph', '$2y$10$oiS31u4Hnjj4bb.IuZC4jedJSFzZSUeWzLB6g1Ieoxjwre1TRUY4S', 'inactive', 1, 0, 0, 0, 'i5ETNigQ8gRTnx38TquCsWaXlYmszZVoMy7sQUAKJaIzntpN7Fw6d972gV97', 1, 1, 'bXHZ7bbFOA02epox6yJMyRJDKdMe5VBK0yVa0N7MVwRbZPjGfrX7FMwabD4q', '2018-05-07 14:40:07', '2018-07-07 22:26:15'),
(64, 'Garry P. Gomez', 'cgomez@stl.ph', '$2y$10$imfodcIlMSccFikzxrygqOhYglNml8kzq1enlHpZrhraFP7Tb.XKC', 'active', 0, 0, 0, 0, 'stKgSpnIBlbzpr6bps7LPQrNVaHytNXHAuIj7gBAkDodgquktMVYYxskbu7d', 1, 0, '6I4ydXKHx6M9U2zvYq0YaK8dSr9SOxc0KNbqUYJzW2j5Xwtw5u6PeMwve8dW', '2018-05-07 20:31:09', '2019-05-29 00:33:26'),
(65, 'Rico C. Ajeto', 'rajeto@stl.ph', '$2y$10$UIiIyVupO0kGeCEf2R0UseUiwV.2e8E2pkQvsp3G.P31EJKRucq9u', 'active', 0, 0, 0, 0, 'lgqf3Fk1UE1jRnh96auLGzg9YhbnXQhehj4Jh7aMdceOOjw9vUgmS6RxiR39', 1, 1, 'OECSShqwJ6Po4UToidxSyHVpBmM5u2dvysjeC67zKKxUfrQH97Dunh2nsWtn', '2018-05-07 22:10:37', '2019-05-13 17:38:25'),
(66, 'Roger Luy', 'rluy@stl.ph', '$2y$10$nVaPuiYERljmc7TlRaYsGeBsngdNjbn5St2eG.3jfjzJyZIq9umTe', 'inactive', 1, 0, 0, 0, 'cNHBMZSViBCfaHqGj9vdZbFx1M1nlz86veej0O3jkFDvxOEt2OOFO8TSBe1m', 1, 1, '1UkMVsWLpRDWfJvDKi0yx51hzHMtU2wZliAZCc6y1tm591PG6ZKBiwoARXuz', '2018-05-16 13:58:33', '2018-07-18 21:30:21'),
(67, 'Abegail Velado', 'avelado@stl.ph', '$2y$10$LUS.cogkvXO4A2NIqgJbJO2aCRkzY5obZBrYPjHSsnTZ6i.P/vTPK', 'active', 0, 0, 0, 0, '2b1uSc47I053jY9tofcukmCs3ql19sWXerEjHra1J5iBlz0SLVM4w7X6t4pX', 1, 0, 'an6UypYLK1ViIgaaAGFjJGbLt937eUMrY2dVKogJZQ5pgu4jlBSkFO1M5oGA', '2018-05-23 00:31:14', '2019-05-29 00:30:30'),
(68, 'John Rey Araneta', 'jraraneta@stl.ph', '$2y$10$q2NyxTCzyCs45M8Xxk89seP72B72qaQWS0RlYKgr4852oh3JpMhiS', 'inactive', 1, 0, 0, 0, NULL, 1, 1, 'cHWeL8WFRhRHsWW2trVO9hYGYzMVmrCaWwO4fh8vcBJWE0aSQnhhmEldbSBi', '2018-05-24 02:13:31', '2018-05-30 15:16:01'),
(69, 'Grie Ybañez', 'gybanez@stl.ph', '$2y$10$BKcyyhK7zfyCSL8v/RMT9.4lmrj4qksNVtKI8iBPY/JuBN284cehG', 'active', 0, 0, 0, 0, 'UC2jwl2aou6aYVpeTqL1cE4kBdNBXFBt370pgn1BZkqsbVRbTy3cd7zaiDaA', 1, 1, 'JBHPaZi4Sry5u3dQUEfNaum5kd7AKwXe1dAiywtU9Ulz6gyZbAlXVoho8qyU', '2018-05-24 15:16:59', '2019-05-27 14:33:08'),
(70, 'Rex Badilla', 'rbadilla@stl.ph', '$2y$10$6IS97AhHPOTK21wX1EMWa.YOBhwinCccqm7VU1IhHyljN3F.8pc6O', 'active', 0, 0, 0, 0, 'uvzkXNnABGM3fggY11jSBXEEevhZlGwAaYHTJ8MzQJzsgNwccLHYxOLv9Duq', 0, 0, '2E5i0HICxwGuIw0DasrnhWUPIzkRwOiQWibN7qIChUSmKJYLVtTDWV1D7gZ2', '2018-05-24 15:31:05', '2019-05-29 00:30:16'),
(71, 'Raziel Tamse', 'rtamse@stl.ph', '$2y$10$HLibbBhWie5Ry2n2wpSTF.YJ/YBt0rf8T87HW9WQa9404eQsHLgpu', 'inactive', 1, 1, 0, 0, 'FGC0zvNb5JTJjd4t6wWDW6bjwpvM06rL1Mg7ve9rp9EzzZpp9n1rjEf8rhfj', 1, 1, 'e4EOrl9Nl5O4sQbfj7UM4ZIPG2p3m0ZRK23WACy6tLeGbHKiQa160eIbslVD', '2018-05-30 16:25:42', '2019-03-16 00:23:49'),
(72, 'mailyn oliveros', 'moliveros@stl.ph', '$2y$10$Tv2fBvVRiFmV3jSXm4gohe69MZqkiiqT/1J.yHXITfrVhQpazX.Oe', 'active', 1, 1, 0, 0, 'CtXymga5QM2gXudUaXkPiCC1KeFgxoGgvs6XZ0nCL1tIBiL57GJNZMFGFO7B', 1, 1, 'MRBgU6ttRBKxiGZerLZy9P6hy4RRzbPgFEwHKI2is56SVjNKBKGcjJJSTODb', '2018-05-30 16:30:17', '2019-04-02 11:53:00'),
(73, 'Mary Grace Peñaflor', 'mgrace@stl.ph', '$2y$10$mulX.BFP0qTBEJLYBLEGs.oBAgfrrdPrKHhd8KHqdT1i/gP7xZKhW', 'active', 0, 0, 0, 0, 'h5VYfks3JJwfXPT7I6CVqaz6r0VaSNZah3WHdiLP4eJwiJB2LADMZS53VMNO', 1, 1, 'rggVP76xFxiXaMbYxbpDrILvjwURrBOxMJfbUHOTozF07AHmQ4Ft7f94MS8e', '2018-06-05 12:31:30', '2019-05-15 13:41:41'),
(74, 'Mabel Buadlart Ramilla', 'mramilla@stl.ph', '$2y$10$/UvNwE8K40XezlAPH6CPc.4RXvGzerXYpP5eIKj/dq.EjAS7Rx7fK', 'inactive', 1, 0, 0, 0, 'HPBdAWya5sDPj6FvBwrsp1Uts5hpYbV235yUklGflZiNR1RvVmQhdIEwWml2', 1, 1, 'BeON2B41iWXMEP67LVB2zZYQmHRIjlOKeDMRXLMZFrIaS2gT9qAdlyR9f7iy', '2018-06-25 17:34:00', '2018-08-16 17:19:52'),
(75, 'Lovely Angel Cabrera', 'lacabrera@stl.ph', '$2y$10$T4Oz04bwQKnJOKMGJLISiu4wAQ7kM4NSktbMhrjbrnuDvI9xEWsdu', 'inactive', 1, 1, 0, 0, 'ykPdI0fZhJItElL0qqM3qm8QbtMhRLAE3tC96z3deHhKVFcMTFNsjBiPE6Lf', 1, 1, 'ctVF4fdhaGx9PGDZ1YoS2VB4bwgfAoZJB5yS7Nw0JpDd2mTrlFOdn5uCPOC1', '2018-07-07 22:24:22', '2019-01-10 23:13:23'),
(76, 'Bede P. Baculio', 'bbaculio@stl.ph', '$2y$10$vz/rc9HSU.RjklDnCDxFs.tBKYpvNUhYPpK9JD0Y2lLKYwcrl62Vm', 'active', 1, 1, 0, 0, '1ot5MZ2caPBSHkwUPNyX9BFLivWqCu0ky2E8eL4GwO5lI0BnwMg0RJBRkp3K', 1, 1, '', '2018-08-10 19:33:02', '2018-08-20 18:25:48'),
(77, 'Eddie P. Go', 'ego@stl.ph', '$2y$10$nviez5wJkU7lkyMe2is2k.rTj1v/0BWeaT2wFObHbvt1jIkxDxt0.', 'active', 1, 1, 0, 0, NULL, 0, 1, '', '2018-08-11 12:58:07', '2018-08-11 12:58:07'),
(78, 'Gemeniano P. Abrego Jr.', 'gabrego@stl.ph', '$2y$10$xFBG6np7rHCxToub7MzgruCoJzaemC4XJir43rPUgsokm5KmBMWhS', 'active', 0, 0, 0, 1, 'V2YNOY4521nPCksJGKbTgNQLGUGA5Pq4ifmcv14kt08mgi0dvmvxtziI0CeH', 1, 1, '78KK1jDr4FCrXPvXJG78LNOciABQXUecsb9gRmeCuamJS5TExzo8pnc3OlZT', '2018-09-03 19:33:32', '2019-05-13 17:37:24'),
(79, 'Angela Amporias', 'aamporias@stl.ph', '$2y$10$U7dGK6faUmvuBg2aQypdz.y.V1o1016iFwRleAxcXtUzv97.XXvWy', 'active', 0, 0, 0, 0, NULL, 0, 1, '79XWPOOpIiE6WrZQUfxzFfjzkSHvyxT5uA981x28HCI5AJf5Mt2RiYM6Dd4j', '2018-09-07 03:10:37', '2019-05-28 00:31:33'),
(80, 'Riza Mae Alcuizar', 'ralcuizar@stl.ph', '$2y$10$8uvXjpA20QRJGqFQHyKSNeo7Ui1JeaWO8tFU0Wjqf7/RebkjKvGsG', 'inactive', 0, 0, 0, 0, NULL, 0, 1, '80HnICnZQvZqZSHhJPvwq8nTdGkI1LXgZupFkAFwR72BYqa77hIlo96MHhhq', '2018-09-12 11:57:21', '2018-12-22 09:57:04'),
(81, 'Rommel B. Gonzaga', 'rgonzaga@stl.ph', '$2y$10$.kHVWmtscIldF89wTRC7j.qWhdhO7JwroCiUxsBNnbpxAp6Eiu.XG', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '81ZMOEOFelkGN8dO8l0hO9xBLKIZrgDUAVHKPapsBAYZdt43wQVJl939JSrs', '2018-09-12 12:39:30', '2018-11-09 13:05:13'),
(82, 'Dan Rey Balanay', 'dbalanay@stl.ph', '$2y$10$.9beUWPrHiqKhk82MKcE6ut6udjqnQCxnZRjL.Lt8aorLPkQsr2za', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '82Kig4KyVFNdxrpYutXmasewqOrkSfXfsSyqt6l1r1vHgcXupXz2d2gzRvyf', '2018-09-12 18:39:55', '2019-01-18 01:03:24'),
(83, 'Marita J. Caslib', 'mcaslib@stl.ph', '$2y$10$CbSAzTRg.qFhFuUIEYGyxuhQSBa66Ll.U0txOgdB7CJswEHGOhrtq', 'active', 0, 0, 0, 1, NULL, 0, 0, '83NLUqZdsW3DV82WAWHW1IA9763NS3YNIrwuYar58FluUj7ZjQt8OjqcfTAx', '2018-09-13 15:34:06', '2019-05-29 00:38:37'),
(84, 'Maria Concepcion C. Loren', 'mloren@stl.ph', '$2y$10$q.er3Yx90unoKmuYqs7yUeydUzolaPfHtU0mWSG6e3oDFLhi.fX3e', 'inactive', 0, 0, 0, 0, NULL, 1, 1, '845tENUCok30R5qRFH96VSZewkdX1qFLb9IPp7MCsUOuxNWw9OSl0bzt6wll', '2018-09-14 19:09:01', '2018-11-20 19:48:08'),
(85, 'Edlyn Pagente', 'epagente@stl.ph', '$2y$10$j6MPtGWWFtBynGoqJweJTumXGbtiKy.K.plc6xOEkQTvgu8DULQfO', 'active', 0, 0, 0, 1, NULL, 0, 1, '85VIAXB6B8iHEWkL53BVT8WxRmLt4LNzlm9OGFRqgkREkdmhENLcvYcGv4zH', '2018-09-15 15:49:12', '2019-05-13 17:37:20'),
(86, 'Mildred Bartido', 'mbartido@stl.ph', '$2y$10$xfKN0j1FcEzaLYnnZXVb6Oaqh1/TsY1Z6DC.0KCX9UdvBZ.fl4ygy', 'active', 0, 0, 0, 1, NULL, 1, 1, '8698Qyts7EXN5AByUVylsA10bFcgUQpkijrdEeYEQS97Pd40AxhMuRM4S98M', '2018-10-03 18:11:50', '2019-05-13 17:38:10'),
(87, 'Jula-lane Barinan', 'jbar@stl.ph', '$2y$10$zFe.yLWqM8ar8L6.gjWS0eQL0VarELHcJ4j7xQ.6P2VuA8bm5fb0K', 'active', 0, 0, 0, 0, 'KnfZnTHVUNtB78FBGHNMMvvN83dlzvqLnUo97VvGPaoXceYaOmsYJUMd6TF1', 0, 1, '87WjzMvPO6FI6xUGqWxTDPUViS2TjZlNZVUmz9LynjIunh8OIodHSIt2CBCX', '2018-10-04 12:10:10', '2019-05-13 17:37:39'),
(88, 'Liafe C. Julito', 'ljulito@stl.ph', '$2y$10$8.JhC12cRVYHxv2aH27c/Oez.pbm3RGplYYjW.42oR3qi37KiXhgO', 'active', 0, 0, 0, 0, 'q7hmxViQO29QABzCBnTVqCMQ29bV5t6DFv7OX01RB36UVM3IB8BmzPTtUOCZ', 0, 0, '88w2c2OK0sbwTNudTC07vrA2bFLazkouobaMNafyDUFXjndNQPf7mOyyR1kZ', '2018-10-04 14:14:35', '2019-05-29 00:31:23'),
(89, 'Mariecris C. Cañedo', 'mariecris@stl.ph', '$2y$10$4temT7OJ7g2RukVFUI/cOOuNA2TcvAl2/rFYVVGKriUpcBrRJoP.K', 'active', 0, 0, 0, 1, NULL, 0, 0, '890hwBzA7MLd3ulX0Dhm7Vt2Wlv0PoGnAMXPpOwmYFVx42mjcBnTSsHn0ZlS', '2018-10-06 12:23:46', '2019-05-29 00:42:21'),
(90, 'Jerry Mei Villarin (C)', 'jvillarin@stl.ph', '$2y$10$NaDowR0vNx.Cq8ORy87ZgeqIrZ5zT56vkk4v32alm91dcv5YX1L4K', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '90BsILB4uaTu4e6CmEtJ1FJhSiwfrJI1w5hQAdb2XYrbGAYJHHd2rNDEht83', '2018-11-08 22:57:42', '2019-01-18 01:04:40'),
(91, 'Roel D. Balcos', 'rbalcos@stl.ph', '$2y$10$lVR6bKAJS5Nz3o/vOjD3OOMC7zBKawcPzQZKPjkIWHlvzp0c3BfVG', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '91vJzMYKbEdY4KbqqVYcTUl1k9VTZVCyKFwdD51W5OTUyaIHRYJA364wAzug', '2018-11-08 22:58:20', '2018-11-17 11:27:46'),
(92, 'Ruth L. Jumawan', 'rjumawan@stl.ph', '$2y$10$D1IG5xSIDgT/4SOjOM5Id.LcZGCNy.Qai8omdeGt/sfJ1tW3.8d2m', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '92uh0ykAq0lhFSdiq773bzfEA0ok4tVErutTVzxL5TBv9B5bJHYkOlKOFS9m', '2018-11-08 23:37:57', '2018-11-17 11:28:23'),
(93, 'Joefel Agan', 'jagan@stl.ph', '$2y$10$EGhBXBlukm/fqTBEtuAHw.AZR/4gvLsP6oTnyGpm/QJ1PXB/iK5sG', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '93h9ulJp7zwyLSSQfom5ecKbNpydMO5mrP5SXGszivAZM45gih98Ej9dlqbD', '2018-11-08 23:42:35', '2018-11-17 11:36:13'),
(94, 'Erlito L. Teves', 'eteves@stl.ph', '$2y$10$YLICllxPrdV9tJwL5XTlmO9YXOwEQsdo9zmt68UrWJ26uXhFTIBJa', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '949hAAlzey4nmgeWTF2u7V5kDRwbQaNOex9gigq8C2SMvdYbCo4VFevEWPZm', '2018-11-08 23:42:57', '2018-11-17 11:28:44'),
(95, 'Pierre Angelo Ruedas', 'pruedas@stl.ph', '$2y$10$Q7QPU453OnqaYyNaA0jH1O0Q5ondW4QyUbmnEYny0CpcrwI9fbf6K', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '95YksF8dE5JgzBMKg6JKrphu1pZuRVPYec7r4JCWEsBkDiDr1X5GXyilWNhC', '2018-11-12 17:50:15', '2018-11-17 11:28:06'),
(96, 'Nel Cabillo', 'ncabillo@stl.ph', '$2y$10$W9bdbhI3ZxFnA4wXkUpFwed4Py.y1CHBAYdcm1z3asTTg375TVS8e', 'active', 0, 0, 0, 1, NULL, 0, 1, '962TPUCIo5nPy4HzMpDkh5ClAM1np4SSymg6RRUaboksscikBJX1SmQ79WR9', '2018-11-16 14:08:26', '2019-05-13 17:38:13'),
(97, 'Michelle Grace P. Peñaflor', 'mpenaflor@stl.ph', '$2y$10$wrFp89dTtT8oXiBX7l6Vq.I1hmGg82kc6/MVDdyJ7fCt8yK2pN77W', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '97kJFTSENDfqbZk0dBMaeueoESbmbPYszPzlU7Pl5th8DxYxZZ06T3eMmLWg', '2018-11-16 16:02:39', '2019-01-18 01:01:09'),
(98, 'Melvin Bation (C)', 'melvin@stl.ph', '$2y$10$geQquccPWd.ThQv94EPdSu4UpFg/fbFk7tMliyMZhY5ykrt.657zC', 'active', 0, 0, 0, 1, NULL, 0, 1, '984ddZh446fkz0k2K0WsF2bv0DSkHFa49VIxtwJjpZFLwfVdwJj0TY8r0BsS', '2018-11-16 17:38:43', '2019-05-13 17:38:07'),
(99, 'Mae Bation (C)', 'mae@stl.ph', '$2y$10$I3qhpM4GNtjtr8Q9ykVNEOnqM8QK2iX1WPTlmC0gHbkWYVlQMrQBK', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '99XSm4TvjCTBy1xGMa8Ov5vjYYEXOVnn6BxAU3zPVzotNkiyJjLK9WfWTMJs', '2018-11-16 17:39:32', '2019-02-01 01:30:23'),
(100, 'ERMIE A. NULO (C)', 'enulo@stl.ph', '$2y$10$btPYDSQ1q0S02FkJKT7LC./QMXj1z/BQbXwg88R7hFagCva9Tg5Ja', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '100sNFSxZYEGELGQLf1Se4f6wON9XQ3yucTE48xD1G7mB24lg7OZxaJuHdjL', '2018-11-16 18:42:20', '2019-01-18 01:03:11'),
(101, 'Rene G. Nulo (C)', 'rnulo@stl.ph', '$2y$10$RNTuMy5dSP0y6p.hl1Pc8OFuVjIQr5QLsAcV/0Uo/fcei6qjBCWRS', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '1018WJTGMoi60ennqBJ5OXCqiqvxosZOEHeIKgvpz07ZYz5deRYAopXdKlnC', '2018-11-16 18:43:11', '2019-01-18 01:05:31'),
(102, 'Roselyn Villaver', 'rvillaver@stl.ph', '$2y$10$RuwGr8vgniJ.U4W2YvyLEugszMtjbyvuk/7hvTGq4.Qnf5HtsCYHS', 'active', 0, 0, 0, 1, NULL, 0, 0, '102zazR1UqERE5VBIrriOiB56d6EvqeAnFtVQYCjymc1htxqpNO0BxFDy4Lf', '2018-11-16 19:12:36', '2019-05-29 00:43:07'),
(103, 'Jocelyn Abello', 'jabello@stl.ph', '$2y$10$k4H1Up41G9clfcXtzhEBx.OYU52Xz.aXWuVgaEsjtb.Ld4LGHfqha', 'active', 0, 0, 0, 1, NULL, 0, 1, '103kBQxbfXXlUNNtOWdniougldh7oIyofoWj4lzqAgX5vzCxYOlLKhcZ0HC4', '2018-11-16 20:27:11', '2019-05-19 13:41:02'),
(104, 'Arlyn Andam (C)', 'aandam@stl.ph', '$2y$10$04LR.JtbDMNK/1noVExLDOhKo1ANAch6YfzsIFvSJYNKoDPOYuzcO', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '104lVxRD9qohYZEsrgi1OQkSRNoFKxB67xt8qfK77q4w7y76WiIdqd4oj6bT', '2018-11-16 21:34:27', '2019-01-18 01:05:17'),
(105, 'Alfe Mae Pirante (C)', 'apirante@stl.ph', '$2y$10$pkZPcdsPVY7cWjMmECbBreDvHFAGPfenA/1TVtNjJT00uN.eaCB92', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '105sivJZCnYQMfBDx40UEdaocPSlzhI9hKcA8cy0eRdZ9nvhAgCnu1YzDQQk', '2018-11-16 21:35:25', '2019-01-18 01:02:53'),
(106, 'Marisa Barrette', 'mbarrette@stl.ph', '$2y$10$9Z98YtLnw.XjR/PgI6TG5uCr2qK5E6igzNa4DJGKIfjOBQFspnAim', 'active', 0, 0, 0, 1, NULL, 0, 1, '106VFXXWbF1CPdSsYuLkHzFmHtEPrtweB2Onf9jyu9xjrd1ifZ8MQqueSvBr', '2018-11-17 16:54:04', '2019-05-13 17:37:56'),
(107, 'Jenelyn G. Dayondon (C)', 'jdayondon@stl.ph', '$2y$10$OLWtomETd0DlrB5tsxLLVeunGyDCM9uzAMeyARnFOsi2HHxC1vvGS', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '107l4AN6FATYXTh7DDQV9tRBz37atZbsCFMnsii23wbM43Xd8mivKxHxG2Rj', '2018-11-17 19:08:32', '2019-01-10 23:17:00'),
(108, 'Glenn B. Olivar (C)', 'golivar@stl.ph', '$2y$10$ZsD.h8El.YmqOkmGtmaKBeas207yEHHP3Ed48Okv3ATDARsCT212C', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '10855GfUSTBuEagffcEfcYWD1x4FQzadBiHnZeMug5U2sLmaALRarICfUxhB', '2018-11-17 19:23:24', '2019-01-10 23:16:54'),
(109, 'Jhuna E Yañez (C)', 'jyanez@stl.ph', '$2y$10$Hnye9n71CRR3GIHd4RB4E.J.kRQur.xSn47z/OfjHPyaVcJpQq0Fa', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '109mOysZvqt8vcB2ywJx2n8NYPoTgQvg3JekgSUwpfDp67Goprd3lOzdGLeS', '2018-11-30 20:03:05', '2019-01-18 01:05:02'),
(110, 'Lolita J. Torres', 'mtorres@stl.ph', '$2y$10$GPO3YePnzGhDFentn5DWAemYAH42aOy2Vpksuki1iAz1VJsybhxaW', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '110N2z65jYLObs9PiNkDkqTqWKyzCFJapOjn33qtiM2DLW3jQmwBlRkC9tPS', '2018-11-30 21:21:17', '2019-01-10 23:14:18'),
(111, 'Ruth Jumawan (c)', 'rjumawan2@stl.ph', '$2y$10$56QLn4Y7RMo/bC5XXyQtm.sLFB2brdbTk88XK9D1IG9bzHuo7grEC', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '111wyxAfUMUPZH6J26fByxvDZBeTyEi7BcuMyL3Z2sJX0ZTjMsg06tX2hADO', '2018-12-05 20:01:26', '2019-01-10 23:16:45'),
(112, 'Jerald B. Maglangit', 'jmaglangit@stl.ph', '$2y$10$kV8fx365CUUE1yIVneAVUewwRrVGrSqBg0zRJefjHEEzUPNcF9Rj2', 'active', 0, 0, 0, 0, NULL, 1, 0, '112uYuUgNtLVueran1lmhdNlihCYz9o9FbQIPwzGFwLE634lotDN0UtUAgLY', '2018-12-11 14:47:39', '2019-05-29 00:34:21'),
(113, 'Marjane Duhaylungsod', 'mduhaylungsod@stl.ph', '$2y$10$OZz4Do5u2Z4e1nm3I73rm.511u/8vHo41P3KAofCeFmX6oGbfWafC', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '113il8kjRPQmWxIWzFtdDpfEDLtesJ1fBUyK0mZLfQZHB1jMkuGsV1yFDsYz', '2018-12-12 15:25:51', '2019-01-18 01:03:57'),
(114, 'Marven June A. Torres', 'mjtorres@stl.ph', '$2y$10$ew/q0KEgANz.MNexjuAfQulOLtUk8RLvx5hIMdmHNeVbZOhLUM0dK', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '114Vn9f05GOVpkh736zeEJLcBAVhfrfiIrnpb3DmBYBLdInOpm2JVnsfgVxv', '2018-12-13 16:35:49', '2019-01-18 01:04:15'),
(115, 'Angelo Ruedas', 'aruedas@stl.ph', '$2y$10$xN0qQrdVPlk.EwQ3.N/MlOD1Su1ybZl0wHjYMADDJVofKYBFyEUp6', 'inactive', 0, 0, 0, 1, NULL, 0, 1, '1152WM3m3CDLG05YUGenyxGaLtk66pys4KQ5e1C4D0dRawgqUSICedCHogpk', '2018-12-30 14:37:41', '2019-01-10 23:14:27'),
(116, 'Marven June A. Torres', 'mtores@stl.ph', '$2y$10$.XcIIUZWJ4VduLWU3oJQIO2rqiIBJu8vtJBwBu9IM4YeQKWy6xcMy', 'active', 0, 0, 0, 1, NULL, 0, 1, '116LBeBIgyGLIzR2kV88MUyTyPDDf88vGbkT5dkA18pNoY5ejvCAEjgCP4an', '2019-01-19 13:45:27', '2019-05-28 00:30:41'),
(117, 'Marcos P. Burgos Jr.', 'mburgos@stl.ph', '$2y$10$jRvs6kvOMfLGN.s2m9dVk.nRgLBKTzf2Lm6EWNKNd5yPRFbXAQEKm', 'inactive', 0, 0, 0, 0, NULL, 0, 1, '117nQGREjlrDQwdKgtVxwf24x73tENkBgtr8kfkj6S7ILfzuqR2p9xW1aDam', '2019-01-21 13:37:59', '2019-01-21 18:33:18'),
(118, 'Erlinda T. Inot', 'einot@stl.ph', '$2y$10$YDqeYYqhGp7KaaXir40TsOaKKEROQ3Z1CDpn2UdeUvm.QQ8wJqpQO', 'active', 0, 0, 0, 0, NULL, 0, 1, '118FUjHEnjEIZg5WiHVL3ofbpxAkaM08TNVgAXOnanMyzXZAjnysTEHUVX9P', '2019-01-21 18:34:26', '2019-05-23 23:42:56'),
(119, 'Mae Bation (c)', 'mbation@stl.ph', '$2y$10$7iIbuddsGTLS.lzTukmQFeYVI9D9y8XfdBDFGDsyuU8zUdo/rdlo.', 'active', 0, 0, 0, 1, NULL, 1, 0, '119Py4NPNfsxpyhGmcL1yJWtBCv2InLUZSAQvXLfK8yABothALhelKE4aGU1', '2019-01-26 17:25:47', '2019-05-29 00:59:37'),
(120, 'Test Account', 'test@stl.ph', '$2y$10$e7aH/qL4FQYTzrx4WzkOOeN7MGKwiPX8dz8Mtat.wCZRTYQgrf7hi', 'active', 1, 0, 0, 1, NULL, 1, 1, '', '2019-01-29 17:21:23', '2019-03-17 01:12:55'),
(121, 'Jenny Ora', 'nnyjeora@stl.ph', '$2y$10$wJ3cW3.WobtLisSlW97tjeBRwdamGCkki7jNLYmzv/iIteu6qFej.', 'inactive', 0, 0, 0, 0, NULL, 0, 1, '121NfZrlPjGDQ59EB4P6guLVoGY8UbfxUWeDCOEtqC7DDVunxhEHDiFymYAm', '2019-03-16 00:22:23', '2019-03-16 00:23:32'),
(122, 'nnyje ora', 'orannyje@stl.ph', '$2y$10$YVj7b.J2WX217VmQBbjRLOv9MW2a68cG6R9s9kB18QDeUkpGNFF4i', 'inactive', 1, 0, 0, 0, '9Bl38AQMKomZascWS7hh9Isvuu0tIAfGyKECYrxfRYmKsPNIJ6EkJY5GuBFo', 1, 1, '', '2019-03-16 00:27:06', '2019-03-16 01:16:26'),
(123, 'Jenny Ora', 'jora@stl.ph', '$2y$10$3YzcvfVcdPtbcdRGteXtruqhH5iHuWH/DPomLIUhbGqmRRt.fW1ce', 'active', 1, 1, 1, 0, '69mHnkTfNB7k3y4TxT39OfJfm5TPwomuWcFKSwHRpZC7FMBiNpUO4cHTAGL2', 1, 1, '', '2019-03-16 01:16:16', '2019-03-17 21:05:46'),
(124, 'Metzler C. Umpa', 'mumpa@stl.ph', '$2y$10$jj5Up9gu2K32h0QyDXJcw.FeHMhwIYfKFoYMt/Tjm4ueFb1PkJaSu', 'active', 0, 0, 0, 1, NULL, 0, 0, '124AW0YeEnGjfP1gILMEXAxRkUu8FvTFpn90MhwMGguv7eyFCAKQWRwZpG7t', '2019-03-25 17:42:26', '2019-05-29 01:18:46'),
(125, 'Jeffrey S. Gallego', 'jef@stl.ph', '$2y$10$77AXzHyRel.jBs3gZ8wFge6FHjjXOGV8La8H5cDIg2RYY63e81GSC', 'active', 0, 0, 0, 1, NULL, 0, 1, '125o15yxrVBWHSjY213mXadDw9ZJMCPIxe0E6iR15kS2iqXLJvxb2PVJSzoN', '2019-04-03 17:46:26', '2019-05-13 17:37:32'),
(126, 'Jessa Mae Baculio', 'jbaculio@stl.ph', '$2y$10$wO4vuwbf1SkzYLJHD0UmDOuzHqSTpetQuG3jZjglLtbLdUzwbPdz2', 'active', 1, 1, 0, 0, 'j8jGo72qN0oUGWn20hdQEbcTJiBgcNnxTnvp3iExLTcWtVaMocSmcWhBDIv3', 1, 1, '', '2019-04-04 15:26:14', '2019-04-04 15:32:54'),
(127, 'Daniela Jean Kiang', 'djkiang@stl.ph', '$2y$10$jlUMWe2./PkUcebOi9ttaOjLWIXCm6qdvxpIXci8SZSfq.K0ujSqy', 'active', 1, 1, 1, 1, '8HggAG8B7bVsLdeYIA4Pe02qqbdrZ3dR1UJCTzcEhP40AFPjaNgjVeADsouu', 1, 1, '', '2019-04-30 17:16:41', '2019-04-30 17:55:04');

-- --------------------------------------------------------

--
-- Table structure for table `user_time_logs`
--

CREATE TABLE `user_time_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mode` varchar(10) NOT NULL,
  `ip_address` varchar(16) NOT NULL,
  `agent` text NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `winning_results`
--

CREATE TABLE `winning_results` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `game` varchar(20) NOT NULL,
  `number` varchar(191) NOT NULL,
  `result_date` date NOT NULL,
  `schedule_key` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `winning_tickets`
--

CREATE TABLE `winning_tickets` (
  `id` int(10) UNSIGNED NOT NULL,
  `winning_result_id` int(10) UNSIGNED NOT NULL,
  `ticket_id` int(10) UNSIGNED NOT NULL,
  `bet_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_log`
--
ALTER TABLE `api_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_at_idx` (`created_at`);

--
-- Indexes for table `bets`
--
ALTER TABLE `bets`
  ADD PRIMARY KEY (`id`,`outlet_id`,`transaction_id`,`ticket_id`),
  ADD KEY `fk_bets_transactions1_idx` (`transaction_id`),
  ADD KEY `fk_bets_outlets1_idx` (`outlet_id`),
  ADD KEY `fk_bets_tickets1_idx` (`ticket_id`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `type` (`type`),
  ADD KEY `number` (`number`),
  ADD KEY `game` (`game`);

--
-- Indexes for table `bet_prices`
--
ALTER TABLE `bet_prices`
  ADD PRIMARY KEY (`id`,`outlet_id`),
  ADD KEY `fk_bet_prices_outlets1_idx` (`outlet_id`);

--
-- Indexes for table `config_global`
--
ALTER TABLE `config_global`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `default_outlets`
--
ALTER TABLE `default_outlets`
  ADD PRIMARY KEY (`id`,`user_id`,`outlet_id`),
  ADD KEY `fk_default_outlets_users1_idx` (`user_id`),
  ADD KEY `fk_default_outlets_outlets1_idx` (`outlet_id`);

--
-- Indexes for table `disable_outlet_records`
--
ALTER TABLE `disable_outlet_records`
  ADD PRIMARY KEY (`id`,`outlet_id`),
  ADD KEY `fk_disable_outlet_records_outlets1_idx` (`outlet_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hierarchy`
--
ALTER TABLE `hierarchy`
  ADD PRIMARY KEY (`id`,`user_superior_id`,`user_subordinate_id`),
  ADD KEY `fk_hierarchy_users1_idx` (`user_superior_id`),
  ADD KEY `fk_hierarchy_users2_idx` (`user_subordinate_id`);

--
-- Indexes for table `hotnumber_searches`
--
ALTER TABLE `hotnumber_searches`
  ADD PRIMARY KEY (`id`,`user_id`),
  ADD KEY `fk_hotnumber_searches_users1_idx` (`user_id`);

--
-- Indexes for table `invalid_tickets`
--
ALTER TABLE `invalid_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_idx` (`user_id`),
  ADD KEY `outlet_id_idx` (`outlet_id`),
  ADD KEY `created_at_idx` (`created_at`),
  ADD KEY `ticket_number_idx` (`ticket_number`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `memos`
--
ALTER TABLE `memos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mobile_numbers`
--
ALTER TABLE `mobile_numbers`
  ADD PRIMARY KEY (`id`,`user_id`),
  ADD UNIQUE KEY `mobile_number_UNIQUE` (`mobile_number`),
  ADD KEY `fk_mobile_numbers_users1_idx` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_id_notifiable_type_index` (`notifiable_id`,`notifiable_type`);

--
-- Indexes for table `offline_sync_logs`
--
ALTER TABLE `offline_sync_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`,`sync_time`);

--
-- Indexes for table `outlets`
--
ALTER TABLE `outlets`
  ADD PRIMARY KEY (`id`,`user_id`),
  ADD KEY `fk_outlets_users_idx` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payouts`
--
ALTER TABLE `payouts`
  ADD PRIMARY KEY (`id`,`user_id`,`outlet_id`,`transaction_id`,`ticket_id`,`bet_id`,`winning_result_id`),
  ADD KEY `fk_payouts_bets1_idx` (`bet_id`),
  ADD KEY `fk_payouts_tickets1_idx` (`ticket_id`),
  ADD KEY `fk_payouts_transactions1_idx` (`transaction_id`),
  ADD KEY `fk_payouts_outlets1_idx` (`outlet_id`),
  ADD KEY `fk_payouts_users1_idx` (`user_id`),
  ADD KEY `fk_payouts_winning_results1_idx` (`winning_result_id`),
  ADD KEY `updated_at` (`updated_at`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`,`user_id`,`outlet_id`,`transaction_id`),
  ADD UNIQUE KEY `ticket_number_UNIQUE` (`ticket_number`),
  ADD KEY `fk_tickets_transactions1_idx` (`transaction_id`),
  ADD KEY `fk_tickets_outlets1_idx` (`outlet_id`),
  ADD KEY `fk_tickets_users1_idx` (`user_id`),
  ADD KEY `result_date` (`result_date`),
  ADD KEY `schedule_key` (`schedule_key`),
  ADD KEY `updated_at` (`updated_at`),
  ADD KEY `is_cancelled` (`is_cancelled`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `ticket_cancellations`
--
ALTER TABLE `ticket_cancellations`
  ADD PRIMARY KEY (`id`,`user_id`,`ticket_id`),
  ADD KEY `fk_ticket_cancellations_users1_idx` (`user_id`),
  ADD KEY `fk_ticket_cancellations_tickets1_idx` (`ticket_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`,`user_id`,`outlet_id`),
  ADD UNIQUE KEY `transaction_code_UNIQUE` (`transaction_code`),
  ADD KEY `fk_transactions_outlets1_idx` (`outlet_id`),
  ADD KEY `fk_transactions_users1_idx` (`user_id`),
  ADD KEY `origin` (`origin`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `email` (`email`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `user_time_logs`
--
ALTER TABLE `user_time_logs`
  ADD PRIMARY KEY (`id`,`user_id`),
  ADD KEY `fk_user_time_logs_users1_idx` (`user_id`);

--
-- Indexes for table `winning_results`
--
ALTER TABLE `winning_results`
  ADD PRIMARY KEY (`id`,`user_id`),
  ADD KEY `fk_winning_results_users1_idx` (`user_id`),
  ADD KEY `result_date` (`result_date`),
  ADD KEY `schedule_key` (`schedule_key`),
  ADD KEY `game` (`game`);

--
-- Indexes for table `winning_tickets`
--
ALTER TABLE `winning_tickets`
  ADD PRIMARY KEY (`id`,`winning_result_id`,`ticket_id`,`bet_id`),
  ADD KEY `fk_winning_tickets_winning_results1_idx` (`winning_result_id`),
  ADD KEY `fk_winning_tickets_tickets1_idx` (`ticket_id`),
  ADD KEY `fk_winning_tickets_bets1_idx` (`bet_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api_log`
--
ALTER TABLE `api_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bets`
--
ALTER TABLE `bets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config_global`
--
ALTER TABLE `config_global`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `default_outlets`
--
ALTER TABLE `default_outlets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `disable_outlet_records`
--
ALTER TABLE `disable_outlet_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hierarchy`
--
ALTER TABLE `hierarchy`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hotnumber_searches`
--
ALTER TABLE `hotnumber_searches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invalid_tickets`
--
ALTER TABLE `invalid_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `memos`
--
ALTER TABLE `memos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mobile_numbers`
--
ALTER TABLE `mobile_numbers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offline_sync_logs`
--
ALTER TABLE `offline_sync_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `outlets`
--
ALTER TABLE `outlets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `payouts`
--
ALTER TABLE `payouts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_cancellations`
--
ALTER TABLE `ticket_cancellations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `user_time_logs`
--
ALTER TABLE `user_time_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `winning_results`
--
ALTER TABLE `winning_results`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `winning_tickets`
--
ALTER TABLE `winning_tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bets`
--
ALTER TABLE `bets`
  ADD CONSTRAINT `fk_bets_outlets1` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_bets_tickets1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bets_transactions1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `bet_prices`
--
ALTER TABLE `bet_prices`
  ADD CONSTRAINT `fk_bet_prices_outlets1` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `default_outlets`
--
ALTER TABLE `default_outlets`
  ADD CONSTRAINT `fk_default_outlets_outlets1` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_default_outlets_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `disable_outlet_records`
--
ALTER TABLE `disable_outlet_records`
  ADD CONSTRAINT `fk_disable_outlet_records_outlets1` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `hierarchy`
--
ALTER TABLE `hierarchy`
  ADD CONSTRAINT `fk_hierarchy_users1` FOREIGN KEY (`user_superior_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_hierarchy_users2` FOREIGN KEY (`user_subordinate_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `hotnumber_searches`
--
ALTER TABLE `hotnumber_searches`
  ADD CONSTRAINT `fk_hotnumber_searches_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `memos`
--
ALTER TABLE `memos`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `mobile_numbers`
--
ALTER TABLE `mobile_numbers`
  ADD CONSTRAINT `fk_mobile_numbers_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `outlets`
--
ALTER TABLE `outlets`
  ADD CONSTRAINT `fk_outlets_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `payouts`
--
ALTER TABLE `payouts`
  ADD CONSTRAINT `fk_payouts_bets1` FOREIGN KEY (`bet_id`) REFERENCES `bets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payouts_outlets1` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payouts_tickets1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payouts_transactions1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payouts_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payouts_winning_results1` FOREIGN KEY (`winning_result_id`) REFERENCES `winning_results` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `fk_tickets_outlets1` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_tickets_transactions1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tickets_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `ticket_cancellations`
--
ALTER TABLE `ticket_cancellations`
  ADD CONSTRAINT `fk_ticket_cancellations_tickets1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ticket_cancellations_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_transactions_outlets1` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transactions_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user_time_logs`
--
ALTER TABLE `user_time_logs`
  ADD CONSTRAINT `fk_user_time_logs_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `winning_results`
--
ALTER TABLE `winning_results`
  ADD CONSTRAINT `fk_winning_results_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `winning_tickets`
--
ALTER TABLE `winning_tickets`
  ADD CONSTRAINT `fk_winning_tickets_bets1` FOREIGN KEY (`bet_id`) REFERENCES `bets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_winning_tickets_tickets1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_winning_tickets_winning_results1` FOREIGN KEY (`winning_result_id`) REFERENCES `winning_results` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
