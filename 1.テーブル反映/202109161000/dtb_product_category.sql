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
-- テーブルの構造 `dtb_product_category`
--

CREATE TABLE IF NOT EXISTS `dtb_product_category` (
  `product_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_product_category`
--

INSERT INTO `dtb_product_category` (`product_id`, `category_id`, `discriminator_type`) VALUES
(7, 7, 'productcategory'),
(7, 11, 'productcategory'),
(7, 61, 'productcategory'),
(7, 62, 'productcategory'),
(26, 3, 'productcategory'),
(27, 3, 'productcategory'),
(28, 57, 'productcategory'),
(47, 6, 'productcategory'),
(48, 6, 'productcategory'),
(78, 1, 'productcategory'),
(85, 15, 'productcategory'),
(85, 40, 'productcategory'),
(91, 9, 'productcategory'),
(92, 7, 'productcategory'),
(94, 9, 'productcategory'),
(111, 13, 'productcategory'),
(112, 13, 'productcategory'),
(113, 13, 'productcategory'),
(114, 13, 'productcategory'),
(115, 13, 'productcategory'),
(116, 14, 'productcategory'),
(117, 14, 'productcategory'),
(118, 14, 'productcategory'),
(119, 14, 'productcategory'),
(120, 14, 'productcategory'),
(121, 15, 'productcategory'),
(124, 14, 'productcategory'),
(127, 15, 'productcategory'),
(128, 10, 'productcategory'),
(129, 10, 'productcategory'),
(130, 10, 'productcategory'),
(131, 15, 'productcategory'),
(132, 36, 'productcategory'),
(133, 35, 'productcategory'),
(144, 39, 'productcategory'),
(145, 39, 'productcategory'),
(146, 39, 'productcategory'),
(147, 39, 'productcategory'),
(148, 39, 'productcategory'),
(149, 39, 'productcategory'),
(150, 40, 'productcategory'),
(151, 40, 'productcategory'),
(152, 40, 'productcategory'),
(153, 40, 'productcategory'),
(154, 40, 'productcategory'),
(155, 40, 'productcategory'),
(156, 41, 'productcategory'),
(157, 41, 'productcategory'),
(158, 41, 'productcategory'),
(159, 41, 'productcategory'),
(160, 41, 'productcategory'),
(161, 42, 'productcategory'),
(162, 42, 'productcategory'),
(163, 42, 'productcategory'),
(164, 42, 'productcategory'),
(165, 42, 'productcategory'),
(166, 42, 'productcategory'),
(167, 42, 'productcategory'),
(168, 42, 'productcategory'),
(169, 42, 'productcategory'),
(170, 42, 'productcategory'),
(171, 42, 'productcategory'),
(172, 42, 'productcategory'),
(173, 42, 'productcategory'),
(174, 42, 'productcategory'),
(175, 42, 'productcategory'),
(176, 43, 'productcategory'),
(177, 43, 'productcategory'),
(178, 43, 'productcategory'),
(179, 43, 'productcategory'),
(180, 43, 'productcategory'),
(181, 43, 'productcategory'),
(182, 43, 'productcategory'),
(183, 43, 'productcategory'),
(184, 45, 'productcategory'),
(185, 44, 'productcategory'),
(186, 45, 'productcategory'),
(187, 44, 'productcategory'),
(188, 45, 'productcategory'),
(189, 44, 'productcategory'),
(190, 45, 'productcategory'),
(191, 44, 'productcategory'),
(192, 47, 'productcategory'),
(193, 47, 'productcategory'),
(194, 46, 'productcategory'),
(195, 47, 'productcategory'),
(196, 46, 'productcategory'),
(197, 47, 'productcategory'),
(198, 46, 'productcategory'),
(199, 47, 'productcategory'),
(200, 46, 'productcategory'),
(201, 47, 'productcategory'),
(202, 46, 'productcategory'),
(203, 47, 'productcategory'),
(204, 46, 'productcategory'),
(205, 47, 'productcategory'),
(206, 46, 'productcategory'),
(207, 47, 'productcategory'),
(208, 46, 'productcategory'),
(209, 47, 'productcategory'),
(210, 46, 'productcategory'),
(211, 47, 'productcategory'),
(212, 46, 'productcategory'),
(213, 47, 'productcategory'),
(214, 46, 'productcategory'),
(215, 47, 'productcategory'),
(216, 46, 'productcategory'),
(217, 47, 'productcategory'),
(218, 46, 'productcategory'),
(219, 47, 'productcategory'),
(220, 46, 'productcategory'),
(221, 47, 'productcategory'),
(222, 46, 'productcategory'),
(223, 47, 'productcategory'),
(224, 46, 'productcategory'),
(225, 47, 'productcategory'),
(226, 46, 'productcategory'),
(227, 46, 'productcategory'),
(228, 46, 'productcategory'),
(229, 46, 'productcategory'),
(230, 46, 'productcategory'),
(231, 46, 'productcategory'),
(232, 46, 'productcategory'),
(233, 47, 'productcategory'),
(234, 47, 'productcategory'),
(235, 47, 'productcategory'),
(236, 47, 'productcategory'),
(237, 47, 'productcategory'),
(238, 47, 'productcategory'),
(239, 47, 'productcategory'),
(240, 46, 'productcategory'),
(241, 46, 'productcategory'),
(242, 46, 'productcategory'),
(243, 46, 'productcategory'),
(244, 47, 'productcategory'),
(245, 47, 'productcategory'),
(246, 47, 'productcategory'),
(248, 39, 'productcategory'),
(249, 49, 'productcategory'),
(250, 49, 'productcategory'),
(251, 49, 'productcategory'),
(252, 49, 'productcategory'),
(253, 51, 'productcategory'),
(254, 51, 'productcategory'),
(255, 51, 'productcategory'),
(256, 51, 'productcategory'),
(257, 51, 'productcategory'),
(258, 51, 'productcategory'),
(259, 50, 'productcategory'),
(260, 50, 'productcategory'),
(261, 50, 'productcategory'),
(262, 50, 'productcategory'),
(263, 53, 'productcategory'),
(264, 53, 'productcategory'),
(265, 53, 'productcategory'),
(266, 53, 'productcategory'),
(267, 53, 'productcategory'),
(268, 52, 'productcategory'),
(269, 52, 'productcategory'),
(270, 52, 'productcategory'),
(271, 52, 'productcategory'),
(272, 52, 'productcategory'),
(273, 52, 'productcategory'),
(274, 52, 'productcategory'),
(275, 53, 'productcategory'),
(276, 53, 'productcategory'),
(277, 50, 'productcategory'),
(278, 51, 'productcategory'),
(279, 51, 'productcategory'),
(280, 50, 'productcategory'),
(281, 55, 'productcategory'),
(282, 55, 'productcategory'),
(283, 55, 'productcategory'),
(284, 55, 'productcategory'),
(285, 55, 'productcategory'),
(286, 55, 'productcategory'),
(287, 55, 'productcategory'),
(288, 55, 'productcategory'),
(289, 55, 'productcategory'),
(290, 54, 'productcategory'),
(291, 54, 'productcategory'),
(292, 54, 'productcategory'),
(293, 54, 'productcategory'),
(294, 54, 'productcategory'),
(295, 54, 'productcategory'),
(296, 54, 'productcategory'),
(297, 54, 'productcategory'),
(298, 54, 'productcategory'),
(299, 54, 'productcategory'),
(300, 53, 'productcategory'),
(301, 57, 'productcategory'),
(302, 57, 'productcategory'),
(303, 5, 'productcategory'),
(304, 39, 'productcategory'),
(305, 38, 'productcategory'),
(306, 37, 'productcategory'),
(307, 3, 'productcategory');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_product_category`
--
ALTER TABLE `dtb_product_category`
  ADD PRIMARY KEY (`product_id`,`category_id`),
  ADD KEY `IDX_B05778914584665A` (`product_id`),
  ADD KEY `IDX_B057789112469DE2` (`category_id`);

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_product_category`
--
ALTER TABLE `dtb_product_category`
  ADD CONSTRAINT `FK_B057789112469DE2` FOREIGN KEY (`category_id`) REFERENCES `dtb_category` (`id`),
  ADD CONSTRAINT `FK_B05778914584665A` FOREIGN KEY (`product_id`) REFERENCES `dtb_product` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
