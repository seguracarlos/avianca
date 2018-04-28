-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-04-2018 a las 19:40:13
-- Versión del servidor: 10.1.26-MariaDB
-- Versión de PHP: 7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `avianca`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `addresses`
--

CREATE TABLE `addresses` (
  `id_address` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `street` varchar(45) NOT NULL,
  `postalcode` int(5) NOT NULL,
  `number` varchar(10) NOT NULL,
  `interior` varchar(10) DEFAULT NULL,
  `neighborhood` int(5) NOT NULL,
  `state_id` int(5) NOT NULL,
  `district` int(5) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `ext` int(11) DEFAULT NULL,
  `url_map` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Direcciones de los clientes, proveedores o Host';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `addresses_users`
--

CREATE TABLE `addresses_users` (
  `id_address` int(11) NOT NULL,
  `id_users` int(11) NOT NULL,
  `street` varchar(45) NOT NULL,
  `postalcode` int(5) NOT NULL,
  `number` varchar(10) NOT NULL,
  `interior` varchar(10) DEFAULT NULL,
  `neighborhood` int(5) NOT NULL,
  `state_id` int(5) NOT NULL,
  `district` int(5) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `ext` int(11) DEFAULT NULL,
  `url_map` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `id_register_qr` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `name_article` varchar(100) DEFAULT NULL,
  `id_category` int(11) NOT NULL,
  `id_color` int(11) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model_serie` varchar(200) DEFAULT NULL,
  `clothes_size` varchar(45) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `reward` varchar(45) DEFAULT NULL,
  `imageone` longtext,
  `imagetwo` longtext,
  `image_name` varchar(400) DEFAULT NULL,
  `image_name_two` varchar(400) DEFAULT NULL,
  `registration_date` datetime DEFAULT NULL,
  `own_alien` int(11) DEFAULT NULL,
  `id_userfound` int(11) DEFAULT NULL,
  `asignated_to` varchar(400) NOT NULL,
  `event_articles` varchar(400) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `breeds_of_pet`
--

CREATE TABLE `breeds_of_pet` (
  `id` int(11) NOT NULL,
  `name_breed` varchar(100) DEFAULT NULL,
  `type_pet` int(11) DEFAULT NULL,
  `description` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `id_parent` int(11) DEFAULT NULL,
  `namecategory` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `color`
--

CREATE TABLE `color` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `company`
--

CREATE TABLE `company` (
  `id_company` int(11) NOT NULL,
  `id_users` int(11) NOT NULL,
  `name_company` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `company_description` varchar(200) DEFAULT NULL,
  `website` varchar(45) DEFAULT NULL,
  `company_isactive` int(11) DEFAULT NULL,
  `name_bank` varchar(100) DEFAULT NULL,
  `number_acount` varchar(100) DEFAULT NULL,
  `interbank_clabe` varchar(100) DEFAULT NULL,
  `sucursal_name` varchar(100) DEFAULT NULL,
  `record_date` date DEFAULT NULL,
  `business` varchar(100) DEFAULT NULL,
  `isprospect` int(11) DEFAULT NULL,
  `interestingin` varchar(100) DEFAULT NULL,
  `type_client` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `company_activity_sector`
--

CREATE TABLE `company_activity_sector` (
  `id` int(11) NOT NULL,
  `id_company` int(11) NOT NULL,
  `id_activity_sector` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registration_update` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `company_type_pet`
--

CREATE TABLE `company_type_pet` (
  `id` int(11) NOT NULL,
  `id_company` int(11) NOT NULL,
  `id_type_pet` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registration_update` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `district`
--

CREATE TABLE `district` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `state_id` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `history_status`
--

CREATE TABLE `history_status` (
  `id` int(11) NOT NULL,
  `id_status` int(11) NOT NULL,
  `id_articles` int(11) NOT NULL,
  `date_change` datetime DEFAULT NULL,
  `name_external` varchar(45) DEFAULT NULL,
  `comment` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `images_company`
--

CREATE TABLE `images_company` (
  `id` int(11) NOT NULL,
  `id_company` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `registration_update` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `location`
--

CREATE TABLE `location` (
  `id` int(11) NOT NULL,
  `id_articles` int(11) NOT NULL,
  `longitude` varchar(45) DEFAULT NULL,
  `latitude` varchar(45) DEFAULT NULL,
  `addres` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `location_pets`
--

CREATE TABLE `location_pets` (
  `id` int(11) NOT NULL,
  `id_pets` int(11) NOT NULL,
  `longitude` varchar(45) DEFAULT NULL,
  `latitude` varchar(45) DEFAULT NULL,
  `addres` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `neighborhood`
--

CREATE TABLE `neighborhood` (
  `id` int(5) NOT NULL,
  `colony` varchar(70) NOT NULL,
  `postal_code` int(6) NOT NULL,
  `district_id` int(5) NOT NULL,
  `state_id` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Colonias';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `id_client` int(11) DEFAULT NULL,
  `folio_order` varchar(45) DEFAULT NULL,
  `number_labels` varchar(45) DEFAULT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `id_register_qr` int(11) NOT NULL,
  `id_users` int(11) NOT NULL,
  `type_pet` varchar(50) DEFAULT NULL,
  `name_pet` varchar(100) DEFAULT NULL,
  `breed_pet` varchar(100) DEFAULT NULL,
  `color_pet` varchar(100) DEFAULT NULL,
  `size_pet` varchar(100) DEFAULT NULL,
  `description_pet` varchar(100) DEFAULT NULL,
  `age_pet` int(11) DEFAULT NULL,
  `name_vet` varchar(45) DEFAULT NULL,
  `phone_vet` int(11) NOT NULL,
  `microchip_pet` varchar(45) DEFAULT NULL,
  `img_pet` longtext,
  `image_name` varchar(400) NOT NULL,
  `reward` float NOT NULL,
  `registration_date` date NOT NULL,
  `birthday_pet` date NOT NULL,
  `weight` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pet_activity_sector`
--

CREATE TABLE `pet_activity_sector` (
  `id` int(11) NOT NULL,
  `sector` varchar(400) NOT NULL,
  `description` varchar(400) NOT NULL,
  `sector_order` int(11) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `registration_update` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pet_type`
--

CREATE TABLE `pet_type` (
  `id` int(11) NOT NULL,
  `type` varchar(400) NOT NULL,
  `order_pet` int(11) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `registration_update` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `push_notifications`
--

CREATE TABLE `push_notifications` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_article` int(11) DEFAULT NULL,
  `id_location_art` int(11) DEFAULT NULL,
  `id_pet` int(11) DEFAULT NULL,
  `id_location_pet` int(11) DEFAULT NULL,
  `title_notification` varchar(400) NOT NULL,
  `message_notificacion` varchar(400) NOT NULL,
  `contact_name` varchar(400) NOT NULL,
  `contact_email` varchar(400) NOT NULL,
  `contact_phone` int(11) NOT NULL,
  `contact_comment` varchar(400) NOT NULL,
  `type_notification` int(11) NOT NULL,
  `status_notification` int(11) NOT NULL DEFAULT '1',
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `isFound` int(11) NOT NULL DEFAULT '0',
  `warehouse` varchar(400) NOT NULL,
  `phone_warehouse` varchar(400) NOT NULL,
  `comment_warehouse` varchar(400) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `register_qr`
--

CREATE TABLE `register_qr` (
  `id` int(11) NOT NULL,
  `id_orders` int(11) NOT NULL,
  `id_status` int(11) NOT NULL,
  `foliocodeqr` varchar(45) DEFAULT NULL,
  `info_qr` varchar(500) DEFAULT NULL,
  `number_qr` varchar(45) DEFAULT NULL,
  `type_qr` int(11) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `returns`
--

CREATE TABLE `returns` (
  `id` int(11) NOT NULL,
  `id_articles` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `surname` varchar(200) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `comment` varchar(200) DEFAULT NULL,
  `warehouse` varchar(200) DEFAULT NULL,
  `phone_warehouse` varchar(45) DEFAULT NULL,
  `date_found` datetime DEFAULT NULL,
  `return_date` datetime DEFAULT NULL,
  `id_userfound` int(11) NOT NULL,
  `id_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `states_of_mexico`
--

CREATE TABLE `states_of_mexico` (
  `id` int(3) NOT NULL,
  `state` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Estados';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `token`
--

CREATE TABLE `token` (
  `id` int(11) NOT NULL,
  `id_users` int(11) NOT NULL,
  `token` varchar(300) DEFAULT NULL,
  `token_created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `password` varchar(300) DEFAULT NULL,
  `perfil` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_details`
--

CREATE TABLE `users_details` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `surname` varchar(200) DEFAULT NULL,
  `campus` varchar(200) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `addres` varchar(500) DEFAULT NULL,
  `image` longtext,
  `pin` varchar(300) DEFAULT NULL,
  `key_inventory` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_device`
--

CREATE TABLE `user_device` (
  `id` int(11) NOT NULL,
  `id_users` int(11) NOT NULL,
  `Key_device` varchar(255) DEFAULT NULL,
  `types` int(11) DEFAULT NULL,
  `date_registration` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id_address`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `addresses_users`
--
ALTER TABLE `addresses_users`
  ADD KEY `id_users` (`id_users`);

--
-- Indices de la tabla `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_articles_register_qr1_idx` (`id_register_qr`),
  ADD KEY `fk_articles_user1_idx` (`id_user`),
  ADD KEY `fk_articles_color1_idx` (`id_color`),
  ADD KEY `fk_articles_category1_idx` (`id_category`);

--
-- Indices de la tabla `breeds_of_pet`
--
ALTER TABLE `breeds_of_pet`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_category1_idx` (`id_parent`);

--
-- Indices de la tabla `color`
--
ALTER TABLE `color`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id_company`),
  ADD KEY `fk_company_users1_idx` (`id_users`);

--
-- Indices de la tabla `company_activity_sector`
--
ALTER TABLE `company_activity_sector`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_company` (`id_company`),
  ADD KEY `id_activity_sector` (`id_activity_sector`);

--
-- Indices de la tabla `company_type_pet`
--
ALTER TABLE `company_type_pet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_company` (`id_company`),
  ADD KEY `id_type_pet` (`id_type_pet`);

--
-- Indices de la tabla `district`
--
ALTER TABLE `district`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `fk_district_states_of_mexico1` (`state_id`);

--
-- Indices de la tabla `history_status`
--
ALTER TABLE `history_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_history_status_status1_idx` (`id_status`),
  ADD KEY `fk_history_status_articles1_idx` (`id_articles`);

--
-- Indices de la tabla `images_company`
--
ALTER TABLE `images_company`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_company` (`id_company`);

--
-- Indices de la tabla `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_location_articles1_idx` (`id_articles`);

--
-- Indices de la tabla `location_pets`
--
ALTER TABLE `location_pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_location_pets_pets1_idx` (`id_pets`);

--
-- Indices de la tabla `neighborhood`
--
ALTER TABLE `neighborhood`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `fk_neighborhood_district1` (`district_id`),
  ADD KEY `fk_neighborhood_states_of_mexico1` (`state_id`);

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pets_users1_idx` (`id_users`),
  ADD KEY `fk_pets_register_qr1_idx` (`id_register_qr`);

--
-- Indices de la tabla `pet_activity_sector`
--
ALTER TABLE `pet_activity_sector`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pet_type`
--
ALTER TABLE `pet_type`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `push_notifications`
--
ALTER TABLE `push_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `register_qr`
--
ALTER TABLE `register_qr`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_register_qr_status1_idx` (`id_status`),
  ADD KEY `fk_register_qr_pedidos1_idx` (`id_orders`);

--
-- Indices de la tabla `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_returns_articles1_idx` (`id_articles`);

--
-- Indices de la tabla `states_of_mexico`
--
ALTER TABLE `states_of_mexico`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_token_users1_idx` (`id_users`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users_details`
--
ALTER TABLE `users_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_details_user1_idx` (`id_user`);

--
-- Indices de la tabla `user_device`
--
ALTER TABLE `user_device`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_device_users1_idx` (`id_users`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id_address` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT de la tabla `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `breeds_of_pet`
--
ALTER TABLE `breeds_of_pet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=304;
--
-- AUTO_INCREMENT de la tabla `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;
--
-- AUTO_INCREMENT de la tabla `color`
--
ALTER TABLE `color`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT de la tabla `company`
--
ALTER TABLE `company`
  MODIFY `id_company` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT de la tabla `company_activity_sector`
--
ALTER TABLE `company_activity_sector`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;
--
-- AUTO_INCREMENT de la tabla `company_type_pet`
--
ALTER TABLE `company_type_pet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;
--
-- AUTO_INCREMENT de la tabla `district`
--
ALTER TABLE `district`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2458;
--
-- AUTO_INCREMENT de la tabla `history_status`
--
ALTER TABLE `history_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT de la tabla `images_company`
--
ALTER TABLE `images_company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT de la tabla `location`
--
ALTER TABLE `location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `location_pets`
--
ALTER TABLE `location_pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;
--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
--
-- AUTO_INCREMENT de la tabla `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT de la tabla `pet_activity_sector`
--
ALTER TABLE `pet_activity_sector`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT de la tabla `pet_type`
--
ALTER TABLE `pet_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `push_notifications`
--
ALTER TABLE `push_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `register_qr`
--
ALTER TABLE `register_qr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;
--
-- AUTO_INCREMENT de la tabla `returns`
--
ALTER TABLE `returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `token`
--
ALTER TABLE `token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;
--
-- AUTO_INCREMENT de la tabla `users_details`
--
ALTER TABLE `users_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;
--
-- AUTO_INCREMENT de la tabla `user_device`
--
ALTER TABLE `user_device`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `addresses_users`
--
ALTER TABLE `addresses_users`
  ADD CONSTRAINT `addresses_users_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `fk_articles_category1` FOREIGN KEY (`id_category`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_articles_color1` FOREIGN KEY (`id_color`) REFERENCES `color` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_articles_register_qr1` FOREIGN KEY (`id_register_qr`) REFERENCES `register_qr` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_articles_user1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `fk_category_category1` FOREIGN KEY (`id_parent`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `company`
--
ALTER TABLE `company`
  ADD CONSTRAINT `fk_company_users1` FOREIGN KEY (`id_users`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `history_status`
--
ALTER TABLE `history_status`
  ADD CONSTRAINT `fk_history_status_articles1` FOREIGN KEY (`id_articles`) REFERENCES `articles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_history_status_status1` FOREIGN KEY (`id_status`) REFERENCES `status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `location`
--
ALTER TABLE `location`
  ADD CONSTRAINT `fk_location_articles1` FOREIGN KEY (`id_articles`) REFERENCES `articles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `location_pets`
--
ALTER TABLE `location_pets`
  ADD CONSTRAINT `fk_location_pets_pets1` FOREIGN KEY (`id_pets`) REFERENCES `pets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `fk_pets_register_qr1` FOREIGN KEY (`id_register_qr`) REFERENCES `register_qr` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pets_users1` FOREIGN KEY (`id_users`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `register_qr`
--
ALTER TABLE `register_qr`
  ADD CONSTRAINT `fk_register_qr_pedidos1` FOREIGN KEY (`id_orders`) REFERENCES `orders` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_register_qr_status1` FOREIGN KEY (`id_status`) REFERENCES `status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `returns`
--
ALTER TABLE `returns`
  ADD CONSTRAINT `fk_returns_articles1` FOREIGN KEY (`id_articles`) REFERENCES `articles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `fk_token_users1` FOREIGN KEY (`id_users`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `users_details`
--
ALTER TABLE `users_details`
  ADD CONSTRAINT `fk_users_details_user1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `user_device`
--
ALTER TABLE `user_device`
  ADD CONSTRAINT `fk_user_device_users1` FOREIGN KEY (`id_users`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
