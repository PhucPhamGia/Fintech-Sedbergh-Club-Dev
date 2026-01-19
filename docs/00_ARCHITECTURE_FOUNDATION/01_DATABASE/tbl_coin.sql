-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 18, 2026 at 09:12 PM
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
-- Table structure for table `tbl_coin`
--

CREATE TABLE `tbl_coin` (
  `id_coin` int(11) NOT NULL,
  `coinname` varchar(50) NOT NULL,
  `ghichu` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_coin`
--

INSERT INTO `tbl_coin` (`id_coin`, `coinname`, `ghichu`) VALUES
(1, 'BTCUSDT', ''),
(5, 'ETHUSDT', ''),
(3, 'SOLUSDT', ''),
(4, 'BNBUSDT', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_coin`
--
ALTER TABLE `tbl_coin`
  ADD PRIMARY KEY (`id_coin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_coin`
--
ALTER TABLE `tbl_coin`
  MODIFY `id_coin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
