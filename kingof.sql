-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  jeu. 19 déc. 2019 à 09:14
-- Version du serveur :  5.7.26
-- Version de PHP :  7.2.18

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
  PRIMARY KEY (`id`),
  KEY `IDX_232B318C77E5854A` (`mode_id`),
  KEY `IDX_232B318CE7EC5785` (`board_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `game`
--

INSERT INTO `game` (`id`, `mode_id`, `board_id`, `name`, `state`, `monsters_select`, `max_players`) VALUES
(1, 1, 1, 'Partie de sky', 2, 0, 6),
(2, 1, 1, 'Partie de sky', 1, 0, 6);

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

--
-- Déchargement des données de la table `game_monster`
--

INSERT INTO `game_monster` (`game_id`, `monster_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 16),
(1, 17),
(1, 19),
(1, 20),
(1, 21),
(1, 23),
(1, 29),
(1, 30),
(2, 1),
(2, 2),
(2, 3),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 16),
(2, 17),
(2, 19),
(2, 20),
(2, 21),
(2, 23),
(2, 29),
(2, 30);

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
-- Structure de la table `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `is_done` tinyint(1) NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8F3F68C5E48FD905` (`game_id`),
  KEY `IDX_8F3F68C599E6F5DF` (`player_id`),
  KEY `is_done` (`is_done`)
) ENGINE=InnoDB AUTO_INCREMENT=645 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `log`
--

INSERT INTO `log` (`id`, `game_id`, `player_id`, `is_done`, `action`) VALUES
(311, 1, 1, 1, 'start_turn'),
(526, 1, 1, 1, 'is_starting_in_city'),
(527, 1, 1, 1, 'init_dices'),
(528, 1, 1, 1, 'throw_dices'),
(529, 1, 1, 1, 'end_turn'),
(530, 1, 2, 1, 'start_turn'),
(531, 1, 2, 1, 'is_starting_in_city'),
(532, 1, 2, 1, 'init_dices'),
(533, 1, 2, 1, 'throw_dices'),
(534, 1, 2, 1, 'end_turn'),
(535, 1, 1, 1, 'start_turn'),
(536, 1, 1, 1, 'is_starting_in_city'),
(537, 1, 1, 1, 'init_dices'),
(538, 1, 1, 1, 'throw_dices'),
(539, 1, 1, 1, 'end_turn'),
(540, 1, 2, 1, 'start_turn'),
(541, 1, 2, 1, 'is_starting_in_city'),
(542, 1, 2, 1, 'init_dices'),
(543, 1, 2, 1, 'throw_dices'),
(544, 1, 2, 1, 'end_turn'),
(545, 1, 1, 1, 'start_turn'),
(546, 1, 1, 1, 'is_starting_in_city'),
(547, 1, 1, 1, 'init_dices'),
(548, 1, 1, 1, 'throw_dices'),
(549, 1, 1, 1, 'end_turn'),
(550, 1, 2, 1, 'start_turn'),
(551, 1, 2, 1, 'is_starting_in_city'),
(552, 1, 2, 1, 'init_dices'),
(553, 1, 2, 1, 'throw_dices'),
(554, 1, 2, 1, 'end_turn'),
(555, 1, 1, 1, 'start_turn'),
(556, 1, 1, 1, 'is_starting_in_city'),
(557, 1, 1, 1, 'init_dices'),
(558, 1, 1, 1, 'throw_dices'),
(559, 1, 1, 1, 'end_turn'),
(560, 1, 2, 1, 'start_turn'),
(561, 1, 2, 1, 'is_starting_in_city'),
(562, 1, 2, 1, 'init_dices'),
(563, 1, 2, 1, 'throw_dices'),
(564, 1, 2, 1, 'end_turn'),
(565, 1, 1, 1, 'start_turn'),
(566, 1, 1, 1, 'is_starting_in_city'),
(567, 1, 1, 1, 'init_dices'),
(568, 1, 1, 1, 'throw_dices'),
(569, 1, 1, 1, 'end_turn'),
(570, 1, 2, 1, 'start_turn'),
(571, 1, 2, 1, 'is_starting_in_city'),
(572, 1, 2, 1, 'init_dices'),
(573, 1, 2, 1, 'throw_dices'),
(574, 1, 2, 1, 'end_turn'),
(575, 1, 1, 1, 'start_turn'),
(576, 1, 1, 1, 'is_starting_in_city'),
(577, 1, 1, 1, 'init_dices'),
(578, 1, 1, 1, 'throw_dices'),
(579, 1, 1, 1, 'end_turn'),
(580, 1, 2, 1, 'start_turn'),
(581, 1, 2, 1, 'is_starting_in_city'),
(582, 1, 2, 1, 'init_dices'),
(583, 1, 2, 1, 'throw_dices'),
(584, 1, 2, 1, 'end_turn'),
(585, 1, 1, 1, 'start_turn'),
(586, 1, 1, 1, 'is_starting_in_city'),
(587, 1, 1, 1, 'init_dices'),
(588, 1, 1, 1, 'throw_dices'),
(589, 1, 1, 1, 'end_turn'),
(590, 1, 2, 1, 'start_turn'),
(591, 1, 2, 1, 'is_starting_in_city'),
(592, 1, 2, 1, 'init_dices'),
(593, 1, 2, 1, 'throw_dices'),
(594, 1, 2, 1, 'end_turn'),
(595, 1, 1, 1, 'start_turn'),
(596, 1, 1, 1, 'is_starting_in_city'),
(597, 1, 1, 1, 'init_dices'),
(598, 1, 1, 1, 'throw_dices'),
(599, 1, 1, 1, 'end_turn'),
(600, 1, 2, 1, 'start_turn'),
(601, 1, 2, 1, 'is_starting_in_city'),
(602, 1, 2, 1, 'init_dices'),
(603, 1, 2, 1, 'throw_dices'),
(604, 1, 2, 1, 'end_turn'),
(605, 1, 1, 1, 'start_turn'),
(606, 1, 1, 1, 'is_starting_in_city'),
(607, 1, 1, 1, 'init_dices'),
(608, 1, 1, 1, 'throw_dices'),
(609, 1, 1, 1, 'end_turn'),
(610, 1, 2, 1, 'start_turn'),
(611, 1, 2, 1, 'is_starting_in_city'),
(612, 1, 2, 1, 'init_dices'),
(613, 1, 2, 1, 'throw_dices'),
(614, 1, 2, 1, 'end_turn'),
(615, 1, 1, 1, 'start_turn'),
(616, 1, 1, 1, 'is_starting_in_city'),
(617, 1, 1, 1, 'init_dices'),
(618, 1, 1, 1, 'throw_dices'),
(619, 1, 1, 1, 'end_turn'),
(620, 1, 2, 1, 'start_turn'),
(621, 1, 2, 1, 'is_starting_in_city'),
(622, 1, 2, 1, 'init_dices'),
(623, 1, 2, 1, 'throw_dices'),
(624, 1, 2, 1, 'end_turn'),
(625, 1, 1, 1, 'start_turn'),
(626, 1, 1, 1, 'is_starting_in_city'),
(627, 1, 1, 1, 'init_dices'),
(628, 1, 1, 1, 'throw_dices'),
(629, 1, 1, 1, 'end_turn'),
(630, 1, 2, 1, 'start_turn'),
(631, 1, 2, 1, 'is_starting_in_city'),
(632, 1, 2, 1, 'init_dices'),
(633, 1, 2, 1, 'throw_dices'),
(634, 1, 2, 1, 'end_turn'),
(635, 1, 1, 1, 'start_turn'),
(636, 1, 1, 1, 'is_starting_in_city'),
(637, 1, 1, 1, 'init_dices'),
(638, 1, 1, 1, 'throw_dices'),
(639, 1, 1, 1, 'end_turn'),
(640, 1, 2, 1, 'start_turn'),
(641, 1, 2, 1, 'is_starting_in_city'),
(642, 1, 2, 1, 'init_dices'),
(643, 1, 2, 1, 'throw_dices'),
(644, 1, 2, 0, 'end_turn');

-- --------------------------------------------------------

--
-- Structure de la table `migration_versions`
--

DROP TABLE IF EXISTS `migration_versions`;
CREATE TABLE IF NOT EXISTS `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20191213143258', '2019-12-13 14:33:06'),
('20191213152855', '2019-12-13 15:29:03'),
('20191213153111', '2019-12-13 15:31:16'),
('20191214024826', '2019-12-14 02:48:37');

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
(11, 9, 'Kong', 'kong.png', 0),
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
(27, 11, 'Draccus', 'draccus.png', 0),
(28, 11, 'Iron Rook', 'iron-rook.png', 0),
(29, 11, 'Kookie', 'kookie.png', 1),
(30, 11, 'Lollybot', 'lollybot.png', 1),
(31, 11, 'Orange Death', 'orange-death.png', 0),
(32, 11, 'Pouic', 'pouic.png', 0),
(33, 11, 'Rozy Pony', 'rozy-pony.png', 0),
(34, 11, 'X-smash Tree', 'x-smash-tree.png', 0),
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
  `gp` smallint(6) NOT NULL,
  `hp` smallint(6) NOT NULL,
  `hp_max` smallint(6) NOT NULL,
  `nb_dices` smallint(6) NOT NULL,
  `nb_mana` int(11) NOT NULL,
  `turn` smallint(6) NOT NULL,
  `is_playing` tinyint(1) NOT NULL,
  `joined_at` datetime NOT NULL,
  `last_log_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_98197A65C5FF1223` (`monster_id`),
  KEY `IDX_98197A65E48FD905` (`game_id`),
  KEY `IDX_98197A65A76ED395` (`user_id`),
  KEY `IDX_98197A656060DF5C` (`last_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `player`
--

INSERT INTO `player` (`id`, `monster_id`, `game_id`, `user_id`, `creator`, `is_ready`, `is_alive`, `in_city`, `gp`, `hp`, `hp_max`, `nb_dices`, `nb_mana`, `turn`, `is_playing`, `joined_at`, `last_log_id`) VALUES
(1, 23, 1, 1, 1, 1, 1, 0, 0, 10, 10, 6, 0, 1, 0, '2019-12-13 09:08:12', 643),
(2, 3, 1, 2, 0, 1, 1, 1, 12, 10, 10, 8, 0, 2, 1, '2019-12-13 09:08:42', 643);

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
  ADD CONSTRAINT `FK_232B318C77E5854A` FOREIGN KEY (`mode_id`) REFERENCES `mode` (`id`),
  ADD CONSTRAINT `FK_232B318CE7EC5785` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`);

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
-- Contraintes pour la table `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `FK_8F3F68C599E6F5DF` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`),
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
-- Contraintes pour la table `rule`
--
ALTER TABLE `rule`
  ADD CONSTRAINT `FK_46D8ACCCD8177B3F` FOREIGN KEY (`box_id`) REFERENCES `box` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
