-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2023 at 12:10 AM
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
-- Dumping data for table `emission_limit`
--

INSERT INTO `emission_limit` (`Limit_id`, `Parameter`, `Limit`, `Limit_units`, `Compliance_demonstration_method`, `Citation`) VALUES
(0, 'PM', 0.400, 'lb/ton', 'Complete emissions test every 5 years.', '63.1505(i)(1)'),
(1, 'Dioxins/Furans', 15.000, 'µg/Mg', 'Complete emissions test every 5 years.', '63.1505(i)(3)'),
(2, 'HCl', 0.400, 'lb/ton', 'Complete emissions test every 5 years.', '63.1505(i)(4)'),
(3, 'PM', 20.000, '%', 'Test Method 9', '401KAR 59:010(3)(2)');

-- --------------------------------------------------------

--
-- Table structure for table `emission_unit`
--

CREATE TABLE `emission_unit` (
  `Unit_id` varchar(10) NOT NULL,
  `Name` varchar(65) NOT NULL,
  `Capacity` decimal(7,1) DEFAULT NULL,
  `Capacity_units` varchar(15) DEFAULT NULL,
  `Facility_AI_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emission_unit`
--

INSERT INTO `emission_unit` (`Unit_id`, `Name`, `Capacity`, `Capacity_units`, `Facility_AI_number`) VALUES
('EU01', 'Tilting Rotary Furnace', 8.0, 'tons/hr', 40313),
('EU04', 'Salt Cake Cool Down Storage', 3.6, 'tons/hr', 40313),
('EU06', 'Salt Cake Cool Down Storage', 3.6, 'tons/hr', 40313);

-- --------------------------------------------------------

--
-- Table structure for table `facility`
--

CREATE TABLE `facility` (
  `Agency_interest_number` int(11) NOT NULL,
  `Name` varchar(65) NOT NULL,
  `Permit_number` varchar(11) DEFAULT NULL,
  `Address` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facility`
--

INSERT INTO `facility` (`Agency_interest_number`, `Name`, `Permit_number`, `Address`) VALUES
(2805, 'Blue Grass Army Depot', 'V-21-020', '431 Battlefield Memorial Highway Richmond, KY 40475'),
(4298, 'Wilco Refining LLC.', 'F-17-073', 'Wolf River Dock Rd.'),
(40313, 'Owl\'s Head Alloys Inc', 'V-22-032', '187 Mitch McConnell Way Bowling Green KY');

-- --------------------------------------------------------

--
-- Table structure for table `fueled_units`
--

CREATE TABLE `fueled_units` (
  `Unit_id` varchar(11) NOT NULL,
  `Fuel_consumption` decimal(7,2) NOT NULL,
  `Facility_AI_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `regulation`
--

CREATE TABLE `regulation` (
  `Citation` varchar(20) NOT NULL,
  `text` varchar(2500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `regulation`
--

INSERT INTO `regulation` (`Citation`, `text`) VALUES
('401KAR 59:010(3)(2)', 'No person shall cause, suffer, allow, or permit any continuous emission into the open air from a control device or stack associated with any affected facility which is equal to or greater than twenty (20) percent opacity.'),
('63.1505(i)(1)', '0.20 kg of PM per Mg (0.40 lb of PM per ton) of feed/charge from a group 1 furnace, that is not a melting/holding furnace processing only clean charge, at a secondary aluminum production facility that is a major source'),
('63.1505(i)(3)', '15 µg of D/F TEQ per Mg (2.1 × 10−4 gr of D/F TEQ per ton) of feed/charge from a group 1 furnace at a secondary aluminum production facility that is a major or area source. This limit does not apply if the furnace processes only clean charge'),
('63.1505(i)(4)', '0.20 kg of HF per Mg (0.40 lb of HF per ton) of feed/charge from an uncontrolled group 1 furnace and 0.20 kg of HCl per Mg (0.40 lb of HCl per ton) of feed/charge or, if the furnace is equipped with an add-on air pollution control device, 10 percent of the uncontrolled HCl emissions, by weight, for a group 1 furnace at a secondary aluminum production facility that is a major source.');

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
-- Dumping data for table `unit_limits`
--

INSERT INTO `unit_limits` (`Unit_id`, `Limit_id`, `Facility_AI_number`) VALUES
('EU01', 0, 40313),
('EU01', 1, 40313),
('EU01', 2, 40313),
('EU04', 3, 40313),
('EU06', 3, 40313);

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
  ADD PRIMARY KEY (`Unit_id`,`Facility_AI_number`),
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
  ADD PRIMARY KEY (`Unit_id`,`Facility_AI_number`),
  ADD KEY `fueled_units_ibfk_2` (`Facility_AI_number`);

--
-- Indexes for table `regulation`
--
ALTER TABLE `regulation`
  ADD PRIMARY KEY (`Citation`);

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
  ADD CONSTRAINT `fueled_units_ibfk_1` FOREIGN KEY (`Unit_id`) REFERENCES `emission_unit` (`Unit_id`),
  ADD CONSTRAINT `fueled_units_ibfk_2` FOREIGN KEY (`Facility_AI_number`) REFERENCES `emission_unit` (`Facility_AI_number`);

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
