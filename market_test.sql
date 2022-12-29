-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-12-2022 a las 00:40:38
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `market_test`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_categories` ()   BEGIN
select c.id_category as id ,c.category as "name" from categories c order by c.category;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_product_by_user_and_category` (`p_id_user` INT, `p_id_category` INT)   begin
select u.*, p.name_product, p.id_product, up.stok_product, c.category from products p
inner join users_products up
on p.id_product=up.product_id
inner join users u
on up.user_id=u.id_user
inner join categories c
on p.category_id=c.id_category
where u.id_user = p_id_user and c.id_category = p_id_category;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `new_user` (`p_email` VARCHAR(45), `p_password` VARCHAR(256), `p_first_name` VARCHAR(110), `p_last_name` VARCHAR(110), `p_phone` VARCHAR(20))   BEGIN
INSERT INTO users(`email`, `password`, `first_name`, `last_name`, `phone`) 
VALUES (p_email, p_password, p_first_name, p_last_name, p_phone);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_token_user` (`p_id_user` INT(11), `p_token` VARCHAR(255), `p_exp_token` INT(50))   BEGIN
UPDATE users SET 
user_token=p_token,
exp_token=p_exp_token
WHERE id_user = p_id_user;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id_category` int(11) NOT NULL,
  `category` varchar(45) COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id_category`, `category`) VALUES
(1, 'viveres'),
(2, 'charcuteria'),
(3, 'perfumeria'),
(4, 'carnes'),
(5, 'verduras'),
(6, 'electronico'),
(7, 'vehiculo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id_product` int(11) NOT NULL,
  `name_product` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id_product`, `name_product`, `category_id`) VALUES
(1, 'Harina Pan', 1),
(2, 'Malta', 1),
(3, 'Queso', 2),
(4, 'Audifono', 6),
(5, 'Pan', 1),
(6, 'Refresco', 1),
(7, 'cereal', 1),
(8, 'Leche', 2),
(9, 'Pasta', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `email` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `password` varchar(256) COLLATE utf8_spanish2_ci NOT NULL,
  `first_name` varchar(110) COLLATE utf8_spanish2_ci NOT NULL,
  `last_name` varchar(110) COLLATE utf8_spanish2_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `user_token` varchar(255) COLLATE utf8_spanish2_ci NOT NULL,
  `exp_token` int(50) NOT NULL,
  `create_at` datetime DEFAULT current_timestamp(),
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id_user`, `email`, `password`, `first_name`, `last_name`, `phone`, `user_token`, `exp_token`, `create_at`, `update_at`) VALUES
(29, 'alex@alex.com', '$2y$10$SfTvEXhxF/QpqHX3Mwl5W.jugUAtsNrfmi6kSIRrPXdMloBIkR6Jq', 'alex', 'vivas', NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE2NzIzNTcwNzQsImV4cCI6MTY3NDk0OTA3NCwiZGF0YSI6eyJpZF91c2VyIjoyOSwiZW1haWwiOiJhbGV4QGFsZXguY29tIiwiZmlyc3RfbmFtZSI6ImFsZXgiLCJsYXN0X25hbWUiOiJ2aXZhcyJ9fQ.iIYlwMqswR0uiiAcv4v1foh6TE7frrLFXnDIqWDFbWtDxTQEDzOpM32', 1674949074, '2022-12-28 16:14:21', NULL),
(30, 'alex10@alex.com', '$2y$10$kMWy2faYk2ObpUzR9sbp7OvCSreDuEL4cnPhruPab/8pDW8IiCFTC', 'alex', 'vivas', NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE2NzIyNzE3MzUsImV4cCI6MTY3NDg2MzczNSwiZGF0YSI6eyJmaXJzdF9uYW1lIjoiYWxleCIsImxhc3RfbmFtZSI6InZpdmFzIn19.vYK0ffsl_IVws7Pdik5J8kE1fz2Gc1Aj9jEAG1CqodrDyHnjKzx7-KYo6WgvaOSUC9Z1DW7ccBDZ8Rx7pFOKFg', 1674863735, '2022-12-28 16:29:30', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_products`
--

CREATE TABLE `users_products` (
  `id_users_products` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `stok_product` int(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_category`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `FK_product_category` (`category_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- Indices de la tabla `users_products`
--
ALTER TABLE `users_products`
  ADD PRIMARY KEY (`id_users_products`),
  ADD KEY `FK_users_products_users` (`user_id`),
  ADD KEY `FK_users_products_products` (`product_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `users_products`
--
ALTER TABLE `users_products`
  MODIFY `id_users_products` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `FK_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id_category`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `users_products`
--
ALTER TABLE `users_products`
  ADD CONSTRAINT `FK_users_products_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_users_products_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
