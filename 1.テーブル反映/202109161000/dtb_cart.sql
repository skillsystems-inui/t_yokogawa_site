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
-- テーブルの構造 `dtb_cart`
--

CREATE TABLE IF NOT EXISTS `dtb_cart` (
  `id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `cart_key` varchar(255) DEFAULT NULL,
  `pre_order_id` varchar(255) DEFAULT NULL,
  `total_price` decimal(12,2) unsigned NOT NULL DEFAULT 0.00,
  `delivery_fee_total` decimal(12,2) unsigned NOT NULL DEFAULT 0.00,
  `sort_no` smallint(5) unsigned DEFAULT NULL,
  `create_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `add_point` decimal(12,0) unsigned NOT NULL DEFAULT 0,
  `use_point` decimal(12,0) unsigned NOT NULL DEFAULT 0,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_cart`
--

INSERT INTO `dtb_cart` (`id`, `customer_id`, `cart_key`, `pre_order_id`, `total_price`, `delivery_fee_total`, `sort_no`, `create_date`, `update_date`, `add_point`, `use_point`, `discriminator_type`) VALUES
(41, NULL, 'SGU1WC4C6OeTz27tsLRqvoWxbcTWYkYb_1', NULL, 2916.00, 0.00, NULL, '2021-09-14 00:30:07', '2021-09-14 00:30:07', 0, 0, 'cart'),
(46, NULL, 'uG684IjQHMO8JFUUMsV2RKV1kp1HVkpe_1', '64b796e265678f79c6e88849cceacc6066735879', 2916.00, 0.00, NULL, '2021-09-14 15:29:05', '2021-09-14 15:46:47', 0, 0, 'cart'),
(47, NULL, 'XCUNm5wbMUOYPQwZKV5tOtM1xHYjuQgz_1', NULL, 419.00, 0.00, NULL, '2021-09-15 00:42:45', '2021-09-15 00:48:38', 0, 0, 'cart'),
(73, 1, '1_1', NULL, 11324.00, 0.00, NULL, '2021-09-15 21:38:04', '2021-09-15 21:38:05', 0, 0, 'cart');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_cart`
--
ALTER TABLE `dtb_cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dtb_cart_pre_order_id_idx` (`pre_order_id`),
  ADD KEY `IDX_FC3C24F09395C3F3` (`customer_id`),
  ADD KEY `dtb_cart_update_date_idx` (`update_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_cart`
--
ALTER TABLE `dtb_cart`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=74;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_cart`
--
ALTER TABLE `dtb_cart`
  ADD CONSTRAINT `FK_FC3C24F09395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `dtb_customer` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
