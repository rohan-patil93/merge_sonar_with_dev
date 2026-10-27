-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 11, 2022 at 12:03 PM
-- Server version: 8.0.27-0ubuntu0.20.04.1
-- PHP Version: 7.2.34-23+ubuntu20.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `letsasso_infistaq_1`
--

-- --------------------------------------------------------

--
-- Table structure for table `eba_vendors_allocations`
--

CREATE TABLE `eba_vendors_allocations` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `transaction_id` int NOT NULL,
  `item_id` int DEFAULT NULL,
  `manpower_requisition_id` int DEFAULT NULL,
  `account_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_leave` decimal(15,1) NOT NULL DEFAULT '0.0',
  `purchase_uom` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_quantity` decimal(9,2) NOT NULL DEFAULT '0.00',
  `item_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `item_amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `tax_amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `discount_amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `total_amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `reason_id` int DEFAULT NULL,
  `ref_rate` decimal(15,2) DEFAULT NULL,
  `item_wa_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `item_st_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `doc_ref_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_ref_date` date DEFAULT NULL,
  `req_date` date DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `approved_quantity` decimal(9,2) DEFAULT NULL,
  `transaction_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_qty` decimal(9,2) DEFAULT NULL,
  `pending_qty` decimal(9,2) DEFAULT NULL,
  `billable` int DEFAULT NULL,
  `allow_overtime` int NOT NULL DEFAULT '1' COMMENT '0:YES,1:NO',
  `is_active` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `eba_vendors_allocations`
--

INSERT INTO `eba_vendors_allocations` (`id`, `company_id`, `transaction_id`, `item_id`, `manpower_requisition_id`, `account_code`, `item_name`, `description`, `min_leave`, `purchase_uom`, `item_quantity`, `item_price`, `item_amount`, `tax_amount`, `discount_amount`, `total_amount`, `reason_id`, `ref_rate`, `item_wa_cost`, `item_st_cost`, `doc_ref_no`, `doc_ref_date`, `req_date`, `effective_date`, `expire_date`, `approved_quantity`, `transaction_type`, `transaction_qty`, `pending_qty`, `billable`, `allow_overtime`, `is_active`) VALUES
(1, 1, 2, 2, 2, '', NULL, NULL, '0.0', NULL, '3.00', '9890000.0000', '29670000.0000', '0.0000', '0.0000', '29670000.0000', NULL, NULL, '0.00', '0.00', '', '0000-00-00', '0000-00-00', '1970-01-01', '1970-01-01', '0.00', '', '0.00', '0.00', NULL, 1, 0),
(2, 1, 3, 2, 2, '', 'Automation QA Engineer - Grade 4', 'Cordia', '0.0', '', '2.00', '345000.0000', '690000.0000', '0.0000', '0.0000', '690000.0000', NULL, '0.00', '0.00', '0.00', '', '0000-00-00', '0000-00-00', '1970-01-01', '1970-01-01', '0.00', '', '0.00', '0.00', 0, 1, 0),
(3, 1, 4, 3, 3, '', 'Automation QA Engineer - Grade 6', '3 jobs', '0.0', '', '3.00', '349000.0000', '1047000.0000', '0.0000', '0.0000', '1047000.0000', NULL, NULL, '0.00', '0.00', '', '0000-00-00', '0000-00-00', '1970-01-01', '1970-01-01', '0.00', '', '0.00', '0.00', NULL, 1, 0),
(5, 1, 5, 1, 1, NULL, NULL, 'ani descrption', '0.0', NULL, '111.00', '10.0000', '1110.0000', '0.0000', '0.0000', '1110.0000', NULL, NULL, '0.00', '0.00', NULL, NULL, NULL, '1970-01-01', '1970-01-01', NULL, NULL, NULL, NULL, NULL, 1, 0),
(6, 1, 5, 2, 2, NULL, NULL, 'cordia descrption', '0.0', NULL, '4.00', '100.0000', '400.0000', '0.0000', '0.0000', '400.0000', NULL, NULL, '0.00', '0.00', NULL, NULL, NULL, '1970-01-01', '1970-01-01', NULL, NULL, NULL, NULL, NULL, 1, 0),
(7, 1, 2, 1, 1, NULL, NULL, 'Ani vendor', '0.0', NULL, '2.00', '8989000.0000', '17978000.0000', '0.0000', '0.0000', '17978000.0000', NULL, NULL, '0.00', '0.00', NULL, NULL, NULL, '1970-01-01', '1970-01-01', NULL, NULL, NULL, NULL, NULL, 1, 0),
(8, 1, 2, 3, 3, NULL, NULL, NULL, '0.0', NULL, '8.00', '5890000.0000', '47120000.0000', '0.0000', '0.0000', '47120000.0000', NULL, NULL, '0.00', '0.00', NULL, NULL, NULL, '1970-01-01', '1970-01-01', NULL, NULL, NULL, NULL, NULL, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `eba_vendors_allocations`
--
ALTER TABLE `eba_vendors_allocations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_id` (`company_id`,`transaction_id`,`item_id`,`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `eba_vendors_allocations`
--
ALTER TABLE `eba_vendors_allocations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
