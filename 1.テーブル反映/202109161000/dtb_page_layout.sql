-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2021 年 9 月 16 日 10:14
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
-- テーブルの構造 `dtb_page_layout`
--

CREATE TABLE IF NOT EXISTS `dtb_page_layout` (
  `page_id` int(10) unsigned NOT NULL,
  `layout_id` int(10) unsigned NOT NULL,
  `sort_no` smallint(5) unsigned NOT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_page_layout`
--

INSERT INTO `dtb_page_layout` (`page_id`, `layout_id`, `sort_no`, `discriminator_type`) VALUES
(0, 0, 2, 'pagelayout'),
(1, 1, 2, 'pagelayout'),
(2, 2, 41, 'pagelayout'),
(3, 2, 41, 'pagelayout'),
(4, 2, 6, 'pagelayout'),
(5, 2, 7, 'pagelayout'),
(6, 2, 8, 'pagelayout'),
(7, 2, 36, 'pagelayout'),
(8, 2, 37, 'pagelayout'),
(9, 2, 9, 'pagelayout'),
(10, 2, 10, 'pagelayout'),
(11, 2, 11, 'pagelayout'),
(12, 2, 12, 'pagelayout'),
(13, 2, 14, 'pagelayout'),
(14, 2, 13, 'pagelayout'),
(15, 2, 41, 'pagelayout'),
(16, 2, 41, 'pagelayout'),
(17, 2, 17, 'pagelayout'),
(18, 2, 18, 'pagelayout'),
(19, 2, 33, 'pagelayout'),
(20, 2, 19, 'pagelayout'),
(21, 2, 41, 'pagelayout'),
(22, 2, 21, 'pagelayout'),
(23, 2, 41, 'pagelayout'),
(24, 2, 22, 'pagelayout'),
(25, 2, 34, 'pagelayout'),
(28, 2, 23, 'pagelayout'),
(29, 2, 24, 'pagelayout'),
(30, 2, 25, 'pagelayout'),
(31, 2, 26, 'pagelayout'),
(32, 2, 27, 'pagelayout'),
(33, 2, 28, 'pagelayout'),
(34, 2, 29, 'pagelayout'),
(35, 2, 41, 'pagelayout'),
(36, 2, 31, 'pagelayout'),
(37, 2, 32, 'pagelayout'),
(38, 2, 39, 'pagelayout'),
(42, 2, 38, 'pagelayout'),
(44, 2, 40, 'pagelayout'),
(45, 2, 41, 'pagelayout'),
(46, 2, 0, 'pagelayout'),
(48, 2, 41, 'pagelayout'),
(49, 2, 41, 'pagelayout'),
(50, 2, 41, 'pagelayout'),
(51, 2, 41, 'pagelayout'),
(52, 2, 41, 'pagelayout'),
(53, 2, 41, 'pagelayout'),
(54, 2, 41, 'pagelayout'),
(55, 2, 41, 'pagelayout'),
(56, 2, 41, 'pagelayout'),
(57, 2, 41, 'pagelayout');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_page_layout`
--
ALTER TABLE `dtb_page_layout`
  ADD PRIMARY KEY (`page_id`,`layout_id`),
  ADD KEY `IDX_F2799941C4663E4` (`page_id`),
  ADD KEY `IDX_F27999418C22AA1A` (`layout_id`);

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_page_layout`
--
ALTER TABLE `dtb_page_layout`
  ADD CONSTRAINT `FK_F27999418C22AA1A` FOREIGN KEY (`layout_id`) REFERENCES `dtb_layout` (`id`),
  ADD CONSTRAINT `FK_F2799941C4663E4` FOREIGN KEY (`page_id`) REFERENCES `dtb_page` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
