-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2023 at 12:58 AM
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
-- Database: `industry`
--

-- --------------------------------------------------------

--
-- Table structure for table `emission_limit`
--

CREATE TABLE `emission_limit` (
  `Limit_id` int(11) NOT NULL,
  `Parameter` varchar(35) NOT NULL,
  `Limit` decimal(7,3) NOT NULL,
  `Limit_units` varchar(15) NOT NULL,
  `Compliance_demonstration_method` varchar(250) DEFAULT NULL,
  `Citation` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emission_unit`
--

CREATE TABLE `emission_unit` (
  `Unit_id` varchar(10) NOT NULL,
  `Name` varchar(65) NOT NULL,
  `Capacity` decimal(7,1) DEFAULT NULL,
  `Capacity_units` varchar(15) DEFAULT NULL,
  `Facility_AI_number` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facility`
--

CREATE TABLE `facility` (
  `Agency_interest_number` int(11) NOT NULL,
  `Name` varchar(65) NOT NULL,
  `Permit_number` varchar(11) DEFAULT NULL,
  `Address` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fueled_units`
--

CREATE TABLE `fueled_units` (
  `Unit_id` varchar(11) NOT NULL,
  `Fuel_consumption` decimal(7,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `regulation`
--

CREATE TABLE `regulation` (
  `Citation` varchar(20) NOT NULL,
  `text` varchar(2500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `unit_limits`
--

CREATE TABLE `unit_limits` (
  `Unit_id` varchar(11) NOT NULL,
  `Limit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `emission_limit`
--
ALTER TABLE `emission_limit`
  ADD PRIMARY KEY (`Limit_id`),
  ADD KEY `Citation` (`Citation`);

--
-- Indexes for table `emission_unit`
--
ALTER TABLE `emission_unit`
  ADD PRIMARY KEY (`Unit_id`),
  ADD KEY `Facility_AI_number` (`Facility_AI_number`);

--
-- Indexes for table `facility`
--
ALTER TABLE `facility`
  ADD PRIMARY KEY (`Agency_interest_number`);

--
-- Indexes for table `fueled_units`
--
ALTER TABLE `fueled_units`
  ADD PRIMARY KEY (`Unit_id`);

--
-- Indexes for table `regulation`
--
ALTER TABLE `regulation`
  ADD PRIMARY KEY (`Citation`);

--
-- Indexes for table `unit_limits`
--
ALTER TABLE `unit_limits`
  ADD PRIMARY KEY (`Unit_id`,`Limit_id`),
  ADD KEY `Limit_id` (`Limit_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `emission_limit`
--
ALTER TABLE `emission_limit`
  ADD CONSTRAINT `emission_limit_ibfk_1` FOREIGN KEY (`Citation`) REFERENCES `regulation` (`Citation`);

--
-- Constraints for table `emission_unit`
--
ALTER TABLE `emission_unit`
  ADD CONSTRAINT `emission_unit_ibfk_1` FOREIGN KEY (`Facility_AI_number`) REFERENCES `facility` (`Agency_interest_number`);

--
-- Constraints for table `fueled_units`
--
ALTER TABLE `fueled_units`
  ADD CONSTRAINT `fueled_units_ibfk_1` FOREIGN KEY (`Unit_id`) REFERENCES `emission_unit` (`Unit_id`);

--
-- Constraints for table `unit_limits`
--
ALTER TABLE `unit_limits`
  ADD CONSTRAINT `unit_limits_ibfk_1` FOREIGN KEY (`Unit_id`) REFERENCES `emission_unit` (`Unit_id`),
  ADD CONSTRAINT `unit_limits_ibfk_2` FOREIGN KEY (`Limit_id`) REFERENCES `emission_limit` (`Limit_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
