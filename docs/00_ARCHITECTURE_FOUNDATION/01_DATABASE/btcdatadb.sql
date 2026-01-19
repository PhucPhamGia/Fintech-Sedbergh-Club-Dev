-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 18, 2026 at 09:11 PM
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
-- Table structure for table `btcdatadb`
--

CREATE TABLE `btcdatadb` (
  `id` int(11) NOT NULL,
  `id_coin` int(11) NOT NULL,
  `date` date NOT NULL,
  `open_time` bigint(20) NOT NULL,
  `open_price` decimal(18,8) NOT NULL,
  `high_price` decimal(18,8) NOT NULL,
  `low_price` decimal(18,8) NOT NULL,
  `close_price` decimal(18,8) NOT NULL,
  `volume` decimal(18,8) NOT NULL,
  `close_time` bigint(20) NOT NULL,
  `quote_volume` decimal(18,8) NOT NULL,
  `number_of_trades` int(11) NOT NULL,
  `taker_base_volume` decimal(18,8) NOT NULL,
  `taker_quote_volume` decimal(18,8) NOT NULL,
  `ma20` double DEFAULT NULL,
  `ma50` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `btcdatadb`
--

INSERT INTO `btcdatadb` (`id`, `id_coin`, `date`, `open_time`, `open_price`, `high_price`, `low_price`, `close_price`, `volume`, `close_time`, `quote_volume`, `number_of_trades`, `taker_base_volume`, `taker_quote_volume`, `ma20`, `ma50`) VALUES
(1, 1, '2025-05-26', 1748260800000, 109772.73000000, 110422.22000000, 108858.28000000, 109434.79000000, 7431.63409000, 1748303999999, 814499600.94303660, 1604057, 3736.57832000, 409550269.12941860, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `btcdatadb`
--
ALTER TABLE `btcdatadb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `open_time` (`open_time`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `btcdatadb`
--
ALTER TABLE `btcdatadb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1737;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
