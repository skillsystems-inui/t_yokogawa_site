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
-- テーブルの構造 `dtb_class_name`
--

CREATE TABLE IF NOT EXISTS `dtb_class_name` (
  `id` int(10) unsigned NOT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `backend_name` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `sort_no` int(10) unsigned NOT NULL,
  `create_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_class_name`
--

INSERT INTO `dtb_class_name` (`id`, `creator_id`, `backend_name`, `name`, `sort_no`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, '受取り店舗', '受取り店舗', 22, '2021-07-01 06:42:16', '2021-09-12 15:01:59', 'classname'),
(2, 1, 'ケーキサイズ', 'ケーキサイズ', 23, '2021-07-01 06:43:17', '2021-09-12 15:01:59', 'classname'),
(3, 1, 'キャンドル 大', 'キャンドル 大', 21, '2021-07-01 06:48:01', '2021-09-12 15:01:59', 'classname'),
(4, 1, 'キャンドル 小', 'キャンドル 小', 20, '2021-07-01 06:50:27', '2021-09-12 15:01:59', 'classname'),
(5, 1, 'Noキャンドル 1つめ', 'Noキャンドル 1つめ', 19, '2021-09-09 06:11:16', '2021-09-12 15:01:59', 'classname'),
(6, 1, 'Noキャンドル 2つめ', 'Noキャンドル 2つめ', 18, '2021-09-09 06:11:42', '2021-09-12 15:01:59', 'classname'),
(7, 1, 'Noキャンドル 3つめ', 'Noキャンドル 3つめ', 17, '2021-09-09 06:25:42', '2021-09-12 15:01:59', 'classname'),
(8, 1, 'メッセージプレート', 'メッセージプレート', 10, '2021-07-01 06:52:11', '2021-09-12 15:01:59', 'classname'),
(9, 1, '持ち帰りポリ袋', '持ち帰りポリ袋', 4, '2021-09-10 05:28:02', '2021-09-12 15:01:59', 'classname'),
(10, 1, '包装方法', '包装方法', 3, '2021-07-01 07:34:37', '2021-09-12 15:01:59', 'classname'),
(11, 1, '熨斗紙の掛け方', '熨斗紙の掛け方', 3, '2021-07-25 06:13:46', '2021-09-12 15:01:59', 'classname'),
(12, 1, '掛け紙・包装の種類', '掛け紙・包装の種類', 2, '2021-07-01 07:34:37', '2021-09-12 15:01:59', 'classname'),
(13, 1, '上書き', '上書き', 1, '2021-07-01 07:36:20', '2021-09-12 15:01:59', 'classname'),
(14, 1, 'デコレーション追加:いちごUP', 'デコレーション追加:いちごUP', 9, '2021-09-10 04:09:25', '2021-09-12 15:01:59', 'classname'),
(15, 1, 'デコレーション追加:フルーツUP', 'デコレーション追加:フルーツUP', 8, '2021-09-10 04:09:39', '2021-09-12 15:01:59', 'classname'),
(16, 1, 'デコレーション追加:生チョコUP', 'デコレーション追加:生チョコUP', 7, '2021-09-10 04:09:56', '2021-09-12 15:01:59', 'classname'),
(17, 1, 'デコレーション追加:絵チョコ (4号)', 'デコレーション追加:絵チョコ (4号)', 6, '2021-09-10 04:10:29', '2021-09-12 15:01:59', 'classname');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_class_name`
--
ALTER TABLE `dtb_class_name`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_187C95AD61220EA6` (`creator_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_class_name`
--
ALTER TABLE `dtb_class_name`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_class_name`
--
ALTER TABLE `dtb_class_name`
  ADD CONSTRAINT `FK_187C95AD61220EA6` FOREIGN KEY (`creator_id`) REFERENCES `dtb_member` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
