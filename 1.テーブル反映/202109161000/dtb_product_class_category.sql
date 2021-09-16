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
-- テーブルの構造 `dtb_product_class_category`
--

CREATE TABLE IF NOT EXISTS `dtb_product_class_category` (
  `product_id` int(10) unsigned NOT NULL,
  `class_category_id` int(10) unsigned NOT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_product_class_category`
--

INSERT INTO `dtb_product_class_category` (`product_id`, `class_category_id`, `discriminator_type`) VALUES
(7, 6, 'productclasscategory'),
(7, 7, 'productclasscategory'),
(7, 8, 'productclasscategory'),
(7, 9, 'productclasscategory'),
(7, 10, 'productclasscategory'),
(7, 11, 'productclasscategory'),
(7, 12, 'productclasscategory'),
(7, 13, 'productclasscategory'),
(7, 14, 'productclasscategory'),
(7, 15, 'productclasscategory'),
(7, 16, 'productclasscategory'),
(7, 17, 'productclasscategory'),
(7, 18, 'productclasscategory'),
(7, 19, 'productclasscategory'),
(7, 20, 'productclasscategory'),
(7, 21, 'productclasscategory'),
(7, 22, 'productclasscategory'),
(7, 23, 'productclasscategory'),
(7, 24, 'productclasscategory'),
(7, 25, 'productclasscategory'),
(7, 26, 'productclasscategory'),
(7, 27, 'productclasscategory'),
(7, 28, 'productclasscategory'),
(7, 29, 'productclasscategory'),
(7, 30, 'productclasscategory'),
(7, 31, 'productclasscategory'),
(7, 32, 'productclasscategory'),
(7, 33, 'productclasscategory'),
(7, 34, 'productclasscategory'),
(7, 35, 'productclasscategory'),
(7, 36, 'productclasscategory'),
(7, 37, 'productclasscategory'),
(7, 38, 'productclasscategory'),
(7, 39, 'productclasscategory'),
(7, 40, 'productclasscategory'),
(7, 41, 'productclasscategory'),
(7, 42, 'productclasscategory'),
(7, 43, 'productclasscategory'),
(7, 44, 'productclasscategory'),
(7, 45, 'productclasscategory'),
(7, 46, 'productclasscategory'),
(7, 47, 'productclasscategory'),
(7, 48, 'productclasscategory'),
(7, 49, 'productclasscategory'),
(7, 50, 'productclasscategory'),
(7, 51, 'productclasscategory'),
(7, 52, 'productclasscategory'),
(7, 53, 'productclasscategory'),
(7, 54, 'productclasscategory'),
(7, 55, 'productclasscategory'),
(7, 56, 'productclasscategory'),
(7, 57, 'productclasscategory'),
(7, 58, 'productclasscategory'),
(7, 59, 'productclasscategory'),
(7, 60, 'productclasscategory'),
(7, 61, 'productclasscategory'),
(7, 62, 'productclasscategory'),
(7, 63, 'productclasscategory'),
(7, 64, 'productclasscategory'),
(7, 65, 'productclasscategory'),
(7, 66, 'productclasscategory'),
(7, 89, 'productclasscategory'),
(7, 90, 'productclasscategory'),
(7, 91, 'productclasscategory'),
(7, 92, 'productclasscategory'),
(7, 93, 'productclasscategory'),
(7, 94, 'productclasscategory'),
(7, 95, 'productclasscategory'),
(7, 96, 'productclasscategory'),
(7, 97, 'productclasscategory'),
(7, 98, 'productclasscategory'),
(7, 99, 'productclasscategory'),
(7, 100, 'productclasscategory'),
(7, 101, 'productclasscategory'),
(7, 102, 'productclasscategory'),
(7, 103, 'productclasscategory'),
(7, 104, 'productclasscategory'),
(7, 105, 'productclasscategory'),
(7, 106, 'productclasscategory'),
(7, 107, 'productclasscategory'),
(7, 108, 'productclasscategory'),
(7, 109, 'productclasscategory'),
(7, 110, 'productclasscategory'),
(7, 111, 'productclasscategory'),
(7, 112, 'productclasscategory'),
(7, 113, 'productclasscategory'),
(7, 114, 'productclasscategory'),
(7, 115, 'productclasscategory'),
(7, 116, 'productclasscategory'),
(7, 117, 'productclasscategory'),
(7, 118, 'productclasscategory'),
(7, 119, 'productclasscategory'),
(7, 120, 'productclasscategory'),
(7, 121, 'productclasscategory'),
(7, 122, 'productclasscategory'),
(7, 123, 'productclasscategory'),
(7, 124, 'productclasscategory'),
(7, 125, 'productclasscategory'),
(7, 126, 'productclasscategory'),
(7, 127, 'productclasscategory'),
(7, 128, 'productclasscategory'),
(7, 129, 'productclasscategory'),
(7, 130, 'productclasscategory'),
(7, 131, 'productclasscategory'),
(7, 132, 'productclasscategory');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_product_class_category`
--
ALTER TABLE `dtb_product_class_category`
  ADD PRIMARY KEY (`product_id`,`class_category_id`),
  ADD KEY `IDX_5C95D6A74584665A` (`product_id`),
  ADD KEY `IDX_5C95D6A72A2852D4` (`class_category_id`);

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_product_class_category`
--
ALTER TABLE `dtb_product_class_category`
  ADD CONSTRAINT `FK_5C95D6A72A2852D4` FOREIGN KEY (`class_category_id`) REFERENCES `dtb_class_category` (`id`),
  ADD CONSTRAINT `FK_5C95D6A74584665A` FOREIGN KEY (`product_id`) REFERENCES `dtb_product` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
