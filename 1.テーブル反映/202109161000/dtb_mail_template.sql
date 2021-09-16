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
-- テーブルの構造 `dtb_mail_template`
--

CREATE TABLE IF NOT EXISTS `dtb_mail_template` (
  `id` int(10) unsigned NOT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `mail_subject` varchar(255) DEFAULT NULL,
  `create_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_mail_template`
--

INSERT INTO `dtb_mail_template` (`id`, `creator_id`, `name`, `file_name`, `mail_subject`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, NULL, '注文受付メール', 'Mail/order.twig', 'ご注文ありがとうございます', '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'mailtemplate'),
(2, NULL, '会員仮登録メール', 'Mail/entry_confirm.twig', '会員登録のご確認', '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'mailtemplate'),
(3, NULL, '会員本登録メール', 'Mail/entry_complete.twig', '会員登録が完了しました。', '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'mailtemplate'),
(4, NULL, '会員退会メール', 'Mail/customer_withdraw_mail.twig', '退会手続きのご完了', '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'mailtemplate'),
(5, NULL, '問合受付メール', 'Mail/contact_mail.twig', 'お問い合わせを受け付けました。', '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'mailtemplate'),
(6, NULL, 'パスワードリセット', 'Mail/forgot_mail.twig', 'パスワード変更のご確認', '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'mailtemplate'),
(7, NULL, 'パスワードリマインダー', 'Mail/reset_complete_mail.twig', 'パスワード変更のお知らせ', '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'mailtemplate'),
(8, NULL, '出荷通知メール', 'Mail/shipping_notify.twig', '商品出荷のお知らせ', '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'mailtemplate');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_mail_template`
--
ALTER TABLE `dtb_mail_template`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_1CB16DB261220EA6` (`creator_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_mail_template`
--
ALTER TABLE `dtb_mail_template`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_mail_template`
--
ALTER TABLE `dtb_mail_template`
  ADD CONSTRAINT `FK_1CB16DB261220EA6` FOREIGN KEY (`creator_id`) REFERENCES `dtb_member` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
