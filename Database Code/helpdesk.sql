-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2024 at 03:08 PM
-- Server version: 8.0.36
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `helpdesk`
--

-- --------------------------------------------------------

--
-- Table structure for table `pagalba`
--

CREATE TABLE `pagalba` (
  `pagalba_id` int NOT NULL,
  `pagalba_busena` text NOT NULL,
  `pagalba_tesktas` text NOT NULL,
  `pagalba_prad_data` date NOT NULL,
  `pagalba_pab_data` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pagalba`
--

INSERT INTO `pagalba` (`pagalba_id`, `pagalba_busena`, `pagalba_tesktas`, `pagalba_prad_data`, `pagalba_pab_data`) VALUES
(1, 'neivykdytas', 'lol', '2024-05-02', NULL),
(2, 'neivykdytas', 'lol', '2024-05-02', NULL),
(3, 'neivykdytas', 'asfdsafdsafdsf', '2024-05-02', NULL),
(4, 'neivykdytas', 'ghkhj', '2024-05-02', NULL),
(5, 'neivykdytas', 'loleris_boliers', '2024-05-02', NULL),
(6, 'neivykdytas', 'dsaffdggfbd fd ', '2024-05-02', NULL),
(7, 'neivykdytas', 'Druuu', '2024-05-02', NULL),
(8, 'neivykdytas', '', '2024-05-02', NULL),
(9, 'neivykdytas', '', '2024-05-03', NULL),
(10, 'neivykdytas', 'sad', '2024-05-03', NULL),
(11, 'neivykdytas', 'asdsdfsfdgfdgfd', '2024-05-03', NULL),
(12, 'neivykdytas', 'saafds', '2024-05-03', NULL),
(13, 'neivykdytas', 'DJafhsdkjfhldsj', '2024-05-03', NULL),
(14, 'neivykdytas', 'afgdsfg', '2024-05-03', NULL),
(15, 'neivykdytas', 'dsfgafdg', '2024-05-03', NULL),
(16, 'neivykdytas', 'Labas ka tu', '2024-05-10', NULL),
(17, 'neivykdytas', 'sdfdsfs', '2024-05-15', NULL),
(18, 'neivykdytas', 'Laba diena mano vardas Ernestas', '2024-05-15', NULL),
(19, 'neivykdytas', 'Laba diena as esu Ernestas !', '2024-05-15', NULL),
(20, 'neivykdytas', 'Laba diena as esu Ernestas !', '2024-05-15', NULL),
(21, 'neivykdytas', 'Laba diena', '2024-05-28', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pagalba`
--
ALTER TABLE `pagalba`
  ADD PRIMARY KEY (`pagalba_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pagalba`
--
ALTER TABLE `pagalba`
  MODIFY `pagalba_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
