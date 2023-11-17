-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2023 at 11:44 PM
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
-- Table structure for table `unit_limits`
--

CREATE TABLE `unit_limits` (
  `Unit_id` varchar(11) NOT NULL,
  `Limit_id` int(11) NOT NULL,
  `Facility_AI_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `unit_limits`
--
ALTER TABLE `unit_limits`
  ADD PRIMARY KEY (`Unit_id`,`Limit_id`,`Facility_AI_number`),
  ADD KEY `unit_limits_ibfk_2` (`Limit_id`),
  ADD KEY `unit_limits_ibfk_3` (`Facility_AI_number`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `unit_limits`
--
ALTER TABLE `unit_limits`
  ADD CONSTRAINT `unit_limits_ibfk_1` FOREIGN KEY (`Unit_id`) REFERENCES `emission_unit` (`Unit_id`),
  ADD CONSTRAINT `unit_limits_ibfk_2` FOREIGN KEY (`Limit_id`) REFERENCES `emission_limit` (`Limit_id`),
  ADD CONSTRAINT `unit_limits_ibfk_3` FOREIGN KEY (`Facility_AI_number`) REFERENCES `emission_unit` (`Facility_AI_number`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
