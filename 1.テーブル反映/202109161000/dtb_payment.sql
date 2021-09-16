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
-- テーブルの構造 `dtb_payment`
--

CREATE TABLE IF NOT EXISTS `dtb_payment` (
  `id` int(10) unsigned NOT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `charge` decimal(12,2) unsigned DEFAULT 0.00,
  `rule_max` decimal(12,2) unsigned DEFAULT NULL,
  `sort_no` smallint(5) unsigned DEFAULT NULL,
  `fixed` tinyint(1) NOT NULL DEFAULT 1,
  `payment_image` varchar(255) DEFAULT NULL,
  `rule_min` decimal(12,2) unsigned DEFAULT NULL,
  `method_class` varchar(255) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `create_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_payment`
--

INSERT INTO `dtb_payment` (`id`, `creator_id`, `payment_method`, `charge`, `rule_max`, `sort_no`, `fixed`, `payment_image`, `rule_min`, `method_class`, `visible`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, '郵便振替', 0.00, NULL, 5, 1, NULL, 0.00, 'Eccube\\Service\\Payment\\Method\\Cash', 1, '2021-09-02 03:47:59', '2021-09-10 09:55:24', 'payment'),
(2, 1, '現金書留', 0.00, NULL, 4, 1, NULL, 0.00, 'Eccube\\Service\\Payment\\Method\\Cash', 1, '2021-09-02 03:47:59', '2021-09-10 09:55:24', 'payment'),
(3, 1, '銀行振込', 0.00, NULL, 3, 1, NULL, 0.00, 'Eccube\\Service\\Payment\\Method\\Cash', 1, '2021-09-02 03:47:59', '2021-09-10 09:55:24', 'payment'),
(4, 1, '代金引換', 0.00, NULL, 2, 1, NULL, 0.00, 'Eccube\\Service\\Payment\\Method\\Cash', 1, '2021-09-02 03:47:59', '2021-09-10 09:55:24', 'payment'),
(5, 1, '店舗支払い', 0.00, NULL, 1, 1, NULL, NULL, 'Eccube\\Service\\Payment\\Method\\Cash', 1, '2021-09-10 08:27:58', '2021-09-10 09:55:24', 'payment');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_payment`
--
ALTER TABLE `dtb_payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7AFF628F61220EA6` (`creator_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_payment`
--
ALTER TABLE `dtb_payment`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_payment`
--
ALTER TABLE `dtb_payment`
  ADD CONSTRAINT `FK_7AFF628F61220EA6` FOREIGN KEY (`creator_id`) REFERENCES `dtb_member` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
