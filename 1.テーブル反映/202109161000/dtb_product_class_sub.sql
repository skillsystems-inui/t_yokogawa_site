-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2021 年 9 月 16 日 10:15
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
-- テーブルの構造 `dtb_product_class_sub`
--

CREATE TABLE IF NOT EXISTS `dtb_product_class_sub` (
  `id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `sale_type_id` smallint(5) unsigned DEFAULT NULL,
  `class_sub_category_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `product_code` varchar(255) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `create_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `currency_code` varchar(255) DEFAULT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_product_class_sub`
--
ALTER TABLE `dtb_product_class_sub`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_4556B0B54584665A` (`product_id`),
  ADD KEY `IDX_4556B0B5B0524E01` (`sale_type_id`),
  ADD KEY `IDX_4556B0B56B7060CB` (`class_sub_category_id`),
  ADD KEY `IDX_4556B0B561220EA6` (`creator_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_product_class_sub`
--
ALTER TABLE `dtb_product_class_sub`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_product_class_sub`
--
ALTER TABLE `dtb_product_class_sub`
  ADD CONSTRAINT `FK_4556B0B54584665A` FOREIGN KEY (`product_id`) REFERENCES `dtb_product` (`id`),
  ADD CONSTRAINT `FK_4556B0B561220EA6` FOREIGN KEY (`creator_id`) REFERENCES `dtb_member` (`id`),
  ADD CONSTRAINT `FK_4556B0B56B7060CB` FOREIGN KEY (`class_sub_category_id`) REFERENCES `dtb_class_category` (`id`),
  ADD CONSTRAINT `FK_4556B0B5B0524E01` FOREIGN KEY (`sale_type_id`) REFERENCES `mtb_sale_type` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
