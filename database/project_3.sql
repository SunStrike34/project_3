-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Мар 10 2024 г., 21:42
-- Версия сервера: 10.4.27-MariaDB
-- Версия PHP: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `project_3`
--

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(249) NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `verified` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `resettable` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `roles_mask` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `registered` int(10) UNSIGNED NOT NULL,
  `last_login` int(10) UNSIGNED DEFAULT NULL,
  `force_logout` mediumint(7) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `username`, `status`, `verified`, `resettable`, `roles_mask`, `registered`, `last_login`, `force_logout`) VALUES
(1, 'admin11434623444@yan.com', '$2y$10$TlSqDH2SiKfeUEEyDNKHrehVvHun8VCxkcrVZHvtOcUOZN2jpk4Xq', NULL, 0, 1, 1, 0, 1708437044, 1708948255, 0),
(4, 'admin1@yan.com', '$2y$10$dFojJ.xfm57ACbjvfWhRXe7KPqA.qqHaBjdDlAtfF.HcgPuQVUip2', NULL, 0, 1, 1, 0, 1708948713, 1710091622, 0),
(11, 'elisabeth67@lang.com', '$2y$10$dwbUXeHVd4WQiw5H.HlFF.ki/QJ1WA3/8It0ApR8JSkyJ738UPVaS', 'Mr. Pietro Tillman', 0, 1, 1, 0, 1709734334, NULL, 0),
(10, 'jdoyle@hotmail.com', '$2y$10$Tt8eirayEIF6K3N6DZksPeB3rbrAajV4NQFNP/XlArW3wg7hqtc/C', 'Tamara Rolfson PhD', 0, 1, 1, 0, 1709734334, NULL, 0),
(7, 'waelchi.constance@gmail.com', '$2y$10$8s6IeXQzrqudsYtroEopl.c4humoPAQh7BC0S7.O4tBLnEEcQ4Fg.', 'Moises Boyle', 0, 1, 1, 0, 1709733543, NULL, 0),
(8, 'ohermiston@funk.com', '$2y$10$c6zrWe1PuN1ClT8AeTV5S.DoK79uGwMw3e.fwmP/GrWM2pGWDTwjq', 'Trevion Morissette', 0, 1, 1, 0, 1709733543, NULL, 0),
(9, 'lakin.mckayla@yahoo.com', '$2y$10$DqZprQ.KhhkQi8A1yEDUK.JvYGXTuCvlr61J1.oi7xJYi2gTre.UG', 'Viva Douglas', 0, 1, 1, 0, 1709733543, NULL, 0),
(12, 'larue63@gmail.com', '$2y$10$X4JXtYXTyhnZ3RYHb8eTru/9jyddM7Kw/f8c63u91FbGA7FKyGf0S', 'Lamont Sporer', 0, 1, 1, 0, 1709734334, NULL, 0),
(13, 'geoffrey.thompson@cremin.com', '$2y$10$SxcsJyPsG/0/sn/FRg4sn.cKU6F7obOP6PEENYrfACpJDncK8E0Ui', 'Sally Stehr', 0, 1, 1, 0, 1709734334, NULL, 0),
(14, 'lbechtelar@hintz.com', '$2y$10$sw99L9W.mlMgKBbpTXfVreC.O.VDnd18BOghxSjCw6Bpih6fdDHZW', 'Mr. Avery Lynch MD', 0, 1, 1, 0, 1709734334, NULL, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `users_confirmations`
--

CREATE TABLE `users_confirmations` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `email` varchar(249) NOT NULL,
  `selector` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `expires` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users_images`
--

CREATE TABLE `users_images` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `href` varchar(255) NOT NULL,
  `format` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
<div class="alert alert-danger" role="alert"><h1>Ошибка</h1><p><strong>SQL запрос:</strong>  <a href="#" class="copyQueryBtn" data-text="SET SQL_QUOTE_SHOW_CREATE = 1">Копировать</a>
<a href="index.php?route=/database/sql&sql_query=SET+SQL_QUOTE_SHOW_CREATE+%3D+1&show_query=1&db=project_3"><span class="text-nowrap"><img src="themes/dot.gif" title="Изменить" alt="Изменить" class="icon ic_b_edit">&nbsp;Изменить</span></a>    </p>
<p>
<code class="sql"><pre>
SET SQL_QUOTE_SHOW_CREATE = 1
</pre></code>
</p>
<p>
    <strong>Ответ MySQL: </strong><a href="./url.php?url=https%3A%2F%2Fdev.mysql.com%2Fdoc%2Frefman%2F8.0%2Fen%2Fserver-error-reference.html" target="mysql_doc"><img src="themes/dot.gif" title="Документация" alt="Документация" class="icon ic_b_help"></a>
</p>
<code>#2006 - MySQL server has gone away</code><br></div>