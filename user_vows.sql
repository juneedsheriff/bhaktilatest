-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 20, 2026 at 04:56 PM
-- Server version: 5.7.23-23
-- PHP Version: 8.1.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webte7nz_bhakti`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_vows`
--

CREATE TABLE `user_vows` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vows_text` text COLLATE utf8_unicode_ci NOT NULL,
  `vows_date` date NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_vows`
--

INSERT INTO `user_vows` (`id`, `user_id`, `vows_text`, `vows_date`, `created_at`) VALUES
(1, 8, 'Completing 3 Jyotri linga darshan this year', '2026-01-19', '2026-01-20 12:23:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user_vows`
--
ALTER TABLE `user_vows`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_vows`
--
ALTER TABLE `user_vows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
