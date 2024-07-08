-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2024 at 05:36 PM
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
-- Database: `schedule`
--

-- --------------------------------------------------------

--
-- Table structure for table `professors`
--

CREATE TABLE `professors` (
  `prof_id` int(11) NOT NULL,
  `prof_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `professors`
--

INSERT INTO `professors` (`prof_id`, `prof_name`) VALUES
(1, 'Lyra Nuevas'),
(2, 'Milagros Engao'),
(3, 'Jessie Paragas'),
(4, 'Windie Velarde'),
(5, 'Giovanni De Los Santos'),
(6, 'Lyndon Alberca'),
(9, 'Ronnie Cabillian'),
(10, 'Benjo Badilla'),
(11, 'Eric Sta Singh'),
(12, 'Ritchell Villafuerte'),
(13, 'Sarah Jane Cabral'),
(14, 'Niel Pascual'),
(15, 'Raymond Daylo'),
(16, 'Diane Remot'),
(17, 'Vienmar Ogrimen'),
(18, 'Clemente'),
(19, 'Pana'),
(20, 'Renomeron'),
(21, 'Agullo'),
(22, 'Acebedo'),
(23, 'Jude Urmenta'),
(24, 'Catindoy'),
(25, 'Maneja');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_name`) VALUES
(1, 'CiscoLab 1'),
(2, 'CiscoLab 2');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `adviser_name` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `capstone_title` varchar(255) NOT NULL,
  `lead_panelist` int(11) NOT NULL,
  `panelist2` int(11) NOT NULL,
  `panelist3` int(11) NOT NULL,
  `room_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `date`, `start_time`, `end_time`, `adviser_name`, `student_name`, `capstone_title`, `lead_panelist`, `panelist2`, `panelist3`, `room_id`) VALUES
(17, '2024-06-24', '08:00:00', '09:30:00', 5, 'Amado, Holanda, Lianza', 'Virtual Assistant with Speech Recognition and Semiotic Inspection Method (SIM) using Mobile App', 1, 15, 19, 1),
(18, '2024-06-24', '09:30:00', '10:00:00', 10, 'Bohol, Claus, Solayao', 'DeafTalk: A Smart Communication Application', 18, 20, 2, 1),
(19, '2024-06-24', '12:00:00', '13:30:00', 17, 'Aure, Badango, Cayaco', 'Streamline your Event Planning with the Entire EVSU Facilities and Venue Reservation System', 5, 16, 12, 1),
(20, '2024-06-24', '13:30:00', '15:00:00', 23, 'Caperino, De Los Santos, Gacoscosim', 'Document Tracking using QR Code Technology with Digitization and GD Function', 4, 13, 21, 1),
(21, '2024-06-24', '15:00:00', '16:30:00', 23, 'De Dios, Carino, Sabalza', 'Cyber War - Cyber Security Game', 12, 5, 17, 1),
(22, '2024-06-24', '16:30:00', '18:00:00', 23, 'Guino, Polinio, Sablawon', 'EVSU iEMS: Integrated Extension Management System', 18, 22, 6, 1),
(23, '2024-06-24', '08:00:00', '09:30:00', 9, 'Macamay, Rosario, Trask', 'Document Data Classification Management System in EVSU Accounting Unit', 23, 21, 11, 2),
(24, '2024-06-24', '09:30:00', '11:00:00', 13, 'Garcia, Gerilla, Tisado', '360 Navigate: Interactive EVSU Companion Map with Panoramic Perspective Using Mobile App', 1, 6, 24, 2),
(25, '2024-06-24', '12:00:00', '13:30:00', 12, 'Delito, Ricarte, Sabanto', 'Primary Student Engagement Analysis with SMS Notification and Data Analytics for Academic Enhancement', 4, 21, 17, 2),
(26, '2024-06-24', '13:30:00', '15:00:00', 3, 'Chu, Monge, Nacasabug', 'Smart Ballot: Enhancing Electoral Management Efficiency', 21, 5, 25, 2),
(27, '2024-06-24', '15:00:00', '16:30:00', 19, 'Caysido, Enero, Villena', 'DAR: Unified Data Reporting System with Data Analytics', 23, 17, 18, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `professors`
--
ALTER TABLE `professors`
  ADD PRIMARY KEY (`prof_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_adviser` (`adviser_name`),
  ADD KEY `FK_lead_panelist` (`lead_panelist`),
  ADD KEY `FK_panelist2` (`panelist2`),
  ADD KEY `FK_panelist3` (`panelist3`),
  ADD KEY `FK_Room` (`room_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `professors`
--
ALTER TABLE `professors`
  MODIFY `prof_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `FK_Room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`),
  ADD CONSTRAINT `FK_adviser` FOREIGN KEY (`adviser_name`) REFERENCES `professors` (`prof_id`),
  ADD CONSTRAINT `FK_lead_panelist` FOREIGN KEY (`lead_panelist`) REFERENCES `professors` (`prof_id`),
  ADD CONSTRAINT `FK_panelist2` FOREIGN KEY (`panelist2`) REFERENCES `professors` (`prof_id`),
  ADD CONSTRAINT `FK_panelist3` FOREIGN KEY (`panelist3`) REFERENCES `professors` (`prof_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
