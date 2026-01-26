-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 18, 2026 at 09:13 PM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `thuy_giaphuc`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth`
--

CREATE TABLE `auth` (
  `id` int(11) NOT NULL,
  `username` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` timestamp NOT NULL,
  `created_at` timestamp NOT NULL,
  `verified` tinyint(1) NOT NULL,
  `verification_token` varchar(100) NOT NULL,
  `verification_expires_at` timestamp NOT NULL,
  `remember_selector` varchar(50) NOT NULL,
  `remember_hash` varchar(100) NOT NULL,
  `remember_expires_at` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `auth`
--

INSERT INTO `auth` (`id`, `username`, `email`, `password`, `last_login`, `created_at`, `verified`, `verification_token`, `verification_expires_at`, `remember_selector`, `remember_hash`, `remember_expires_at`) VALUES
(1, 'freakybob', 'imtiendat0907@gmail.com', '$2y$10$hz1YBzOdLQO50j6DoawkeOI8lyM0gopkhBLAQ/HCbT80sxuXIJIT.', '2026-01-06 16:02:11', '2026-01-06 16:02:11', 0, '', '2026-01-06 16:02:11', '', '', '2026-01-08 15:25:16'),
(2, 'debug', 'debug@debug.com', '$2y$10$o8sbk9f1ds5rt7LubJ.4bOThJz.2Ktnx9r7YqWF1tAu5axIt4y0..', '2026-01-06 16:02:21', '2026-01-06 16:02:21', 0, '', '2026-01-06 16:02:21', '', '', '2026-01-06 16:02:21'),
(3, 'phucphamgia', 'phamgiaphuc1409@gmail.com', '$2y$10$0HsRtyztVksHYDQDiNE1hujaVor2/j4ah762pyRX5i9BYKlYaUwtW', '0000-00-00 00:00:00', '2026-01-06 09:16:12', 0, '', '0000-00-00 00:00:00', '', '', '2026-01-08 15:23:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth`
--
ALTER TABLE `auth`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auth`
--
ALTER TABLE `auth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
