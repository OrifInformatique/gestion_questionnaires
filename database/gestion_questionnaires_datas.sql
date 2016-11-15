-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mar 15 Novembre 2016 à 11:14
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

--
-- Contenu de la table `t_questionnaire`
--

INSERT INTO `t_questionnaire` (`ID`, `Questionnaire_Name`, `PDF`, `Corrige_PDF`, `Creation_Date`) VALUES
(1, 'Questionnaire1', 'voicilepdf', 'coivilecorrige', '2016-11-08 00:00:00'),
(3, 'Questionnaire2', 'voicilepdf', 'coivilecorrige', '2016-11-08 00:00:00');

--
-- Contenu de la table `t_user_type`
--

INSERT INTO `t_user_type` (`id`, `name`, `access_level`) VALUES
(1, 'administrator', 2),
(2, 'members', 1);

--
-- Contenu de la table `t_user`
--

INSERT INTO `t_user` (`id`, `user_type`, `user`, `password`) VALUES
(1, 1, 'user1', '$2y$10$KLWXNi8NdBCWG.zme9nvN.4b3W9dIzWxgd4XfP2Ylbu5D2jctGta2'),
(2, 2, 'user2', '$2y$10$SVQwDrrKwlcRtGTZ6mTnh.ajB3CJBfVXKSRsc5AUdiFXPqqq9S1Im');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
