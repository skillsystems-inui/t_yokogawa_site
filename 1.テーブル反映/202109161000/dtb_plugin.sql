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
-- テーブルの構造 `dtb_plugin`
--

CREATE TABLE IF NOT EXISTS `dtb_plugin` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 0,
  `version` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `initialized` tinyint(1) NOT NULL DEFAULT 0,
  `create_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_plugin`
--

INSERT INTO `dtb_plugin` (`id`, `name`, `code`, `enabled`, `version`, `source`, `initialized`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, '関連商品プラグイン', 'RelatedProduct4', 1, '4.0.1', '1761', 1, '2021-09-02 06:29:57', '2021-09-02 06:30:34', 'plugin'),
(2, 'Coupon Plugin for EC-CUBE4', 'Coupon4', 1, '4.0.7', '1923', 1, '2021-09-02 06:31:31', '2021-09-02 06:31:52', 'plugin'),
(3, '最短お届け日調整プラグイン', 'DeliveryDate4', 1, '1.1.2', '1732', 1, '2021-09-02 16:46:30', '2021-09-02 16:47:58', 'plugin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_plugin`
--
ALTER TABLE `dtb_plugin`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_plugin`
--
ALTER TABLE `dtb_plugin`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
