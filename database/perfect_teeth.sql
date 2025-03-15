-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 15, 2025 at 07:15 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perfect_teeth`
--

-- --------------------------------------------------------

--
-- Table structure for table `citas`
--

CREATE TABLE `citas` (
  `id_cita` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_doctor` int(11) NOT NULL,
  `fecha_cita` date NOT NULL,
  `hora_cita` varchar(10) DEFAULT NULL,
  `id_consultas` int(11) NOT NULL,
  `estado` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consultas`
--

CREATE TABLE `consultas` (
  `id_consultas` int(11) NOT NULL,
  `tipo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `consultas`
--

INSERT INTO `consultas` (`id_consultas`, `tipo`) VALUES
(1, 'Valoración y diagnóstico '),
(2, 'Limpieza o profilaxis dental'),
(3, 'Resinas (calzas)'),
(4, 'Endodoncia (tratamiento de conducto)'),
(5, 'Ortodoncia'),
(6, 'Prótesis total, fija o removible '),
(7, 'Cirugía oral'),
(8, 'Implantes'),
(9, 'Estética dental');

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `id_doctor` int(11) NOT NULL,
  `nombreD` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `sexo` varchar(255) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `correo_eletronico` varchar(255) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `id_especialidad` int(255) NOT NULL,
  `session_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`id_doctor`, `nombreD`, `apellido`, `sexo`, `fecha_nacimiento`, `telefono`, `correo_eletronico`, `clave`, `id_especialidad`, `session_token`) VALUES
(1, 'Emily Valeria', 'Bernal Jaimes', 'Femenino', '2001-07-05', '3105547320', 'emilybernal902@gmail.com', '$2y$12$Qc6mbuTsSoslxk3JNGkljeIexJW1.6qIpXz3WiMZf6W3aq5tfSsrO', 1, '8af2634d7fc33673a3a1538147cb95e3d6956661f74b7cb477ef3d1dc747275b');

-- --------------------------------------------------------

--
-- Table structure for table `especialidad`
--

CREATE TABLE `especialidad` (
  `id_especialidad` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `especialidad`
--

INSERT INTO `especialidad` (`id_especialidad`, `tipo`) VALUES
(1, 'Odontólogo general'),
(2, 'Odontopediatra'),
(3, 'Ortodoncista'),
(4, 'Patólogo oral'),
(5, 'Endodoncista');

-- --------------------------------------------------------

--
-- Table structure for table `informe_medico`
--

CREATE TABLE `informe_medico` (
  `id_informe` int(11) NOT NULL,
  `id_cita` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `examen_intraoral` text DEFAULT NULL,
  `examen_extraoral` text DEFAULT NULL,
  `examen_atm` text DEFAULT NULL,
  `observacion_intraoral` text DEFAULT NULL,
  `observacion_extraoral_atm` text DEFAULT NULL,
  `descripcion_radiografica` text DEFAULT NULL,
  `diagnostico_periodontal` text DEFAULT NULL,
  `pronostico` text DEFAULT NULL,
  `evolucion` text DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `radiografia` varchar(255) DEFAULT NULL,
  `foto_boca` varchar(255) DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT NULL,
  `plan_tratamiento` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pacientes`
--

CREATE TABLE `pacientes` (
  `id_paciente` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `sexo` varchar(60) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `correo_electronico` varchar(255) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `tipo_imagen` varchar(20) DEFAULT 'perfil',
  `eps` varchar(255) DEFAULT NULL,
  `ocupacion` varchar(255) DEFAULT NULL,
  `estado_civil` varchar(50) DEFAULT NULL,
  `cedula` varchar(50) DEFAULT NULL,
  `emergencia_nombre` varchar(255) DEFAULT NULL,
  `emergencia_telefono` varchar(50) DEFAULT NULL,
  `menor_acompanante` varchar(255) DEFAULT NULL,
  `menor_parentesco` varchar(50) DEFAULT NULL,
  `menor_telefono` varchar(50) DEFAULT NULL,
  `tipo_sangre` varchar(10) DEFAULT NULL,
  `alertas_medicas` text DEFAULT NULL,
  `lugar_direccion_residencia` varchar(255) DEFAULT NULL,
  `session_token` varchar(255) DEFAULT NULL,
  `numero_documento` varchar(20) DEFAULT NULL,
  `historia_cardiovasculares` enum('Sí','No') DEFAULT 'No',
  `historia_hemorragicas` enum('Sí','No') DEFAULT 'No',
  `historia_dermatologicas` enum('Sí','No') DEFAULT 'No',
  `historia_mentales` enum('Sí','No') DEFAULT 'No',
  `historia_diabetes` enum('Sí','No') DEFAULT 'No',
  `historia_cancer` enum('Sí','No') DEFAULT 'No',
  `historia_artritis` enum('Sí','No') DEFAULT 'No',
  `historia_alergias` enum('Sí','No') DEFAULT 'No',
  `historia_cirugias` enum('Sí','No') DEFAULT 'No',
  `historia_otros` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paciente_diagnostico`
--

CREATE TABLE `paciente_diagnostico` (
  `id_diagnostico` int(11) NOT NULL,
  `id_cita` int(11) NOT NULL,
  `diagnostico` text NOT NULL,
  `descripcion` text NOT NULL,
  `medicina` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `unavailable_dates`
--

CREATE TABLE `unavailable_dates` (
  `id_unavailable` int(11) NOT NULL,
  `id_doctor` int(11) NOT NULL,
  `unavailable_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id_cita`),
  ADD KEY `consultas-pacientes` (`id_consultas`),
  ADD KEY `doctor-cita` (`id_doctor`),
  ADD KEY `id_paciente` (`id_paciente`);

--
-- Indexes for table `consultas`
--
ALTER TABLE `consultas`
  ADD PRIMARY KEY (`id_consultas`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`id_doctor`),
  ADD KEY `id_especialidad` (`id_especialidad`);

--
-- Indexes for table `especialidad`
--
ALTER TABLE `especialidad`
  ADD PRIMARY KEY (`id_especialidad`);

--
-- Indexes for table `informe_medico`
--
ALTER TABLE `informe_medico`
  ADD PRIMARY KEY (`id_informe`),
  ADD KEY `id_cita` (`id_cita`),
  ADD KEY `id_paciente` (`id_paciente`);

--
-- Indexes for table `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id_paciente`);

--
-- Indexes for table `paciente_diagnostico`
--
ALTER TABLE `paciente_diagnostico`
  ADD PRIMARY KEY (`id_diagnostico`),
  ADD KEY `id_cita_fk` (`id_cita`);

--
-- Indexes for table `unavailable_dates`
--
ALTER TABLE `unavailable_dates`
  ADD PRIMARY KEY (`id_unavailable`),
  ADD UNIQUE KEY `id_doctor` (`id_doctor`,`unavailable_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `citas`
--
ALTER TABLE `citas`
  MODIFY `id_cita` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consultas`
--
ALTER TABLE `consultas`
  MODIFY `id_consultas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `id_doctor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `informe_medico`
--
ALTER TABLE `informe_medico`
  MODIFY `id_informe` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id_paciente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paciente_diagnostico`
--
ALTER TABLE `paciente_diagnostico`
  MODIFY `id_diagnostico` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unavailable_dates`
--
ALTER TABLE `unavailable_dates`
  MODIFY `id_unavailable` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `consultas-pacientes` FOREIGN KEY (`id_consultas`) REFERENCES `consultas` (`id_consultas`),
  ADD CONSTRAINT `doctor-cita` FOREIGN KEY (`id_doctor`) REFERENCES `doctor` (`id_doctor`),
  ADD CONSTRAINT `id_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`);

--
-- Constraints for table `doctor`
--
ALTER TABLE `doctor`
  ADD CONSTRAINT `id_especialidad` FOREIGN KEY (`id_especialidad`) REFERENCES `especialidad` (`id_especialidad`);

--
-- Constraints for table `informe_medico`
--
ALTER TABLE `informe_medico`
  ADD CONSTRAINT `informe_medico_ibfk_1` FOREIGN KEY (`id_cita`) REFERENCES `citas` (`id_cita`),
  ADD CONSTRAINT `informe_medico_ibfk_2` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`);

--
-- Constraints for table `paciente_diagnostico`
--
ALTER TABLE `paciente_diagnostico`
  ADD CONSTRAINT `id_cita_fk` FOREIGN KEY (`id_cita`) REFERENCES `citas` (`id_cita`);

--
-- Constraints for table `unavailable_dates`
--
ALTER TABLE `unavailable_dates`
  ADD CONSTRAINT `unavailable_dates_ibfk_1` FOREIGN KEY (`id_doctor`) REFERENCES `doctor` (`id_doctor`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
