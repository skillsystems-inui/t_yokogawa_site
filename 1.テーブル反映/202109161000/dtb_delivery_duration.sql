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
-- テーブルの構造 `dtb_delivery_duration`
--

CREATE TABLE IF NOT EXISTS `dtb_delivery_duration` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `duration` smallint(6) NOT NULL DEFAULT 0,
  `sort_no` int(10) unsigned NOT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_delivery_duration`
--

INSERT INTO `dtb_delivery_duration` (`id`, `name`, `duration`, `sort_no`, `discriminator_type`) VALUES
(1, '即日', 0, 0, 'deliveryduration'),
(2, '1～2日後', 1, 1, 'deliveryduration'),
(3, '3～4日後', 3, 2, 'deliveryduration'),
(4, '1週間以降', 7, 3, 'deliveryduration'),
(5, '2週間以降', 14, 4, 'deliveryduration'),
(6, '3週間以降', 21, 5, 'deliveryduration'),
(7, '1ヶ月以降', 30, 6, 'deliveryduration'),
(8, '2ヶ月以降', 60, 7, 'deliveryduration'),
(9, 'お取り寄せ(商品入荷後)', -1, 8, 'deliveryduration');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_delivery_duration`
--
ALTER TABLE `dtb_delivery_duration`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_delivery_duration`
--
ALTER TABLE `dtb_delivery_duration`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
