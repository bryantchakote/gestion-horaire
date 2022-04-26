-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 24 août 2020 à 13:33
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

CREATE DATABASE pointage_horaire;
USE pointage_horaire;

-- --------------------------------------------------------

--
-- Structure de la table `absences`
--

CREATE TABLE `absences` (
  `id_abs` int(11) NOT NULL,
  `date_jour` date NOT NULL,
  `nbre_heures` int(11) NOT NULL,
  `id_pers` int(11) NOT NULL,
  `id_motif` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

CREATE TABLE `enseignants` (
  `id_pers` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

CREATE TABLE `etudiants` (
  `niveau` varchar(50) NOT NULL,
  `matricule` varchar(15) NOT NULL,
  `id_pers` int(11) NOT NULL,
  `est_delegue` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


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


-- --------------------------------------------------------

--
-- Structure de la table `motifs`
--

CREATE TABLE `motifs` (
  `id_motif` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


--
-- Déchargement des données de la table `motifs`
--

INSERT INTO `motifs` (`id_motif`, `libelle`) VALUES
(1, 'Inconnu'),
(2, 'Non justifié'),
(3, 'Maladie'),
(4, 'Famille'),
(5, 'Professionel'),
(6, 'Autres');

-- --------------------------------------------------------

--
-- Structure de la table `personnes`
--

CREATE TABLE `personnes` (
  `id_pers` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `sexe` varchar(1) NOT NULL,
  `email` varchar(70) NOT NULL,
  `mdp` varchar(160) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `personnes`
--

INSERT INTO `personnes` (`id_pers`, `nom`, `prenom`, `sexe`, `email`, `mdp`) VALUES
(1, 'Tsamo', 'Etienne', 'M', 'tsamo@gmail.com', '$2y$10$7d8H/WkAlc/MDVcgKpcLJufGkm2lCX2EjXudgUxXVKMOIyd.mPwXS');


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
(1, 1);


--
-- Index pour les tables déchargées
--

--
-- Index pour la table `absences`
--
ALTER TABLE `absences`
  ADD PRIMARY KEY (`id_abs`),
  ADD KEY `absences_personnes_FK` (`id_pers`),
  ADD KEY `absences_motifs0_FK` (`id_motif`);

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
-- Index pour la table `motifs`
--
ALTER TABLE `motifs`
  ADD PRIMARY KEY (`id_motif`);

--
-- Index pour la table `personnes`
--
ALTER TABLE `personnes`
  ADD PRIMARY KEY (`id_pers`),
  ADD UNIQUE KEY `personnes_AK` (`email`);

--
-- Index pour la table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id_pers`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `absences`
--
ALTER TABLE `absences`
  MODIFY `id_abs` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `instants`
--
ALTER TABLE `instants`
  MODIFY `id_ins` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `motifs`
--
ALTER TABLE `motifs`
  MODIFY `id_motif` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `personnes`
--
ALTER TABLE `personnes`
  MODIFY `id_pers` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `absences`
--
ALTER TABLE `absences`
  ADD CONSTRAINT `absences_motifs0_FK` FOREIGN KEY (`id_motif`) REFERENCES `motifs` (`id_motif`),
  ADD CONSTRAINT `absences_personnes_FK` FOREIGN KEY (`id_pers`) REFERENCES `personnes` (`id_pers`);

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
