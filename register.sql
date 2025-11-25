-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-10-2025 a las 12:43:56
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `unidad_uno`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `register`
--

CREATE TABLE `register` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `secondname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pssword` varchar(100) NOT NULL,
  `recovery_code` varchar(100) NOT NULL,
  `recovery_expiration` datetime DEFAULT NULL,
  `session_token` varchar(64) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `register`
--

INSERT INTO `register` (`id`, `name`, `secondname`, `email`, `pssword`, `recovery_code`, `recovery_expiration`, `session_token`, `role`) VALUES
(1, 'karol', 'martinez', 'kbethmtz@gmail.com', '$2y$10$6WlcVmA2qBPkMqfMk2jgye0bw20airrgLzEkzjN80U/a0Rg28gx5S', '1e29008c597b67fa16d5b81b3e911b74', '2025-07-18 04:34:54', '257512c779cb6861576ba16f9c4eb4992b57a97cd0051cd47fc0e86e420445bd', 'usuario'),
(2, 'Admin User', 'Admin', 'admin@example.com', '$2y$10$UGPiQORl02dLGlt4ubkXoeruVY6UtuPnvCd2fuxBAvvfLQn.3S9su', '', NULL, NULL, 'administrador'),
(3, 'Editor User', 'Editor', 'editor@example.com', '$2y$10$hEfMy/k/nTlRQt3za6e3b.m/BgdfWffb0UGftdc5jYS.4l2i0wABu', '', NULL, NULL, 'editor'),
(4, 'Regular User', 'User', 'user@example.com', '$2y$10$Z.m4JxpkjdJA1dc9pEubO.oaETv/8QnVi6z7Afcm8MnKvPQeEM5HG', '', NULL, NULL, 'usuario'),
(5, 'lucas', 'pelucas', 'karolpeachzen@gmail.com', '$2y$10$3bs3CKlClvRM/4YorCknhO5ACQdb.eP9fMmZfZS7BrUsygP7a9vTO', '', NULL, '1ef93e33bfaaeb9028c44e7020dd95880bd4b7c5a63d2a68ec04c4ebca077500', 'usuario');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `register`
--
ALTER TABLE `register`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
