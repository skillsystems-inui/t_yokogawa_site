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
-- テーブルの構造 `dtb_product_class_name`
--

CREATE TABLE IF NOT EXISTS `dtb_product_class_name` (
  `product_id` int(10) unsigned NOT NULL,
  `class_name_id` int(10) unsigned NOT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_product_class_name`
--

INSERT INTO `dtb_product_class_name` (`product_id`, `class_name_id`, `discriminator_type`) VALUES
(7, 3, 'productclassname'),
(7, 4, 'productclassname'),
(7, 5, 'productclassname'),
(7, 6, 'productclassname'),
(7, 7, 'productclassname'),
(7, 8, 'productclassname'),
(7, 9, 'productclassname'),
(7, 14, 'productclassname'),
(7, 15, 'productclassname'),
(7, 16, 'productclassname'),
(7, 17, 'productclassname');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_product_class_name`
--
ALTER TABLE `dtb_product_class_name`
  ADD PRIMARY KEY (`product_id`,`class_name_id`),
  ADD KEY `IDX_6A50F3C84584665A` (`product_id`),
  ADD KEY `IDX_6A50F3C8B462FB2A` (`class_name_id`);

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_product_class_name`
--
ALTER TABLE `dtb_product_class_name`
  ADD CONSTRAINT `FK_6A50F3C84584665A` FOREIGN KEY (`product_id`) REFERENCES `dtb_product` (`id`),
  ADD CONSTRAINT `FK_6A50F3C8B462FB2A` FOREIGN KEY (`class_name_id`) REFERENCES `dtb_class_name` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
