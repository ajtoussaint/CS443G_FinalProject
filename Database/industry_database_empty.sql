-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2023 at 08:42 PM
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
  `Citation` varchar(20) DEFAULT NULL
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
-- Constraints for dumped tables
--

--
-- Constraints for table `emission_limit`
--
ALTER TABLE `emission_limit`
  ADD CONSTRAINT `emission_limit_ibfk_1` FOREIGN KEY (`Citation`) REFERENCES `regulation` (`Citation`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
