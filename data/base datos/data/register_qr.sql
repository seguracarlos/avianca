-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-04-2018 a las 06:13:24
-- Versión del servidor: 10.1.21-MariaDB
-- Versión de PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `recuperalo`
--

--
-- Volcado de datos para la tabla `register_qr`
--

INSERT INTO `register_qr` (`id`, `id_orders`, `id_status`, `foliocodeqr`, `info_qr`, `number_qr`, `type_qr`, `registration_date`) VALUES
(1, 1, 7, 'ABCD0001', 'http://www.pegalinas.com/', '1', 1, '2018-03-27 22:46:22'),
(2, 1, 3, 'ABCD0002', 'http://www.pegalinas.com/', '2', 1, '2018-03-27 22:46:22'),
(3, 1, 7, 'ABCD0003', 'http://www.pegalinas.com/', '3', 1, '2018-03-27 22:46:22'),
(4, 1, 2, 'ABCD0004', 'http://www.pegalinas.com/', '4', 1, '2018-03-27 22:46:22'),
(5, 1, 5, 'ABCD0005', 'http://www.pegalinas.com/', '5', 1, '2018-03-27 22:46:22'),
(6, 2, 2, 'ABCD0006', 'http://www.pegalinas.com/', '6', 1, '2018-03-27 22:46:22'),
(7, 2, 1, 'ABCD0007', 'http://www.pegalinas.com/', '7', 1, '2018-03-27 22:46:22'),
(8, 2, 1, 'ABCD0008', 'http://www.pegalinas.com/', '8', 1, '2018-03-27 22:46:22'),
(9, 2, 1, 'ABCD0009', 'http://www.pegalinas.com/', '9', 1, '2018-03-27 22:46:22'),
(10, 2, 1, 'ABCD0010', 'http://www.pegalinas.com/', '10', 1, '2018-03-27 22:46:22'),
(11, 3, 1, 'rmx_58ebbee8ec595', 'https://desarrollo.recupera.net/articles/codeqr/rmx_58ebbee8ec595', '1', 2, '2018-03-27 22:46:22'),
(12, 3, 1, 'rmx_58ebbeecb484f', 'https://desarrollo.recupera.net/articles/codeqr/rmx_58ebbeecb484f', '1', 2, '2018-03-27 22:46:22'),
(13, 9, 7, '598c6f28', 'https://desarrollo.recupera.net/public/articles/codeqr/598c6f28', '1', 2, '2018-03-27 22:46:22'),
(14, 9, 2, 'a5a24588', 'https://desarrollo.recupera.net/public/articles/codeqr/a5a24588', '2', 2, '2018-03-27 22:46:22'),
(15, 9, 1, '19dc5ef6', 'https://desarrollo.recupera.net/public/articles/codeqr/19dc5ef6', '3', 2, '2018-03-27 22:46:22'),
(16, 10, 2, '7291c806', 'https://desarrollo.recupera.net/public/articles/codeqr/7291c806', '1', 2, '2018-03-27 22:46:22'),
(17, 10, 1, '0e2eeae9', 'https://desarrollo.recupera.net/public/articles/codeqr/0e2eeae9', '2', 2, '2018-03-27 22:46:22'),
(18, 10, 1, 'ee6e4d40', 'https://desarrollo.recupera.net/public/articles/codeqr/ee6e4d40', '3', 2, '2018-03-27 22:46:22'),
(19, 10, 1, '4c8b356b', 'https://desarrollo.recupera.net/public/articles/codeqr/4c8b356b', '4', 2, '2018-03-27 22:46:22'),
(20, 10, 5, '6fb45740', 'https://desarrollo.recupera.net/public/articles/codeqr/6fb45740', '5', 2, '2018-03-27 22:46:22'),
(21, 10, 1, '1974694a', 'https://desarrollo.recupera.net/public/articles/codeqr/1974694a', '6', 2, '2018-03-27 22:46:22'),
(22, 11, 1, '7f8d652a', 'https://desarrollo.recupera.net/public/articles/codeqr/7f8d652a', '1', 2, '2018-03-27 22:46:22'),
(23, 11, 1, '490d227d', 'https://desarrollo.recupera.net/public/articles/codeqr/490d227d', '2', 2, '2018-03-27 22:46:22'),
(24, 11, 4, '48371777', 'https://desarrollo.recupera.net/public/articles/codeqr/48371777', '3', 2, '2018-03-27 22:46:22'),
(25, 11, 5, '983d5fb6', 'https://desarrollo.recupera.net/public/articles/codeqr/983d5fb6', '4', 2, '2018-03-27 22:46:22'),
(26, 11, 1, '7ba856eb', 'https://desarrollo.recupera.net/public/articles/codeqr/7ba856eb', '5', 0, '2018-03-27 22:46:22'),
(27, 11, 1, '06720b13', 'https://desarrollo.recupera.net/public/articles/codeqr/06720b13', '6', 0, '2018-03-27 22:46:22'),
(28, 12, 1, 'ceb9da9c', 'https://desarrollo.recupera.net/public/articles/codeqr/ceb9da9c', '1', 0, '2018-03-27 22:46:22'),
(29, 12, 1, '81000e83', 'https://desarrollo.recupera.net/public/articles/codeqr/81000e83', '2', 0, '2018-03-27 22:46:22'),
(30, 12, 1, '09bf5235', 'https://desarrollo.recupera.net/public/articles/codeqr/09bf5235', '3', 0, '2018-03-27 22:46:22'),
(31, 13, 1, 'd5bd5ce1', 'https://desarrollo.recupera.net/public/articles/codeqr/d5bd5ce1', '1', 0, '2018-03-27 22:46:22'),
(32, 13, 1, '0479c2a9', 'https://desarrollo.recupera.net/public/articles/codeqr/0479c2a9', '2', 0, '2018-03-27 22:46:22'),
(33, 13, 1, '8ba5707e', 'https://desarrollo.recupera.net/public/articles/codeqr/8ba5707e', '3', 0, '2018-03-27 22:46:22'),
(34, 14, 1, '0cbf5519', 'https://desarrollo.recupera.net/public/articles/codeqr/0cbf5519', '1', 0, '2018-03-27 22:46:22'),
(35, 14, 1, '1bc58d29', 'https://desarrollo.recupera.net/public/articles/codeqr/1bc58d29', '2', 0, '2018-03-27 22:46:22'),
(36, 14, 1, 'c2547f21', 'https://desarrollo.recupera.net/public/articles/codeqr/c2547f21', '3', 0, '2018-03-27 22:46:22'),
(37, 15, 1, 'b5fda238', 'https://desarrollo.recupera.net/public/articles/codeqr/b5fda238', '1', 0, '2018-03-27 22:46:22'),
(38, 15, 1, 'b3240e1c', 'https://desarrollo.recupera.net/public/articles/codeqr/b3240e1c', '2', 0, '2018-03-27 22:46:22'),
(39, 15, 1, '6ea22d50', 'https://desarrollo.recupera.net/public/articles/codeqr/6ea22d50', '3', 0, '2018-03-27 22:46:22'),
(40, 16, 1, '2b957de3', 'https://desarrollo.recupera.net/public/articles/codeqr/2b957de3', '1', 0, '2018-03-27 22:46:22'),
(41, 17, 1, '31b77ef9', 'https://desarrollo.recupera.net/public/articles/codeqr/31b77ef9', '1', 0, '2018-03-27 22:46:22'),
(42, 18, 1, '23A52020', 'http://localhost/articles/codeqr/23A52020', '1', 0, '2018-03-27 22:46:22'),
(43, 19, 1, '37944F46', 'http://localhost/articles/codeqr/37944F46', '1', 0, '2018-03-27 22:46:22'),
(44, 20, 1, 'FA4CCA58', 'http://localhost/articles/codeqr/FA4CCA58', '1', 0, '2018-03-27 22:46:22'),
(45, 21, 1, '77392238', 'http://localhost/articles/codeqr/77392238', '1', 0, '2018-03-27 22:46:22'),
(46, 22, 1, '0746CE0D', 'http://localhost/articles/codeqr/0746CE0D', '1', 0, '2018-03-27 22:46:22'),
(47, 23, 1, '014B6E58', 'http://localhost/articles/codeqr/014B6E58', '1', 0, '2018-03-27 22:46:22'),
(48, 24, 1, 'A0B88D5A', 'http://localhost/articles/codeqr/A0B88D5A', '1', 0, '2018-03-27 22:46:22'),
(49, 25, 1, '24371D50', 'http://localhost/articles/codeqr/24371D50', '1', 0, '2018-03-27 22:46:22'),
(50, 26, 1, 'EEAF7A32', 'http://localhost/articles/codeqr/EEAF7A32', '1', 0, '2018-03-27 22:46:22'),
(51, 27, 1, '6ABA5F6D', 'http://localhost/articles/codeqr/6ABA5F6D', '1', 0, '2018-03-27 22:46:22'),
(52, 28, 1, '938DF74A', 'http://localhost/articles/codeqr/938DF74A', '1', 0, '2018-03-27 22:46:22'),
(53, 29, 1, '9526510A', 'http://localhostPROYECTOSJL2016/ProyectosZF2/PegalinasLive/RepositorioGit/recuperalo/public/articles/codeqr/9526510A', '1', 0, '2018-03-27 22:46:22'),
(54, 30, 1, '0F9B7866', 'http://localhostPROYECTOSJL2016/ProyectosZF2/PegalinasLive/RepositorioGit/recuperalo/public/articles/codeqr/0F9B7866', '1', 0, '2018-03-27 22:46:22'),
(55, 31, 1, 'E95C71AF', 'http://localhost/public/articles/codeqr/E95C71AF', '1', 0, '2018-03-27 22:46:22'),
(56, 32, 1, 'BCCC49BE', 'https://recupera.net/articles/codeqr/BCCC49BE', '1', 0, '2018-03-27 22:46:22'),
(57, 33, 1, 'F4663584', 'https://recupera.net/articles/codeqr/F4663584', '1', 0, '2018-03-27 22:46:22'),
(58, 34, 1, '29D794DA', 'https://recupera.net/articles/codeqr/29D794DA', '1', 0, '2018-03-27 22:46:22'),
(59, 35, 1, '15BDAA30', 'https://recupera.net/articles/codeqr/15BDAA30', '1', 0, '2018-03-27 22:46:22'),
(60, 36, 1, '6CB25492', 'https://recupera.net/articles/codeqr/6CB25492', '1', 0, '2018-03-27 22:46:22'),
(61, 37, 1, 'BA74CE6D', 'https://recupera.net/articles/codeqr/BA74CE6D', '1', 0, '2018-03-27 22:46:22'),
(62, 38, 1, '45BF982B', 'https://recupera.net/articles/codeqr/45BF982B', '1', 0, '2018-03-27 22:46:22'),
(63, 39, 1, '879FD48C', 'http://192.168.1.64/articles/codeqr/879FD48C', '1', 0, '2018-03-27 22:46:22'),
(64, 39, 1, '5BF7C1B8', 'http://192.168.1.64/articles/codeqr/5BF7C1B8', '2', 0, '2018-03-27 22:46:22'),
(65, 39, 1, '5937C01A', 'http://192.168.1.64/articles/codeqr/5937C01A', '3', 0, '2018-03-27 22:46:22'),
(66, 39, 1, '60BF5652', 'http://192.168.1.64/articles/codeqr/60BF5652', '4', 0, '2018-03-27 22:46:22'),
(67, 39, 1, '4086C586', 'http://192.168.1.64/articles/codeqr/4086C586', '5', 0, '2018-03-27 22:46:22'),
(68, 39, 1, '4CD28ECF', 'http://192.168.1.64/articles/codeqr/4CD28ECF', '6', 0, '2018-03-27 22:46:22'),
(69, 40, 1, 'CBD9806F', 'http://192.168.1.64/articles/codeqr/CBD9806F', '1', 0, '2018-03-27 22:46:22'),
(70, 40, 1, 'CC25B980', 'http://192.168.1.64/articles/codeqr/CC25B980', '2', 0, '2018-03-27 22:46:22'),
(71, 41, 1, 'E3C0D212', 'http://192.168.1.64/articles/codeqr/E3C0D212', '1', 0, '2018-03-27 22:46:22'),
(72, 41, 1, '834CA7CE', 'http://192.168.1.64/articles/codeqr/834CA7CE', '2', 0, '2018-03-27 22:46:22'),
(73, 42, 1, '77AC1081', 'http://192.168.1.64/articles/codeqr/77AC1081', '1', 1, '2018-03-27 22:46:22'),
(74, 42, 1, 'D3E7B648', 'http://192.168.1.64/articles/codeqr/D3E7B648', '2', 1, '2018-03-27 22:46:22'),
(75, 43, 1, 'AF893B7B', 'http://192.168.1.64/articles/codeqr/AF893B7B', '1', 2, '2018-03-27 22:46:22'),
(76, 43, 1, 'BD8B4E71', 'http://192.168.1.64/articles/codeqr/BD8B4E71', '2', 2, '2018-03-27 22:46:22'),
(77, 44, 1, '10F7F5AD', 'http://localhost/articles/codeqr/10F7F5AD', '1', 2, '2018-03-27 22:46:22'),
(78, 44, 1, '7D54D099', 'http://localhost/articles/codeqr/7D54D099', '2', 2, '2018-03-27 22:46:22'),
(79, 44, 1, '6A263F0C', 'http://localhost/articles/codeqr/6A263F0C', '3', 2, '2018-03-27 22:46:22'),
(80, 44, 1, '3A7F20A0', 'http://localhost/articles/codeqr/3A7F20A0', '4', 2, '2018-03-27 22:46:22'),
(81, 44, 1, '98D3C09B', 'http://localhost/articles/codeqr/98D3C09B', '5', 2, '2018-03-27 22:46:22'),
(82, 44, 1, 'D50CF918', 'http://localhost/articles/codeqr/D50CF918', '6', 2, '2018-03-27 22:46:22'),
(83, 44, 1, '4468E81F', 'http://localhost/articles/codeqr/4468E81F', '7', 2, '2018-03-27 22:46:22'),
(84, 44, 1, '75782E00', 'http://localhost/articles/codeqr/75782E00', '8', 2, '2018-03-27 22:46:22'),
(85, 44, 1, '31E121D3', 'http://localhost/articles/codeqr/31E121D3', '9', 2, '2018-03-27 22:46:22'),
(86, 44, 1, '956AE68F', 'http://localhost/articles/codeqr/956AE68F', '10', 2, '2018-03-27 22:46:22'),
(87, 45, 1, 'C855037F', 'http://localhost/pets/codeqr/public/C855037F', '1', 2, '2018-03-27 22:46:22'),
(88, 45, 1, '0EDC3125', 'http://localhost/pets/codeqr/public/0EDC3125', '2', 2, '2018-03-27 22:46:22'),
(89, 45, 1, 'F394B140', 'http://localhost/pets/codeqr/public/F394B140', '3', 2, '2018-03-27 22:46:22'),
(90, 46, 1, 'F7EF0838', 'http://localhost/articles/codeqr/public/F7EF0838', '1', 1, '2018-03-27 22:46:22'),
(91, 46, 1, 'E0A46C07', 'http://localhost/articles/codeqr/public/E0A46C07', '2', 1, '2018-03-27 22:46:22'),
(92, 46, 1, 'A4684867', 'http://localhost/articles/codeqr/public/A4684867', '3', 1, '2018-03-27 22:46:22'),
(93, 47, 1, 'BB8A909C', 'http://localhost/articles/codeqr/BB8A909C', '1', 1, '2018-03-27 22:46:22'),
(94, 48, 1, '2D3F9847', 'http://localhost/articles/codeqr/public/2D3F9847', '1', 1, '2018-03-27 22:46:22'),
(95, 48, 1, '6B98E1D2', 'http://localhost/articles/codeqr/public/6B98E1D2', '2', 1, '2018-03-27 22:46:22'),
(96, 48, 1, '0EEA9766', 'http://localhost/articles/codeqr/public/0EEA9766', '3', 1, '2018-03-27 22:46:22'),
(97, 49, 1, 'D24567C2', 'http://localhost/articles/codeqr/D24567C2', '1', 1, '2018-03-27 22:46:22'),
(98, 49, 1, '1E823CF9', 'http://localhost/articles/codeqr/1E823CF9', '2', 1, '2018-03-27 22:46:22'),
(99, 50, 1, '60CAE2E7', 'http://localhost/pets/codeqr/60CAE2E7', '1', 2, '2018-03-27 22:46:22'),
(100, 50, 1, '5A8E982E', 'http://localhost/pets/codeqr/5A8E982E', '2', 2, '2018-03-27 22:46:22'),
(101, 51, 1, 'FAFEF9CB', 'http://localhost/IOF/PROYECTOSJL2016/ProyectosZF2/PegalinasLive/RepositorioGit/recupera.net/public/articles/codeqr/FAFEF9CB', '1', 1, '2018-03-27 22:46:22'),
(102, 51, 1, 'CC79045B', 'http://localhost/IOF/PROYECTOSJL2016/ProyectosZF2/PegalinasLive/RepositorioGit/recupera.net/public/articles/codeqr/CC79045B', '2', 1, '2018-03-27 22:46:22'),
(103, 52, 1, 'A0005844', 'http://localhost/IOF/PROYECTOSJL2016/ProyectosZF2/PegalinasLive/RepositorioGit/recupera.net/public/articles/codeqr/A0005844', '1', 1, '2018-03-27 22:46:55'),
(104, 53, 1, 'BCEAF636', 'http://localhost/IOF/PROYECTOSJL2016/ProyectosZF2/PegalinasLive/RepositorioGit/recupera.net/public/articles/codeqr/BCEAF636', '1', 1, '2018-03-27 22:47:15'),
(105, 54, 1, '743B338D', 'http://localhost/IOF/PROYECTOSJL2016/ProyectosZF2/PegalinasLive/RepositorioGit/recupera.net/public/articles/codeqr/743B338D', '1', 1, '2018-03-27 22:49:17'),
(106, 54, 1, '77F0B72C', 'http://localhost/IOF/PROYECTOSJL2016/ProyectosZF2/PegalinasLive/RepositorioGit/recupera.net/public/articles/codeqr/77F0B72C', '2', 1, '2018-03-27 22:49:18'),
(107, 54, 1, '6B84820F', 'http://localhost/IOF/PROYECTOSJL2016/ProyectosZF2/PegalinasLive/RepositorioGit/recupera.net/public/articles/codeqr/6B84820F', '3', 1, '2018-03-27 22:49:19'),
(108, 55, 1, '988AF956', 'http://localhost/IOF/PROYECTOSJL2016/ProyectosZF2/PegalinasLive/RepositorioGit/recupera.net/public/pets/codeqr/988AF956', '1', 2, '2018-03-27 23:00:02'),
(109, 55, 1, '96208178', 'http://localhost/IOF/PROYECTOSJL2016/ProyectosZF2/PegalinasLive/RepositorioGit/recupera.net/public/pets/codeqr/96208178', '2', 2, '2018-03-27 23:00:03'),
(110, 55, 1, 'F032D8DB', 'http://localhost/IOF/PROYECTOSJL2016/ProyectosZF2/PegalinasLive/RepositorioGit/recupera.net/public/pets/codeqr/F032D8DB', '3', 2, '2018-03-27 23:00:04');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
