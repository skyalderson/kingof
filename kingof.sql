-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  Dim 05 jan. 2020 à 15:02
-- Version du serveur :  5.7.26
-- Version de PHP :  7.2.18

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `kingof`
--
CREATE DATABASE IF NOT EXISTS `kingof` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `kingof`;

-- --------------------------------------------------------

--
-- Structure de la table `board`
--

DROP TABLE IF EXISTS `board`;
CREATE TABLE IF NOT EXISTS `board` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT '1',
  `img_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `board`
--

INSERT INTO `board` (`id`, `name`, `available`, `img_name`) VALUES
(1, 'King of Tokyo', 1, 'tokyo.png'),
(2, 'King of New-York', 0, 'newyork.png');

-- --------------------------------------------------------

--
-- Structure de la table `board_rule`
--

DROP TABLE IF EXISTS `board_rule`;
CREATE TABLE IF NOT EXISTS `board_rule` (
  `board_id` int(11) NOT NULL,
  `rule_id` int(11) NOT NULL,
  PRIMARY KEY (`board_id`,`rule_id`),
  KEY `IDX_9C4EF5EFE7EC5785` (`board_id`),
  KEY `IDX_9C4EF5EF744E0351` (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `board_rule`
--

INSERT INTO `board_rule` (`board_id`, `rule_id`) VALUES
(1, 1),
(1, 2),
(1, 4),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(2, 1),
(2, 3),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9);

-- --------------------------------------------------------

--
-- Structure de la table `box`
--

DROP TABLE IF EXISTS `box`;
CREATE TABLE IF NOT EXISTS `box` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `box_type_id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8A9483ADC8E12A2` (`box_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `box`
--

INSERT INTO `box` (`id`, `box_type_id`, `name`, `short_name`) VALUES
(1, 1, 'King of Tokyo - 1ère Édition', 'King of Tokyo'),
(2, 1, 'King of Tokyo - 2ème Édition', 'King of Tokyo'),
(3, 3, 'Power Up! King of Tokyo', 'Power Up!'),
(4, 3, 'Halloween Collector Pack', 'Halloween Pack'),
(5, 3, 'Monster Pack 1: Cthulhu', 'Monster Pack 1'),
(6, 3, 'Monster Pack 2: King Kong', 'Monster Pack 2'),
(7, 3, 'Monster Pack 3: Anubis', 'Monster Pack 3'),
(8, 3, 'Monster Pack 4: Cybertooth', 'Monster Pack 4'),
(9, 2, 'King of New-York', 'King of New-York'),
(10, 3, 'Power Up! King of New-York', 'Power Up!'),
(11, 4, 'Monstres Bonus', 'Monstres Bonus');

-- --------------------------------------------------------

--
-- Structure de la table `box_type`
--

DROP TABLE IF EXISTS `box_type`;
CREATE TABLE IF NOT EXISTS `box_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `box_type`
--

INSERT INTO `box_type` (`id`, `name`) VALUES
(1, 'King of Tokyo'),
(2, 'King of New-York'),
(3, 'Extensions'),
(4, 'Monstres Bonus');

-- --------------------------------------------------------

--
-- Structure de la table `game`
--

DROP TABLE IF EXISTS `game`;
CREATE TABLE IF NOT EXISTS `game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mode_id` int(11) NOT NULL,
  `board_id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` smallint(6) NOT NULL,
  `monsters_select` smallint(6) NOT NULL,
  `max_players` smallint(6) NOT NULL,
  `winner_id` int(11) DEFAULT NULL,
  `victory_type` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_232B318C77E5854A` (`mode_id`),
  KEY `IDX_232B318CE7EC5785` (`board_id`),
  KEY `IDX_232B318C5DFCD4B8` (`winner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `game_data_update`
--

DROP TABLE IF EXISTS `game_data_update`;
CREATE TABLE IF NOT EXISTS `game_data_update` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_id` int(11) NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `player_id` int(11) DEFAULT NULL,
  `value2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `former_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_268E4663EA675D86` (`log_id`),
  KEY `IDX_268E466399E6F5DF` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `game_monster`
--

DROP TABLE IF EXISTS `game_monster`;
CREATE TABLE IF NOT EXISTS `game_monster` (
  `game_id` int(11) NOT NULL,
  `monster_id` int(11) NOT NULL,
  PRIMARY KEY (`game_id`,`monster_id`),
  KEY `IDX_B19CB9DBE48FD905` (`game_id`),
  KEY `IDX_B19CB9DBC5FF1223` (`monster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `game_rule`
--

DROP TABLE IF EXISTS `game_rule`;
CREATE TABLE IF NOT EXISTS `game_rule` (
  `game_id` int(11) NOT NULL,
  `rule_id` int(11) NOT NULL,
  PRIMARY KEY (`game_id`,`rule_id`),
  KEY `IDX_ADCDC0E0E48FD905` (`game_id`),
  KEY `IDX_ADCDC0E0744E0351` (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `kot_card`
--

DROP TABLE IF EXISTS `kot_card`;
CREATE TABLE IF NOT EXISTS `kot_card` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` smallint(6) NOT NULL,
  `type` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `kot_card`
--

INSERT INTO `kot_card` (`id`, `name`, `price`, `type`, `img_name`) VALUES
(1, 'GARDE NATIONALE', 3, 'action', 'garde-nationale.png'),
(2, 'LA PETITE BOUTIQUE DU COIN', 3, 'action', 'la-petite-boutique-du-coin.png'),
(3, 'LANCE-FLAMMES', 3, 'action', 'lance-flammes.png'),
(4, 'SOIN', 3, 'action', 'soin'),
(5, 'BOMBARDEMENT AÉRIEN', 4, 'action', 'bombardement-aerien'),
(6, 'TANK', 4, 'action', 'tank'),
(7, 'TRAMWAY', 4, 'action', 'tramway'),
(8, 'AVIONS DE CHASSE', 5, 'action', 'avions-de-chasse.png'),
(9, 'IMMEUBLE', 5, 'action', 'immeuble'),
(10, 'LA MORT VIENT DU CIEL', 5, 'action', 'la-mort-vient-du-ciel'),
(11, 'CENTRALE NUCLÉAIRE', 6, 'action', 'centrale-nucleaire'),
(12, 'GRATTE-CIEL', 6, 'action', 'gratte-ciel'),
(13, 'RAFFINERIE DE GAZ', 6, 'action', 'raffinerie-de-gaz'),
(14, 'TEMPÊTE MAGNÉTIQUE', 6, 'action', 'tempete-magnetique'),
(15, 'ORDRE D\'ÉVACUATION', 7, 'action', 'ordre-d-evacuation'),
(16, 'RECHARGE', 8, 'action', 'recharge'),
(17, 'ÉNERGIE SOLAIRE', 2, 'pouvoir', 'energie-solaire'),
(18, 'EXPÉRIENCE SCIENTIFIQUE', 2, 'pouvoir', 'experience-scientifique'),
(19, 'CAMOUFLAGE', 3, 'pouvoir', 'camouflage'),
(20, 'DARDS EMPOISONNÉS', 3, 'pouvoir', 'dards-empoisonnes'),
(21, 'DESTRUCTION TOTALE', 3, 'pouvoir', 'destruction-totale'),
(22, 'ÉLASTIQUE', 3, 'pouvoir', 'elastique'),
(23, 'GUÉRISON RAPIDE', 3, 'pouvoir', 'guerison-rapide'),
(24, 'L\'AMI DES ENFANTS', 3, 'pouvoir', 'l-ami-des-enfants'),
(25, 'MÉDIATIQUE', 3, 'pouvoir', 'mediatique'),
(26, 'MÉTAMORPHOSE', 3, 'pouvoir', 'metamorphose'),
(27, 'MONSTRE BATTERIE', 3, 'pouvoir', 'monstre-batterie'),
(28, 'NOUS NE FAISONS QUE LE RENDRE PLUS FORT', 3, 'pouvoir', 'nous-ne-faisons-que-le-redre-plus-fort'),
(29, 'OPPORTUNISTE', 3, 'pouvoir', 'opportuniste'),
(30, 'ORIGINE EXTRA-TERRESTRE', 3, 'pouvoir', 'origine-extra-terrestre'),
(31, 'PLAN B', 3, 'pouvoir', 'plan-b'),
(32, 'PRÉDATEUR SÉLECTIF', 3, 'pouvoir', 'predateur-selectif'),
(33, 'RÉSERVE D\'ÉNERGIE', 3, 'pouvoir', 'reserve-d-energie'),
(34, 'SONDE PSYCHIQUE', 3, 'pouvoir', 'sonde-psychique'),
(35, 'TROP MIGNON', 3, 'pouvoir', 'trop-mignon'),
(36, 'ARMURE DE PLAQUES', 4, 'pouvoir', 'armure-de-plaques'),
(37, 'BOISSON ÉNERGISANTE', 4, 'pouvoir', 'boisson-energisante'),
(38, 'CRACHAT VENIMEUX', 4, 'pouvoir', 'crachat-venimeux'),
(39, 'DÉTRITIVORE', 4, 'pouvoir', 'detritivore'),
(40, 'ÉCRAN DE FUMÉE', 4, 'pouvoir', 'ecran-de-fumee'),
(41, 'ENCORE PLUS GRAND', 4, 'pouvoir', 'encore-plus-grand'),
(42, 'GOURMET', 4, 'pouvoir', 'gourmet'),
(43, 'NÉCROPHAGE', 4, 'pouvoir', 'necrophage'),
(44, 'NINJA URBAIN', 4, 'pouvoir', 'ninja-urbain'),
(45, 'RAYON REVIGORANT', 4, 'pouvoir', 'rayon-revigorant'),
(46, 'RÉGÉNÉRATION', 4, 'pouvoir', 'regeneration'),
(47, 'SOUFFLE DE FEU', 4, 'pouvoir', 'souffle-de-feu'),
(48, 'TENTACULES PARASITES', 4, 'pouvoir', 'tentacules-parasites'),
(49, 'URBAVORE', 4, 'pouvoir', 'urbavore'),
(50, 'CERVEAU DÉMESURÉ', 5, 'pouvoir', 'cerveau-demesure'),
(51, 'CONTRÔLE DU TEMPS', 5, 'pouvoir', 'controle-du-temps'),
(52, 'FOUISSEUR', 5, 'pouvoir', 'fouisseur'),
(53, 'HERBIVORE', 5, 'pouvoir', 'herbivore'),
(54, 'MONSTRE ALPHA', 5, 'pouvoir', 'monstre-alpha'),
(55, 'QUEUE À PIQUANTS', 5, 'pouvoir', 'queue-a-piquants'),
(56, 'RÉACTEURS', 5, 'pouvoir', 'reacteurs'),
(57, 'AILES', 6, 'pouvoir', 'ailes'),
(58, 'ATTAQUE ACIDE', 6, 'pouvoir', 'attaque-acide'),
(59, 'RAYON RÉDUCTEUR', 6, 'pouvoir', 'rayon-reducteur'),
(60, 'IL RESTAIT UN ŒUF', 7, 'pouvoir', 'il-restait-un-oeuf'),
(61, 'SOUFFLE NOVA', 7, 'pouvoir', 'souffle-nova'),
(62, 'UNE TÊTE DE PLUS', 7, 'pouvoir', 'une-tete-de-plus'),
(63, 'MIMÉTISME', 8, 'pouvoir', 'mimetisme');

-- --------------------------------------------------------

--
-- Structure de la table `kot_card_game`
--

DROP TABLE IF EXISTS `kot_card_game`;
CREATE TABLE IF NOT EXISTS `kot_card_game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `kot_card_id` int(11) NOT NULL,
  `player_id` int(11) DEFAULT NULL,
  `state` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_937D0B32E48FD905` (`game_id`),
  KEY `IDX_937D0B32CE77C980` (`kot_card_id`),
  KEY `IDX_937D0B3299E6F5DF` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `is_done` tinyint(1) NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `next_player_id` int(11) DEFAULT NULL,
  `next_action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8F3F68C5E48FD905` (`game_id`),
  KEY `IDX_8F3F68C599E6F5DF` (`player_id`),
  KEY `is_done` (`is_done`),
  KEY `IDX_8F3F68C5DE6C20EC` (`next_player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mode`
--

DROP TABLE IF EXISTS `mode`;
CREATE TABLE IF NOT EXISTS `mode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `mode`
--

INSERT INTO `mode` (`id`, `name`, `available`) VALUES
(1, 'Casual', 1),
(2, 'Tournoi', 0);

-- --------------------------------------------------------

--
-- Structure de la table `monster`
--

DROP TABLE IF EXISTS `monster`;
CREATE TABLE IF NOT EXISTS `monster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `box_id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `available` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_245EC6F4D8177B3F` (`box_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `monster`
--

INSERT INTO `monster` (`id`, `box_id`, `name`, `img_name`, `available`) VALUES
(1, 1, 'Alienoid', 'alienoid.png', 1),
(2, 1, 'Cyber Bunny', 'cyber-bunny.png', 1),
(3, 1, 'Giga Zaur', 'giga-zaur.png', 1),
(4, 1, 'Kraken', 'kraken.png', 0),
(5, 1, 'Meka Dragon', 'meka-dragon.png', 1),
(6, 1, 'The King', 'the-king.png', 1),
(7, 2, 'Cyber Kitty', 'cyber-kitty.png', 1),
(8, 2, 'Space Penguin', 'space-penguin.png', 1),
(9, 9, 'Captain Fish', 'captain-fish.png', 0),
(10, 9, 'Drakonis', 'drakonis.png', 0),
(11, 9, 'Kong', 'kong.png', 1),
(12, 9, 'Mantis', 'mantis.png', 0),
(13, 9, 'Rob', 'rob.png', 0),
(14, 9, 'Sheriff', 'sheriff.png', 0),
(15, 3, 'Pandakai', 'pandakai.png', 0),
(16, 4, 'Pumpkin Jack', 'pumpkin-jack.png', 1),
(17, 4, 'Boogie Woogie', 'boogie-woogie.png', 1),
(18, 5, 'Cthulhu', 'cthulhu.png', 0),
(19, 6, 'King Kong', 'king-kong.png', 1),
(20, 7, 'Anubis', 'anubis.png', 1),
(21, 8, 'Cybertooth', 'cybertooth.png', 1),
(22, 10, 'Mega Shark', 'mega-shark.png', 0),
(23, 11, 'Ali-San', 'ali-san.png', 1),
(24, 11, 'Baby Gigazaur', 'baby-gigazaur.png', 0),
(25, 11, 'Brockenbar', 'brockenbar.png', 0),
(26, 11, 'Crabomination', 'crabomination.png', 0),
(27, 11, 'Draccus', 'draccus.png', 1),
(28, 11, 'Iron Rook', 'iron-rook.png', 0),
(29, 11, 'Kookie', 'kookie.png', 1),
(30, 11, 'Lollybot', 'lollybot.png', 1),
(31, 11, 'Orange Death', 'orange-death.png', 1),
(32, 11, 'Pouic', 'pouic.png', 0),
(33, 11, 'Rozy Pony', 'rozy-pony.png', 1),
(34, 11, 'X-smash Tree', 'x-smash-tree.png', 1),
(35, 11, 'Zombie Alpha', 'zombie-alpha.png', 0);

-- --------------------------------------------------------

--
-- Structure de la table `player`
--

DROP TABLE IF EXISTS `player`;
CREATE TABLE IF NOT EXISTS `player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `monster_id` int(11) DEFAULT NULL,
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `creator` tinyint(1) NOT NULL,
  `is_ready` tinyint(1) NOT NULL,
  `is_alive` tinyint(1) NOT NULL,
  `in_city` smallint(6) NOT NULL,
  `vp` smallint(6) NOT NULL,
  `hp` smallint(6) NOT NULL,
  `hp_max` smallint(6) NOT NULL,
  `nb_dices` smallint(6) NOT NULL,
  `nb_mana` int(11) NOT NULL,
  `turn` smallint(6) NOT NULL,
  `is_playing` tinyint(1) NOT NULL,
  `joined_at` datetime NOT NULL,
  `last_log_id` int(11) DEFAULT NULL,
  `throws_left` smallint(6) NOT NULL,
  `has_decided_about_tokyo` tinyint(1) NOT NULL,
  `is_winner` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_98197A65C5FF1223` (`monster_id`),
  KEY `IDX_98197A65E48FD905` (`game_id`),
  KEY `IDX_98197A65A76ED395` (`user_id`),
  KEY `IDX_98197A656060DF5C` (`last_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `resolve_order`
--

DROP TABLE IF EXISTS `resolve_order`;
CREATE TABLE IF NOT EXISTS `resolve_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL,
  `resolve_order` smallint(6) NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E534A81899E6F5DF` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rule`
--

DROP TABLE IF EXISTS `rule`;
CREATE TABLE IF NOT EXISTS `rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `available` tinyint(1) NOT NULL DEFAULT '1',
  `img_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `box_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_46D8ACCCD8177B3F` (`box_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rule`
--

INSERT INTO `rule` (`id`, `name`, `description`, `available`, `img_name`, `box_id`) VALUES
(1, 'Cartes Évolution', NULL, 0, 'cartes-evolution.png', 3),
(2, 'Tuiles Cultiste', NULL, 0, 'tuiles-cultiste.png', 5),
(3, 'Tuiles Cultiste / Temple de Cthulhu', NULL, 0, 'tuiles-cultiste-temple-de-cthulhu.png', 5),
(4, 'Tour de Tokyo', NULL, 0, 'tour-de-tokyo.png', 6),
(5, 'Empire State Building', NULL, 0, 'empire-state-building.png', 6),
(6, 'Carte Belle', NULL, 0, 'carte-belle.png', 6),
(7, 'Dé du Destin & Cartes Malédiction', NULL, 0, 'de-du-destin.png', 7),
(8, 'Mode Berserk', NULL, 0, 'mode-berserk.png', 8),
(9, 'Évolutions Mutantes', NULL, 0, 'evolutions-mutantes.png', 8);

-- --------------------------------------------------------

--
-- Structure de la table `throw_tokyo_dice`
--

DROP TABLE IF EXISTS `throw_tokyo_dice`;
CREATE TABLE IF NOT EXISTS `throw_tokyo_dice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL,
  `face` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_kept` tinyint(1) DEFAULT NULL,
  `position` smallint(6) DEFAULT NULL,
  `type` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B8EF9EEA99E6F5DF` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `username`, `roles`, `password`) VALUES
(1, 'sky', 'a:1:{i:0;s:9:\"ROLE_USER\";}', '$argon2i$v=19$m=65536,t=4,p=1$SFY0WWhiZy9EYmdjUXlCZg$Wdwt0CnHnwL+06fTRI0p4KRA2a+M9dzvTzPrD71yy2w'),
(2, 'indi', 'a:1:{i:0;s:9:\"ROLE_USER\";}', '$argon2i$v=19$m=65536,t=4,p=1$QldMTjN1a0xBSXBCcFc3Rg$BwPmABGpLLRVJwG9+zmvvKpRzSEHFEQPqaLfp3YGP7k'),
(3, 'ninja', 'a:1:{i:0;s:9:\"ROLE_USER\";}', '$argon2i$v=19$m=65536,t=4,p=1$QmtTTC5WejFwWmtjVlJnMA$+y2f0zKaUZ8bFg64nC67ijtzzLuz5iMaWNVHzX6376E'),
(4, 'useravecunnomlong', 'a:1:{i:0;s:9:\"ROLE_USER\";}', '$argon2i$v=19$m=65536,t=4,p=1$bHY1Q05YbjluVTFsMFhvdw$cIsCs14PfAyLzk1uPx800AsU2r8Zgq/REPDMbm9H5+w'),
(5, 'user5', 'a:1:{i:0;s:9:\"ROLE_USER\";}', '$argon2i$v=19$m=65536,t=4,p=1$cnZmSWtEdmoxbTZ0R3o4aA$jh0YFO3m6mZrjfRaJ4WvgeEoi6RHBFpdvlfhnwpTBQQ'),
(6, 'user6', 'a:1:{i:0;s:9:\"ROLE_USER\";}', '$argon2i$v=19$m=65536,t=4,p=1$M21XbGJ5dzBSQUM3SThxLg$nqEbFzEdeuLIGNmvox8ooL0tP0FIQxCPKOag074Tkv0'),
(7, 'user7', 'a:1:{i:0;s:9:\"ROLE_USER\";}', '$argon2i$v=19$m=65536,t=4,p=1$RkxRTnA2WDVCTDZYMXRpTA$47FsV5aCJcHNMTHLt5iuaxFn8fMjNtuQnb4BQvdIA7s');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `board_rule`
--
ALTER TABLE `board_rule`
  ADD CONSTRAINT `FK_9C4EF5EF744E0351` FOREIGN KEY (`rule_id`) REFERENCES `rule` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_9C4EF5EFE7EC5785` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `box`
--
ALTER TABLE `box`
  ADD CONSTRAINT `FK_8A9483ADC8E12A2` FOREIGN KEY (`box_type_id`) REFERENCES `box_type` (`id`);

--
-- Contraintes pour la table `game`
--
ALTER TABLE `game`
  ADD CONSTRAINT `FK_232B318C5DFCD4B8` FOREIGN KEY (`winner_id`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `FK_232B318C77E5854A` FOREIGN KEY (`mode_id`) REFERENCES `mode` (`id`),
  ADD CONSTRAINT `FK_232B318CE7EC5785` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`);

--
-- Contraintes pour la table `game_data_update`
--
ALTER TABLE `game_data_update`
  ADD CONSTRAINT `FK_268E466399E6F5DF` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `FK_268E4663EA675D86` FOREIGN KEY (`log_id`) REFERENCES `log` (`id`);

--
-- Contraintes pour la table `game_monster`
--
ALTER TABLE `game_monster`
  ADD CONSTRAINT `FK_B19CB9DBC5FF1223` FOREIGN KEY (`monster_id`) REFERENCES `monster` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_B19CB9DBE48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `game_rule`
--
ALTER TABLE `game_rule`
  ADD CONSTRAINT `FK_ADCDC0E0744E0351` FOREIGN KEY (`rule_id`) REFERENCES `rule` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_ADCDC0E0E48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `kot_card_game`
--
ALTER TABLE `kot_card_game`
  ADD CONSTRAINT `FK_937D0B3299E6F5DF` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `FK_937D0B32CE77C980` FOREIGN KEY (`kot_card_id`) REFERENCES `kot_card` (`id`),
  ADD CONSTRAINT `FK_937D0B32E48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`);

--
-- Contraintes pour la table `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `FK_8F3F68C599E6F5DF` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `FK_8F3F68C5DE6C20EC` FOREIGN KEY (`next_player_id`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `FK_8F3F68C5E48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`);

--
-- Contraintes pour la table `monster`
--
ALTER TABLE `monster`
  ADD CONSTRAINT `FK_245EC6F4D8177B3F` FOREIGN KEY (`box_id`) REFERENCES `box` (`id`);

--
-- Contraintes pour la table `player`
--
ALTER TABLE `player`
  ADD CONSTRAINT `FK_98197A656060DF5C` FOREIGN KEY (`last_log_id`) REFERENCES `log` (`id`),
  ADD CONSTRAINT `FK_98197A65A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_98197A65C5FF1223` FOREIGN KEY (`monster_id`) REFERENCES `monster` (`id`),
  ADD CONSTRAINT `FK_98197A65E48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`);

--
-- Contraintes pour la table `resolve_order`
--
ALTER TABLE `resolve_order`
  ADD CONSTRAINT `FK_E534A81899E6F5DF` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

--
-- Contraintes pour la table `rule`
--
ALTER TABLE `rule`
  ADD CONSTRAINT `FK_46D8ACCCD8177B3F` FOREIGN KEY (`box_id`) REFERENCES `box` (`id`);

--
-- Contraintes pour la table `throw_tokyo_dice`
--
ALTER TABLE `throw_tokyo_dice`
  ADD CONSTRAINT `FK_B8EF9EEA99E6F5DF` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
