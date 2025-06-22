-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 16-06-2025 a las 06:33:59
-- Versión del servidor: 8.3.0
-- Versión de PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `penka`
--
CREATE DATABASE IF NOT EXISTS `penka` DEFAULT CHARACTER SET latin1 COLLATE latin1_spanish_ci;
USE `penka`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunas`
--

DROP TABLE IF EXISTS `comunas`;
CREATE TABLE IF NOT EXISTS `comunas` (
  `idcomunas` int NOT NULL AUTO_INCREMENT,
  `comuna` varchar(45) CHARACTER SET latin1 COLLATE latin1_spanish_ci DEFAULT NULL,
  `estado` int DEFAULT NULL,
  `idprovincias` int NOT NULL,
  PRIMARY KEY (`idcomunas`,`idprovincias`),
  KEY `fk_comunas_provincias1_idx` (`idprovincias`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `comunas`
--

INSERT INTO `comunas` (`idcomunas`, `comuna`, `estado`, `idprovincias`) VALUES
(1, 'La Serena', 1, 1),
(2, 'Coquimbo', 1, 1),
(3, 'Andacollo', 1, 1),
(4, 'La Higuera', 1, 1),
(5, 'Paiguano', 1, 1),
(6, 'Vicuña', 1, 1),
(7, 'Illapel', 1, 3),
(8, 'Canela', 1, 3),
(9, 'Los Vilos', 1, 3),
(10, 'Salamanca', 1, 3),
(11, 'Ovalle', 1, 2),
(12, 'Combarbalá', 1, 2),
(13, 'Monte Patria', 1, 2),
(14, 'Punitaqui', 1, 2),
(15, 'Río Hurtado', 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeria`
--

DROP TABLE IF EXISTS `galeria`;
CREATE TABLE IF NOT EXISTS `galeria` (
  `idgaleria` int NOT NULL AUTO_INCREMENT,
  `foto` varchar(45) CHARACTER SET latin1 COLLATE latin1_spanish_ci DEFAULT NULL,
  `estado` int DEFAULT NULL,
  `principal` int DEFAULT NULL,
  `idpropiedades` int NOT NULL,
  PRIMARY KEY (`idgaleria`,`idpropiedades`),
  KEY `fk_galeria_propiedades1_idx` (`idpropiedades`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propiedades`
--

DROP TABLE IF EXISTS `propiedades`;
CREATE TABLE IF NOT EXISTS `propiedades` (
  `idpropiedades` int NOT NULL AUTO_INCREMENT,
  `titulopropiedad` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `descripcion` text CHARACTER SET latin1 COLLATE latin1_spanish_ci,
  `cant_banos` int DEFAULT NULL,
  `cant_domitorios` int DEFAULT NULL,
  `area_total` int DEFAULT NULL,
  `area_construida` int DEFAULT NULL,
  `precio_pesos` int DEFAULT NULL,
  `precio_uf` int DEFAULT NULL,
  `fecha_publicacion` date DEFAULT NULL,
  `estado` int DEFAULT NULL,
  `idtipo_propiedad` int NOT NULL,
  `sectores_idsectores` int NOT NULL,
  `bodega` int DEFAULT NULL,
  `estacionamiento` int DEFAULT NULL,
  `logia` int DEFAULT NULL,
  `cocinaamoblada` int DEFAULT NULL,
  `antejardin` int DEFAULT NULL,
  `patiotrasero` int DEFAULT NULL,
  `piscina` int DEFAULT NULL,
  PRIMARY KEY (`idpropiedades`),
  KEY `fk_propiedades_tipo_propiedad1_idx` (`idtipo_propiedad`),
  KEY `fk_propiedades_sectores1_idx` (`sectores_idsectores`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provincias`
--

DROP TABLE IF EXISTS `provincias`;
CREATE TABLE IF NOT EXISTS `provincias` (
  `idprovincias` int NOT NULL AUTO_INCREMENT,
  `provincia` varchar(45) CHARACTER SET latin1 COLLATE latin1_spanish_ci DEFAULT NULL,
  `estado` int DEFAULT NULL,
  `idregiones` int NOT NULL,
  PRIMARY KEY (`idprovincias`,`idregiones`),
  KEY `fk_provincias_regiones_idx` (`idregiones`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `provincias`
--

INSERT INTO `provincias` (`idprovincias`, `provincia`, `estado`, `idregiones`) VALUES
(1, 'Elqui', 1, 1),
(2, 'Limarí', 1, 1),
(3, 'Choapa', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `regiones`
--

DROP TABLE IF EXISTS `regiones`;
CREATE TABLE IF NOT EXISTS `regiones` (
  `idregiones` int NOT NULL AUTO_INCREMENT,
  `region` varchar(45) CHARACTER SET latin1 COLLATE latin1_spanish_ci DEFAULT NULL,
  `estado` int DEFAULT NULL,
  PRIMARY KEY (`idregiones`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `regiones`
--

INSERT INTO `regiones` (`idregiones`, `region`, `estado`) VALUES
(1, 'Coquimbo', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sectores`
--

DROP TABLE IF EXISTS `sectores`;
CREATE TABLE IF NOT EXISTS `sectores` (
  `idsectores` int NOT NULL AUTO_INCREMENT,
  `sector` varchar(45) CHARACTER SET latin1 COLLATE latin1_spanish_ci DEFAULT NULL,
  `estado` int DEFAULT NULL,
  `idcomunas` int NOT NULL,
  PRIMARY KEY (`idsectores`,`idcomunas`),
  KEY `fk_sectores_comunas1_idx` (`idcomunas`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `sectores`
--

INSERT INTO `sectores` (`idsectores`, `sector`, `estado`, `idcomunas`) VALUES
(1, 'Balmaceda', 1, 1),
(2, '4 Esquinas', 1, 1),
(6, 'Bosque San Carlos', 1, 2),
(9, 'Tierras Blancas', 1, 2),
(10, 'Sindempart', 1, 2),
(11, 'La Cantera', 1, 2),
(12, 'La Florida', 1, 1),
(13, 'Las Compañias', 1, 1),
(14, 'Centro de La Serena', 1, 1),
(15, 'La Pampa', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_propiedad`
--

DROP TABLE IF EXISTS `tipo_propiedad`;
CREATE TABLE IF NOT EXISTS `tipo_propiedad` (
  `idtipo_propiedad` int NOT NULL AUTO_INCREMENT,
  `tipo` varchar(45) CHARACTER SET latin1 COLLATE latin1_spanish_ci DEFAULT NULL,
  `estado` int DEFAULT NULL,
  PRIMARY KEY (`idtipo_propiedad`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `tipo_propiedad`
--

INSERT INTO `tipo_propiedad` (`idtipo_propiedad`, `tipo`, `estado`) VALUES
(1, 'Casa', 1),
(2, 'Departamento', 1),
(3, 'Terreno', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rut` varchar(12) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `nombres` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `ap_paterno` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `ap_materno` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `usuario` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `clave` varchar(250) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `sexo` varchar(4) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `estado` int NOT NULL,
  `npropiedad` int NOT NULL,
  `telefono` varchar(20) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `fechanacimiento` date NOT NULL,
  `tipo` int NOT NULL,
  `certificado` varchar(100) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `rut`, `nombres`, `ap_paterno`, `ap_materno`, `usuario`, `clave`, `sexo`, `estado`, `npropiedad`, `telefono`, `fechanacimiento`, `tipo`, `certificado`) VALUES
(1, '21202091-5', 'Bastian Alejandro ', 'Larraguibel', 'Ortiz', 'basty7@hotmail.cl', '$2y$10$btH.SSBNjEWDfZpnS83hzO99NNbGdmCCLycSlKy4PS5HyO0g9GLYm', '0', 1, 1200, '+56979073663', '2025-04-28', 3, ''),
(3, '21202091-5', 'Bastian Alejandro ', 'Larraguibel ', 'Ortiz', 'homer16530@hotmail.com', '$2a$12$ynh30dIr3RUjzGsAF8nUb.8J0T/i3w6qJ4BhlbhwcsjLnP6YbMZ7u', '0', 1, 0, '', '0000-00-00', 3, ''),
(24, '21.202.091-5', 'admin', 'admin', 'admin', 'admin@admin.cl', '$2y$10$vMYauqgfztOM8sUcrtLMYe.oydEjS03NKLlo7r61FstJHEUOyP84W', 'M', 0, 123, '123', '2025-05-02', 3, ''),
(43, '21.202.091-5', 'Holas123@', 'Holas123@', 'Holas123@', 'Holas123@sad.cl', '$2y$10$EMWmPaVWtZblNsvaiuwjN.H8jq2lh1014m21BZFhH2mMe2kH0DfVK', 'M', 0, 2131, '+56989083553', '2025-04-28', 2, ''),
(35, '21.202.091-5', 'Rosa Maria', 'Hola1234@', 'Hola1234@', 'Hola1234@jc.cl', 'Hola1234@', 'M', 0, 12312, '+56912312311', '2025-05-06', 2, ''),
(36, '21.202.091-5', 'Hola1234@', 'Hola1234@', 'Hola1234@', 'Hola1234@prueba.cl', '$2y$10$Q/vxFDEkEO9NkRvTiC.uq.ics0QioHAZQnKfOlzwmhaw49DzZ9NRi', 'M', 0, 2147483647, '+56912334415', '2025-04-28', 2, ''),
(29, '21.202.091-5', 'Rosas Maria', 'asd', 'asd', 'basty1237@hotmail.cl', 'Hola123@', 'M', 0, 123, '+56912312313', '2025-04-28', 2, ''),
(32, '21.202.091-5', 'Hola123@', 'Hola123@', 'Hola123@', 'Hola123@sd.cl', 'Hola123@', '', 0, 1212, '+56912121212', '2025-05-12', 2, ''),
(39, '21.202.091-5', '', '', '', '', '$2y$10$/1EpgFeFmybLB8UxgqKNKeJHAB6DEewTZBzsS19BCJ04iMsuvEUyO', '', 3, 0, '', '0000-00-00', 0, ''),
(40, '21.202.091-5', 'Hola123@', 'Hola123@', 'Hola123@', 'Hola123@Hola123.cl', '$2y$10$bvK/QU2Uk7Zjq1H3RAF85uJ4iqkopF1jS5PxFn0WJz.gpgS9Pn4w2', 'M', 1, 123, '+56979073663', '2025-05-06', 3, ''),
(41, '21.202.091-5', 'aasd', 'asd', 'asd', 'basty7@hotmail.cl', '$2y$10$BHNlVCPQKip853RxFV3v1.aZLr2AhKEfH0fzPYNlVq2DjEO6he2nS', 'M', 1, 123, '+56979073663', '2025-04-28', 3, ''),
(42, '21.202.091-5', 'Rosa Maria', 'Hola123@', 'Hola123@', 'Hola123@asd.cl', '$2y$10$VYETMUN3ESgXD3wHe8vo6.gJyqeRV.4vjOZr13VMtAgDuFPuoLVQm', 'M', 0, 123, '+56979073663', '2025-03-31', 3, ''),
(44, '13.649.080-K', 'Rosa Maria', 'holaz', 'holaz', 'holaz@asd.cl', '$2y$10$heNtAVzTLMsLliqWEz.dQ.kCdkoKw8f64NH76y8t80WargSSebu0y', 'M', 1, 2147483647, '+56979073663', '2025-04-28', 3, ''),
(45, '21.202.091-5', 'Rosa Maria', 'asd', 'asd', 'asd@hot.cl', '$2y$10$eu.oK3VSsRCHlw1/7yJKa.eQO7PsjlMfwYI5NQVb4EtdieUtG89hy', 'M', 0, 123, '+56979073663', '2025-04-28', 2, ''),
(48, '21.202.091-5', 'Rosa Maria', 'asda', 'asdasd', 'asd@asd.cl', '$2y$10$qTXv0nH.QwQjXtEGsKHIDuAbt0Z6gKzIPLkuveBeHw3wa4wCKSXhq', 'M', 0, 0, '+56979073663', '2025-04-28', 1, 'Certamen 2.pdf'),
(49, '21.202.091-5', 'asdas', 'asd', 'asd', 'hoj@hoj.cl', '$2y$10$lf.7qAM2Mig7jfra/nMC1eIZqT73MuCqx09OODv/jVy5iL61qvPqW', '0', 0, 0, '+56979073663', '2025-04-28', 1, 'Certamen 2.pdf'),
(50, '21.202.091-5', 'asdas', 'asdad', 'asda', 'asd@awseq.cl', '$2y$10$zFVaAb.wuHec/ZC8KTk20OUdKaOcTFsm211tGEcw817V0Kqzu/HUe', 'M', 0, 0, '+56979073663', '2025-04-28', 1, 'Certamen 2.pdf'),
(51, '21.202.091-5', 'asdad', 'asdad', 'qwe', 'qwe@qwe.cl', '$2y$10$mKAk434AWYM4rhOJoe6KJO4OpudB/yhQzsjoOSfU9oxXagUBV/LYu', 'M', 1, 0, '+56979073662', '2025-04-28', 1, 'Certamen 2.pdf'),
(52, '21.202.091-5', 'hola@', 'hola@', 'hola@', 'hola@hoa.cl', '$2y$10$naXrc/dS6dNDcI4.0wwhvOy3AcV7X72I3O5xekHLj17oRHCqPscyq', 'M', 1, 0, '+56979073663', '2025-04-28', 3, ''),
(61, '21.202.091-5', 'Prueba3', 'Prueba3', 'Prueba3', 'prueba3@pnk.cl', '$2y$10$nVpsMPRtxUnhF.oCT/xvfOx2Xh9EKn624fk7M5PxYc6BgsJQPrKQC', 'M', 1, 12312, '+56927075434', '2025-05-26', 2, ''),
(59, '21.202.091-5', 'Prueba1', 'Prueba1', 'Prueba1', 'basty46@gmail.com', '$2y$10$fBgsDUCh6t5Zd/uInDqqbuQizsxOUTzuOyNmtFC6AuMgJvGTQRKbm', 'M', 1, 44, '+56927075434', '2025-06-03', 2, ''),
(60, '21.202.091-5', 'Prueba2', 'Prueba2', 'Prueba2', 'prueba2@pnk.cl', '$2y$10$fcUaxpzH8rqNLXVSrfRHR.7b06UKtpNJa8uwBcQ6vifzemUNlXmZa', '0', 1, 12312, '+56927075434', '2025-05-26', 2, '');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comunas`
--
ALTER TABLE `comunas`
  ADD CONSTRAINT `fk_comunas_provincias1` FOREIGN KEY (`idprovincias`) REFERENCES `provincias` (`idprovincias`);

--
-- Filtros para la tabla `galeria`
--
ALTER TABLE `galeria`
  ADD CONSTRAINT `fk_galeria_propiedades1` FOREIGN KEY (`idpropiedades`) REFERENCES `propiedades` (`idpropiedades`);

--
-- Filtros para la tabla `propiedades`
--
ALTER TABLE `propiedades`
  ADD CONSTRAINT `fk_propiedades_sectores1` FOREIGN KEY (`sectores_idsectores`) REFERENCES `sectores` (`idsectores`),
  ADD CONSTRAINT `fk_propiedades_tipo_propiedad1` FOREIGN KEY (`idtipo_propiedad`) REFERENCES `tipo_propiedad` (`idtipo_propiedad`);

--
-- Filtros para la tabla `provincias`
--
ALTER TABLE `provincias`
  ADD CONSTRAINT `fk_provincias_regiones` FOREIGN KEY (`idregiones`) REFERENCES `regiones` (`idregiones`);

--
-- Filtros para la tabla `sectores`
--
ALTER TABLE `sectores`
  ADD CONSTRAINT `fk_sectores_comunas1` FOREIGN KEY (`idcomunas`) REFERENCES `comunas` (`idcomunas`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
