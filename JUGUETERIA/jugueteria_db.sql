-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-02-2026 a las 17:15:17
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
-- Base de datos: `jugueteria_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Figuras de Acción', 'Superhéroes, villanos y personajes de películas.', '2026-02-16 15:33:43'),
(2, 'Juegos de Mesa', 'Diversión para toda la familia.', '2026-02-16 15:33:43'),
(3, 'Peluches', 'Suaves y adorables amigos.', '2026-02-16 15:33:43'),
(4, 'Construcción', 'Sets de LEGO y bloques para armar.', '2026-02-16 15:33:43'),
(5, 'Muñecas', 'Barbie, Polly Pocket y más.', '2026-02-16 15:33:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` enum('in','out','adjustment','sale') NOT NULL,
  `quantity` int(11) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `inventory_logs`
--

INSERT INTO `inventory_logs` (`id`, `product_id`, `user_id`, `type`, `quantity`, `note`, `created_at`) VALUES
(1, 1, 1, 'sale', 1, 'Venta Orden #1', '2026-02-16 15:38:19'),
(2, 2, 1, 'sale', 1, 'Venta Orden #1', '2026-02-16 15:38:19'),
(3, 3, 1, 'sale', 1, 'Venta Orden #1', '2026-02-16 15:38:19'),
(4, 5, 1, 'sale', 1, 'Venta Orden #1', '2026-02-16 15:38:19'),
(5, 4, 1, 'sale', 1, 'Venta Orden #1', '2026-02-16 15:38:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `created_at`) VALUES
(1, 1, 134.95, 'completed', '2026-02-16 15:38:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 29.99),
(2, 1, 2, 1, 19.99),
(3, 1, 3, 1, 24.99),
(4, 1, 5, 1, 49.99),
(5, 1, 4, 1, 9.99);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `stock`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Batman Figura Deluxe', 'Figura articulada de Batman con capa de tela y accesorios.', 29.99, 49, '3fe9c552a2584ee7bc11476525523868xl.jpg', 'active', '2026-02-16 15:33:43', '2026-02-16 15:51:50'),
(2, 1, 'Spider-Man Titan Hero', 'Figura de 30cm de Spider-Man, serie Titan Hero.', 19.99, 39, 'spiderman.jpg', 'active', '2026-02-16 15:33:43', '2026-02-16 15:38:19'),
(3, 2, 'Monopoly Clásico', 'El emocionante juego de operar propiedades inmobiliarias.', 24.99, 29, 'monopoly.jpg', 'active', '2026-02-16 15:33:43', '2026-02-16 15:38:19'),
(4, 2, 'Uno Cards', 'Juego de cartas clásico para toda la familia.', 9.99, 99, 'uno.jpg', 'active', '2026-02-16 15:33:43', '2026-02-16 15:38:19'),
(5, 3, 'Oso Teddy Gigante', 'Oso de peluche gigante de 1 metro, muy suave.', 49.99, 9, 'teddy.jpg', 'active', '2026-02-16 15:33:43', '2026-02-16 15:38:19'),
(6, 4, 'LEGO Star Wars X-Wing', 'Construye tu propio caza estelar X-Wing de la Alianza Rebelde.', 89.99, 15, 'lego_xwing.jpg', 'active', '2026-02-16 15:33:43', '2026-02-16 15:33:43'),
(7, 5, 'Barbie Fashionista', 'Muñeca Barbie con conjunto de moda actual.', 15.99, 60, 'barbie.jpg', 'active', '2026-02-16 15:33:43', '2026-02-16 15:33:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'client');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 2,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role_id`, `created_at`) VALUES
(1, 'Administrador', 'admin@jugueteria.com', '$2y$10$o56JAGNxAE7cnO7QQ9ybren9wAk4d25nxN/GHMzFVO7B99/nnekRS', 1, '2026-02-16 14:54:08');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `fk_logs_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_logs_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_details_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_details_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_categories` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
