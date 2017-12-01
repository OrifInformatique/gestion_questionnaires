-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  jeu. 16 nov. 2017 à 15:24
-- Version du serveur :  10.1.28-MariaDB
-- Version de PHP :  7.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `gestion_questionnaires`
--

-- --------------------------------------------------------

--
-- Structure de la table `t_answer_distribution`
--

CREATE TABLE `t_answer_distribution` (
  `ID` int(11) NOT NULL,
  `FK_Question` int(11) NOT NULL,
  `Question_Part` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Answer_Part` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_cloze_text`
--

CREATE TABLE `t_cloze_text` (
  `ID` int(11) NOT NULL,
  `FK_Question` int(11) NOT NULL,
  `Cloze_Text` text COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_cloze_text_answer`
--

CREATE TABLE `t_cloze_text_answer` (
  `ID` int(11) NOT NULL,
  `FK_Cloze_Text` int(11) NOT NULL,
  `Answer` text COLLATE utf8_unicode_ci NOT NULL,
  `Answer_Order` int(11) DEFAULT NULL,
  `Creation_Date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_free_answer`
--

CREATE TABLE `t_free_answer` (
  `ID` int(11) NOT NULL,
  `FK_Question` int(11) NOT NULL,
  `Answer` text COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_multiple_answer`
--

CREATE TABLE `t_multiple_answer` (
  `ID` int(11) NOT NULL,
  `FK_Question` int(11) NOT NULL,
  `Answer` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_multiple_choice`
--

CREATE TABLE `t_multiple_choice` (
  `ID` int(11) NOT NULL,
  `FK_Question` int(11) NOT NULL,
  `Answer` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Valid` tinyint(1) DEFAULT NULL,
  `Creation_Date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_picture_landmark`
--

CREATE TABLE `t_picture_landmark` (
  `ID` int(11) NOT NULL,
  `FK_Question` int(11) NOT NULL,
  `Symbol` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `Answer` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_question`
--

CREATE TABLE `t_question` (
  `ID` int(11) NOT NULL,
  `FK_Topic` int(11) NOT NULL,
  `FK_Question_Type` int(11) NOT NULL,
  `Question` text COLLATE utf8_unicode_ci NOT NULL,
  `Nb_Desired_Answers` int(11) DEFAULT NULL,
  `Table_With_Definition` tinyint(1) DEFAULT NULL,
  `Picture_Name` text COLLATE utf8_unicode_ci,
  `Points` int(11) DEFAULT NULL,
  `Creation_Date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_questionnaire`
--

CREATE TABLE `t_questionnaire` (
  `ID` int(11) NOT NULL,
  `Questionnaire_Name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `PDF` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Corrige_PDF` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Creation_Date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_question_questionnaire`
--

CREATE TABLE `t_question_questionnaire` (
  `ID` int(11) NOT NULL,
  `FK_Question` int(11) NOT NULL,
  `FK_Questionnaire` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_question_type`
--

CREATE TABLE `t_question_type` (
  `ID` int(11) NOT NULL,
  `Type_Name` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `t_question_type`
--

INSERT INTO `t_question_type` (`ID`, `Type_Name`) VALUES
(1, 'ChoixMultiples'),
(2, 'ReponsesMultiples'),
(3, 'DistributionReponses'),
(4, 'TexteATrous'),
(5, 'Tableaux'),
(6, 'ReponsesLibre'),
(7, 'ImageReperes');

-- --------------------------------------------------------

--
-- Structure de la table `t_table_cell`
--

CREATE TABLE `t_table_cell` (
  `ID` int(11) NOT NULL,
  `FK_Question` int(11) NOT NULL,
  `Content` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Column_Nb` int(11) DEFAULT NULL,
  `Row_Nb` int(11) DEFAULT NULL,
  `Header` tinyint(1) DEFAULT NULL,
  `Display_In_Question` tinyint(1) DEFAULT NULL,
  `Creation_Date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_temp_questionnaire`
--

CREATE TABLE `t_temp_questionnaire` (
  `ID` int(11) NOT NULL,
  `ID_Question` int(11) NOT NULL,
  `Question` text COLLATE utf8_unicode_ci NOT NULL,
  `Picture_Path` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Content` text COLLATE utf8_unicode_ci NOT NULL,
  `Answer_Zone` text COLLATE utf8_unicode_ci NOT NULL,
  `Points` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_temp_questionnaire_answer`
--

CREATE TABLE `t_temp_questionnaire_answer` (
  `ID` int(11) NOT NULL,
  `ID_Question` int(11) NOT NULL,
  `Question` text COLLATE utf8_unicode_ci NOT NULL,
  `Content` text COLLATE utf8_unicode_ci NOT NULL,
  `Picture_Path` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Picture_Answers` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Answer` text COLLATE utf8_unicode_ci NOT NULL,
  `Points` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_topic`
--

CREATE TABLE `t_topic` (
  `ID` int(11) NOT NULL,
  `FK_Parent_Topic` int(11) DEFAULT NULL,
  `Topic` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime DEFAULT CURRENT_TIMESTAMP,
  `Archive` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `t_topic`
--

INSERT INTO `t_topic` (`ID`, `FK_Parent_Topic`, `Topic`, `Creation_Date`, `Archive`) VALUES
(1, NULL, 'Anatomie', NULL, NULL),
(85, 1, 'Appareil génital', '2017-11-16 09:06:10', NULL),
(86, 1, 'Appareil digestif', '2017-11-16 09:23:12', NULL),
(87, 1, 'Appareil urinaire', '2017-11-16 09:41:26', NULL),
(88, 1, 'Appareil cardiovasculaire', '2017-11-16 09:41:58', NULL),
(89, 1, 'Appareil respiratoire', '2017-11-16 09:43:44', NULL),
(90, 1, 'Système nerveux', '2017-11-16 09:43:44', NULL),
(91, 1, 'Introduction', '2017-11-16 10:16:08', NULL),
(92, 1, 'Appareil locomoteur', '2017-11-16 10:22:11', NULL),
(93, 1, 'Système immunitaire', '2017-11-16 11:52:02', NULL),
(94, 1, 'Système endocrinien', '2017-11-16 11:53:10', NULL),
(95, 1, 'Organe des sens', '2017-11-16 11:56:48', NULL),
(96, 1, 'Cellules Tissus', '2017-11-16 11:56:48', NULL),
(97, NULL, 'Dactylographie', '2017-11-16 11:58:04', NULL),
(98, 97, 'Partie 2a: Rubriques d\'un rapport médical ou types - Simple', '2017-11-16 13:18:59', NULL),
(99, 97, 'Partie 2a: Rubriques d\'un rapport médical ou types - Compliqué', '2017-11-16 13:20:10', NULL),
(100, 97, 'Partie 2b: Applications informatiques', '2017-11-16 13:20:30', NULL),
(101, 97, 'ANNEXES', '2017-11-16 13:21:17', NULL),
(102, NULL, 'Administration et organisation du cabinet', '2017-11-16 13:21:27', NULL),
(103, 102, 'Cabinet médical - simple', '2017-11-16 13:21:38', NULL),
(104, 102, 'Cabinet médical - complexe', '2017-11-16 13:33:04', NULL),
(105, 102, 'Gestion de RDV et d\'appels téléphonique - simple', '2017-11-16 13:33:28', NULL),
(106, 102, 'Gestion de RDV et d\'appels téléphonique - complexe', '2017-11-16 13:37:07', NULL),
(107, 102, 'Gestion de RDV - complexe', '2017-11-16 13:37:07', NULL),
(108, 102, 'Classer / Archiver - simple', '2017-11-16 13:37:31', NULL),
(109, 102, 'Classer / Archiver - complexe', '2017-11-16 13:37:31', NULL),
(110, 102, 'Comptabilité - simple', '2017-11-16 13:38:16', NULL),
(111, 102, 'Comptabilité - complexe', '2017-11-16 13:38:16', NULL),
(112, 102, 'Hygiène - complexe', '2017-11-16 13:38:44', NULL),
(113, 102, 'Comptabilité - multiples', '2017-11-16 13:39:17', NULL),
(114, 102, 'Cabinet médical - multiples', '2017-11-16 13:39:17', NULL),
(115, 102, 'Saisie rendez-vous et modification rendez-vous - multiples', '2017-11-16 13:40:56', NULL),
(116, 102, 'Classer / Archiver - multiples', '2017-11-16 13:40:56', NULL),
(117, 102, 'ANNEXES', '2017-11-16 13:41:29', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `t_user`
--

CREATE TABLE `t_user` (
  `ID` int(11) NOT NULL,
  `FK_User_Type` int(11) NOT NULL,
  `User` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `t_user`
--

INSERT INTO `t_user` (`ID`, `FK_User_Type`, `User`, `Password`) VALUES
(1, 2, 'admin', '$2y$10$aEqREuXq.4BgSq6ZJrfWre2FZEeOKvh8BIadXX3Hix0vzubqdA.ja'),
(2, 1, 'user', '$2y$10$aEqREuXq.4BgSq6ZJrfWre2FZEeOKvh8BIadXX3Hix0vzubqdA.ja');

-- --------------------------------------------------------

--
-- Structure de la table `t_user_type`
--

CREATE TABLE `t_user_type` (
  `ID` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `access_level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `t_user_type`
--

INSERT INTO `t_user_type` (`ID`, `name`, `access_level`) VALUES
(1, 'Administrator', 2),
(2, 'Member', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `t_answer_distribution`
--
ALTER TABLE `t_answer_distribution`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_t_answer_distribution_t_question1` (`FK_Question`);

--
-- Index pour la table `t_cloze_text`
--
ALTER TABLE `t_cloze_text`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_t_cloze_text_t_question1_idx` (`FK_Question`);

--
-- Index pour la table `t_cloze_text_answer`
--
ALTER TABLE `t_cloze_text_answer`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_t_cloze_text_answer_t_cloze_text1_idx` (`FK_Cloze_Text`);

--
-- Index pour la table `t_free_answer`
--
ALTER TABLE `t_free_answer`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_t_free_answer_t_question1_idx` (`FK_Question`);

--
-- Index pour la table `t_multiple_answer`
--
ALTER TABLE `t_multiple_answer`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_t_multiple_answer_t_question1` (`FK_Question`);

--
-- Index pour la table `t_multiple_choice`
--
ALTER TABLE `t_multiple_choice`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_t_multiple_choice_t_question1_idx` (`FK_Question`);

--
-- Index pour la table `t_picture_landmark`
--
ALTER TABLE `t_picture_landmark`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_t_picture_landmark_t_question1_idx` (`FK_Question`);

--
-- Index pour la table `t_question`
--
ALTER TABLE `t_question`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_t_question_t_question_type1_idx` (`FK_Question_Type`),
  ADD KEY `fk_t_question_t_topic1_idx` (`FK_Topic`);

--
-- Index pour la table `t_questionnaire`
--
ALTER TABLE `t_questionnaire`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `t_question_questionnaire`
--
ALTER TABLE `t_question_questionnaire`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_t_question_has_t_questionnaire_t_questionnaire1_idx` (`FK_Questionnaire`),
  ADD KEY `fk_t_question_has_t_questionnaire_t_question1_idx` (`FK_Question`);

--
-- Index pour la table `t_question_type`
--
ALTER TABLE `t_question_type`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `t_table_cell`
--
ALTER TABLE `t_table_cell`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_t_table_cell_t_question_idx` (`FK_Question`);

--
-- Index pour la table `t_temp_questionnaire`
--
ALTER TABLE `t_temp_questionnaire`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `t_temp_questionnaire_answer`
--
ALTER TABLE `t_temp_questionnaire_answer`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `t_topic`
--
ALTER TABLE `t_topic`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_t_topic_t_topic1_idx` (`FK_Parent_Topic`);

--
-- Index pour la table `t_user`
--
ALTER TABLE `t_user`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_t_user_t_user_type1_idx` (`FK_User_Type`);

--
-- Index pour la table `t_user_type`
--
ALTER TABLE `t_user_type`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `t_answer_distribution`
--
ALTER TABLE `t_answer_distribution`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_cloze_text`
--
ALTER TABLE `t_cloze_text`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `t_cloze_text_answer`
--
ALTER TABLE `t_cloze_text_answer`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT pour la table `t_free_answer`
--
ALTER TABLE `t_free_answer`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=317;

--
-- AUTO_INCREMENT pour la table `t_multiple_answer`
--
ALTER TABLE `t_multiple_answer`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT pour la table `t_multiple_choice`
--
ALTER TABLE `t_multiple_choice`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT pour la table `t_picture_landmark`
--
ALTER TABLE `t_picture_landmark`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=280;

--
-- AUTO_INCREMENT pour la table `t_question`
--
ALTER TABLE `t_question`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=456;

--
-- AUTO_INCREMENT pour la table `t_questionnaire`
--
ALTER TABLE `t_questionnaire`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `t_question_questionnaire`
--
ALTER TABLE `t_question_questionnaire`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT pour la table `t_table_cell`
--
ALTER TABLE `t_table_cell`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_temp_questionnaire`
--
ALTER TABLE `t_temp_questionnaire`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_temp_questionnaire_answer`
--
ALTER TABLE `t_temp_questionnaire_answer`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_topic`
--
ALTER TABLE `t_topic`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT pour la table `t_user`
--
ALTER TABLE `t_user`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `t_user_type`
--
ALTER TABLE `t_user_type`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `t_answer_distribution`
--
ALTER TABLE `t_answer_distribution`
  ADD CONSTRAINT `fk_t_answer_distribution_t_question1` FOREIGN KEY (`FK_Question`) REFERENCES `t_question` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `t_cloze_text`
--
ALTER TABLE `t_cloze_text`
  ADD CONSTRAINT `fk_t_cloze_text_t_question1` FOREIGN KEY (`FK_Question`) REFERENCES `t_question` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `t_cloze_text_answer`
--
ALTER TABLE `t_cloze_text_answer`
  ADD CONSTRAINT `fk_t_cloze_text_answer_t_cloze_text1` FOREIGN KEY (`FK_Cloze_Text`) REFERENCES `t_cloze_text` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `t_free_answer`
--
ALTER TABLE `t_free_answer`
  ADD CONSTRAINT `fk_t_free_answer_t_question1` FOREIGN KEY (`FK_Question`) REFERENCES `t_question` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `t_multiple_answer`
--
ALTER TABLE `t_multiple_answer`
  ADD CONSTRAINT `fk_t_multiple_answer_t_question1` FOREIGN KEY (`FK_Question`) REFERENCES `t_question` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `t_multiple_choice`
--
ALTER TABLE `t_multiple_choice`
  ADD CONSTRAINT `fk_t_multiple_choice_t_question1` FOREIGN KEY (`FK_Question`) REFERENCES `t_question` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `t_picture_landmark`
--
ALTER TABLE `t_picture_landmark`
  ADD CONSTRAINT `fk_t_picture_landmark_t_question1` FOREIGN KEY (`FK_Question`) REFERENCES `t_question` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `t_question`
--
ALTER TABLE `t_question`
  ADD CONSTRAINT `fk_t_question_t_question_type1` FOREIGN KEY (`FK_Question_Type`) REFERENCES `t_question_type` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_t_question_t_topic1` FOREIGN KEY (`FK_Topic`) REFERENCES `t_topic` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `t_question_questionnaire`
--
ALTER TABLE `t_question_questionnaire`
  ADD CONSTRAINT `fk_t_question_has_t_questionnaire_t_question1` FOREIGN KEY (`FK_Question`) REFERENCES `t_question` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_t_question_has_t_questionnaire_t_questionnaire1` FOREIGN KEY (`FK_Questionnaire`) REFERENCES `t_questionnaire` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `t_table_cell`
--
ALTER TABLE `t_table_cell`
  ADD CONSTRAINT `fk_t_table_cell_t_question` FOREIGN KEY (`FK_Question`) REFERENCES `t_question` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `t_topic`
--
ALTER TABLE `t_topic`
  ADD CONSTRAINT `fk_t_topic_t_topic1` FOREIGN KEY (`FK_Parent_Topic`) REFERENCES `t_topic` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `t_user`
--
ALTER TABLE `t_user`
  ADD CONSTRAINT `fk_t_user_t_user_type1` FOREIGN KEY (`FK_User_Type`) REFERENCES `t_user_type` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
