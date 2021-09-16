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
-- テーブルの構造 `dtb_page`
--

CREATE TABLE IF NOT EXISTS `dtb_page` (
  `id` int(10) unsigned NOT NULL,
  `master_page_id` int(10) unsigned DEFAULT NULL,
  `page_name` varchar(255) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `edit_type` smallint(5) unsigned NOT NULL DEFAULT 1,
  `author` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `create_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `meta_robots` varchar(255) DEFAULT NULL,
  `meta_tags` varchar(4000) DEFAULT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_page`
--

INSERT INTO `dtb_page` (`id`, `master_page_id`, `page_name`, `url`, `file_name`, `edit_type`, `author`, `description`, `keyword`, `create_date`, `update_date`, `meta_robots`, `meta_tags`, `discriminator_type`) VALUES
(0, NULL, 'プレビューデータ', 'preview', NULL, 1, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(1, NULL, 'TOPページ', 'homepage', 'index', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(2, NULL, '商品一覧ページ', 'product_list', 'Product/list', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(3, NULL, '商品詳細ページ', 'product_detail', 'Product/detail', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 06:48:33', NULL, '<meta property="og:type" content="og:product" /><meta property="og:title" content="{{ Product.name }}" />\r\n<meta property="og:image" content="{{ url(''homepage'') }}{{ asset(Product.main_list_image|no_image_product, ''save_image'') }}" />\r\n<meta property="og:description" content="{{ Product.description_list|striptags }}" />\r\n<meta property="og:url" content="{{ url(''product_detail'', {''id'': Product.id}) }}" />\r\n<meta property="product:price:amount" content="{{ Product.getPrice02IncTaxMin }}"/>\r\n<meta property="product:price:currency" content="{{ eccube_config.currency }}"/>\r\n<meta property="product:product_link" content="{{ url(''product_detail'', {''id'': Product.id}) }}"/>\r\n<meta property="product:retailer_title" content="{{ BaseInfo.shop_name }}"/>', 'page'),
(4, NULL, 'MYページ', 'mypage', 'Mypage/index', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(5, NULL, 'MYページ/会員登録内容変更(入力ページ)', 'mypage_change', 'Mypage/change', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(6, NULL, 'MYページ/会員登録内容変更(完了ページ)', 'mypage_change_complete', 'Mypage/change_complete', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(7, NULL, 'MYページ/お届け先一覧', 'mypage_delivery', 'Mypage/delivery', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(8, NULL, 'MYページ/お届け先追加', 'mypage_delivery_new', 'Mypage/delivery_edit', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(9, NULL, 'MYページ/お気に入り一覧', 'mypage_favorite', 'Mypage/favorite', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(10, NULL, 'MYページ/購入履歴詳細', 'mypage_history', 'Mypage/history', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(11, NULL, 'MYページ/ログイン', 'mypage_login', 'Mypage/login', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(12, NULL, 'MYページ/退会手続き(入力ページ)', 'mypage_withdraw', 'Mypage/withdraw', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(13, NULL, 'MYページ/退会手続き(完了ページ)', 'mypage_withdraw_complete', 'Mypage/withdraw_complete', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(14, NULL, '当サイトについて', 'help_about', 'Help/about', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(15, NULL, '現在のカゴの中', 'cart', 'Cart/index', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(16, NULL, 'お問い合わせ(入力ページ)', 'contact', 'Contact/index', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(17, NULL, 'お問い合わせ(完了ページ)', 'contact_complete', 'Contact/complete', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(18, NULL, '会員登録(入力ページ)', 'entry', 'Entry/index', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(19, NULL, 'ご利用規約', 'help_agreement', 'Help/agreement', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(20, NULL, '会員登録(完了ページ)', 'entry_complete', 'Entry/complete', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(21, NULL, '特定商取引に関する法律に基づく表記', 'help_tradelaw', 'Help/tradelaw', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(22, NULL, '本会員登録(完了ページ)', 'entry_activate', 'Entry/activate', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(23, NULL, '商品購入', 'shopping', 'Shopping/index', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(24, NULL, '商品購入/お届け先の指定', 'shopping_shipping', 'Shopping/shipping', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(25, NULL, '商品購入/お届け先の複数指定', 'shopping_shipping_multiple', 'Shopping/shipping_multiple', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(28, NULL, '商品購入/ご注文完了', 'shopping_complete', 'Shopping/complete', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(29, NULL, 'プライバシーポリシー', 'help_privacy', 'Help/privacy', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(30, NULL, '商品購入ログイン', 'shopping_login', 'Shopping/login', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(31, NULL, '非会員購入情報入力', 'shopping_nonmember', 'Shopping/nonmember', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(32, NULL, '商品購入/お届け先の追加', 'shopping_shipping_edit', 'Shopping/shipping_edit', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(33, NULL, '商品購入/お届け先の複数指定(お届け先の追加)', 'shopping_shipping_multiple_edit', 'Shopping/shipping_multiple_edit', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(34, NULL, '商品購入/購入エラー', 'shopping_error', 'Shopping/shopping_error', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(35, NULL, 'ご利用ガイド', 'help_guide', 'Help/guide', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(36, NULL, 'パスワード再発行(入力ページ)', 'forgot', 'Forgot/index', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', NULL, NULL, 'page'),
(37, NULL, 'パスワード再発行(完了ページ)', 'forgot_complete', 'Forgot/complete', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(38, NULL, 'パスワード再発行(再設定ページ)', 'forgot_reset', 'Forgot/reset', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(42, NULL, '商品購入/遷移', 'shopping_redirect_to', 'Shopping/index', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(44, 8, 'MYページ/お届け先編集', 'mypage_delivery_edit', 'Mypage/delivery_edit', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(45, NULL, '商品購入/ご注文確認', 'shopping_confirm', 'Shopping/confirm', 2, NULL, NULL, NULL, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'noindex', NULL, 'page'),
(46, NULL, '商品購入/クーポン利用', 'plugin_coupon_shopping', 'Coupon4/Resource/template/default/shopping_coupon', 2, NULL, NULL, NULL, '2021-09-02 06:31:52', '2021-09-02 06:31:52', 'noindex', NULL, 'page'),
(48, NULL, '商品一覧(カテゴリ単位)', 'product_lineup', 'product_lineup', 0, NULL, NULL, NULL, '2021-09-10 07:15:31', '2021-09-10 07:33:20', NULL, NULL, 'page'),
(49, NULL, 'recruit', 'recruit', 'recruit', 0, NULL, NULL, NULL, '2021-09-13 09:11:37', '2021-09-14 01:14:24', NULL, NULL, 'page'),
(50, NULL, 'recruit_a', 'recruit_a', 'recruit_a', 0, NULL, NULL, NULL, '2021-09-14 01:22:05', '2021-09-14 01:22:05', NULL, NULL, 'page'),
(51, NULL, 'recruit_ｂ', 'recruit_b', 'recruit_b', 0, NULL, NULL, NULL, '2021-09-14 01:43:49', '2021-09-14 01:43:49', NULL, NULL, 'page'),
(52, NULL, 'ourbrand', 'ourbrand', 'ourbrand', 0, NULL, NULL, NULL, '2021-09-15 09:17:08', '2021-09-15 09:17:08', NULL, NULL, 'page'),
(53, NULL, 'about_finish', 'about_finish', 'about_finish', 0, NULL, NULL, NULL, '2021-09-15 09:26:11', '2021-09-15 09:26:11', NULL, NULL, 'page'),
(54, NULL, 'about_material', 'about_material', 'about_material', 0, NULL, NULL, NULL, '2021-09-15 09:27:28', '2021-09-15 09:27:28', NULL, NULL, 'page'),
(55, NULL, 'shop', 'shop', 'shop', 0, NULL, NULL, NULL, '2021-09-15 09:40:23', '2021-09-15 09:40:23', NULL, NULL, 'page'),
(56, NULL, 'faq', 'faq', 'faq', 0, NULL, NULL, NULL, '2021-09-15 09:58:30', '2021-09-15 09:58:30', NULL, NULL, 'page'),
(57, NULL, 'regbag', 'regbag', 'regbag', 0, NULL, NULL, NULL, '2021-09-15 10:24:51', '2021-09-15 10:24:51', NULL, NULL, 'page');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_page`
--
ALTER TABLE `dtb_page`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E3951A67D0618E8C` (`master_page_id`),
  ADD KEY `dtb_page_url_idx` (`url`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_page`
--
ALTER TABLE `dtb_page`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=58;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_page`
--
ALTER TABLE `dtb_page`
  ADD CONSTRAINT `FK_E3951A67D0618E8C` FOREIGN KEY (`master_page_id`) REFERENCES `dtb_page` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
