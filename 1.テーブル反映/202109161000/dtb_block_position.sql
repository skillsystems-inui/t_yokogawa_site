-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2021 年 9 月 16 日 10:12
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
-- テーブルの構造 `dtb_block_position`
--

CREATE TABLE IF NOT EXISTS `dtb_block_position` (
  `section` int(10) unsigned NOT NULL,
  `block_id` int(10) unsigned NOT NULL,
  `layout_id` int(10) unsigned NOT NULL,
  `block_row` int(10) unsigned DEFAULT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_block_position`
--

INSERT INTO `dtb_block_position` (`section`, `block_id`, `layout_id`, `block_row`, `discriminator_type`) VALUES
(3, 7, 1, 0, 'blockposition'),
(3, 7, 2, 0, 'blockposition'),
(3, 10, 2, 1, 'blockposition'),
(7, 15, 1, 0, 'blockposition'),
(7, 16, 1, 1, 'blockposition'),
(7, 17, 1, 2, 'blockposition'),
(7, 18, 1, 3, 'blockposition'),
(7, 19, 1, 4, 'blockposition'),
(7, 20, 1, 5, 'blockposition'),
(7, 21, 1, 7, 'blockposition'),
(7, 22, 1, 8, 'blockposition'),
(7, 23, 1, 9, 'blockposition'),
(7, 25, 1, 6, 'blockposition'),
(7, 26, 2, 0, 'blockposition'),
(10, 6, 2, 0, 'blockposition'),
(10, 24, 1, 0, 'blockposition'),
(11, 4, 1, 1, 'blockposition'),
(11, 4, 2, 1, 'blockposition'),
(11, 9, 1, 2, 'blockposition'),
(11, 9, 2, 2, 'blockposition'),
(11, 13, 1, 0, 'blockposition'),
(11, 13, 2, 0, 'blockposition');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_block_position`
--
ALTER TABLE `dtb_block_position`
  ADD PRIMARY KEY (`section`,`block_id`,`layout_id`),
  ADD KEY `IDX_35DCD731E9ED820C` (`block_id`),
  ADD KEY `IDX_35DCD7318C22AA1A` (`layout_id`);

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_block_position`
--
ALTER TABLE `dtb_block_position`
  ADD CONSTRAINT `FK_35DCD7318C22AA1A` FOREIGN KEY (`layout_id`) REFERENCES `dtb_layout` (`id`),
  ADD CONSTRAINT `FK_35DCD731E9ED820C` FOREIGN KEY (`block_id`) REFERENCES `dtb_block` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
