-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mer 31 Août 2016 à 16:12
-- Version du serveur :  5.6.15-log
-- Version de PHP :  5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `gestion_questionnaires`
--

-- --------------------------------------------------------

--
-- Structure de la table `t_answer_distribution`
--

CREATE TABLE IF NOT EXISTS `t_answer_distribution` (
  `ID` int(11) NOT NULL,
  `FK_Question` int(11) NOT NULL,
  `Question_Part` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Answer_Part` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` int(11) NOT NULL,
  PRIMARY KEY (`ID`,`FK_Question`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_cloze_text`
--

CREATE TABLE IF NOT EXISTS `t_cloze_text` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` int(11) NOT NULL,
  `Cloze_Text` text COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime NOT NULL,
  PRIMARY KEY (`ID`,`FK_Question`),
  KEY `fk_t_cloze_text_t_question1_idx` (`FK_Question`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `t_cloze_text_answer`
--

CREATE TABLE IF NOT EXISTS `t_cloze_text_answer` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Cloze_Text` int(11) NOT NULL,
  `Answer` text COLLATE utf8_unicode_ci NOT NULL,
  `Answer_Order` int(11) NOT NULL,
  `Creation_Date` datetime NOT NULL,
  PRIMARY KEY (`ID`,`FK_Cloze_Text`),
  KEY `fk_t_cloze_text_answer_t_cloze_text1_idx` (`FK_Cloze_Text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `t_free_answer`
--

CREATE TABLE IF NOT EXISTS `t_free_answer` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` int(11) NOT NULL,
  `Answer` text COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime NOT NULL,
  PRIMARY KEY (`ID`,`FK_Question`),
  KEY `fk_t_free_answer_t_question1_idx` (`FK_Question`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `t_module`
--

CREATE TABLE IF NOT EXISTS `t_module` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Number` int(11) NOT NULL,
  `Creation_Date` datetime NOT NULL,
  `Archive` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `t_multiple_answer`
--

CREATE TABLE IF NOT EXISTS `t_multiple_answer` (
  `ID` int(11) NOT NULL,
  `FK_Question` int(11) NOT NULL,
  `Answer` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime NOT NULL,
  PRIMARY KEY (`FK_Question`,`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_multiple_choice`
--

CREATE TABLE IF NOT EXISTS `t_multiple_choice` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` int(11) NOT NULL,
  `Answer` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Valid` tinyint(1) NOT NULL,
  `Creation_Date` datetime NOT NULL,
  PRIMARY KEY (`ID`,`FK_Question`),
  KEY `fk_t_multiple_choice_t_question1_idx` (`FK_Question`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `t_picture_landmark`
--

CREATE TABLE IF NOT EXISTS `t_picture_landmark` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` int(11) NOT NULL,
  `Symbol` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `Answer` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime NOT NULL,
  PRIMARY KEY (`ID`,`FK_Question`),
  KEY `fk_t_picture_landmark_t_question1_idx` (`FK_Question`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `t_question`
--

CREATE TABLE IF NOT EXISTS `t_question` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Topic` int(11) NOT NULL,
  `FK_Question_Type` int(11) NOT NULL,
  `Question` text COLLATE utf8_unicode_ci NOT NULL,
  `Nb_Desired_Answers` int(11) NOT NULL,
  `Table_With_Definition` tinyint(1) NOT NULL,
  `Picture_Name` text COLLATE utf8_unicode_ci NOT NULL,
  `Points` int(11) NOT NULL,
  `Creation_Date` datetime NOT NULL,
  PRIMARY KEY (`ID`,`FK_Topic`,`FK_Question_Type`),
  KEY `fk_t_question_t_question_type1_idx` (`FK_Question_Type`),
  KEY `fk_t_question_t_topic1_idx` (`FK_Topic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `t_questionnaire`
--

CREATE TABLE IF NOT EXISTS `t_questionnaire` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Questionnaire_Name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `PDF` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Corrige_PDF` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `t_question_questionnaire`
--

CREATE TABLE IF NOT EXISTS `t_question_questionnaire` (
  `FK_Question` int(11) NOT NULL,
  `FK_Questionnaire` int(11) NOT NULL,
  PRIMARY KEY (`FK_Question`,`FK_Questionnaire`),
  KEY `fk_t_question_has_t_questionnaire_t_questionnaire1_idx` (`FK_Questionnaire`),
  KEY `fk_t_question_has_t_questionnaire_t_question1_idx` (`FK_Question`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_question_type`
--

CREATE TABLE IF NOT EXISTS `t_question_type` (
  `ID` int(11) NOT NULL,
  `Type_Name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_table_cell`
--

CREATE TABLE IF NOT EXISTS `t_table_cell` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` int(11) NOT NULL,
  `Content` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Column_Nb` int(11) NOT NULL,
  `Row_Nb` int(11) NOT NULL,
  `Header` tinyint(1) NOT NULL,
  `Display_In_Question` tinyint(1) NOT NULL,
  `Creation_Date` datetime NOT NULL,
  PRIMARY KEY (`ID`,`FK_Question`),
  KEY `fk_t_table_cell_t_question_idx` (`FK_Question`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `t_temp_questionnaire`
--

CREATE TABLE IF NOT EXISTS `t_temp_questionnaire` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Question` int(11) NOT NULL,
  `Question` text COLLATE utf8_unicode_ci NOT NULL,
  `Picture_Path` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Content` text COLLATE utf8_unicode_ci NOT NULL,
  `Answer_Zone` text COLLATE utf8_unicode_ci NOT NULL,
  `Points` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `t_temp_questionnaire_answer`
--

CREATE TABLE IF NOT EXISTS `t_temp_questionnaire_answer` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Question` int(11) NOT NULL,
  `Question` text COLLATE utf8_unicode_ci NOT NULL,
  `Content` text COLLATE utf8_unicode_ci NOT NULL,
  `Picture_Path` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Picture_Answers` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Answer` text COLLATE utf8_unicode_ci NOT NULL,
  `Points` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `t_topic`
--

CREATE TABLE IF NOT EXISTS `t_topic` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Module` int(11) NOT NULL,
  `Topic` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` int(11) NOT NULL,
  `Archive` int(11) NOT NULL,
  PRIMARY KEY (`ID`,`FK_Module`),
  KEY `fk_t_topic_t_module1_idx` (`FK_Module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
