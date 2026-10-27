-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 07, 2022 at 01:28 PM
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
-- Table structure for table `it_declaration_categories`
--

CREATE TABLE `it_declaration_categories` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `parent_category_id` int NOT NULL,
  `category_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_short_name` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `age_limit` int NOT NULL,
  `individual_limit` int NOT NULL,
  `maximum_limit` decimal(15,2) NOT NULL,
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` int DEFAULT NULL,
  `current_status` int NOT NULL,
  `status_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `it_declaration_categories`
--

INSERT INTO `it_declaration_categories` (`id`, `company_id`, `parent_category_id`, `category_name`, `category_short_name`, `age_limit`, `individual_limit`, `maximum_limit`, `remarks`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`) VALUES
(1, 1, 0, 'IT Statement', 'ITS', 0, 0, '0.00', '', 0, 0, '2020-11-25 04:48:42', NULL, NULL, '2020-11-25 04:48:42', NULL, NULL),
(2, 1, 0, 'Deduction - Chapter VI A', 'ITS', 0, 0, '0.00', '', 0, 0, '2020-11-25 04:49:18', NULL, NULL, '2020-11-25 04:49:18', NULL, NULL),
(3, 1, 0, 'Exemption - Section 10', 'ITS', 0, 0, '0.00', '', 0, 0, '2020-11-25 04:49:45', NULL, NULL, '2020-11-25 04:49:45', NULL, NULL),
(4, 1, 0, 'Income - Perevious Employer', 'ITS', 0, 0, '0.00', '', 0, 0, '2020-11-25 04:50:09', NULL, NULL, '2020-11-25 04:50:09', NULL, NULL),
(5, 1, 0, 'Other/income/ Section 24', 'ITS', 0, 0, '0.00', '', 0, 0, '2020-11-25 04:50:27', NULL, NULL, '2020-11-25 04:50:27', NULL, NULL),
(6, 1, 1, 'Section 80C,80CCC,80CCD', '80C', 1, 1, '150000.00', '', 0, 0, '2020-11-25 04:59:06', NULL, NULL, '2020-11-25 04:59:06', NULL, NULL),
(7, 1, 1, 'Section 80D', '', 0, 0, '0.00', '', 0, 0, '2020-11-25 04:59:50', NULL, NULL, '2020-11-25 04:59:50', NULL, NULL),
(8, 1, 1, 'Other Sections', '', 1, 0, '0.00', '', 0, 0, '2020-11-25 05:01:05', NULL, NULL, '2020-11-25 05:01:05', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `it_declaration_categories`
--
ALTER TABLE `it_declaration_categories`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `it_declaration_categories`
--
ALTER TABLE `it_declaration_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
