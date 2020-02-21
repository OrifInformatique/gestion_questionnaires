-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  ven. 21 fév. 2020 à 09:16
-- Version du serveur :  10.1.35-MariaDB
-- Version de PHP :  7.2.9

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
-- Structure de la table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `t_answer_distribution`
--

CREATE TABLE `t_answer_distribution` (
  `ID` int(11) NOT NULL,
  `FK_Question` int(11) NOT NULL,
  `Question_Part` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Answer_Part` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_cloze_text`
--

CREATE TABLE `t_cloze_text` (
  `ID` int(11) NOT NULL,
  `FK_Question` int(11) NOT NULL,
  `Cloze_Text` text COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
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
  `Creation_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_free_answer`
--

CREATE TABLE `t_free_answer` (
  `ID` int(11) NOT NULL,
  `FK_Question` int(11) NOT NULL,
  `Answer` text COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_multiple_answer`
--

CREATE TABLE `t_multiple_answer` (
  `ID` int(11) NOT NULL,
  `FK_Question` int(11) NOT NULL,
  `Answer` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
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
  `Creation_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
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
  `Creation_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
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
  `Creation_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Archive` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_questionnaire`
--

CREATE TABLE `t_questionnaire` (
  `ID` int(11) NOT NULL,
  `Questionnaire_Name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Questionnaire_Subtitle` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `PDF` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Corrige_PDF` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Creation_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_questionnaire_model`
--

CREATE TABLE `t_questionnaire_model` (
  `ID` int(11) NOT NULL,
  `Base_Name` varchar(100) NOT NULL,
  `Questionnaire_Name` varchar(100) NOT NULL,
  `Questionnaire_Subtitle` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `t_questionnaire_model_topic`
--

CREATE TABLE `t_questionnaire_model_topic` (
  `ID` int(11) NOT NULL,
  `FK_Questionnaire_Model` int(11) NOT NULL,
  `FK_Topic` int(11) NOT NULL,
  `Nb_Topic_Questions` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `Type_Name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Archive` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `t_question_type`
--

INSERT INTO `t_question_type` (`ID`, `Type_Name`, `Archive`) VALUES
(1, 'Choix multiples', 0),
(2, 'Réponses multiples', 0),
(3, 'Distribution de réponses', 1),
(4, 'Texte à trous', 0),
(5, 'Tableaux', 1),
(6, 'Réponse libre', 0),
(7, 'Image avec repères', 0);

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
  `Creation_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_topic`
--

CREATE TABLE `t_topic` (
  `ID` int(11) NOT NULL,
  `FK_Parent_Topic` int(11) DEFAULT NULL,
  `Topic` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Archive` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `fk_user_type` int(11) NOT NULL,
  `username` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `archive` tinyint(4) NOT NULL DEFAULT '0',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_type`
--

CREATE TABLE `user_type` (
  `id` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `access_level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `user_type`
--

INSERT INTO `user_type` (`id`, `name`, `access_level`) VALUES
(1, 'Administrator', 4),
(2, 'Member', 1),
(3, 'Manager', 2);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`id`);

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
-- Index pour la table `t_questionnaire_model`
--
ALTER TABLE `t_questionnaire_model`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `t_questionnaire_model_topic`
--
ALTER TABLE `t_questionnaire_model_topic`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_Questionnaire_Model` (`FK_Questionnaire_Model`),
  ADD KEY `FK_Topic` (`FK_Topic`);

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
-- Index pour la table `t_topic`
--
ALTER TABLE `t_topic`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_t_topic_t_topic1_idx` (`FK_Parent_Topic`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_t_user_t_user_type1_idx` (`fk_user_type`);

--
-- Index pour la table `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_cloze_text_answer`
--
ALTER TABLE `t_cloze_text_answer`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_free_answer`
--
ALTER TABLE `t_free_answer`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_multiple_answer`
--
ALTER TABLE `t_multiple_answer`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_multiple_choice`
--
ALTER TABLE `t_multiple_choice`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_picture_landmark`
--
ALTER TABLE `t_picture_landmark`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_question`
--
ALTER TABLE `t_question`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_questionnaire`
--
ALTER TABLE `t_questionnaire`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_questionnaire_model`
--
ALTER TABLE `t_questionnaire_model`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_questionnaire_model_topic`
--
ALTER TABLE `t_questionnaire_model_topic`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_question_questionnaire`
--
ALTER TABLE `t_question_questionnaire`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_table_cell`
--
ALTER TABLE `t_table_cell`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `t_topic`
--
ALTER TABLE `t_topic`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_type`
--
ALTER TABLE `user_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- Contraintes pour la table `t_questionnaire_model_topic`
--
ALTER TABLE `t_questionnaire_model_topic`
  ADD CONSTRAINT `t_questionnaire_model_topic_ibfk_1` FOREIGN KEY (`FK_Topic`) REFERENCES `t_topic` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `t_questionnaire_model_topic_ibfk_2` FOREIGN KEY (`FK_Questionnaire_Model`) REFERENCES `t_questionnaire_model` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

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
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_t_user_t_user_type1` FOREIGN KEY (`FK_User_Type`) REFERENCES `user_type` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
