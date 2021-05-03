-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 01-03-2021 a las 22:37:03
-- Versión del servidor: 5.7.33-0ubuntu0.18.04.1
-- Versión de PHP: 7.2.24-0ubuntu0.18.04.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `creator`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blurt_orders`
--

CREATE TABLE `blurt_orders` (
  `id` int(11) NOT NULL,
  `memo` varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
  `account_metadata` longtext COLLATE utf8_spanish2_ci NOT NULL,
  `buyer_email` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `user` varchar(20) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `amount` double NOT NULL,
  `status` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '00-00-0000 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
 
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `close_orders`
--

CREATE TABLE `close_orders` (
  `id` int(11) NOT NULL,
  `account_metadata` longtext COLLATE utf8_spanish2_ci NOT NULL,
  `buyer_email` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `tx_id` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `method` varchar(20) COLLATE utf8_spanish2_ci NOT NULL DEFAULT 'coinpayments',
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coinpayments`
--

CREATE TABLE `coinpayments` (
  `id` int(11) NOT NULL,
  `public` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `private` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `name` varchar(60) COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `open_orders`
--

CREATE TABLE `open_orders` (
  `id` int(11) NOT NULL,
  `tx_id` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `amount` float NOT NULL,
  `currency` varchar(10) COLLATE utf8_spanish2_ci NOT NULL,
  `buyer_email` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `deposit_address` varchar(80) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `account_metadata` longtext COLLATE utf8_spanish2_ci NOT NULL,
  `method` varchar(20) COLLATE utf8_spanish2_ci NOT NULL DEFAULT 'coinpayments',
  `status` int(11) NOT NULL DEFAULT '0',
  `time` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paypal`
--

CREATE TABLE `paypal` (
  `id` int(11) NOT NULL,
  `email` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `client_id` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `secret` varchar(150) COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE `settings` (
  `creator` varchar(60) COLLATE utf8_spanish2_ci NOT NULL,
  `creatorKey` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `admin_mail` varchar(255) COLLATE utf8_spanish2_ci NOT NULL,
  `account_price` float NOT NULL DEFAULT '3'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `blurt_orders`
--
ALTER TABLE `blurt_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `close_orders`
--
ALTER TABLE `close_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `coinpayments`
--
ALTER TABLE `coinpayments`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `open_orders`
--
ALTER TABLE `open_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `paypal`
--
ALTER TABLE `paypal`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `blurt_orders`
--
ALTER TABLE `blurt_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `close_orders`
--
ALTER TABLE `close_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `coinpayments`
--
ALTER TABLE `coinpayments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `open_orders`
--
ALTER TABLE `open_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `paypal`
--
ALTER TABLE `paypal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
