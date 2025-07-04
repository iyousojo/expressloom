-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2025 at 03:54 PM
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
-- Database: `solomon`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `account_type` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) NOT NULL DEFAULT 'assets/userimg/defualt.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `password`, `address`, `country`, `state`, `zip_code`, `account_type`, `profile_pic`) VALUES
(2, 'geor', 'nnnn', 'Solomojohnbull', 'solomonjohnbull676@gmail.com', '$2y$10$T9GOZ0zTfN.YQAcr1tyt3uC4Wy0HAvx36rp1uot7HBLLzZpMbQRoG', 'Idhakhomun Street, Ekpoma 310104, Edo', 'Nigeria', 'Edo', '300001', 'business', 'assets/userimg/defualt.jpg'),
(3, 'geor', 'nnnn', 'Solomojohnbull', 'solomonjohnbull676@gmail.com', '$2y$10$ptsANQr9vSuOg6tytJubXOv3Trcl8WKWHmJkiH98p0dds4paduKRS', 'Idhakhomun Street, Ekpoma 310104, Edo', 'Nigeria', 'Edo', '300001', 'personal', 'assets/userimg/defualt.jpg'),
(4, 'geor', '', 'Solomojohnbull', 'solo2@GMAIL.COM', '$2y$10$Expj9D6Igb3FVy/J9zRdye69aKNDg34Gk5V5gahs1hnrhwscREdQK', 'Idhakhomun Street, Ekpoma 310104, Edo', 'Nigeria', 'Edo', '300001', 'personal', 'assets/userimg/defualt.jpg'),
(5, 'geor', 'nnnn', 'Solomojohnbull', 'solomon103@gmail.com', '$2y$10$wZMQmmJRlsgA5whUDITaMep2SWJiDZyj47UQJ0WIqY.CLaVcoTXky', 'Idhakhomun Street, Ekpoma 310104, Edo', 'Nigeria', 'Edo', '300001', 'personal', 'assets/userimg/defualt.jpg'),
(6, 'solomon', 'johnbull', 'iyobhebhe', 'solomonjohnbull676@gmail.com', '$2y$10$BmM.E97rv8aPRZxl6bfT1OSzy3OFGOflaVcmW6/OauyMY74Eh/IIy', 'Idhakhomun Street, Ekpoma 310104, Edo', 'Nigeria', 'Nasarawa', '300001', 'personal', 'assets/userimg/defualt.jpg'),
(7, 'solomon', 'johnbull', 'iyobhebhe', 'solomonjohnbull1234@gmail.com', '$2y$10$wW.jYoiMtasthsatHVBJSeZfWIWpn89Xg1Mb8.vmpzCtUWa2PeOnK', 'Idhakhomun Street, Ekpoma 310104, Edo', 'Nigeria', 'Nasarawa', '300001', 'business', 'assets/userimg/defualt.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
