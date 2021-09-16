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
-- テーブルの構造 `dtb_news`
--

CREATE TABLE IF NOT EXISTS `dtb_news` (
  `id` int(10) unsigned NOT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `publish_date` datetime DEFAULT NULL COMMENT '(DC2Type:datetimetz)',
  `title` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `url` varchar(4000) DEFAULT NULL,
  `link_method` tinyint(1) NOT NULL DEFAULT 0,
  `create_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_news`
--

INSERT INTO `dtb_news` (`id`, `creator_id`, `publish_date`, `title`, `description`, `url`, `link_method`, `create_date`, `update_date`, `visible`, `discriminator_type`) VALUES
(1, 1, '2021-09-30 15:00:00', 'サイトリニューアルいたしました', '旬の色どりスイーツとこだわりのジェラートをお届けします。', NULL, 0, '2021-09-02 03:47:59', '2021-09-15 10:41:05', 1, 'news'),
(2, 1, '2020-08-31 00:00:00', '新規会員登録で300ポイントプレゼント！', 'いつもT.YOKOGAWAをご愛顧いただきまして誠にありがとうございます。\r\nこの度のホームページのリニューアルに伴い、新規会員登録で300ポイントプレゼントキャンペーンを行っております！\r\n\r\nご利用方法\r\n①「新規会員登録」よりお客様の情報をご入力ください。\r\n②登録が完了すると、自動的にポイントが付与されます。\r\n\r\nポイントは1ポイント1円として500ポイントからご利用いただけます。\r\n\r\nぜひこの機会にご利用くださいませ！', NULL, 0, '2021-09-15 10:42:03', '2021-09-15 10:42:03', 1, 'news');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_news`
--
ALTER TABLE `dtb_news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_EA4C351761220EA6` (`creator_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_news`
--
ALTER TABLE `dtb_news`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_news`
--
ALTER TABLE `dtb_news`
  ADD CONSTRAINT `FK_EA4C351761220EA6` FOREIGN KEY (`creator_id`) REFERENCES `dtb_member` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
