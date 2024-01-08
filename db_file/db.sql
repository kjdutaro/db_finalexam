-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 08, 2024 at 03:58 AM
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
-- Database: `documenttracking`
--

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

CREATE TABLE `document` (
  `document_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `DateCreated` datetime DEFAULT NULL,
  `status` varchar(1000) DEFAULT NULL,
  `isAccomplished` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document`
--

INSERT INTO `document` (`document_id`, `file_path`, `file_name`, `DateCreated`, `status`, `isAccomplished`) VALUES
(1, '659b585311826_samplepng.png', 'samplepng.png', '2024-01-08 10:05:07', 'Received', 1),
(2, '659b58858d89c_Sample docu.docx', 'Sample docu.docx', '2024-01-08 10:05:57', NULL, 0),
(3, '659b58b4bdb53_Sample docu.pdf', 'Sample docu.pdf', '2024-01-08 10:06:44', '', 1),
(4, '659b5e82061be_sample.xlsx', 'sample.xlsx', '2024-01-08 10:31:30', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `personnel`
--

CREATE TABLE `personnel` (
  `personnel_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personnel`
--

INSERT INTO `personnel` (`personnel_id`, `name`, `password`, `email`) VALUES
(1, 'tester', 'test', 'test@mail.com'),
(2, 'Juan Dela Cruz', 'password123', 'juan@mail.com'),
(3, 'Maria Santos', 'password456', 'maria@mail.com'),
(4, 'Pedro Reyes', 'password789', 'pedro@mail.com'),
(5, 'Luisa Rodriguez', 'passwordabc', 'luisa@mail.com'),
(6, 'Miguel Garcia', 'passworddef', 'miguel@mail.com'),
(7, 'Sofia Hernandez', 'passwordghi', 'sofia@mail.com'),
(8, 'Diego Ramos', 'passwordjkl', 'diego@mail.com'),
(9, 'Isabella Reyes', 'passwordmno', 'isabella@mail.com'),
(10, 'Mateo Cruz', 'passwordpqr', 'mateo@mail.com'),
(11, 'Camila Torres', 'passwordstu', 'camila@mail.com');

-- --------------------------------------------------------

--
-- Table structure for table `recipient`
--

CREATE TABLE `recipient` (
  `recipient_id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL,
  `personnel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipient`
--

INSERT INTO `recipient` (`recipient_id`, `track_id`, `personnel_id`) VALUES
(2, 2, 2),
(3, 3, 1),
(4, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sender`
--

CREATE TABLE `sender` (
  `sender_id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL,
  `personnel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sender`
--

INSERT INTO `sender` (`sender_id`, `track_id`, `personnel_id`) VALUES
(1, 1, 1),
(3, 3, 2),
(4, 4, 2);

-- --------------------------------------------------------

--
-- Table structure for table `trackdetails`
--

CREATE TABLE `trackdetails` (
  `track_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `origin_office` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trackdetails`
--

INSERT INTO `trackdetails` (`track_id`, `document_id`, `origin_office`) VALUES
(1, 1, 'HR Department'),
(2, 2, 'HR Department'),
(3, 3, 'Motorpool'),
(4, 4, 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`document_id`);

--
-- Indexes for table `personnel`
--
ALTER TABLE `personnel`
  ADD PRIMARY KEY (`personnel_id`);

--
-- Indexes for table `recipient`
--
ALTER TABLE `recipient`
  ADD PRIMARY KEY (`recipient_id`),
  ADD KEY `track_id` (`track_id`),
  ADD KEY `personnel_id` (`personnel_id`);

--
-- Indexes for table `sender`
--
ALTER TABLE `sender`
  ADD PRIMARY KEY (`sender_id`),
  ADD KEY `track_id` (`track_id`),
  ADD KEY `personnel_id` (`personnel_id`);

--
-- Indexes for table `trackdetails`
--
ALTER TABLE `trackdetails`
  ADD PRIMARY KEY (`track_id`),
  ADD KEY `document_id` (`document_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `personnel`
--
ALTER TABLE `personnel`
  MODIFY `personnel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `recipient`
--
ALTER TABLE `recipient`
  MODIFY `recipient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sender`
--
ALTER TABLE `sender`
  MODIFY `sender_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `trackdetails`
--
ALTER TABLE `trackdetails`
  MODIFY `track_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `recipient`
--
ALTER TABLE `recipient`
  ADD CONSTRAINT `recipient_ibfk_1` FOREIGN KEY (`track_id`) REFERENCES `trackdetails` (`track_id`),
  ADD CONSTRAINT `recipient_ibfk_2` FOREIGN KEY (`personnel_id`) REFERENCES `personnel` (`personnel_id`);

--
-- Constraints for table `sender`
--
ALTER TABLE `sender`
  ADD CONSTRAINT `sender_ibfk_1` FOREIGN KEY (`track_id`) REFERENCES `trackdetails` (`track_id`),
  ADD CONSTRAINT `sender_ibfk_2` FOREIGN KEY (`personnel_id`) REFERENCES `personnel` (`personnel_id`);

--
-- Constraints for table `trackdetails`
--
ALTER TABLE `trackdetails`
  ADD CONSTRAINT `trackdetails_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `document` (`document_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
