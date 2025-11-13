-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-11-2025 a las 15:15:49
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pibd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anuncios`
--

CREATE TABLE `anuncios` (
  `IdAnuncio` int(10) UNSIGNED NOT NULL,
  `TAnuncio` tinyint(3) UNSIGNED NOT NULL,
  `TVivienda` tinyint(3) UNSIGNED NOT NULL,
  `FPrincipal` varchar(255) DEFAULT NULL,
  `Alternativo` varchar(255) DEFAULT NULL,
  `Titulo` varchar(255) NOT NULL,
  `Precio` decimal(10,2) NOT NULL,
  `Texto` text DEFAULT NULL,
  `Ciudad` varchar(255) DEFAULT NULL,
  `Pais` int(10) UNSIGNED NOT NULL,
  `Superficie` decimal(10,2) DEFAULT NULL,
  `NHabitaciones` tinyint(4) DEFAULT NULL,
  `NBanyos` tinyint(4) DEFAULT NULL,
  `Planta` int(11) DEFAULT NULL,
  `Anyo` int(11) DEFAULT NULL,
  `FRegistro` datetime DEFAULT current_timestamp(),
  `Usuario` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `anuncios`
--

INSERT INTO `anuncios` (`IdAnuncio`, `TAnuncio`, `TVivienda`, `FPrincipal`, `Alternativo`, `Titulo`, `Precio`, `Texto`, `Ciudad`, `Pais`, `Superficie`, `NHabitaciones`, `NBanyos`, `Planta`, `Anyo`, `FRegistro`, `Usuario`) VALUES
(101, 2, 2, 'piso1.jpg', 'Apartamento moderno con vistas', 'Apartamento de Alquiler en el Centro', 850.00, 'Luminoso apartamento recién reformado, ideal para parejas o estudiantes. Cerca de todos los servicios.', 'Alicante', 1, 75.50, 2, 1, 3, 2005, '2025-11-13 14:54:38', 1),
(102, 1, 2, 'casa2.jpg', 'Chalet independiente con jardín y piscina', 'Gran Chalet en Venta, Zona Residencial', 350000.00, 'Hermosa casa con 4 dormitorios, garaje doble y jardín privado. Excelente oportunidad de compra.', 'Madrid', 1, 220.00, 4, 3, 0, 1998, '2025-11-13 14:54:38', 2),
(103, 2, 3, 'oficina3.jpg', 'Despacho amplio con mucha luz natural', 'Oficina en Alquiler, Distrito Financiero', 1200.00, 'Oficina totalmente equipada con varias salas de reuniones. Perfecta para startups o PYMES.', 'París', 2, 95.00, 0, 1, 5, 2010, '2025-11-13 14:54:38', 3),
(104, 1, 5, 'garaje4.jpg', 'Plaza de parking en edificio vigilado', 'Plaza de Garaje en Venta, zona centro', 25000.00, 'Amplia plaza de garaje, fácil acceso y seguridad 24 horas. Cerca de la estación central.', 'Valencia', 1, 15.00, 0, 0, 0, 2015, '2025-11-13 14:54:38', 1),
(105, 1, 2, 'ático5.jpg', 'Ático con terraza y vistas panorámicas', 'Venta de Ático de Lujo con Terraza', 495000.00, 'Espectacular ático con 100m² de terraza y acabados de alta calidad. Dos dormitorios dobles.', 'Roma', 3, 110.00, 2, 2, 7, 2022, '2025-11-13 14:54:38', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estilos`
--

CREATE TABLE `estilos` (
  `IdEstilo` int(10) UNSIGNED NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Fichero` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estilos`
--

INSERT INTO `estilos` (`IdEstilo`, `Nombre`, `Descripcion`, `Fichero`) VALUES
(1, 'Predeterminado', 'Estilo base del sitio web.', 'estilo.css'),
(2, 'Alto Contraste', 'Esquema de colores de alto contraste.', 'contraste.css'),
(3, 'Letra Grande', 'Estilo con fuentes aumentadas.', 'letra_grande.css'),
(4, 'Contraste y Letra Grande', 'Combina alto contraste y letra grande.', 'letra_y_contraste.css'),
(5, 'Nocturno', 'Esquema de colores oscuros para la noche.', 'noche.css'),
(6, 'Impresión', 'Estilo optimizado para imprimir documentos.', 'impreso.css');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotos`
--

CREATE TABLE `fotos` (
  `IdFoto` int(10) UNSIGNED NOT NULL,
  `Titulo` varchar(255) DEFAULT NULL,
  `Foto` varchar(255) DEFAULT NULL,
  `Alternativo` varchar(255) DEFAULT NULL,
  `Anuncio` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `fotos`
--

INSERT INTO `fotos` (`IdFoto`, `Titulo`, `Foto`, `Alternativo`, `Anuncio`) VALUES
(201, 'Salón principal', 'salon_chalet.jpg', 'Salón con chimenea y vigas de madera.', 102),
(202, 'Cocina equipada', 'cocina_chalet.jpeg', 'Cocina estilo rústico totalmente equipada.', 102),
(203, 'Jardín trasero', 'jardin_chalet.jpeg', 'Vista del jardín con zona de barbacoa.', 102),
(204, 'Vista frontal del piso', 'piso.jpg', 'Foto principal de la fachada del apartamento.', 101);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `IdMensaje` int(10) UNSIGNED NOT NULL,
  `TMensaje` tinyint(3) UNSIGNED NOT NULL,
  `Texto` text DEFAULT NULL,
  `Anuncio` int(10) UNSIGNED DEFAULT NULL,
  `UsuOrigen` int(10) UNSIGNED NOT NULL,
  `UsuDestino` int(10) UNSIGNED NOT NULL,
  `FRegistro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`IdMensaje`, `TMensaje`, `Texto`, `Anuncio`, `UsuOrigen`, `UsuDestino`, `FRegistro`) VALUES
(1, 2, 'Buenas tardes, estoy muy interesado. ¿Podríamos coordinar una visita mañana por la tarde, alrededor de las 17:00h? Gracias.', 101, 3, 1, '2025-11-13 15:08:25'),
(2, 1, 'Quería preguntar sobre las condiciones de alquiler. ¿El propietario acepta mascotas pequeñas (un perro de 5kg)?', 101, 4, 1, '2025-11-13 15:08:25'),
(3, 1, 'Hola, ¿podrían especificar el coste de la comunidad y si está incluido en los 850€? También saber si el piso tiene aire acondicionado.', 101, 2, 1, '2025-11-13 15:08:25'),
(4, 3, 'Ofrezco 330.000€ por el chalet si la venta se cierra antes de fin de mes. ¿Sería posible considerar esta oferta?', 102, 5, 2, '2025-11-13 15:08:25'),
(5, 1, 'Nos interesa la zona. ¿El chalet se encuentra cerca de colegios internacionales o del transporte público?', 102, 1, 2, '2025-11-13 15:08:25'),
(6, 2, 'Me gustaría solicitar una cita urgente para ver la oficina, ya que necesito mudarme la próxima semana. ¿Tienen disponibilidad hoy?', 103, 4, 3, '2025-11-13 15:08:25'),
(7, 1, '¿La plaza de garaje es para coche grande? El anuncio no especifica las dimensiones exactas en metros.', 104, 3, 1, '2025-11-13 15:08:25'),
(8, 3, 'Estoy buscando un ático de lujo similar. Si bajan el precio a 450.000€, me gustaría formalizar una oferta de compra en firme.', 105, 1, 2, '2025-11-13 15:08:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises`
--

CREATE TABLE `paises` (
  `IdPais` int(10) UNSIGNED NOT NULL,
  `NomPais` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paises`
--

INSERT INTO `paises` (`IdPais`, `NomPais`) VALUES
(1, 'España'),
(2, 'Francia'),
(3, 'Italia'),
(4, 'Alemania'),
(5, 'Portugal'),
(6, 'Reino Unido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `IdSolicitud` int(10) UNSIGNED NOT NULL,
  `Anuncio` int(10) UNSIGNED NOT NULL,
  `Texto` text DEFAULT NULL,
  `Nombre` varchar(200) DEFAULT NULL,
  `Email` varchar(254) DEFAULT NULL,
  `Direccion` text DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Color` varchar(255) DEFAULT NULL,
  `Copias` int(11) DEFAULT NULL,
  `Resolucion` int(11) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `IColor` tinyint(1) DEFAULT NULL,
  `IPrecio` tinyint(1) DEFAULT NULL,
  `FRegistro` datetime DEFAULT current_timestamp(),
  `Coste` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`IdSolicitud`, `Anuncio`, `Texto`, `Nombre`, `Email`, `Direccion`, `Telefono`, `Color`, `Copias`, `Resolucion`, `Fecha`, `IColor`, `IPrecio`, `FRegistro`, `Coste`) VALUES
(1, 101, 'Solicitud urgente para evento promocional. Por favor, enviar antes del 2026-02-01.', 'Juan Pérez García', 'juan.perez@email.com', 'Calle Mayor, 10, 1º B, 03001, Alicante, España', '600111222', '#000000', 1, 450, '2026-02-01', 0, 1, '2025-11-13 15:13:23', 17.60),
(2, 102, NULL, 'María Lopez Ruiz', 'maria.l.ruiz@ejemplo.es', 'Av. de la Liberté, 55, 75001, París, Francia', '3312345678', '#FF0000', 1, 900, '2026-03-10', 1, 0, '2025-11-13 15:13:23', 73.30),
(3, 103, 'Imprimir sin el logo de la empresa si es posible.', 'Hugo Sánchez Torres', 'hugo.s@correo.net', 'Via Roma, 3, Int. 2, 00100, Roma, Italia', NULL, '#0000FF', 2, 150, NULL, 0, 1, '2025-11-13 15:13:23', 37.00),
(4, 101, 'Necesito 10 copias para distribución en ferias inmobiliarias.', 'Sara Jiménez Cano', 'sara.jc@web.es', 'Plaza de la Constitución, 1, 10100, Madrid, España', '677889900', '#CCCCCC', 10, 450, NULL, 0, 0, '2025-11-13 15:13:23', 375.00),
(5, 102, 'La fecha de recepción es flexible, lo importante es la calidad del color.', 'Mario Casas L.', 'mario.c@mail.com', 'Königsallee, 1, 40212, Düsseldorf, Alemania', NULL, '#FFFF00', 1, 300, '2026-01-20', 1, 1, '2025-11-13 15:13:23', 55.00),
(6, 103, NULL, 'Laura Gil', 'laura.g@mail.com', 'Rua Augusta, 10, 1100-053, Lisboa, Portugal', '3519123456', '#123456', 1, 750, NULL, 0, 1, '2025-11-13 15:13:23', 37.40),
(7, 101, 'Requerimos una tirada corta de alta calidad para presentar a inversores.', 'Pedro Ramos', 'pedro.r@mail.com', 'Carrer de Balmes, 100, 08008, Barcelona, España', '699112233', '#FF9900', 3, 600, '2026-03-01', 1, 0, '2025-11-13 15:13:23', 95.90),
(8, 102, 'Solicitud de prueba. Solo necesitamos 1 copia para validar el diseño.', 'Ana Martín', 'ana.m@mail.com', 'Oxford Street, 1, W1D 1AN, Londres, Reino Unido', '442079460123', '#000000', 1, 150, NULL, 0, 1, '2025-11-13 15:13:23', 17.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposanuncios`
--

CREATE TABLE `tiposanuncios` (
  `IdTAnuncio` tinyint(3) UNSIGNED NOT NULL,
  `NomTAnuncio` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposanuncios`
--

INSERT INTO `tiposanuncios` (`IdTAnuncio`, `NomTAnuncio`) VALUES
(1, 'Venta'),
(2, 'Alquiler');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposmensajes`
--

CREATE TABLE `tiposmensajes` (
  `IdTMensaje` tinyint(3) UNSIGNED NOT NULL,
  `NomTMensaje` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposmensajes`
--

INSERT INTO `tiposmensajes` (`IdTMensaje`, `NomTMensaje`) VALUES
(1, 'Más información'),
(2, 'Solicitar una cita'),
(3, 'Comunicar una oferta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposviviendas`
--

CREATE TABLE `tiposviviendas` (
  `IdTVivienda` tinyint(3) UNSIGNED NOT NULL,
  `NomTVivienda` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposviviendas`
--

INSERT INTO `tiposviviendas` (`IdTVivienda`, `NomTVivienda`) VALUES
(1, 'Obra nueva'),
(2, 'Vivienda'),
(3, 'Oficina'),
(4, 'Local'),
(5, 'Garaje');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `IdUsuario` int(10) UNSIGNED NOT NULL,
  `NomUsuario` varchar(15) NOT NULL,
  `Clave` varchar(255) NOT NULL,
  `Email` varchar(254) NOT NULL,
  `Sexo` tinyint(4) DEFAULT NULL,
  `FNacimiento` date DEFAULT NULL,
  `Ciudad` varchar(255) DEFAULT NULL,
  `Pais` int(10) UNSIGNED NOT NULL,
  `Foto` varchar(255) DEFAULT NULL,
  `FRegistro` datetime DEFAULT current_timestamp(),
  `Estilo` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`IdUsuario`, `NomUsuario`, `Clave`, `Email`, `Sexo`, `FNacimiento`, `Ciudad`, `Pais`, `Foto`, `FRegistro`, `Estilo`) VALUES
(1, 'leigh', '1234', 'leigh@ejemplo.com', 1, '1990-05-15', 'Alicante', 1, 'leigh.jpg', '2025-11-13 14:50:28', 2),
(2, 'maria', 'pass', 'maria@ejemplo.com', 0, '1985-11-20', 'Paris', 2, 'maria.jpg', '2025-11-13 14:50:28', 4),
(3, 'usuario1', 'usuario1', 'usuario1@ejemplo.com', 1, '2000-01-01', 'Roma', 3, 'user1.jpg', '2025-11-13 14:50:28', 1),
(4, 'hugo', 'abcd', 'hugo@ejemplo.com', 1, '1995-03-25', 'Berlín', 4, 'hugo.jpg', '2025-11-13 14:50:28', 3),
(5, 'saray', '1111', 'saray@ejemplo.com', 0, '1992-08-10', 'Lisboa', 5, 'saray.jpg', '2025-11-13 14:50:28', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  ADD PRIMARY KEY (`IdAnuncio`),
  ADD KEY `TAnuncio` (`TAnuncio`),
  ADD KEY `TVivienda` (`TVivienda`),
  ADD KEY `Pais` (`Pais`),
  ADD KEY `Usuario` (`Usuario`);

--
-- Indices de la tabla `estilos`
--
ALTER TABLE `estilos`
  ADD PRIMARY KEY (`IdEstilo`);

--
-- Indices de la tabla `fotos`
--
ALTER TABLE `fotos`
  ADD PRIMARY KEY (`IdFoto`),
  ADD KEY `Anuncio` (`Anuncio`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`IdMensaje`),
  ADD KEY `TMensaje` (`TMensaje`),
  ADD KEY `Anuncio` (`Anuncio`),
  ADD KEY `UsuOrigen` (`UsuOrigen`),
  ADD KEY `UsuDestino` (`UsuDestino`);

--
-- Indices de la tabla `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`IdPais`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`IdSolicitud`),
  ADD KEY `Anuncio` (`Anuncio`);

--
-- Indices de la tabla `tiposanuncios`
--
ALTER TABLE `tiposanuncios`
  ADD PRIMARY KEY (`IdTAnuncio`);

--
-- Indices de la tabla `tiposmensajes`
--
ALTER TABLE `tiposmensajes`
  ADD PRIMARY KEY (`IdTMensaje`);

--
-- Indices de la tabla `tiposviviendas`
--
ALTER TABLE `tiposviviendas`
  ADD PRIMARY KEY (`IdTVivienda`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`IdUsuario`),
  ADD UNIQUE KEY `NomUsuario` (`NomUsuario`),
  ADD KEY `Pais` (`Pais`),
  ADD KEY `Estilo` (`Estilo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  MODIFY `IdAnuncio` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT de la tabla `estilos`
--
ALTER TABLE `estilos`
  MODIFY `IdEstilo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `fotos`
--
ALTER TABLE `fotos`
  MODIFY `IdFoto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `IdMensaje` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
  MODIFY `IdPais` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `IdSolicitud` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tiposanuncios`
--
ALTER TABLE `tiposanuncios`
  MODIFY `IdTAnuncio` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tiposmensajes`
--
ALTER TABLE `tiposmensajes`
  MODIFY `IdTMensaje` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tiposviviendas`
--
ALTER TABLE `tiposviviendas`
  MODIFY `IdTVivienda` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `IdUsuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `anuncios`
--
ALTER TABLE `anuncios`
  ADD CONSTRAINT `anuncios_ibfk_1` FOREIGN KEY (`TAnuncio`) REFERENCES `tiposanuncios` (`IdTAnuncio`),
  ADD CONSTRAINT `anuncios_ibfk_2` FOREIGN KEY (`TVivienda`) REFERENCES `tiposviviendas` (`IdTVivienda`),
  ADD CONSTRAINT `anuncios_ibfk_3` FOREIGN KEY (`Pais`) REFERENCES `paises` (`IdPais`),
  ADD CONSTRAINT `anuncios_ibfk_4` FOREIGN KEY (`Usuario`) REFERENCES `usuarios` (`IdUsuario`);

--
-- Filtros para la tabla `fotos`
--
ALTER TABLE `fotos`
  ADD CONSTRAINT `fotos_ibfk_1` FOREIGN KEY (`Anuncio`) REFERENCES `anuncios` (`IdAnuncio`);

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`TMensaje`) REFERENCES `tiposmensajes` (`IdTMensaje`),
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`Anuncio`) REFERENCES `anuncios` (`IdAnuncio`),
  ADD CONSTRAINT `mensajes_ibfk_3` FOREIGN KEY (`UsuOrigen`) REFERENCES `usuarios` (`IdUsuario`),
  ADD CONSTRAINT `mensajes_ibfk_4` FOREIGN KEY (`UsuDestino`) REFERENCES `usuarios` (`IdUsuario`);

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`Anuncio`) REFERENCES `anuncios` (`IdAnuncio`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`Pais`) REFERENCES `paises` (`IdPais`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`Estilo`) REFERENCES `estilos` (`IdEstilo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
