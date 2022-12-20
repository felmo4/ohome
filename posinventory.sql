-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 27, 2022 at 06:25 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `posinventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblactivity`
--

CREATE TABLE `tblactivity` (
  `activityNO` int(10) NOT NULL,
  `time` datetime NOT NULL DEFAULT current_timestamp(),
  `details` varchar(100) NOT NULL,
  `employeeID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tblactivity`
--

INSERT INTO `tblactivity` (`activityNO`, `time`, `details`, `employeeID`) VALUES
(1, '2022-01-13 12:17:19', 'Product edited: P0001', '1001'),
(2, '2022-01-13 19:10:07', 'Product edited: P0001', '1001'),
(3, '2022-01-15 13:24:05', '', '1001'),
(4, '2022-01-15 13:45:06', 'Stock added: 1111111114', '1001'),
(5, '2022-01-15 17:03:13', 'Stock adjustment: product: P0101, reason:Damaged, returned to supplier', '1001'),
(6, '2022-01-15 17:05:53', 'Product edited: P0101', '1001'),
(7, '2022-01-15 17:09:22', 'Product edited: P0102', '1001'),
(8, '2022-01-15 17:27:43', 'Stock adjustment: product: P0102, reason: Damaged, returned to supplier', '1001'),
(9, '2022-01-15 17:42:51', 'Product edited: P0101', '1001'),
(10, '2022-01-15 17:43:14', 'Product edited: P0101', '1001'),
(11, '2022-01-15 17:43:24', 'Product edited: P0102', '1001'),
(12, '2022-01-16 11:15:53', 'Supplier added: Ohome Corporation', '1001'),
(13, '2022-01-16 11:16:31', 'Supplier edited: Ohome Corporation', '1001'),
(14, '2022-01-16 11:17:04', 'Supplier edited: Ohome Corporation', '1001'),
(15, '2022-01-16 11:17:20', 'Supplier added: mali', '1001'),
(16, '2022-01-16 11:17:26', 'Supplier deleted: ', '1001'),
(17, '2022-01-16 11:25:42', 'Category added: mali', '1001'),
(18, '2022-01-16 11:26:09', 'Category edited: ay mali', '1001'),
(19, '2022-01-16 11:26:18', 'Category deleted: ', '1001'),
(20, '2022-01-17 00:06:11', 'Product added: OUT', '1001'),
(21, '2022-01-17 15:34:22', 'New invoice: invoice number: 1111111111', '1001'),
(22, '2022-01-17 16:20:43', 'New invoice: invoice number: 1111111112', '1001'),
(23, '2022-01-17 16:53:23', 'New invoice: invoice number: 1111111113', '1001'),
(24, '2022-01-17 17:08:48', 'New invoice: invoice number: 1111111114', '1001'),
(25, '2022-01-17 17:20:55', 'New invoice: invoice number: 1111111115', '1001'),
(26, '2022-01-17 19:09:18', 'Stock added: reference number: 1111111115', '1001'),
(27, '2022-01-17 19:56:11', 'Stock adjustment: product: P0001, reason: Damaged, returned to supplier', '1001'),
(28, '2022-01-18 11:37:25', 'Invoice void: invoice number: 1111111115', '1001'),
(29, '2022-01-18 12:10:24', 'Invoice void: invoice number: 1111111111, by: 1001, reason: ', '1001'),
(30, '2022-01-18 14:24:17', 'New invoice: invoice number: 1111111111', '1001'),
(31, '2022-01-18 14:27:00', 'New invoice: invoice number: 1111111112', '1001'),
(32, '2022-01-18 14:32:52', 'New invoice: invoice number: 1111111113', '1001'),
(33, '2022-01-18 14:39:02', 'Item void: invoice number: 1111111113, by: 1001, reason: ', '1001'),
(34, '2022-01-18 14:43:48', 'New invoice: invoice number: 1111111119', '1001'),
(35, '2022-01-18 14:45:50', 'Invoice void: invoice number: 1111111119, by: 1001, reason: ', '1001'),
(36, '2022-01-18 14:47:23', 'Item void: invoice number: 1111111111, by: 1001, reason: ', '1001'),
(37, '2022-01-18 23:10:29', 'New invoice: invoice number: 1111111119', '1001'),
(38, '2022-01-18 23:11:23', 'Invoice return: invoice number: 1111111119, reason: ', '1001'),
(39, '2022-01-18 23:15:02', 'New invoice: invoice number: 22222222222', '1001'),
(40, '2022-01-18 23:15:53', 'Invoice return: invoice number: 22222222222, reason: ', '1001'),
(41, '2022-01-20 18:03:43', 'PB0254', '1001'),
(42, '2022-01-20 19:43:10', 'Product bundle added: PB0255', '1001'),
(43, '2022-01-20 19:56:28', 'Product bundle added: PB0255', '1001'),
(44, '2022-01-20 20:01:45', 'Product bundle added: PB0255', '1001'),
(45, '2022-01-20 20:07:03', 'Product bundle added: PB0255', '1001'),
(46, '2022-01-20 21:59:38', 'Product bundle added: PB0255', '1001'),
(47, '2022-01-20 22:05:56', 'Product bundle added: PB0259', '1001'),
(48, '2022-01-20 22:07:02', 'Product bundle added: PB0259', '1001'),
(49, '2022-01-20 23:22:23', 'Product deleted: PB0255', '1001'),
(50, '2022-01-20 23:30:29', 'Product bundle deleted: PB0259', '1001'),
(51, '2022-01-20 23:38:11', 'Product bundle added: PB0254', '1001'),
(52, '2022-01-21 13:33:31', 'Product bundle added: PB0266', '1001'),
(53, '2022-01-21 23:50:37', 'Stock added: reference number: 1111111120', '1001'),
(54, '2022-01-21 23:57:25', 'Stock added: reference number: 1111111121', '1001'),
(55, '2022-01-22 01:07:09', 'Items bundled: 1 PB0254', '1001'),
(56, '2022-01-22 01:08:44', 'Items unbundled: 1 PB0254', '1001'),
(57, '2022-01-22 01:13:43', 'Items bundled: 2 PB0254', '1001'),
(58, '2022-01-22 01:14:26', 'Items unbundled: 1 PB0254', '1001'),
(59, '2022-01-22 18:41:40', 'New item discount: Bronx Big Sale', '1001'),
(60, '2022-01-22 22:49:18', 'Item discount edited: Bronx Big Sale', '1001'),
(61, '2022-01-22 23:10:07', 'Item discount edited: Bronx Big Sale', '1001'),
(62, '2022-01-23 10:29:58', 'Item discount edited: Basco Sale', '1001'),
(63, '2022-01-23 14:59:33', 'Item discount edited: Basco Sale', '1001'),
(64, '2022-01-23 15:00:00', 'Item discount edited: Basco Sale', '1001'),
(65, '2022-01-23 15:00:28', 'Item discount edited: Basco Sale', '1001'),
(66, '2022-01-23 16:26:48', 'Invoice return: invoice number: 1111111113, reason: ', '1001'),
(67, '2022-01-23 16:37:31', 'Invoice return: invoice number: 22222222222, reason: ', '1001'),
(68, '2022-01-23 16:40:43', 'Invoice return: invoice number: 1111111119, reason: ', '1001'),
(69, '2022-01-23 16:55:02', 'Item void: invoice number: 1111111113, by: 1001, reason: ', '1001'),
(70, '2022-01-23 16:57:56', 'Item void: invoice number: 1111111113, by: 1001, reason: ', '1001'),
(71, '2022-01-23 17:03:27', 'Item void: invoice number: 1111111114, by: 1001, reason: ', '1001'),
(72, '2022-01-23 17:04:15', 'Invoice void: invoice number: 1111111113, by: 1001, reason: ', '1001'),
(73, '2022-01-23 17:18:57', 'Invoice return: invoice number: 1111111111, reason: ', '1001'),
(74, '2022-01-23 17:27:40', 'Item return: invoice number: 1111111119, reason: ', '1001'),
(75, '2022-01-23 18:17:09', 'Item return: invoice number: 1111111119, reason: ', '1001'),
(76, '2022-01-24 04:08:34', 'Category deleted: ', '1001'),
(77, '2022-01-24 07:33:41', 'Category deleted: ', '1001'),
(78, '2022-01-24 08:34:22', 'New user registered: 2001', '1001'),
(79, '2022-01-24 08:51:53', 'Category deleted: ', '1001'),
(80, '2022-01-24 08:55:12', 'Category deleted: ', '1001'),
(81, '2022-01-24 08:58:49', 'User deactivated: ralph', '1001'),
(82, '2022-01-24 09:01:13', 'User activated: ralph', '1001'),
(83, '2022-01-24 09:01:15', 'User deactivated: ralph', '1001'),
(84, '2022-01-24 09:01:17', 'User activated: ralph', '1001'),
(85, '2022-01-24 09:02:16', 'User deactivated: ralph', '1001'),
(86, '2022-01-24 09:02:26', 'User activated: ralph', '1001'),
(87, '2022-01-24 09:29:31', 'New employee registered: 1002', '1001'),
(88, '2022-01-24 09:35:52', 'User resigned: ', '1001'),
(89, '2022-01-24 09:38:27', 'User resigned: 2001', '1001'),
(90, '2022-01-24 09:53:57', 'User password changed', '1001'),
(91, '2022-01-24 09:54:25', 'User password changed', '1001'),
(92, '2022-01-24 16:24:11', 'Item discount edited: Basco Sale', '1001'),
(93, '2022-01-24 17:52:26', 'New invoice: invoice number: 20220124-001', '2001'),
(94, '2022-01-24 18:26:39', 'Product added: P0008', '1001'),
(95, '2022-01-24 18:27:19', 'Product edited: P0008', '1001'),
(96, '2022-01-24 18:28:36', 'Items bundled: 2 PB0254', '1001'),
(97, '2022-01-24 18:29:24', 'Items unbundled: 1 PB0254', '1001'),
(98, '2022-01-24 18:31:44', 'Product bundle added: PB0244', '1001'),
(99, '2022-01-24 18:34:12', 'Stock added: reference number: 20220124', '1001'),
(100, '2022-01-24 18:37:07', 'Supplier added: Mandaue Foam', '1001'),
(101, '2022-01-24 18:37:46', 'Category added: Cabinet', '1001'),
(102, '2022-01-24 18:38:03', 'Category edited: Lighting', '1001'),
(103, '2022-01-24 18:38:15', 'Category deleted: Lighting', '1001'),
(104, '2022-01-24 18:38:20', 'Supplier deleted: Mandaue Foam', '1001'),
(105, '2022-01-24 18:43:56', 'New item discount: Mandaue Foam Clearance', '1001'),
(106, '2022-01-24 18:44:33', 'Item discount edited: Mandaue Foam Clearance', '1001'),
(107, '2022-01-24 18:45:11', 'Item discount edited: Mandaue Foam Clearance', '1001'),
(108, '2022-01-24 18:48:01', 'Item return: invoice number: 1111111112, reason: Damaged, returned to supplier', '1001'),
(109, '2022-01-24 18:52:36', 'New employee registered: Jon', '1001'),
(110, '2022-01-24 18:52:53', 'Employee resigned: Jon', '1001'),
(111, '2022-01-24 18:53:26', 'New employee registered: 1004', '1001'),
(112, '2022-01-24 18:54:20', 'New user registered: 1004', '1001'),
(113, '2022-01-24 18:54:28', 'User deactivated: ralph', '1001'),
(114, '2022-01-24 18:54:31', 'User activated: ralph', '1001'),
(115, '2022-01-24 18:54:34', 'User deactivated: ralph', '1001'),
(116, '2022-01-24 18:54:38', 'User deactivated: jon', '1001'),
(117, '2022-01-24 18:54:41', 'User activated: ralph', '1001'),
(118, '2022-01-24 19:51:35', 'New invoice: invoice number: 20220124-003', '2001'),
(119, '2022-01-24 19:52:16', 'Item return: invoice number: 20220124-001, reason: ', '2001'),
(120, '2022-01-24 19:56:42', 'New invoice: invoice number: 20220124-007', '2001'),
(121, '2022-01-24 19:57:11', 'Item return: invoice number: 20220124-001, reason: Damaged, returned to supplier', '2001'),
(122, '2022-01-24 19:58:58', 'Product added: P0018', '1001'),
(123, '2022-01-24 19:59:19', 'Product deleted: OUT', '1001'),
(124, '2022-01-24 20:01:11', 'Product bundle added: PB0678', '1001'),
(125, '2022-01-24 20:02:11', 'Stock added: reference number: 2022012409', '1001'),
(126, '2022-01-24 20:02:31', 'Items bundled: 3 PB0678', '1001'),
(127, '2022-01-24 20:03:02', 'Stock adjustment: product: P0101, reason: Damaged, returned to supplier', '1001'),
(128, '2022-01-24 20:03:29', 'Supplier added: Mandaue Foam', '1001'),
(129, '2022-01-24 20:03:44', 'Category added: Lighting', '1001'),
(130, '2022-01-24 20:04:00', 'Category added: Cabinet', '1001'),
(131, '2022-01-24 20:08:28', 'New invoice: invoice number: 20220124-010', '2001'),
(132, '2022-01-24 20:09:09', 'Item return: invoice number: 20220124-001, reason: ', '2001'),
(133, '2022-01-24 20:09:23', 'Invoice return: invoice number: 20220124-003, reason: ', '2001'),
(134, '2022-01-24 20:11:27', 'Product added: P0205', '1001'),
(135, '2022-01-24 20:17:56', 'Product added: OUT', '1001'),
(136, '2022-01-24 20:18:11', 'Product edited: OUT', '1001'),
(137, '2022-01-24 20:18:19', 'Product deleted: OUT', '1001'),
(138, '2022-01-24 20:20:51', 'Product added: to', '1001'),
(139, '2022-01-24 20:21:06', 'Product deleted: to', '1001'),
(140, '2022-01-24 20:22:00', 'Items bundled: 2 PB0244', '1001'),
(141, '2022-01-24 20:22:12', 'Items unbundled: 1 PB0244', '1001'),
(142, '2022-01-24 20:23:21', 'Product bundle added: PB4567', '1001'),
(143, '2022-01-24 20:24:41', 'Stock added: reference number: 20220124230', '1001'),
(144, '2022-01-24 20:25:08', 'Stock adjustment: product: P0001, reason: Damaged, returned to supplier', '1001'),
(145, '2022-01-24 20:26:32', 'Supplier edited: Mandaue Foam', '1001'),
(146, '2022-01-24 20:26:43', 'Supplier deleted: Mandaue Foam', '1001'),
(147, '2022-01-24 20:27:08', 'Category added: sample', '1001'),
(148, '2022-01-24 20:27:12', 'Category deleted: sample', '1001'),
(149, '2022-01-24 20:29:03', 'New item discount: Omni Clearance', '1001'),
(150, '2022-01-24 20:31:41', 'Item discount deleted: ', '1001'),
(151, '2022-01-24 20:33:47', 'New item discount: Mandaue Foam Clearance', '1001'),
(152, '2022-01-24 20:37:12', 'New user registered: 1002', '1001'),
(153, '2022-01-24 20:37:18', 'User deactivated: kryll', '1001'),
(154, '2022-01-24 20:37:22', 'User activated: kryll', '1001'),
(155, '2022-01-25 10:33:18', 'Product edited: P0205', '1001'),
(156, '2022-01-25 11:19:38', 'Product edited: PB0244', '1001'),
(157, '2022-01-25 11:39:49', 'Product bundle deleted: PB0266', '1001'),
(158, '2022-01-25 12:55:25', 'Product edited: P0008', '1001'),
(159, '2022-01-25 13:34:22', 'Items unbundled: 1 PB0244', '1001'),
(160, '2022-01-25 13:36:27', 'Items bundled: 2 PB4567', '1001'),
(161, '2022-01-25 13:36:42', 'Product bundle deleted: PB4567', '1001'),
(162, '2022-01-25 13:38:05', 'Items bundled: 2 PB0244', '1001'),
(163, '2022-01-25 13:38:15', 'Items unbundled: 1 PB0244', '1001'),
(164, '2022-01-25 13:39:16', 'Product bundle deleted: PB0244', '1001'),
(165, '2022-01-25 13:40:03', 'Product bundle deleted: PB0254', '1001'),
(166, '2022-01-25 13:47:06', 'Product bundle added: PB0266', '1001'),
(167, '2022-01-25 16:47:49', 'Product deleted: P0008', '1001'),
(168, '2022-01-25 16:49:52', 'Product edited: P0102', '1001'),
(169, '2022-01-25 17:24:19', 'Supplier added: ', '1001'),
(170, '2022-01-25 17:31:20', 'Category edited: Cabinet', '1001'),
(171, '2022-01-25 18:07:07', 'Item discount deleted: ', '1001'),
(172, '2022-01-25 18:08:25', 'Item discount deleted: Omni Clearance', '1001'),
(173, '2022-01-25 18:16:44', 'Item discount edited: Bronx Big Sale', '1001'),
(174, '2022-01-25 18:16:55', 'Item discount edited: Bronx Big Sale', '1001'),
(175, '2022-01-25 18:20:15', 'New item discount: Sofa for singles', '1001'),
(176, '2022-01-25 19:49:19', 'New invoice: invoice number: 20220125-001', '2001'),
(177, '2022-01-25 20:00:53', 'New invoice: invoice number: 20220124-002', '2001'),
(178, '2022-01-26 11:50:39', 'Items bundled: 2 PB0678', '1001'),
(179, '2022-01-27 11:44:07', 'Cashier login successful', '2001'),
(180, '2022-01-27 11:45:50', 'Cashier logged out', '2001'),
(181, '2022-01-27 11:46:01', 'Cashier login successful', '2001'),
(182, '2022-01-27 11:46:05', 'Cashier logged out', '2001'),
(183, '2022-01-27 11:46:12', 'Admin login successful', '1001'),
(184, '2022-01-27 11:54:08', 'Admin logged out', '1001'),
(185, '2022-01-27 11:56:28', 'Cashier login successful', '2001'),
(186, '2022-01-27 11:57:53', 'New invoice: invoice number: 20220127-001', '2001'),
(187, '2022-01-27 11:58:37', 'Item return: invoice number: 20220127-001, reason: ', '2001'),
(188, '2022-01-27 12:04:03', 'New invoice: invoice number: 20220127-002', '2001'),
(189, '2022-01-27 12:17:57', 'Item return: invoice number: 20220127-001, reason: Damaged, returned to supplier', '2001'),
(190, '2022-01-27 12:18:28', 'Cashier logged out', '2001'),
(191, '2022-01-27 12:18:37', 'Admin login successful', '1001'),
(192, '2022-01-27 12:19:22', 'Admin logged out', '1001'),
(193, '2022-01-27 12:19:26', 'Cashier login successful', '2001'),
(194, '2022-01-27 12:20:20', 'Item return: invoice number: 20220127-002, reason: Damaged, returned to supplier', '2001'),
(195, '2022-01-27 12:22:32', 'Invoice return: invoice number: 20220127-002, reason: Damaged, returned to supplier', '2001'),
(196, '2022-01-27 12:22:52', 'Cashier logged out', '2001'),
(197, '2022-01-27 12:23:05', 'Cashier login successful', '2001'),
(198, '2022-01-27 12:23:12', 'Cashier logged out', '2001'),
(199, '2022-01-27 12:23:16', 'Cashier login successful', '2001'),
(200, '2022-01-27 12:26:59', 'Cashier logged out', '2001'),
(201, '2022-01-27 12:27:12', 'Admin login successful', '1001'),
(202, '2022-01-27 12:32:22', 'Items unbundled: 1 PB0678', '1001'),
(203, '2022-01-27 12:32:39', 'Items bundled: 3 PB0678', '1001'),
(204, '2022-01-27 12:43:56', 'Admin logged out', '1001'),
(205, '2022-01-27 12:45:37', 'Cashier login successful', '2001'),
(206, '2022-01-27 12:46:42', 'New invoice: invoice number: 20220127-003', '2001'),
(207, '2022-01-27 12:47:37', 'Item return: invoice number: 20220127-003, reason: Damaged, returned to supplier', '2001'),
(208, '2022-01-27 12:47:54', 'Cashier logged out', '2001'),
(209, '2022-01-27 12:52:13', 'Cashier login successful', '2001'),
(210, '2022-01-27 12:53:11', 'New invoice: invoice number: 20220127-004', '2001'),
(211, '2022-01-27 12:53:49', 'Item return: invoice number: 20220127-004, reason: Damaged, returned to supplier', '2001'),
(212, '2022-01-27 12:54:04', 'Cashier logged out', '2001'),
(213, '2022-01-27 12:58:32', 'Admin login successful', '1001'),
(214, '2022-01-27 12:59:19', 'Product added: product', '1001'),
(215, '2022-01-27 12:59:32', 'Product edited: product', '1001'),
(216, '2022-01-27 12:59:43', 'Product deleted: product', '1001'),
(217, '2022-01-27 13:00:34', 'Items unbundled: 2 PB0678', '1001'),
(218, '2022-01-27 13:00:46', 'Items bundled: 1 PB0678', '1001'),
(219, '2022-01-27 13:01:55', 'Stock added: reference number: 3424523', '1001'),
(220, '2022-01-27 13:02:17', 'Items bundled: 5 PB0266', '1001'),
(221, '2022-01-27 13:02:55', 'Stock adjustment: product: P0101, reason: Damaged, returned to supplier', '1001'),
(222, '2022-01-27 13:03:20', 'Supplier added: supplier', '1001'),
(223, '2022-01-27 13:03:37', 'Category added: category', '1001'),
(224, '2022-01-27 13:05:29', 'Item discount edited: Basco Sale', '1001'),
(225, '2022-01-27 13:07:50', 'New employee registered: 1005', '1001'),
(226, '2022-01-27 13:08:07', 'New user registered: 1005', '1001'),
(227, '2022-01-27 13:08:20', 'User deactivated: cashier', '1001'),
(228, '2022-01-27 13:08:31', 'User deactivated: rochelle', '1001'),
(229, '2022-01-27 13:08:55', 'User activated: rochelle', '1001'),
(230, '2022-01-27 13:09:00', 'User activated: cashier', '1001'),
(231, '2022-01-27 13:09:11', 'Admin logged out', '1001'),
(232, '2022-01-27 13:09:22', 'Cashier login successful', '1005'),
(233, '2022-01-27 13:09:26', 'Cashier logged out', '1005'),
(234, '2022-01-27 13:09:45', 'Admin login successful', '1001'),
(235, '2022-01-27 13:10:13', 'User password changed', '1001'),
(236, '2022-01-27 13:10:18', 'Admin logged out', '1001'),
(237, '2022-01-27 13:10:33', 'Admin login successful', '1001'),
(238, '2022-01-27 13:10:38', 'Admin logged out', '1001');

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `categoryID` int(10) NOT NULL,
  `description` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tblcategory`
--

INSERT INTO `tblcategory` (`categoryID`, `description`) VALUES
(2, 'Bed Frame'),
(5, 'Bundle'),
(8, 'Cabinet'),
(10, 'category'),
(7, 'Lighting'),
(3, 'Mattress'),
(1, 'Sofa');

-- --------------------------------------------------------

--
-- Table structure for table `tblemployees`
--

CREATE TABLE `tblemployees` (
  `employeeID` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tblemployees`
--

INSERT INTO `tblemployees` (`employeeID`, `name`, `status`) VALUES
('1001', 'Ralph Maglalang', 'active'),
('1002', 'Kryll Aldana', 'active'),
('1004', 'Jon Snow', 'active'),
('1005', 'Cashier', 'active'),
('2001', 'Rochelle Apostol', 'resigned');

-- --------------------------------------------------------

--
-- Table structure for table `tblproductbundles`
--

CREATE TABLE `tblproductbundles` (
  `bundleID` int(10) NOT NULL,
  `bundlecode` varchar(10) NOT NULL,
  `productcode` varchar(10) NOT NULL,
  `quantity` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tblproductbundles`
--

INSERT INTO `tblproductbundles` (`bundleID`, `bundlecode`, `productcode`, `quantity`) VALUES
(10, 'PB0678', 'P0018', 1),
(12, 'PB0266', 'P0205', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tblproducts`
--

CREATE TABLE `tblproducts` (
  `productcode` varchar(10) NOT NULL,
  `barcode` varchar(20) NOT NULL,
  `description` varchar(50) DEFAULT NULL,
  `details` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) NOT NULL,
  `quantity` int(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tblproducts`
--

INSERT INTO `tblproducts` (`productcode`, `barcode`, `description`, `details`, `price`, `category`, `quantity`) VALUES
('P0001', '6745464354', 'Basco Sofa L-Shape', 'Basco Sofa L-Shape', '31700.00', 'Sofa', 12),
('P0018', '3253345634', 'Single Sofa', 'Single Sofa, black', '8000.00', 'Sofa', 4),
('P0101', '5687646534', 'Bronx Bed Single', 'Bronx Bed Single', '5114.00', 'Bed Frame', 10),
('P0102', '1098798431', 'Bronx Bed Semi-double', 'Bronx Bed Semi-double', '7849.00', 'Bed Frame', 9),
('P0205', '4325235259', 'Omni 10W', 'Omni 10W', '50.00', 'Lighting', 10),
('PB0266', '4633456345', 'B1T1 Omni 10w', 'B1T1 Omni 10w', '100.00', 'Bundle', 5),
('PB0678', '6789723423', 'Single Sofa Set', 'Single Sofa 2pcs', '8000.00', 'Bundle', 5);

-- --------------------------------------------------------

--
-- Table structure for table `tblpromos`
--

CREATE TABLE `tblpromos` (
  `promoID` int(10) NOT NULL,
  `promotype` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  `rewardtype` varchar(5) NOT NULL,
  `reward` int(10) NOT NULL,
  `product` varchar(50) NOT NULL DEFAULT 'All ',
  `used` int(10) NOT NULL DEFAULT 0,
  `uselimit` int(10) DEFAULT NULL,
  `periodstart` date NOT NULL DEFAULT current_timestamp(),
  `periodend` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tblpromos`
--

INSERT INTO `tblpromos` (`promoID`, `promotype`, `description`, `rewardtype`, `reward`, `product`, `used`, `uselimit`, `periodstart`, `periodend`) VALUES
(1, 'Item Discount', 'Basco Sale', 'Php', 10000, 'P0001', 2, 10, '2022-01-22', '2022-01-30'),
(2, 'Item Discount', 'Bronx Big Sale', '%', 80, 'P0101', 10, 0, '2022-01-23', '2022-01-30'),
(3, 'Item Discount', 'Bronx Big Sale', '%', 75, 'P0102', 4, 0, '2022-01-25', '2022-01-30'),
(7, 'Item Discount', 'Sofa for singles', '%', 44, 'P0018', 2, 0, '2022-01-25', '2022-01-26'),
(8, 'Item Discount', 'Sofa for singles', '%', 44, 'PB0678', 4, 0, '2022-01-25', '2022-01-26');

-- --------------------------------------------------------

--
-- Table structure for table `tblreturns`
--

CREATE TABLE `tblreturns` (
  `returnID` int(10) NOT NULL,
  `purchasetime` datetime NOT NULL,
  `returntime` datetime NOT NULL DEFAULT current_timestamp(),
  `invoiceNO` varchar(20) NOT NULL,
  `productcode` varchar(10) NOT NULL,
  `description` varchar(50) NOT NULL,
  `quantity` int(10) NOT NULL,
  `itemtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tblreturns`
--

INSERT INTO `tblreturns` (`returnID`, `purchasetime`, `returntime`, `invoiceNO`, `productcode`, `description`, `quantity`, `itemtotal`, `discount`) VALUES
(5, '2022-01-16 23:10:29', '2022-01-23 18:17:09', '1111111119', 'P0102', 'Bronx Bed Semi-double', 1, '7840.00', '0.00'),
(6, '2022-01-22 23:59:59', '2022-01-24 18:48:01', '1111111112', 'P0001', 'Basco Sofa L-Shape', 2, '63400.00', '0.00'),
(7, '2022-01-24 17:52:25', '2022-01-24 19:52:16', '20220124-001', 'PROMO P000', 'Basco Sofa L-Shape', 1, '21700.00', '10000.00'),
(8, '2022-01-24 17:52:25', '2022-01-24 19:57:11', '20220124-001', 'PROMO P010', 'Bronx Bed Single', 1, '1022.80', '4091.20'),
(9, '2022-01-24 17:52:25', '2022-01-24 20:09:09', '20220124-001', 'P0102', 'Bronx Bed Semi-double', 2, '15680.00', '0.00'),
(10, '2022-01-24 19:51:35', '2022-01-24 20:09:23', '20220124-003', 'P0102', 'Bronx Bed Semi-double', 1, '7840.00', '0.00'),
(11, '2022-01-24 19:51:35', '2022-01-24 20:09:23', '20220124-003', 'P0008', 'Mandaue Foam ', 2, '30000.00', '0.00'),
(12, '2022-01-27 11:57:53', '2022-01-27 11:58:37', '20220127-001', 'PB0678', 'Single Sofa Set', 2, '16000.00', '0.00'),
(13, '2022-01-27 11:57:53', '2022-01-27 12:17:57', '20220127-001', 'P0101', 'Bronx Bed Single', 3, '3068.40', '4091.20'),
(14, '2022-01-27 12:04:03', '2022-01-27 12:20:19', '20220127-002', 'P0102', 'Bronx Bed Semi-double', 2, '3924.50', '5886.75'),
(15, '2022-01-27 12:04:03', '2022-01-27 12:22:31', '20220127-002', 'P0001', 'Basco Sofa L-Shape', 2, '63400.00', '0.00'),
(16, '2022-01-27 12:46:42', '2022-01-27 12:47:37', '20220127-003', 'P0001', 'Basco Sofa L-Shape', 2, '63400.00', '0.00'),
(17, '2022-01-27 12:53:11', '2022-01-27 12:53:49', '20220127-004', 'PB0678', 'Single Sofa Set', 2, '16000.00', '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `tblsales`
--

CREATE TABLE `tblsales` (
  `saleNO` int(10) NOT NULL,
  `invoiceNO` varchar(20) NOT NULL,
  `productcode` varchar(10) NOT NULL,
  `description` varchar(50) NOT NULL,
  `quantity` int(10) NOT NULL,
  `itemtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `discountdetails` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tblsales`
--

INSERT INTO `tblsales` (`saleNO`, `invoiceNO`, `productcode`, `description`, `quantity`, `itemtotal`, `discount`, `discountdetails`) VALUES
(7, '1111111114', 'P0001', 'Basco Sofa L-Shape', 3, '95100.00', '0.00', NULL),
(18, '22222222222', 'P0001', 'Basco Sofa L-Shape', 3, '95100.00', '0.00', NULL),
(25, '20220124-007', 'P0102', 'Bronx Bed Semi-double', 2, '15680.00', '0.00', ''),
(26, '20220124-007', 'PROMO P010', 'Bronx Bed Single', 1, '1022.80', '4091.20', 'Bronx Big Sale 80% OFF'),
(27, '20220124-007', 'PROMO P000', 'Basco Sofa L-Shape', 1, '21700.00', '10000.00', 'Basco Sale 10000Php OFF'),
(28, '20220124-010', 'P0018', 'Single Sofa', 3, '24000.00', '0.00', ''),
(29, '20220124-010', 'PB0254', 'Bronx Collection', 2, '21998.00', '0.00', ''),
(30, '20220125-001', 'PROMO PB06', 'Single Sofa Set', 3, '13440.00', '3520.00', 'Sofa for singles 44% OFF'),
(31, '20220125-001', 'P0205', 'Omni 10W', 1, '50.00', '0.00', ''),
(32, '20220125-001', 'PROMO P010', 'Bronx Bed Single', 2, '2045.60', '4091.20', 'Bronx Big Sale 80% OFF'),
(33, '20220124-002', 'P0001', 'Basco Sofa L-Shape', 1, '31700.00', '0.00', ''),
(34, '20220124-002', 'PB0678', 'Single Sofa Set', 1, '4480.00', '3520.00', 'Sofa for singles 44% OFF'),
(35, '20220124-002', 'P0018', 'Single Sofa', 2, '8960.00', '3520.00', 'Sofa for singles 44% OFF'),
(36, '20220124-002', 'P0101', 'Bronx Bed Single', 2, '2045.60', '4091.20', 'Bronx Big Sale 80% OFF'),
(42, '20220127-003', 'P0102', 'Bronx Bed Semi-double', 2, '3924.50', '5886.75', 'Bronx Big Sale 75% OFF'),
(43, '20220127-004', 'P0101', 'Bronx Bed Single', 1, '1022.80', '4091.20', 'Bronx Big Sale 80% OFF');

-- --------------------------------------------------------

--
-- Table structure for table `tblsalesinvoice`
--

CREATE TABLE `tblsalesinvoice` (
  `invoiceID` int(10) NOT NULL,
  `invoiceNO` varchar(20) NOT NULL,
  `time` datetime NOT NULL DEFAULT current_timestamp(),
  `discount` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `bill` decimal(10,2) NOT NULL,
  `billchange` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tblsalesinvoice`
--

INSERT INTO `tblsalesinvoice` (`invoiceID`, `invoiceNO`, `time`, `discount`, `total`, `bill`, `billchange`) VALUES
(11, '22222222222', '2022-01-23 23:15:02', '0.00', '95100.00', '95100.00', '0.00'),
(16, '20220124-007', '2022-01-24 19:56:42', '14091.20', '38402.80', '39000.00', '597.20'),
(17, '20220124-010', '2022-01-24 20:08:27', '0.00', '45998.00', '46000.00', '2.00'),
(18, '20220125-001', '2022-01-25 19:49:19', '18742.40', '15535.60', '16000.00', '464.40'),
(19, '20220124-002', '2022-01-25 20:00:52', '18742.40', '47185.60', '47500.00', '314.40'),
(22, '20220127-003', '2022-01-27 12:46:42', '11773.50', '3924.50', '67400.00', '63475.50'),
(23, '20220127-004', '2022-01-27 12:53:11', '4091.20', '1022.80', '18000.00', '16977.20');

-- --------------------------------------------------------

--
-- Table structure for table `tblstock`
--

CREATE TABLE `tblstock` (
  `stockNO` int(10) NOT NULL,
  `referenceNO` varchar(20) NOT NULL,
  `productcode` varchar(10) NOT NULL,
  `description` varchar(50) NOT NULL,
  `quantity` int(10) NOT NULL,
  `itemtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tblstock`
--

INSERT INTO `tblstock` (`stockNO`, `referenceNO`, `productcode`, `description`, `quantity`, `itemtotal`) VALUES
(1, '1111111113', 'P0101', 'Bronx Bed Single', 2, '10228.00'),
(2, '1111111114', 'P0001', 'Basco Sofa L-Shape', 5, '158500.00'),
(3, '1111111115', 'P0102', 'Bronx Bed Semi-double', 2, '15680.00'),
(4, '1111111115', 'P0001', 'Basco Sofa L-Shape', 2, '63400.00'),
(5, '1111111115', 'P0101', 'Bronx Bed Single', 3, '15342.00'),
(6, '1111111120', 'P0101', 'Bronx Bed Single', 2, '10228.00'),
(7, '1111111121', 'P0102', 'Bronx Bed Semi-double', 3, '23520.00'),
(8, '20220124', 'P0008', 'Mandaue Foam ', 10, '150000.00'),
(9, '20220124', 'P0101', 'Bronx Bed Single', 15, '76710.00'),
(10, '2022012409', 'P0018', 'Single Sofa', 10, '80000.00'),
(11, '20220124230', 'P0205', 'Omni 10W', 5, '250.00'),
(12, '20220124230', 'P0018', 'Single Sofa', 5, '40000.00'),
(13, '3424523', 'P0205', 'Omni 10W', 20, '1000.00');

-- --------------------------------------------------------

--
-- Table structure for table `tblstockinvoice`
--

CREATE TABLE `tblstockinvoice` (
  `stockinvoiceNO` int(10) NOT NULL,
  `referenceNO` varchar(20) NOT NULL,
  `time` datetime NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `supplier` varchar(50) NOT NULL,
  `admin` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tblstockinvoice`
--

INSERT INTO `tblstockinvoice` (`stockinvoiceNO`, `referenceNO`, `time`, `total`, `supplier`, `admin`) VALUES
(1, '1111111111', '2022-01-15 13:00:00', '18068.00', 'Mollimo Inc.', '1001'),
(2, '1111111112', '2022-01-15 13:21:43', '41928.00', 'Mollimo Inc.', '1001'),
(3, '1111111113', '2022-01-15 13:24:05', '10228.00', 'Mollimo Inc.', '1001'),
(4, '1111111114', '2022-01-15 13:45:06', '158500.00', 'Mollimo Inc.', '1001'),
(5, '1111111115', '2022-01-17 19:09:17', '94422.00', 'Ohome Corporation', '1001'),
(6, '1111111120', '2022-01-21 23:50:37', '10228.00', 'Ohome Corporation', '1001'),
(7, '1111111121', '2022-01-21 23:57:24', '23520.00', 'Mollimo Inc.', '1001'),
(8, '20220124', '2022-01-24 18:34:12', '226710.00', 'Ohome Corporation', '1001'),
(9, '2022012409', '2022-01-24 20:02:11', '80000.00', 'Mollimo Inc.', '1001'),
(10, '20220124230', '2022-01-24 20:24:41', '40250.00', 'Mandaue Foam', '1001'),
(11, '3424523', '2022-01-27 13:01:55', '1000.00', 'Ohome Corporation', '1001');

-- --------------------------------------------------------

--
-- Table structure for table `tblsuppliers`
--

CREATE TABLE `tblsuppliers` (
  `supplierID` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `person` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tblsuppliers`
--

INSERT INTO `tblsuppliers` (`supplierID`, `name`, `person`, `phone`, `email`) VALUES
(1, 'Mollimo Inc.', 'Ralph Emmanuel', '09999999999', 'mollimo.inc@gmail.com'),
(2, 'Ohome Corporation', 'Pres Rochelle', '5010026', 'ohomeco@yahoo.com'),
(7, 'supplier', 'supplier', 'supplier', 'supplier');

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `userID` int(10) NOT NULL,
  `employeeID` varchar(10) NOT NULL,
  `username` varchar(10) NOT NULL,
  `password` varchar(10) NOT NULL,
  `role` varchar(10) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`userID`, `employeeID`, `username`, `password`, `role`, `status`) VALUES
(1, '1001', 'ralph', 'maglalang', 'admin', 'active'),
(2, '2001', 'rochelle', 'apostol', 'cashier', 'active'),
(3, '1004', 'jon', 'snow', 'admin', 'inactive'),
(4, '1002', 'kryll', 'aldana', 'cashier', 'active'),
(5, '1005', 'cashier', 'cashier', 'cashier', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblactivity`
--
ALTER TABLE `tblactivity`
  ADD PRIMARY KEY (`activityNO`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`categoryID`),
  ADD UNIQUE KEY `description` (`description`);

--
-- Indexes for table `tblemployees`
--
ALTER TABLE `tblemployees`
  ADD PRIMARY KEY (`employeeID`);

--
-- Indexes for table `tblproductbundles`
--
ALTER TABLE `tblproductbundles`
  ADD PRIMARY KEY (`bundleID`),
  ADD KEY `bundle_product` (`productcode`);

--
-- Indexes for table `tblproducts`
--
ALTER TABLE `tblproducts`
  ADD PRIMARY KEY (`productcode`),
  ADD UNIQUE KEY `barcode` (`barcode`);

--
-- Indexes for table `tblpromos`
--
ALTER TABLE `tblpromos`
  ADD PRIMARY KEY (`promoID`);

--
-- Indexes for table `tblreturns`
--
ALTER TABLE `tblreturns`
  ADD PRIMARY KEY (`returnID`),
  ADD KEY `sale_product` (`productcode`);

--
-- Indexes for table `tblsales`
--
ALTER TABLE `tblsales`
  ADD PRIMARY KEY (`saleNO`),
  ADD KEY `sale_product` (`productcode`);

--
-- Indexes for table `tblsalesinvoice`
--
ALTER TABLE `tblsalesinvoice`
  ADD PRIMARY KEY (`invoiceID`),
  ADD UNIQUE KEY `invoiceNO` (`invoiceNO`);

--
-- Indexes for table `tblstock`
--
ALTER TABLE `tblstock`
  ADD PRIMARY KEY (`stockNO`),
  ADD KEY `stock_product` (`productcode`);

--
-- Indexes for table `tblstockinvoice`
--
ALTER TABLE `tblstockinvoice`
  ADD PRIMARY KEY (`stockinvoiceNO`),
  ADD UNIQUE KEY `referenceNO` (`referenceNO`);

--
-- Indexes for table `tblsuppliers`
--
ALTER TABLE `tblsuppliers`
  ADD PRIMARY KEY (`supplierID`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `employeeID_borrow` (`employeeID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblactivity`
--
ALTER TABLE `tblactivity`
  MODIFY `activityNO` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=239;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `categoryID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblproductbundles`
--
ALTER TABLE `tblproductbundles`
  MODIFY `bundleID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tblpromos`
--
ALTER TABLE `tblpromos`
  MODIFY `promoID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tblreturns`
--
ALTER TABLE `tblreturns`
  MODIFY `returnID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tblsales`
--
ALTER TABLE `tblsales`
  MODIFY `saleNO` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `tblsalesinvoice`
--
ALTER TABLE `tblsalesinvoice`
  MODIFY `invoiceID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tblstock`
--
ALTER TABLE `tblstock`
  MODIFY `stockNO` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tblstockinvoice`
--
ALTER TABLE `tblstockinvoice`
  MODIFY `stockinvoiceNO` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tblsuppliers`
--
ALTER TABLE `tblsuppliers`
  MODIFY `supplierID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `userID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblproductbundles`
--
ALTER TABLE `tblproductbundles`
  ADD CONSTRAINT `bundle_product` FOREIGN KEY (`productcode`) REFERENCES `tblproducts` (`productcode`) ON DELETE CASCADE;

--
-- Constraints for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD CONSTRAINT `employeeID_borrow` FOREIGN KEY (`employeeID`) REFERENCES `tblemployees` (`employeeID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
