-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 30 juil. 2020 à 17:14
-- Version du serveur :  10.4.11-MariaDB
-- Version de PHP : 7.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `pointage_horaire`
--

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

CREATE TABLE `enseignants` (
  `id_pers` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`id_pers`) VALUES
(36),
(37),
(38),
(39),
(40),
(41),
(42),
(43),
(44),
(45),
(46),
(47),
(48),
(49),
(50),
(51),
(52),
(53);

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

CREATE TABLE `etudiants` (
  `niveau` varchar(50) NOT NULL,
  `matricule` varchar(15) NOT NULL,
  `id_pers` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `etudiants`
--

INSERT INTO `etudiants` (`niveau`, `matricule`, `id_pers`) VALUES
('L1', '99344155239', 1),
('L1', '79139045894', 2),
('L1', '54967826564', 3),
('L1', '46165570979', 4),
('L1', '48290231413', 5),
('L1', '18024210165', 6),
('L1', '98482477241', 7),
('L1', '26076128435', 8),
('L1', '96740130362', 9),
('L1', '14948158574', 10),
('L1', '46243737126', 11),
('L1', '22117741028', 12),
('L1', '91544323171', 13),
('L1', '33926586645', 14),
('L1', '93961752596', 15),
('L2', '25285234921', 16),
('L2', '24648594902', 17),
('L2', '62895171416', 18),
('L2', '18823441027', 19),
('L2', '28540830753', 20),
('L2', '23878022296', 21),
('L2', '54497290906', 22),
('L2', '31182282538', 23),
('L2', '66648848791', 24),
('L2', '19566702014', 25),
('L2', '55099136704', 26),
('L2', '54281109612', 27),
('L2', '87101519677', 28),
('L2', '54960420142', 29),
('L2', '62618990553', 30),
('L3', '38137502754', 31),
('L3', '65936690948', 32),
('L3', '67362507392', 33),
('L3', '34895502309', 34),
('L3', '34052400386', 35);

-- --------------------------------------------------------

--
-- Structure de la table `instants`
--

CREATE TABLE `instants` (
  `id_ins` int(11) NOT NULL,
  `date_jour` date NOT NULL,
  `heure_arrivee` time NOT NULL,
  `heure_depart` time DEFAULT NULL,
  `id_pers` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `instants`
--

INSERT INTO `instants` (`id_ins`, `date_jour`, `heure_arrivee`, `heure_depart`, `id_pers`) VALUES
(1, '2020-02-03', '06:09:29', '17:55:01', 4),
(2, '2020-02-03', '06:32:37', '17:20:14', 23),
(3, '2020-02-03', '06:41:29', '18:57:53', 2),
(4, '2020-02-03', '06:48:33', '18:57:16', 32),
(5, '2020-02-03', '06:49:59', '18:01:14', 57),
(6, '2020-02-03', '07:54:30', '17:10:13', 44),
(7, '2020-02-03', '08:15:28', '17:33:07', 29),
(8, '2020-02-03', '08:18:36', '18:46:29', 51),
(9, '2020-02-03', '08:30:21', '17:24:16', 41),
(10, '2020-02-03', '08:31:56', '17:18:39', 20),
(11, '2020-02-03', '08:37:21', '17:57:43', 42),
(12, '2020-02-03', '08:44:32', '17:07:14', 21),
(13, '2020-02-03', '08:48:54', '18:16:52', 19),
(14, '2020-02-03', '08:52:47', '17:59:00', 58),
(15, '2020-02-04', '06:20:52', '18:34:12', 42),
(16, '2020-02-04', '06:21:56', '18:33:08', 19),
(17, '2020-02-04', '06:35:01', '18:09:44', 3),
(18, '2020-02-04', '06:52:35', '17:12:21', 9),
(19, '2020-02-04', '06:54:04', '17:41:04', 41),
(20, '2020-02-04', '06:54:08', '17:27:42', 9),
(21, '2020-02-04', '07:02:55', '17:41:57', 4),
(22, '2020-02-04', '07:46:46', '17:43:10', 35),
(23, '2020-02-04', '07:47:23', '17:27:05', 29),
(24, '2020-02-04', '07:51:09', '17:49:44', 16),
(25, '2020-02-04', '08:11:42', '17:56:36', 34),
(26, '2020-02-04', '08:34:01', '17:53:56', 14),
(27, '2020-02-04', '08:42:31', '18:29:23', 21),
(28, '2020-02-04', '08:42:43', '18:35:48', 54),
(29, '2020-02-05', '06:14:50', '18:34:41', 20),
(30, '2020-02-05', '06:35:37', '17:08:08', 3),
(31, '2020-02-05', '06:51:03', '17:58:52', 28),
(32, '2020-02-05', '06:53:49', '17:35:41', 4),
(33, '2020-02-05', '06:57:30', '17:51:27', 33),
(34, '2020-02-05', '06:58:12', '18:28:20', 34),
(35, '2020-02-05', '07:07:12', '18:26:17', 16),
(36, '2020-02-05', '07:08:03', '18:04:11', 50),
(37, '2020-02-05', '07:17:22', '18:17:10', 7),
(38, '2020-02-05', '07:20:47', '18:43:27', 21),
(39, '2020-02-05', '07:46:46', '18:21:39', 33),
(40, '2020-02-05', '07:51:44', '18:28:50', 15),
(41, '2020-02-05', '08:16:37', '17:23:20', 22),
(42, '2020-02-05', '08:20:51', '17:15:40', 19),
(43, '2020-02-05', '08:25:20', '18:55:11', 47),
(44, '2020-02-05', '08:31:30', '18:32:57', 33),
(45, '2020-02-05', '08:49:03', '17:58:03', 31),
(46, '2020-02-05', '08:57:12', '18:26:38', 29),
(47, '2020-02-06', '06:00:45', '18:43:47', 42),
(48, '2020-02-06', '06:29:18', '17:36:32', 6),
(49, '2020-02-06', '06:58:11', '17:40:22', 50),
(50, '2020-02-06', '07:14:24', '17:25:33', 2),
(51, '2020-02-06', '07:18:02', '17:45:46', 53),
(52, '2020-02-06', '07:21:34', '18:34:00', 16),
(53, '2020-02-06', '07:29:42', '17:43:14', 12),
(54, '2020-02-06', '07:34:02', '18:23:30', 4),
(55, '2020-02-06', '07:35:34', '18:44:17', 40),
(56, '2020-02-06', '07:49:27', '17:11:13', 18),
(57, '2020-02-06', '07:53:12', '18:25:37', 21),
(58, '2020-02-06', '08:03:22', '18:37:46', 20),
(59, '2020-02-06', '08:22:52', '17:02:51', 30),
(60, '2020-02-06', '08:25:37', '18:57:36', 33),
(61, '2020-02-06', '08:33:54', '18:22:27', 29),
(62, '2020-02-06', '08:33:32', '17:03:32', 54),
(63, '2020-02-06', '08:40:54', '18:00:41', 60),
(64, '2020-02-06', '08:53:21', '18:42:39', 39),
(65, '2020-02-07', '06:15:23', '18:54:05', 42),
(66, '2020-02-07', '06:19:01', '17:11:07', 4),
(67, '2020-02-07', '06:24:31', '17:35:01', 41),
(68, '2020-02-07', '06:41:11', '18:24:05', 55),
(69, '2020-02-07', '06:44:00', '17:40:29', 23),
(70, '2020-02-07', '06:49:34', '17:53:40', 14),
(71, '2020-02-07', '06:57:38', '18:00:33', 47),
(72, '2020-02-07', '07:03:57', '18:59:14', 53),
(73, '2020-02-07', '07:15:10', '18:37:06', 19),
(74, '2020-02-07', '07:34:23', '17:12:48', 8),
(75, '2020-02-07', '07:37:18', '17:49:34', 43),
(76, '2020-02-07', '07:40:24', '18:06:07', 22),
(77, '2020-02-07', '07:43:36', '18:59:37', 33),
(78, '2020-02-07', '07:59:22', '17:38:57', 29),
(79, '2020-02-07', '08:06:12', '18:55:44', 58),
(80, '2020-02-07', '08:07:23', '17:59:00', 14),
(81, '2020-02-07', '08:19:49', '18:35:53', 44),
(82, '2020-02-07', '08:25:03', '17:16:34', 1),
(83, '2020-02-07', '08:33:35', '18:05:27', 43),
(84, '2020-02-07', '08:44:24', '18:03:20', 25),
(85, '2020-02-07', '08:55:15', '17:27:03', 50);

-- --------------------------------------------------------

--
-- Structure de la table `personnes`
--

CREATE TABLE `personnes` (
  `id_pers` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `personnes`
--

INSERT INTO `personnes` (`id_pers`, `nom`) VALUES
(1, 'ETU1'),
(2, 'ETU2'),
(3, 'ETU3'),
(4, 'ETU4'),
(5, 'ETU5'),
(6, 'ETU6'),
(7, 'ETU7'),
(8, 'ETU8'),
(9, 'ETU9'),
(10, 'ETU10'),
(11, 'ETU11'),
(12, 'ETU12'),
(13, 'ETU13'),
(14, 'ETU14'),
(15, 'ETU15'),
(16, 'ETU16'),
(17, 'ETU17'),
(18, 'ETU18'),
(19, 'ETU19'),
(20, 'ETU20'),
(21, 'ETU21'),
(22, 'ETU22'),
(23, 'ETU23'),
(24, 'ETU24'),
(25, 'ETU25'),
(26, 'ETU26'),
(27, 'ETU27'),
(28, 'ETU28'),
(29, 'ETU29'),
(30, 'ETU30'),
(31, 'ETU31'),
(32, 'ETU32'),
(33, 'ETU33'),
(34, 'ETU34'),
(35, 'ETU35'),
(36, 'ENS1'),
(37, 'ENS2'),
(38, 'ENS3'),
(39, 'ENS4'),
(40, 'ENS5'),
(41, 'ENS6'),
(42, 'ENS7'),
(43, 'ENS8'),
(44, 'ENS9'),
(45, 'ENS10'),
(46, 'ENS11'),
(47, 'ENS12'),
(48, 'ENS13'),
(49, 'ETU14'),
(50, 'ENS15'),
(51, 'ENS16'),
(52, 'ENS17'),
(53, 'ENS18'),
(54, 'STF1'),
(55, 'STF2'),
(56, 'STF3'),
(57, 'STF4'),
(58, 'STF5'),
(59, 'STF6'),
(60, 'STF7');

-- --------------------------------------------------------

--
-- Structure de la table `staff`
--

CREATE TABLE `staff` (
  `est_admin` tinyint(1) NOT NULL,
  `id_pers` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `staff`
--

INSERT INTO `staff` (`est_admin`, `id_pers`) VALUES
(0, 54),
(0, 55),
(1, 56),
(0, 57),
(1, 58),
(1, 59),
(0, 60);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `enseignants`
--
ALTER TABLE `enseignants`
  ADD PRIMARY KEY (`id_pers`);

--
-- Index pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD PRIMARY KEY (`id_pers`),
  ADD UNIQUE KEY `etudiants_AK` (`matricule`);

--
-- Index pour la table `instants`
--
ALTER TABLE `instants`
  ADD PRIMARY KEY (`id_ins`),
  ADD KEY `instants_personnes_FK` (`id_pers`);

--
-- Index pour la table `personnes`
--
ALTER TABLE `personnes`
  ADD PRIMARY KEY (`id_pers`);

--
-- Index pour la table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id_pers`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `instants`
--
ALTER TABLE `instants`
  MODIFY `id_ins` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT pour la table `personnes`
--
ALTER TABLE `personnes`
  MODIFY `id_pers` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `enseignants`
--
ALTER TABLE `enseignants`
  ADD CONSTRAINT `enseignants_personnes_FK` FOREIGN KEY (`id_pers`) REFERENCES `personnes` (`id_pers`);

--
-- Contraintes pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD CONSTRAINT `etudiants_personnes_FK` FOREIGN KEY (`id_pers`) REFERENCES `personnes` (`id_pers`);

--
-- Contraintes pour la table `instants`
--
ALTER TABLE `instants`
  ADD CONSTRAINT `instants_personnes_FK` FOREIGN KEY (`id_pers`) REFERENCES `personnes` (`id_pers`);

--
-- Contraintes pour la table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_personnes_FK` FOREIGN KEY (`id_pers`) REFERENCES `personnes` (`id_pers`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
