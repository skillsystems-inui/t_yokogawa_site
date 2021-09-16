-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2021 年 9 月 16 日 10:13
-- サーバのバージョン： 10.5.11-MariaDB-log
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tyokogawa_ec2`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `dtb_delivery_fee`
--

CREATE TABLE IF NOT EXISTS `dtb_delivery_fee` (
  `id` int(10) unsigned NOT NULL,
  `delivery_id` int(10) unsigned DEFAULT NULL,
  `pref_id` smallint(5) unsigned DEFAULT NULL,
  `fee` decimal(12,2) unsigned NOT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_delivery_fee`
--

INSERT INTO `dtb_delivery_fee` (`id`, `delivery_id`, `pref_id`, `fee`, `discriminator_type`) VALUES
(1, 1, 1, 1800.00, 'deliveryfee'),
(2, 1, 2, 1000.00, 'deliveryfee'),
(3, 1, 3, 1000.00, 'deliveryfee'),
(4, 1, 4, 1000.00, 'deliveryfee'),
(5, 1, 5, 1000.00, 'deliveryfee'),
(6, 1, 6, 1000.00, 'deliveryfee'),
(7, 1, 7, 1000.00, 'deliveryfee'),
(8, 1, 8, 1000.00, 'deliveryfee'),
(9, 1, 9, 1000.00, 'deliveryfee'),
(10, 1, 10, 1000.00, 'deliveryfee'),
(11, 1, 11, 1000.00, 'deliveryfee'),
(12, 1, 12, 1000.00, 'deliveryfee'),
(13, 1, 13, 1000.00, 'deliveryfee'),
(14, 1, 14, 1000.00, 'deliveryfee'),
(15, 1, 15, 1000.00, 'deliveryfee'),
(16, 1, 16, 1000.00, 'deliveryfee'),
(17, 1, 17, 1000.00, 'deliveryfee'),
(18, 1, 18, 1000.00, 'deliveryfee'),
(19, 1, 19, 1000.00, 'deliveryfee'),
(20, 1, 20, 1000.00, 'deliveryfee'),
(21, 1, 21, 1000.00, 'deliveryfee'),
(22, 1, 22, 1000.00, 'deliveryfee'),
(23, 1, 23, 1000.00, 'deliveryfee'),
(24, 1, 24, 1000.00, 'deliveryfee'),
(25, 1, 25, 1000.00, 'deliveryfee'),
(26, 1, 26, 1000.00, 'deliveryfee'),
(27, 1, 27, 1000.00, 'deliveryfee'),
(28, 1, 28, 1000.00, 'deliveryfee'),
(29, 1, 29, 1000.00, 'deliveryfee'),
(30, 1, 30, 1000.00, 'deliveryfee'),
(31, 1, 31, 1000.00, 'deliveryfee'),
(32, 1, 32, 1000.00, 'deliveryfee'),
(33, 1, 33, 1000.00, 'deliveryfee'),
(34, 1, 34, 1000.00, 'deliveryfee'),
(35, 1, 35, 1000.00, 'deliveryfee'),
(36, 1, 36, 1000.00, 'deliveryfee'),
(37, 1, 37, 1000.00, 'deliveryfee'),
(38, 1, 38, 1000.00, 'deliveryfee'),
(39, 1, 39, 1000.00, 'deliveryfee'),
(40, 1, 40, 1000.00, 'deliveryfee'),
(41, 1, 41, 1000.00, 'deliveryfee'),
(42, 1, 42, 1000.00, 'deliveryfee'),
(43, 1, 43, 1000.00, 'deliveryfee'),
(44, 1, 44, 1000.00, 'deliveryfee'),
(45, 1, 45, 1000.00, 'deliveryfee'),
(46, 1, 46, 1500.00, 'deliveryfee'),
(47, 1, 47, 2000.00, 'deliveryfee');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_delivery_fee`
--
ALTER TABLE `dtb_delivery_fee`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_491552412136921` (`delivery_id`),
  ADD KEY `IDX_4915524E171EF5F` (`pref_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_delivery_fee`
--
ALTER TABLE `dtb_delivery_fee`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=95;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_delivery_fee`
--
ALTER TABLE `dtb_delivery_fee`
  ADD CONSTRAINT `FK_491552412136921` FOREIGN KEY (`delivery_id`) REFERENCES `dtb_delivery` (`id`),
  ADD CONSTRAINT `FK_4915524E171EF5F` FOREIGN KEY (`pref_id`) REFERENCES `mtb_pref` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
