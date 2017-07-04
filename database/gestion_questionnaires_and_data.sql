-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  127.0.0.1
-- Généré le :  Mar 04 Juillet 2017 à 09:39
-- Version du serveur :  5.7.14
-- Version de PHP :  5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `gestion_questionnaires`
--
CREATE DATABASE IF NOT EXISTS `gestion_questionnaires` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `gestion_questionnaires`;

-- --------------------------------------------------------

--
-- Structure de la table `t_answer_distribution`
--

CREATE TABLE IF NOT EXISTS `t_answer_distribution` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` int(11) NOT NULL,
  `Question_Part` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Answer_Part` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_t_answer_distribution_t_question1` (`FK_Question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_answer_distribution`
--

TRUNCATE TABLE `t_answer_distribution`;
-- --------------------------------------------------------

--
-- Structure de la table `t_cloze_text`
--

CREATE TABLE IF NOT EXISTS `t_cloze_text` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` int(11) NOT NULL,
  `Cloze_Text` text COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_t_cloze_text_t_question1_idx` (`FK_Question`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_cloze_text`
--

TRUNCATE TABLE `t_cloze_text`;
--
-- Contenu de la table `t_cloze_text`
--

INSERT INTO `t_cloze_text` (`ID`, `FK_Question`, `Cloze_Text`, `Creation_Date`) VALUES
(1, 136, 'Le pharynx est un carrefour […..], il se trouve au-dessus du […..] et de l\' […..], il communique avec la […..] et il es le passage du […..] en chemin vers l\' […..].', '2017-06-21 11:57:44'),
(2, 193, 'Les poumons sont logés dans […..], ils reposent sur un muscle plat, appelé le […..]. Ils sont découpés en […..] par des […..]', '2017-06-22 11:09:27'),
(3, 194, 'Au moment des échanges gazeux respiratoire, […] sort de l’artériol pulmonnaire puis est expiré et [….] est inspiré avant de pénétrer dans le sang.', '2017-06-22 11:09:28'),
(4, 195, 'La difficulté à respirer s\'appelle […]. Elle survient en premier à […] puis également […] lorsqu\'elle s\'aggrave', '2017-06-22 11:09:28'),
(5, 213, 'Le cœur est un organe […..], il est aussi appelé [.....], logé au centre du […..], il fonctionne comme […..] pour faire circuler […..] dans notre corps.', '2017-06-22 11:10:52'),
(6, 214, 'La phase de contraction du cœur s’appelle la […] et la phase de remplissage s\'appelle la […]', '2017-06-22 11:10:52'),
(7, 215, 'Le rétressissement d\'une artère s\'appelle une […]', '2017-06-22 11:10:53'),
(8, 216, 'Une coloration bleutée de la peau s\'appelle une […]', '2017-06-22 11:10:53'),
(9, 217, 'La maladie créant un rétressissement et un durcissement progressif de la paroi des artères s’appelle ', '2017-06-22 11:10:53'),
(10, 240, 'Le bol alimentaire est d\'abord mastiqué dans [...] puis lorsque on avale, il descend par [...] avant d\'arriver dans […]', '2017-06-22 11:11:09'),
(11, 255, 'L\'appareil urinaire est constitué de 2 organes principaux qui sont les […..], ils sont constitués d\'unités fonctionnelles qui se nomment  les [.....] capables de filtrer le [.....] pour produire de [.....], ils possèdent chacun un canal excréteur qui sont les […..] qui conduisent l\'urine jusque dans la […..] pour ensuite être éliminée à l\'extérieur par un canal qui s\'appelle [.....]', '2017-06-29 13:46:24');

-- --------------------------------------------------------

--
-- Structure de la table `t_cloze_text_answer`
--

CREATE TABLE IF NOT EXISTS `t_cloze_text_answer` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Cloze_Text` int(11) NOT NULL,
  `Answer` text COLLATE utf8_unicode_ci NOT NULL,
  `Answer_Order` int(11) DEFAULT NULL,
  `Creation_Date` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_t_cloze_text_answer_t_cloze_text1_idx` (`FK_Cloze_Text`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_cloze_text_answer`
--

TRUNCATE TABLE `t_cloze_text_answer`;
--
-- Contenu de la table `t_cloze_text_answer`
--

INSERT INTO `t_cloze_text_answer` (`ID`, `FK_Cloze_Text`, `Answer`, `Answer_Order`, `Creation_Date`) VALUES
(1, 1, 'aéro-digestif', 1, '2017-06-21 11:57:44'),
(2, 1, 'larynx', 2, '2017-06-21 11:57:44'),
(3, 1, 'œsophage', 3, '2017-06-21 11:57:44'),
(4, 1, 'bouche', 4, '2017-06-21 11:57:44'),
(5, 1, 'bol alimentaire', 5, '2017-06-21 11:57:44'),
(6, 1, 'estomac', 6, '2017-06-21 11:57:44'),
(7, 2, 'la cage thoracique', 1, '2017-06-22 11:09:27'),
(8, 2, 'diaphragme', 2, '2017-06-22 11:09:27'),
(9, 2, 'lobes', 3, '2017-06-22 11:09:27'),
(10, 2, 'scissures', 4, '2017-06-22 11:09:27'),
(11, 3, 'Gaz carbonique', 1, '2017-06-22 11:09:28'),
(12, 3, 'Oxygène', 2, '2017-06-22 11:09:28'),
(13, 4, 'dyspnée', 1, '2017-06-22 11:09:28'),
(14, 4, 'repos', 2, '2017-06-22 11:09:28'),
(15, 4, 'effort', 3, '2017-06-22 11:09:28'),
(16, 5, 'musculaire creux', 1, '2017-06-22 11:10:52'),
(17, 5, 'myocarde', 2, '2017-06-22 11:10:52'),
(18, 5, 'thorax', 3, '2017-06-22 11:10:52'),
(19, 5, 'une pompe', 4, '2017-06-22 11:10:52'),
(20, 5, 'le sang', 5, '2017-06-22 11:10:52'),
(21, 6, 'systole', 1, '2017-06-22 11:10:52'),
(22, 6, 'dyastole', 2, '2017-06-22 11:10:52'),
(23, 7, 'sténose', 1, '2017-06-22 11:10:53'),
(24, 8, 'cyanose', 1, '2017-06-22 11:10:53'),
(25, 9, 'artériosclérose', 1, '2017-06-22 11:10:53'),
(26, 10, 'la bouche', 1, '2017-06-22 11:11:09'),
(27, 10, 'l\'œsophage', 2, '2017-06-22 11:11:09'),
(28, 10, 'l\'estomac', 3, '2017-06-22 11:11:09'),
(29, 11, 'reins', 1, '2017-06-29 13:46:24'),
(30, 11, 'néphrons', 2, '2017-06-29 13:46:24'),
(31, 11, 'sang', 3, '2017-06-29 13:46:24'),
(32, 11, 'l\'urine', 4, '2017-06-29 13:46:24'),
(33, 11, 'uretères', 5, '2017-06-29 13:46:24'),
(34, 11, 'vessie', 6, '2017-06-29 13:46:24'),
(35, 11, 'l\'urètre', 7, '2017-06-29 13:46:24');

-- --------------------------------------------------------

--
-- Structure de la table `t_free_answer`
--

CREATE TABLE IF NOT EXISTS `t_free_answer` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` int(11) NOT NULL,
  `Answer` text COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_t_free_answer_t_question1_idx` (`FK_Question`)
) ENGINE=InnoDB AUTO_INCREMENT=199 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_free_answer`
--

TRUNCATE TABLE `t_free_answer`;
--
-- Contenu de la table `t_free_answer`
--

INSERT INTO `t_free_answer` (`ID`, `FK_Question`, `Answer`, `Creation_Date`) VALUES
(54, 98, 'Un neurone', '2017-06-08 12:25:21'),
(55, 99, 'Respiration cellulaire, production d\'énergie (ATP)', '2017-06-08 12:25:21'),
(56, 100, 'Assemblée de cellules ayant une fonction \nidentique', '2017-06-08 12:25:21'),
(57, 101, 'l\'ADN qui est du matériel génétique', '2017-06-08 12:25:21'),
(58, 102, 'Science qui a pour objet l’étude des maladies', '2017-06-08 12:25:21'),
(59, 103, 'Renseingement que fourni le malade sur lui-même ou son entourage sur le début de sa maladie jusqu\'au moment où il se trouve soumis à l\'observation du médecin', '2017-06-08 12:25:21'),
(60, 104, 'Il s’agit des analyses médicales ou imageries médicales demandées pour un patient présentant une maladie ou afin d’affiner un diagnostic', '2017-06-08 12:25:22'),
(61, 105, 'Acte par lequel le médecin, groupant les symptômes qu’offre son patient, les rattache à une maladie', '2017-06-08 12:25:22'),
(62, 106, 'La cellule', '2017-06-08 12:25:22'),
(63, 107, 'La mitose', '2017-06-08 12:25:22'),
(64, 110, 'Etirement des ligaments articulaires à la suite d\'une distortion brusque d\'une articulation', '2017-06-20 08:27:57'),
(65, 111, 'Déplacement permanent de deux surfaces articulaires', '2017-06-20 08:27:57'),
(66, 112, 'L\'atlas, première vertèbre du rachis', '2017-06-20 08:27:57'),
(67, 113, 'L\'axis, qui s\'articule avec l\'atlas\n', '2017-06-20 08:27:57'),
(68, 114, 'La synovie', '2017-06-20 08:27:57'),
(69, 115, 'Muscles striés squelettiques, par exemple : biceps, triceps, quadriceps, abducteurs, adducteurs, extenseurs,\nfléchisseurs des membres, muscles du dos, du tronc, fessiers, muscles oculaires, de la main', '2017-06-20 08:27:58'),
(70, 116, 'Le myocarde ou muscle cardiaque', '2017-06-20 08:27:58'),
(71, 117, 'Dans la paroi des viscères, des organes du tube digestif, de l\'appareil urogénital, de l\'appareil respiratoire, des vaisseaux etc.\n', '2017-06-20 08:27:58'),
(72, 118, 'La ceinture scapulaire', '2017-06-20 08:27:58'),
(73, 119, 'La ceinture pelvienne', '2017-06-20 08:27:58'),
(74, 120, '33 vertèbres', '2017-06-20 08:27:58'),
(75, 121, 'Lors d\'un mouvement, l\'abduction s\'éloigne du corps et l\'adduction se rapproche du corps', '2017-06-20 08:27:58'),
(76, 122, 'Calcium', '2017-06-20 08:27:58'),
(77, 123, 'Humérus, Radius, Ulna (=cubitus)', '2017-06-20 08:27:58'),
(78, 124, '7', '2017-06-20 08:27:58'),
(79, 125, '5', '2017-06-20 08:27:58'),
(80, 126, 'Facilite le glissement entre certains tendons, muscles et os / évite les frottements', '2017-06-20 08:27:58'),
(81, 127, 'Capsulite', '2017-06-20 08:27:58'),
(82, 128, 'Muscle strillé', '2017-06-20 08:27:59'),
(83, 129, 'Agoniste', '2017-06-20 08:27:59'),
(84, 130, 'Antagoniste', '2017-06-20 08:27:59'),
(85, 131, 'Radiographie', '2017-06-20 08:27:59'),
(86, 137, 'Grâce aux mouvements péristaltiques', '2017-06-21 11:57:44'),
(87, 138, 'Diarrhées, émission de selles liquides et fréquentes.', '2017-06-21 11:57:44'),
(88, 139, 'Dénutrition grave, diarrhées abondantes au long cours avec stéatorrhée, amaigrissement, anémie, oedèmes, carences.', '2017-06-21 11:57:44'),
(89, 140, 'Diminution ou abolition de l\'alimentation par refus de la nourriture, avec amaigrissement extrême, troubles psychiques et souvent une aménorhée.', '2017-06-21 11:57:44'),
(90, 141, 'L\'œsophage.', '2017-06-21 11:57:44'),
(91, 142, 'Le cardia et le pylore.', '2017-06-21 11:57:44'),
(92, 143, 'La partie supérieure de l\'abdomen et sous le diaphragme.', '2017-06-21 11:57:44'),
(93, 144, 'C\'est le foie qui est une glande endocrine et qui sécrète la bile.', '2017-06-21 11:57:45'),
(94, 145, 'La vésicule biliaire', '2017-06-21 11:57:45'),
(95, 146, 'L\'épiglotte', '2017-06-21 11:57:45'),
(96, 147, 'Sécrétion de la bile, nombreuses synthèses de \nprotéines par exemple des facteurs de coagulation\nmétabolisme des graisses, détoxification', '2017-06-21 11:57:45'),
(97, 148, 'Partie exocrine de la glande: sécrétion du suc pancréatique; partie endocrine: production de \nl\'insuline et du glucagon, pour régler l\'utilisation\ndu glucose', '2017-06-21 11:57:45'),
(98, 149, 'Organe du goût où se trouvent les papilles gustatives. Elle participe à la déglutition et à la phonation, Malaxage des aliments pour la fabrication du bol alimentaire, première étape de la digestion.', '2017-06-21 11:57:45'),
(99, 153, 'Un neurone', '2017-06-22 11:08:14'),
(100, 154, 'Respiration cellulaire, production d\'énergie (ATP)', '2017-06-22 11:08:14'),
(101, 155, 'Assemblée de cellules ayant une fonction \nidentique', '2017-06-22 11:08:14'),
(102, 156, 'l\'ADN qui est du matériel génétique', '2017-06-22 11:08:14'),
(103, 157, 'Science qui a pour objet l’étude des maladies', '2017-06-22 11:08:14'),
(104, 158, 'Renseingement que fourni le malade sur lui-même ou son entourage sur le début de sa maladie jusqu\'au moment où il se trouve soumis à l\'observation du médecin', '2017-06-22 11:08:14'),
(105, 159, 'Il s’agit des analyses médicales ou imageries médicales demandées pour un patient présentant une maladie ou afin d’affiner un diagnostic', '2017-06-22 11:08:14'),
(106, 160, 'Acte par lequel le médecin, groupant les symptômes qu’offre son patient, les rattache à une maladie', '2017-06-22 11:08:14'),
(107, 161, 'La cellule', '2017-06-22 11:08:14'),
(108, 162, 'La mitose', '2017-06-22 11:08:14'),
(109, 165, 'Etirement des ligaments articulaires à la suite d\'une distortion brusque d\'une articulation', '2017-06-22 11:08:30'),
(110, 166, 'Déplacement permanent de deux surfaces articulaires', '2017-06-22 11:08:31'),
(111, 167, 'L\'atlas, première vertèbre du rachis', '2017-06-22 11:08:31'),
(112, 168, 'L\'axis, qui s\'articule avec l\'atlas\n', '2017-06-22 11:08:31'),
(113, 169, 'La synovie', '2017-06-22 11:08:31'),
(114, 170, 'Muscles striés squelettiques, par exemple : biceps, triceps, quadriceps, abducteurs, adducteurs, extenseurs,\nfléchisseurs des membres, muscles du dos, du tronc, fessiers, muscles oculaires, de la main', '2017-06-22 11:08:31'),
(115, 171, 'Le myocarde ou muscle cardiaque', '2017-06-22 11:08:31'),
(116, 172, 'Dans la paroi des viscères, des organes du tube digestif, de l\'appareil urogénital, de l\'appareil respiratoire, des vaisseaux etc.\n', '2017-06-22 11:08:31'),
(117, 173, 'La ceinture scapulaire', '2017-06-22 11:08:31'),
(118, 174, 'La ceinture pelvienne', '2017-06-22 11:08:31'),
(119, 175, '33 vertèbres', '2017-06-22 11:08:31'),
(120, 176, 'Lors d\'un mouvement, l\'abduction s\'éloigne du corps et l\'adduction se rapproche du corps', '2017-06-22 11:08:32'),
(121, 177, 'Calcium', '2017-06-22 11:08:32'),
(122, 178, 'Humérus, Radius, Ulna (=cubitus)', '2017-06-22 11:08:32'),
(123, 179, '7', '2017-06-22 11:08:32'),
(124, 180, '5', '2017-06-22 11:08:32'),
(125, 181, 'Facilite le glissement entre certains tendons, muscles et os / évite les frottements', '2017-06-22 11:08:32'),
(126, 182, 'Capsulite', '2017-06-22 11:08:32'),
(127, 183, 'Muscle strillé', '2017-06-22 11:08:32'),
(128, 184, 'Agoniste', '2017-06-22 11:08:32'),
(129, 185, 'Antagoniste', '2017-06-22 11:08:32'),
(130, 186, 'Radiographie', '2017-06-22 11:08:32'),
(131, 196, 'Le diaphragme', '2017-06-22 11:09:28'),
(132, 197, '8-12/14 fois par minute, moins vite au repos', '2017-06-22 11:09:28'),
(133, 198, 'L\' asthme, BPCO, une pneumonie, emphysème, une obstruction des voies  respiratoires, une intoxication au monoxyde de carbone, etc.', '2017-06-22 11:09:28'),
(134, 199, 'Vrai le poumon gauche est plus petit que le poumon droit', '2017-06-22 11:09:28'),
(135, 200, 'La plèvre', '2017-06-22 11:09:28'),
(136, 201, 'Le médiastin', '2017-06-22 11:09:28'),
(137, 202, 'Les alvéoles', '2017-06-22 11:09:28'),
(138, 203, 'Vrai ', '2017-06-22 11:09:28'),
(139, 204, 'Faux, le diaphragme se relâche et s\'élève lors de l\'expiration.', '2017-06-22 11:09:29'),
(140, 205, 'Les globules rouges ou hématies', '2017-06-22 11:09:29'),
(141, 206, 'Obstruction aiguë, accès de dyspnée expiratoire, spasmes, congestion et hypersécrétion du mucus des bronches', '2017-06-22 11:09:29'),
(142, 207, 'Difficulté de la respiration (ressentie par le malade donc symptôme survenant d’abord à l’effort, puis même au repos).', '2017-06-22 11:09:29'),
(143, 218, 'Une fonction de pompe, circulation du sang dans les vaisseaux', '2017-06-22 11:10:53'),
(144, 219, '2 oreillettes ou atrium et 2 ventricules', '2017-06-22 11:10:53'),
(145, 220, 'Artères', '2017-06-22 11:10:53'),
(146, 221, 'Transport du sang oxygéné du cœur vers tous les organes du corps, appelée aussi circulation systémique', '2017-06-22 11:10:53'),
(147, 222, 'Transport du sang du cœur dans les alvéoles des poumons pour recharger le sang en oxygène et pour éliminer le dioxyde de carbone, appelée circulation pulmonaire', '2017-06-22 11:10:53'),
(148, 223, 'La catégorie des muscles striés et il est le seul de à ne pas dépendre de la volonté', '2017-06-22 11:10:53'),
(149, 224, 'Distribuer le sang oxygéné dans tout le corps', '2017-06-22 11:10:53'),
(150, 225, 'Ramener le sang chargé en gaz carbonique (CO2) vers les poumons', '2017-06-22 11:10:54'),
(151, 226, 'L\'aorte, elle part du ventricule gauche', '2017-06-22 11:10:54'),
(152, 227, 'La contraction du cœur et l\'expulsion du sang ', '2017-06-22 11:10:54'),
(153, 228, 'La dilatation et l\'aspiration du sang', '2017-06-22 11:10:54'),
(154, 229, 'C\'est la force exercée par le flux sanguin contre la paroi des artères du corps', '2017-06-22 11:10:54'),
(155, 230, '140/90 mmHg', '2017-06-22 11:10:54'),
(156, 231, 'coronaires', '2017-06-22 11:10:54'),
(157, 232, 'Nécrose d’une partie du muscle cardiaque', '2017-06-22 11:10:54'),
(158, 233, 'Respriation subjectivement difficile', '2017-06-22 11:10:54'),
(159, 241, 'Grâce aux mouvements péristaltiques', '2017-06-22 11:11:09'),
(160, 242, 'Diarrhées, émission de selles liquides et fréquentes.', '2017-06-22 11:11:09'),
(161, 243, 'L\'œsophage.', '2017-06-22 11:11:09'),
(162, 244, 'La partie supérieure de l\'abdomen et sous le diaphragme.', '2017-06-22 11:11:09'),
(163, 245, 'le foie', '2017-06-22 11:11:10'),
(164, 246, 'La vésicule biliaire', '2017-06-22 11:11:10'),
(165, 247, 'Sécrétion de la bile, nombreuses synthèses de \nprotéines par exemple des facteurs de coagulation\nmétabolisme des graisses, détoxification', '2017-06-22 11:11:10'),
(166, 248, 'A la digestion des graisses', '2017-06-22 11:11:10'),
(167, 256, 'L\'urètre', '2017-06-29 13:46:25'),
(168, 257, 'Les reins', '2017-06-29 13:46:25'),
(169, 258, 'Les uretères', '2017-06-29 13:46:25'),
(170, 259, 'La vessie', '2017-06-29 13:46:25'),
(171, 260, 'Faux, l\'urine est un liquide stérile', '2017-06-29 13:46:25'),
(172, 261, 'Une infection urinaire', '2017-06-29 13:46:25'),
(173, 262, 'Le sperme', '2017-06-29 13:46:25'),
(174, 263, 'Oui il est possible de vivre avec un seul rein', '2017-06-29 13:46:25'),
(175, 264, 'Difficulté à la miction, vidange incomplète avec résidu vésical après la miction', '2017-06-29 13:46:25'),
(176, 265, 'Rein (bassinet, néphrons)', '2017-06-29 13:46:25'),
(177, 267, 'Etirement des ligaments articulaires à la suite d\'une distortion brusque d\'une articulation', '2017-07-04 09:10:42'),
(178, 268, 'Déplacement permanent de deux surfaces articulaires', '2017-07-04 09:10:42'),
(179, 269, 'L\'atlas, première vertèbre du rachis', '2017-07-04 09:10:42'),
(180, 270, 'L\'axis, qui s\'articule avec l\'atlas\n', '2017-07-04 09:10:42'),
(181, 271, 'La synovie', '2017-07-04 09:10:42'),
(182, 272, 'Muscles striés squelettiques, par exemple : biceps, triceps, quadriceps, abducteurs, adducteurs, extenseurs,\nfléchisseurs des membres, muscles du dos, du tronc, fessiers, muscles oculaires, de la main', '2017-07-04 09:10:43'),
(183, 273, 'Le myocarde ou muscle cardiaque', '2017-07-04 09:10:43'),
(184, 274, 'Dans la paroi des viscères, des organes du tube digestif, de l\'appareil urogénital, de l\'appareil respiratoire, des vaisseaux etc.\n', '2017-07-04 09:10:43'),
(185, 275, 'La ceinture scapulaire', '2017-07-04 09:10:43'),
(186, 276, 'La ceinture pelvienne', '2017-07-04 09:10:43'),
(187, 277, '33 vertèbres', '2017-07-04 09:10:43'),
(188, 278, 'Lors d\'un mouvement, l\'abduction s\'éloigne du corps et l\'adduction se rapproche du corps', '2017-07-04 09:10:43'),
(189, 279, 'Calcium', '2017-07-04 09:10:43'),
(190, 280, 'Humérus, Radius, Ulna (=cubitus)', '2017-07-04 09:10:43'),
(191, 281, '7', '2017-07-04 09:10:43'),
(192, 282, '5', '2017-07-04 09:10:43'),
(193, 283, 'Facilite le glissement entre certains tendons, muscles et os / évite les frottements', '2017-07-04 09:10:43'),
(194, 284, 'Capsulite', '2017-07-04 09:10:44'),
(195, 285, 'Muscle strillé', '2017-07-04 09:10:44'),
(196, 286, 'Agoniste', '2017-07-04 09:10:44'),
(197, 287, 'Antagoniste', '2017-07-04 09:10:44'),
(198, 288, 'Radiographie', '2017-07-04 09:10:44');

-- --------------------------------------------------------

--
-- Structure de la table `t_multiple_answer`
--

CREATE TABLE IF NOT EXISTS `t_multiple_answer` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` int(11) NOT NULL,
  `Answer` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_t_multiple_answer_t_question1` (`FK_Question`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_multiple_answer`
--

TRUNCATE TABLE `t_multiple_answer`;
--
-- Contenu de la table `t_multiple_answer`
--

INSERT INTO `t_multiple_answer` (`ID`, `FK_Question`, `Answer`, `Creation_Date`) VALUES
(30, 97, 'Observation', '2017-06-08 12:25:21'),
(31, 97, 'Palpation', '2017-06-08 12:25:21'),
(32, 97, 'Percussion', '2017-06-08 12:25:21'),
(33, 97, 'Auscultation', '2017-06-08 12:25:21'),
(34, 152, 'Observation', '2017-06-22 11:08:13'),
(35, 152, 'Palpation', '2017-06-22 11:08:13'),
(36, 152, 'Percussion', '2017-06-22 11:08:13'),
(37, 152, 'Auscultation', '2017-06-22 11:08:13'),
(38, 192, 'Toux', '2017-06-22 11:09:27'),
(39, 192, 'Fièvre', '2017-06-22 11:09:27'),
(40, 192, 'Fatigue', '2017-06-22 11:09:27'),
(41, 192, 'Expectoration', '2017-06-22 11:09:27'),
(42, 192, 'Douleur thoracique', '2017-06-22 11:09:27'),
(43, 211, 'Ischémie', '2017-06-22 11:10:52'),
(44, 211, 'Angine de poitrine', '2017-06-22 11:10:52'),
(45, 211, 'Infarctus du myocarde', '2017-06-22 11:10:52'),
(46, 211, 'athérosclérose', '2017-06-22 11:10:52'),
(47, 211, 'hypertension artierielle', '2017-06-22 11:10:52'),
(48, 212, 'Tabac', '2017-06-22 11:10:52'),
(49, 212, 'Cholestérol', '2017-06-22 11:10:52'),
(50, 212, 'Diabète', '2017-06-22 11:10:52'),
(51, 212, 'Hypertension artérielle', '2017-06-22 11:10:52'),
(52, 212, 'Sédentarité', '2017-06-22 11:10:52'),
(53, 237, 'Insuline', '2017-06-22 11:11:08'),
(54, 237, 'Glucagon', '2017-06-22 11:11:08'),
(55, 238, 'Glucide', '2017-06-22 11:11:09'),
(56, 238, 'Lipide', '2017-06-22 11:11:09'),
(57, 238, 'Protéine', '2017-06-22 11:11:09'),
(58, 239, 'Goût', '2017-06-22 11:11:09'),
(59, 239, 'Déglutition', '2017-06-22 11:11:09'),
(60, 239, 'Phonation', '2017-06-22 11:11:09'),
(61, 239, 'Malaxage des aliments', '2017-06-22 11:11:09');

-- --------------------------------------------------------

--
-- Structure de la table `t_multiple_choice`
--

CREATE TABLE IF NOT EXISTS `t_multiple_choice` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` int(11) NOT NULL,
  `Answer` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Valid` tinyint(1) DEFAULT NULL,
  `Creation_Date` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_t_multiple_choice_t_question1_idx` (`FK_Question`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_multiple_choice`
--

TRUNCATE TABLE `t_multiple_choice`;
--
-- Contenu de la table `t_multiple_choice`
--

INSERT INTO `t_multiple_choice` (`ID`, `FK_Question`, `Answer`, `Valid`, `Creation_Date`) VALUES
(26, 95, 'observation', 1, '2017-06-08 12:17:13'),
(27, 95, 'anamnèse', 0, '2017-06-08 12:17:13'),
(28, 95, 'radiographie', 0, '2017-06-08 12:17:13'),
(29, 95, 'auscultation', 1, '2017-06-08 12:17:13'),
(30, 95, 'palpation', 1, '2017-06-08 12:17:13'),
(31, 96, 'observation', 1, '2017-06-08 12:25:20'),
(32, 96, 'anamnèse', 0, '2017-06-08 12:25:20'),
(33, 96, 'radiographie', 0, '2017-06-08 12:25:21'),
(34, 96, 'auscultation', 1, '2017-06-08 12:25:21'),
(35, 96, 'palpation', 1, '2017-06-08 12:25:21'),
(36, 151, 'observation', 1, '2017-06-22 11:08:13'),
(37, 151, 'anamnèse', 0, '2017-06-22 11:08:13'),
(38, 151, 'radiographie', 0, '2017-06-22 11:08:13'),
(39, 151, 'auscultation', 1, '2017-06-22 11:08:13'),
(40, 151, 'palpation', 1, '2017-06-22 11:08:13'),
(41, 191, 'Fréquente', 1, '2017-06-22 11:09:27'),
(42, 191, 'réversible', 0, '2017-06-22 11:09:27'),
(43, 191, 'influencée par le tabagisme', 1, '2017-06-22 11:09:27'),
(44, 191, 'une maladie cardiaque', 0, '2017-06-22 11:09:27'),
(45, 210, '1', 0, '2017-06-22 11:10:51'),
(46, 235, 'Il communique avec la bouche', 1, '2017-06-22 11:11:08'),
(47, 235, 'Il ne sert qu\'au passage de l\'air', 0, '2017-06-22 11:11:08'),
(48, 235, 'C\'est le carrefour aéro-digestif', 1, '2017-06-22 11:11:08'),
(49, 235, 'Il est situé au dessus de l\'œsophage', 1, '2017-06-22 11:11:08'),
(50, 236, '1', 1, '2017-06-22 11:11:08'),
(51, 250, 'eau', 0, '2017-06-29 13:46:23'),
(52, 250, 'bactérie', 1, '2017-06-29 13:46:23'),
(53, 250, 'créatinine', 0, '2017-06-29 13:46:23'),
(54, 250, 'sucre', 1, '2017-06-29 13:46:23'),
(55, 250, 'sang', 1, '2017-06-29 13:46:23'),
(56, 251, 'Difficulté à uriner', 0, '2017-06-29 13:46:23'),
(57, 251, 'Miction trop fréquente', 0, '2017-06-29 13:46:23'),
(58, 251, 'Douleur en urinant', 1, '2017-06-29 13:46:24'),
(59, 252, 'Volume urinaire trop important', 0, '2017-06-29 13:46:24'),
(60, 252, 'Sang dans les urines', 1, '2017-06-29 13:46:24'),
(61, 252, 'Absence d\'urine', 0, '2017-06-29 13:46:24'),
(62, 253, '1', 1, '2017-06-29 13:46:24'),
(63, 254, '1', 0, '2017-06-29 13:46:24');

-- --------------------------------------------------------

--
-- Structure de la table `t_picture_landmark`
--

CREATE TABLE IF NOT EXISTS `t_picture_landmark` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` int(11) NOT NULL,
  `Symbol` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `Answer` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_t_picture_landmark_t_question1_idx` (`FK_Question`)
) ENGINE=InnoDB AUTO_INCREMENT=191 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_picture_landmark`
--

TRUNCATE TABLE `t_picture_landmark`;
--
-- Contenu de la table `t_picture_landmark`
--

INSERT INTO `t_picture_landmark` (`ID`, `FK_Question`, `Symbol`, `Answer`, `Creation_Date`) VALUES
(70, 108, 'Ro', '1', '2017-06-08 12:25:22'),
(71, 109, 'Ep', '1', '2017-06-08 12:25:22'),
(72, 109, 'Om', '2', '2017-06-08 12:25:22'),
(73, 109, 'Hy', '3', '2017-06-08 12:25:22'),
(74, 109, 'Hy', '4', '2017-06-08 12:25:22'),
(75, 109, 'Fl', '5', '2017-06-08 12:25:22'),
(76, 109, 'Fo', '6', '2017-06-08 12:25:22'),
(77, 109, 'Hy', '7', '2017-06-08 12:25:22'),
(78, 109, 'Fl', '8', '2017-06-08 12:25:22'),
(79, 109, 'Fo', '9', '2017-06-08 12:25:22'),
(80, 132, 'os', '1', '2017-06-20 08:27:59'),
(81, 132, 'os', '2', '2017-06-20 08:27:59'),
(82, 132, 'co', '3', '2017-06-20 08:27:59'),
(83, 132, 'cl', '4', '2017-06-20 08:27:59'),
(84, 132, 'om', '5', '2017-06-20 08:27:59'),
(85, 132, 'st', '6', '2017-06-20 08:27:59'),
(86, 132, 'th', '7', '2017-06-20 08:27:59'),
(87, 132, 'hu', '8', '2017-06-20 08:27:59'),
(88, 132, 'ra', '9', '2017-06-20 08:27:59'),
(89, 132, 'cu', '10', '2017-06-20 08:27:59'),
(90, 133, 'Hu', 'A', '2017-06-20 08:27:59'),
(91, 133, 'Ra', 'B', '2017-06-20 08:27:59'),
(92, 133, 'Ul', 'C', '2017-06-20 08:27:59'),
(93, 134, 'Fé', 'A', '2017-06-20 08:28:00'),
(94, 134, 'Ti', 'B', '2017-06-20 08:28:00'),
(95, 134, 'Ro', 'C', '2017-06-20 08:28:00'),
(96, 135, 'Fé', 'A', '2017-06-20 08:28:00'),
(97, 135, 'Il', 'B', '2017-06-20 08:28:00'),
(98, 150, 'ph', '1', '2017-06-21 11:57:45'),
(99, 150, 'œs', '2', '2017-06-21 11:57:45'),
(100, 150, 'fo', '3', '2017-06-21 11:57:45'),
(101, 150, 'es', '4', '2017-06-21 11:57:45'),
(102, 150, 'vé', '5', '2017-06-21 11:57:45'),
(103, 163, 'Ro', '1', '2017-06-22 11:08:14'),
(104, 164, 'Ep', '1', '2017-06-22 11:08:15'),
(105, 164, 'Om', '2', '2017-06-22 11:08:15'),
(106, 164, 'Hy', '3', '2017-06-22 11:08:15'),
(107, 164, 'Hy', '4', '2017-06-22 11:08:15'),
(108, 164, 'Fl', '5', '2017-06-22 11:08:15'),
(109, 164, 'Fo', '6', '2017-06-22 11:08:15'),
(110, 164, 'Hy', '7', '2017-06-22 11:08:15'),
(111, 164, 'Fl', '8', '2017-06-22 11:08:15'),
(112, 164, 'Fo', '9', '2017-06-22 11:08:15'),
(113, 187, 'os', '1', '2017-06-22 11:08:33'),
(114, 187, 'os', '2', '2017-06-22 11:08:33'),
(115, 187, 'co', '3', '2017-06-22 11:08:33'),
(116, 187, 'cl', '4', '2017-06-22 11:08:33'),
(117, 187, 'om', '5', '2017-06-22 11:08:33'),
(118, 187, 'st', '6', '2017-06-22 11:08:33'),
(119, 187, 'th', '7', '2017-06-22 11:08:33'),
(120, 187, 'hu', '8', '2017-06-22 11:08:33'),
(121, 187, 'ra', '9', '2017-06-22 11:08:33'),
(122, 187, 'cu', '10', '2017-06-22 11:08:33'),
(123, 188, 'Hu', 'A', '2017-06-22 11:08:33'),
(124, 188, 'Ra', 'B', '2017-06-22 11:08:33'),
(125, 188, 'Ul', 'C', '2017-06-22 11:08:33'),
(126, 189, 'Fé', 'A', '2017-06-22 11:08:33'),
(127, 189, 'Ti', 'B', '2017-06-22 11:08:33'),
(128, 189, 'Ro', 'C', '2017-06-22 11:08:33'),
(129, 190, 'Fé', 'A', '2017-06-22 11:08:33'),
(130, 190, 'Il', 'B', '2017-06-22 11:08:33'),
(131, 208, 'ca', '1', '2017-06-22 11:09:29'),
(132, 208, 'ca', '2', '2017-06-22 11:09:29'),
(133, 208, 'ph', '3', '2017-06-22 11:09:29'),
(134, 208, 'ép', '4', '2017-06-22 11:09:29'),
(135, 208, 'la', '5', '2017-06-22 11:09:29'),
(136, 208, 'tr', '6', '2017-06-22 11:09:29'),
(137, 208, 'br', '7', '2017-06-22 11:09:29'),
(138, 208, 'po', '8', '2017-06-22 11:09:29'),
(139, 208, 'br', '9', '2017-06-22 11:09:29'),
(140, 208, 'sc', '10', '2017-06-22 11:09:29'),
(141, 208, 'al', '11', '2017-06-22 11:09:29'),
(142, 208, 'di', '12', '2017-06-22 11:09:29'),
(143, 208, 'œs', '13', '2017-06-22 11:09:29'),
(144, 209, 'Ne', 'A', '2017-06-22 11:09:30'),
(145, 209, 'Ph', 'B', '2017-06-22 11:09:30'),
(146, 209, 'La', 'C', '2017-06-22 11:09:30'),
(147, 209, 'Tr', 'D', '2017-06-22 11:09:30'),
(148, 234, 've', '1', '2017-06-22 11:10:54'),
(149, 234, 'ao', '2', '2017-06-22 11:10:54'),
(150, 234, 'ar', '3', '2017-06-22 11:10:54'),
(151, 234, 'or', '4', '2017-06-22 11:10:54'),
(152, 234, 'or', '5', '2017-06-22 11:10:54'),
(153, 234, 'va', '6', '2017-06-22 11:10:55'),
(154, 234, 'va', '7', '2017-06-22 11:10:55'),
(155, 234, 'va', '8', '2017-06-22 11:10:55'),
(156, 234, 'va', '9', '2017-06-22 11:10:55'),
(157, 234, 've', '10', '2017-06-22 11:10:55'),
(158, 234, 've', '11', '2017-06-22 11:10:55'),
(159, 234, 've', '12', '2017-06-22 11:10:55'),
(160, 249, 'ph', '1', '2017-06-22 11:11:10'),
(161, 249, 'œs', '2', '2017-06-22 11:11:10'),
(162, 249, 'fo', '3', '2017-06-22 11:11:10'),
(163, 249, 'es', '4', '2017-06-22 11:11:10'),
(164, 249, 'vé', '5', '2017-06-22 11:11:10'),
(165, 266, 've', '1', '2017-06-29 13:46:26'),
(166, 266, 'ao', '2', '2017-06-29 13:46:26'),
(167, 266, 'gl', '3', '2017-06-29 13:46:26'),
(168, 266, 'né', '4', '2017-06-29 13:46:26'),
(169, 266, 're', '5', '2017-06-29 13:46:26'),
(170, 266, 'ur', '6', '2017-06-29 13:46:26'),
(171, 266, 've', '7', '2017-06-29 13:46:26'),
(172, 266, 'ur', '8', '2017-06-29 13:46:26'),
(173, 289, 'os', '1', '2017-07-04 09:10:44'),
(174, 289, 'os', '2', '2017-07-04 09:10:44'),
(175, 289, 'co', '3', '2017-07-04 09:10:44'),
(176, 289, 'cl', '4', '2017-07-04 09:10:44'),
(177, 289, 'om', '5', '2017-07-04 09:10:44'),
(178, 289, 'st', '6', '2017-07-04 09:10:44'),
(179, 289, 'th', '7', '2017-07-04 09:10:44'),
(180, 289, 'hu', '8', '2017-07-04 09:10:44'),
(181, 289, 'ra', '9', '2017-07-04 09:10:44'),
(182, 289, 'cu', '10', '2017-07-04 09:10:44'),
(183, 290, 'Hu', 'A', '2017-07-04 09:10:44'),
(184, 290, 'Ra', 'B', '2017-07-04 09:10:44'),
(185, 290, 'Ul', 'C', '2017-07-04 09:10:44'),
(186, 291, 'Fé', 'A', '2017-07-04 09:10:45'),
(187, 291, 'Ti', 'B', '2017-07-04 09:10:45'),
(188, 291, 'Ro', 'C', '2017-07-04 09:10:45'),
(189, 292, 'Fé', 'A', '2017-07-04 09:10:45'),
(190, 292, 'Il', 'B', '2017-07-04 09:10:45');

-- --------------------------------------------------------

--
-- Structure de la table `t_question`
--

CREATE TABLE IF NOT EXISTS `t_question` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Topic` int(11) NOT NULL,
  `FK_Question_Type` int(11) NOT NULL,
  `Question` text COLLATE utf8_unicode_ci NOT NULL,
  `Nb_Desired_Answers` int(11) DEFAULT NULL,
  `Table_With_Definition` tinyint(1) DEFAULT NULL,
  `Picture_Name` text COLLATE utf8_unicode_ci,
  `Points` int(11) DEFAULT NULL,
  `Creation_Date` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_t_question_t_question_type1_idx` (`FK_Question_Type`),
  KEY `fk_t_question_t_topic1_idx` (`FK_Topic`)
) ENGINE=InnoDB AUTO_INCREMENT=293 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_question`
--

TRUNCATE TABLE `t_question`;
--
-- Contenu de la table `t_question`
--

INSERT INTO `t_question` (`ID`, `FK_Topic`, `FK_Question_Type`, `Question`, `Nb_Desired_Answers`, `Table_With_Definition`, `Picture_Name`, `Points`, `Creation_Date`) VALUES
(95, 2, 1, 'Cocher les éléments qui font partie de l\'examen clinique du patient', NULL, NULL, NULL, NULL, '2017-06-08 12:17:13'),
(96, 2, 1, 'Cocher les éléments qui font partie de l\'examen clinique du patient', NULL, NULL, NULL, NULL, '2017-06-08 12:25:20'),
(97, 2, 2, 'Citer les quatres temps de l\'examen clinique du patient', 4, NULL, NULL, NULL, '2017-06-08 12:25:21'),
(98, 2, 6, 'Quel est le nom de la cellule nerveuse ?', NULL, NULL, NULL, 1, '2017-06-08 12:25:21'),
(99, 2, 6, 'Quel est le rôle et la  fonction des mitochondries ?', NULL, NULL, NULL, 1, '2017-06-08 12:25:21'),
(100, 2, 6, 'Définir un tissu ?', NULL, NULL, NULL, 1, '2017-06-08 12:25:21'),
(101, 2, 6, 'Que contient le noyau cellulaire ?', NULL, NULL, NULL, 1, '2017-06-08 12:25:21'),
(102, 2, 6, 'Donner la définition de : pathologie', NULL, NULL, NULL, 1, '2017-06-08 12:25:21'),
(103, 2, 6, 'Donner la définition d\': anamnèse', NULL, NULL, NULL, 1, '2017-06-08 12:25:21'),
(104, 2, 6, 'Donner la définition d\': examen complémentaire', NULL, NULL, NULL, 1, '2017-06-08 12:25:22'),
(105, 2, 6, 'Donner la définition de : Diagnostic', NULL, NULL, NULL, 1, '2017-06-08 12:25:22'),
(106, 2, 6, 'Quel élément de base constitue tous les tissus du corps humain?', NULL, NULL, NULL, 1, '2017-06-08 12:25:22'),
(107, 2, 6, 'Comment s\'appelle la division cellulaire qui permet à une celle de se répliquer?', NULL, NULL, NULL, 1, '2017-06-08 12:25:22'),
(108, 2, 7, 'Comment s\'appelle le mouvmeent de la hanche droite sur ce schéma', NULL, NULL, 'Re_hanche.jpg', NULL, '2017-06-08 12:25:22'),
(109, 2, 7, 'Nommer les cadrans de l\'abdomen', NULL, NULL, 'abdomen.jpg', NULL, '2017-06-08 12:25:22'),
(110, 3, 6, 'Définir une entorse ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:57'),
(111, 3, 6, 'Définir luxation articulaire ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:57'),
(112, 3, 6, 'Quelle est la première vertèbre qui supporte\n le crâne ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:57'),
(113, 3, 6, 'Quelle est le nom de la 2ème vertèbre qui \npermet l\'articulation de la tête ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:57'),
(114, 3, 6, 'Quel est le nom du liquide que le médecin peut retirer d\'une articulation enflée ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:57'),
(115, 3, 6, 'Quels types de muscles peuvent être contrôlés par la volonté ? Donné un exemple', NULL, NULL, NULL, 1, '2017-06-20 08:27:58'),
(116, 3, 6, 'Citer un muscle strié automatique ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:58'),
(117, 3, 6, 'Où trouve-t-on des muscles lisses ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:58'),
(118, 3, 6, 'Les mains, les bras sont reliés au thorax par quelle ceinture Où trouve-t-on des muscles lisses ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:58'),
(119, 3, 6, 'Les pieds, les jambes sont reliés au bassin par quelle ceinture ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:58'),
(120, 3, 6, 'Combien de vertèbres constituent la colonne vertébrale', NULL, NULL, NULL, 1, '2017-06-20 08:27:58'),
(121, 3, 6, 'Quelle est la différence entre l\'abduction et l\'adduction ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:58'),
(122, 3, 6, 'De quel minéral est principalement constitué de l’os ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:58'),
(123, 3, 6, 'Quels sont les os du bras et de l’avant-bras ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:58'),
(124, 3, 6, 'Combien a-t-on de vertèbres cervicales ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:58'),
(125, 3, 6, 'Combien a-t-on de vertèbres lombaires ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:58'),
(126, 3, 6, 'Quel est le rôle des bourses synoviales ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:58'),
(127, 3, 6, 'Comment appelle-t-on l’inflammation d’une capsule articulaire ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:58'),
(128, 3, 6, 'Comment appelle-t-on les muscles soumis à l’action de la volonté', NULL, NULL, NULL, 1, '2017-06-20 08:27:59'),
(129, 3, 6, 'Comment appelle-t-on deux muscles dont l’action est la même ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:59'),
(130, 3, 6, 'Comment appelle-t-on deux muscles dont l’action s’oppose ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:59'),
(131, 3, 6, 'Quel examen complémentaire est réalisé en première intention afin de rechercher une fracture ?', NULL, NULL, NULL, 1, '2017-06-20 08:27:59'),
(132, 3, 7, 'Ecriver le nom des os du squelette', NULL, NULL, 'squelette.jpg', NULL, '2017-06-20 08:27:59'),
(133, 3, 7, 'Nommer les points A, B et C', NULL, NULL, 'Coude.jpg', NULL, '2017-06-20 08:27:59'),
(134, 3, 7, 'Nommer les points A, B et C', NULL, NULL, 'Genou.jpg', NULL, '2017-06-20 08:28:00'),
(135, 3, 7, 'Nommer les points A et B', NULL, NULL, 'Hanche.jpg', NULL, '2017-06-20 08:28:00'),
(136, 2, 4, 'Compléter ce texte à trous avec les mots manquants. ', NULL, NULL, NULL, NULL, '2017-06-21 11:57:43'),
(137, 2, 6, 'Comment se font les déplacements du bol alimentaire le long du tube digestif ?', NULL, NULL, NULL, 1, '2017-06-21 11:57:44'),
(138, 2, 6, 'Comment appelle -t-on l\'accélération du transit intestinal ?', NULL, NULL, NULL, 1, '2017-06-21 11:57:44'),
(139, 2, 6, 'Quelles seront les conséquences d\'une sévère malabsorbsion ?', NULL, NULL, NULL, 1, '2017-06-21 11:57:44'),
(140, 2, 6, 'Définir une anorexie mentale ?', NULL, NULL, NULL, 1, '2017-06-21 11:57:44'),
(141, 2, 6, 'Quel est le nom du conduit qui relie le pharynx à l\'estomac ?', NULL, NULL, NULL, 1, '2017-06-21 11:57:44'),
(142, 2, 6, 'Comment se nomme le sphincter se trouvant à l\'entrée de l\'estomac et celui se trouvant à la sortie ?', NULL, NULL, NULL, 1, '2017-06-21 11:57:44'),
(143, 2, 6, 'Dans quelle partie de l\'abdomen se trouve l\'estomac et sous quel muscle ?', NULL, NULL, NULL, 1, '2017-06-21 11:57:44'),
(144, 2, 6, 'Quel est le nom de la plus grosse glande du tube digestif (de l\'organisme) et que produit-elle ?', NULL, NULL, NULL, 1, '2017-06-21 11:57:45'),
(145, 2, 6, 'Comment se nomme le réservoir à bile ?', NULL, NULL, NULL, 1, '2017-06-21 11:57:45'),
(146, 2, 6, 'Au moment de l\'ingestion d\'aliments, la fermeture de la trachée se fait par quel organe ?', NULL, NULL, NULL, 1, '2017-06-21 11:57:45'),
(147, 2, 6, 'Donner les fonctions principales du foie?', NULL, NULL, NULL, 1, '2017-06-21 11:57:45'),
(148, 2, 6, 'Donner les  principales fonctions du pancréas?', NULL, NULL, NULL, 1, '2017-06-21 11:57:45'),
(149, 2, 6, 'Quels sont les 3  fonctions principales de la langue ?', NULL, NULL, NULL, 1, '2017-06-21 11:57:45'),
(150, 2, 7, 'Ecriver le nom des organes du tube digestif.', NULL, NULL, 'tubedigestif.jpeg', NULL, '2017-06-21 11:57:45'),
(151, 27, 1, 'Cocher les éléments qui font partie de l\'examen clinique du patient', NULL, NULL, NULL, NULL, '2017-06-22 11:08:13'),
(152, 27, 2, 'Citer les quatres temps de l\'examen clinique du patient', 4, NULL, NULL, NULL, '2017-06-22 11:08:13'),
(153, 27, 6, 'Quel est le nom de la cellule nerveuse ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:14'),
(154, 27, 6, 'Quel est le rôle et la  fonction des mitochondries ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:14'),
(155, 27, 6, 'Définir un tissu ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:14'),
(156, 27, 6, 'Que contient le noyau cellulaire ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:14'),
(157, 27, 6, 'Donner la définition de : pathologie', NULL, NULL, NULL, 1, '2017-06-22 11:08:14'),
(158, 27, 6, 'Donner la définition d\': anamnèse', NULL, NULL, NULL, 1, '2017-06-22 11:08:14'),
(159, 27, 6, 'Donner la définition d\': examen complémentaire', NULL, NULL, NULL, 1, '2017-06-22 11:08:14'),
(160, 27, 6, 'Donner la définition de : Diagnostic', NULL, NULL, NULL, 1, '2017-06-22 11:08:14'),
(161, 27, 6, 'Quel élément de base constitue tous les tissus du corps humain?', NULL, NULL, NULL, 1, '2017-06-22 11:08:14'),
(162, 27, 6, 'Comment s\'appelle la division cellulaire qui permet à une celle de se répliquer?', NULL, NULL, NULL, 1, '2017-06-22 11:08:14'),
(163, 27, 7, 'Comment s\'appelle le mouvmeent de la hanche droite sur ce schéma', NULL, NULL, 'Re_hanche.jpg', NULL, '2017-06-22 11:08:14'),
(164, 27, 7, 'Nommer les cadrans de l\'abdomen', NULL, NULL, 'abdomen.jpg', NULL, '2017-06-22 11:08:15'),
(165, 27, 6, 'Définir une entorse ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:30'),
(166, 27, 6, 'Définir luxation articulaire ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:31'),
(167, 27, 6, 'Quelle est la première vertèbre qui supporte\n le crâne ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:31'),
(168, 27, 6, 'Quelle est le nom de la 2ème vertèbre qui \npermet l\'articulation de la tête ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:31'),
(169, 27, 6, 'Quel est le nom du liquide que le médecin peut retirer d\'une articulation enflée ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:31'),
(170, 27, 6, 'Quels types de muscles peuvent être contrôlés par la volonté ? Donné un exemple', NULL, NULL, NULL, 1, '2017-06-22 11:08:31'),
(171, 27, 6, 'Citer un muscle strié automatique ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:31'),
(172, 27, 6, 'Où trouve-t-on des muscles lisses ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:31'),
(173, 27, 6, 'Les mains, les bras sont reliés au thorax par quelle ceinture Où trouve-t-on des muscles lisses ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:31'),
(174, 27, 6, 'Les pieds, les jambes sont reliés au bassin par quelle ceinture ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:31'),
(175, 27, 6, 'Combien de vertèbres constituent la colonne vertébrale', NULL, NULL, NULL, 1, '2017-06-22 11:08:31'),
(176, 27, 6, 'Quelle est la différence entre l\'abduction et l\'adduction ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:32'),
(177, 27, 6, 'De quel minéral est principalement constitué de l’os ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:32'),
(178, 27, 6, 'Quels sont les os du bras et de l’avant-bras ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:32'),
(179, 27, 6, 'Combien a-t-on de vertèbres cervicales ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:32'),
(180, 27, 6, 'Combien a-t-on de vertèbres lombaires ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:32'),
(181, 27, 6, 'Quel est le rôle des bourses synoviales ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:32'),
(182, 27, 6, 'Comment appelle-t-on l’inflammation d’une capsule articulaire ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:32'),
(183, 27, 6, 'Comment appelle-t-on les muscles soumis à l’action de la volonté', NULL, NULL, NULL, 1, '2017-06-22 11:08:32'),
(184, 27, 6, 'Comment appelle-t-on deux muscles dont l’action est la même ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:32'),
(185, 27, 6, 'Comment appelle-t-on deux muscles dont l’action s’oppose ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:32'),
(186, 27, 6, 'Quel examen complémentaire est réalisé en première intention afin de rechercher une fracture ?', NULL, NULL, NULL, 1, '2017-06-22 11:08:32'),
(187, 27, 7, 'Ecriver le nom des os du squelette', NULL, NULL, 'squelette.jpg', NULL, '2017-06-22 11:08:33'),
(188, 27, 7, 'Nommer les points A, B et C', NULL, NULL, 'Coude.jpg', NULL, '2017-06-22 11:08:33'),
(189, 27, 7, 'Nommer les points A, B et C', NULL, NULL, 'Genou.jpg', NULL, '2017-06-22 11:08:33'),
(190, 27, 7, 'Nommer les points A et B', NULL, NULL, 'Hanche.jpg', NULL, '2017-06-22 11:08:33'),
(191, 27, 1, 'La Bronchopneumopathie chronique obstructive est:(plusieurs réponses possible)', NULL, NULL, NULL, NULL, '2017-06-22 11:09:27'),
(192, 27, 2, 'Donner trois symptômes d\'une pneumonie', 3, NULL, NULL, NULL, '2017-06-22 11:09:27'),
(193, 27, 4, 'Compléter ce texte à trous avec les mots manquants.', NULL, NULL, NULL, NULL, '2017-06-22 11:09:27'),
(194, 27, 4, 'Compléter ce texte à trous avec les mots manquants.', NULL, NULL, NULL, NULL, '2017-06-22 11:09:27'),
(195, 27, 4, 'Compléter ce texte à trous avec les mots manquants.', NULL, NULL, NULL, NULL, '2017-06-22 11:09:28'),
(196, 27, 6, 'Quel est le muscle prioncipal  de la respiration ?', NULL, NULL, NULL, 1, '2017-06-22 11:09:28'),
(197, 27, 6, 'A quelle fréquence respirez-vous ?', NULL, NULL, NULL, 1, '2017-06-22 11:09:28'),
(198, 27, 6, 'Citer deux maladies qui peuvent provoquer une détresse respiratoire ?', NULL, NULL, NULL, 1, '2017-06-22 11:09:28'),
(199, 27, 6, 'Vrai ou faux : le poumon gauche est plus petit que le poumon droit.', NULL, NULL, NULL, 1, '2017-06-22 11:09:28'),
(200, 27, 6, 'Comment se nomme la membrane séreuse qui entoure et recouvre les poumons ?', NULL, NULL, NULL, 1, '2017-06-22 11:09:28'),
(201, 27, 6, 'Quel est le nom de la partie centrale du thorax ?', NULL, NULL, NULL, 1, '2017-06-22 11:09:28'),
(202, 27, 6, 'Comment se nomme les petits sacs où se font les échanges gazeux ?', NULL, NULL, NULL, 1, '2017-06-22 11:09:28'),
(203, 27, 6, 'Vrai ou faux : lors de l\'inspiration, le diaphragme se contracte et s\'abaisse', NULL, NULL, NULL, 1, '2017-06-22 11:09:28'),
(204, 27, 6, 'Vrai ou faux : lors de l\'expiration, le diaphragme se contracte et s\'abaisse ?', NULL, NULL, NULL, 1, '2017-06-22 11:09:29'),
(205, 27, 6, 'Quelles est le nom des globules qui transportent l\'oxygène ?', NULL, NULL, NULL, 1, '2017-06-22 11:09:29'),
(206, 27, 6, 'Donner la définition d\'une crise d\'asthme', NULL, NULL, NULL, 1, '2017-06-22 11:09:29'),
(207, 27, 6, 'Qu\'est ce qu\'une dyspnée', NULL, NULL, NULL, 1, '2017-06-22 11:09:29'),
(208, 27, 7, 'Compléter le schéma de l\'appareil respiratoire avec le nom des organes.', NULL, NULL, 'appRespiratoire.jpg', NULL, '2017-06-22 11:09:29'),
(209, 27, 7, 'Compléter le schéma de l\'appareil respiratoire avec le nom des organes.', NULL, NULL, 'Appareil_respiratoire.jpg', NULL, '2017-06-22 11:09:29'),
(210, 27, 1, 'Les veines qui sortent des poumons contiennent du sang desoxygéné', NULL, NULL, NULL, NULL, '2017-06-22 11:10:51'),
(211, 27, 2, 'Citer deux pathologies cardiaques fréquentes', 2, NULL, NULL, NULL, '2017-06-22 11:10:52'),
(212, 27, 2, 'Citer deux facteurs de risque cardiovasculaire', 2, NULL, NULL, NULL, '2017-06-22 11:10:52'),
(213, 27, 4, 'Compléter ce texte à trous avec les mots manquants.', NULL, NULL, NULL, NULL, '2017-06-22 11:10:52'),
(214, 27, 4, 'Compléter ce texte à trous avec les mots manquants.', NULL, NULL, NULL, NULL, '2017-06-22 11:10:52'),
(215, 27, 4, 'Compléter la phrase', NULL, NULL, NULL, NULL, '2017-06-22 11:10:53'),
(216, 27, 4, 'Compléter la phrase', NULL, NULL, NULL, NULL, '2017-06-22 11:10:53'),
(217, 27, 4, 'Compléter la phrase', NULL, NULL, NULL, NULL, '2017-06-22 11:10:53'),
(218, 27, 6, 'Quelle est la fonction du cœur ?', NULL, NULL, NULL, 1, '2017-06-22 11:10:53'),
(219, 27, 6, 'Nommer les quatres cavités cardiaques ?', NULL, NULL, NULL, 1, '2017-06-22 11:10:53'),
(220, 27, 6, 'Quels noms donne-t-on aux vaisseaux qui\nquittent le cœur ?', NULL, NULL, NULL, 1, '2017-06-22 11:10:53'),
(221, 27, 6, 'Fonction de la grande circulation ?', NULL, NULL, NULL, 1, '2017-06-22 11:10:53'),
(222, 27, 6, 'Fonction de la petite circulation ?', NULL, NULL, NULL, 1, '2017-06-22 11:10:53'),
(223, 27, 6, 'De quelle catégorie de muscles fait partie le cœur et pour quelle raison est-il l\'exception de sa catégorie ?', NULL, NULL, NULL, 1, '2017-06-22 11:10:53'),
(224, 27, 6, 'Quelle est la fonction principale des artères ?', NULL, NULL, NULL, 1, '2017-06-22 11:10:53'),
(225, 27, 6, 'Quelle est la fonction principale des veines ?', NULL, NULL, NULL, 1, '2017-06-22 11:10:54'),
(226, 27, 6, 'Quel est le nom de la plus grande artère du cœur\net de quel ventricule elle part ?', NULL, NULL, NULL, 1, '2017-06-22 11:10:54'),
(227, 27, 6, 'Quel est le rôle de la systole ?', NULL, NULL, NULL, 1, '2017-06-22 11:10:54'),
(228, 27, 6, 'Quel est le rôle de la diastale?', NULL, NULL, NULL, 1, '2017-06-22 11:10:54'),
(229, 27, 6, 'Donner la définition de la tension artérielle ?', NULL, NULL, NULL, 1, '2017-06-22 11:10:54'),
(230, 27, 6, 'Quelles sont les valeurs normales de la tension artérielle données par l\'OMS ?', NULL, NULL, NULL, 1, '2017-06-22 11:10:54'),
(231, 27, 6, 'Comment s\'appelle les artères qui nourissent le cœur?', NULL, NULL, NULL, 1, '2017-06-22 11:10:54'),
(232, 27, 6, 'Quelle est la définition d\'un infarctus du myocarde?', NULL, NULL, NULL, 1, '2017-06-22 11:10:54'),
(233, 27, 6, 'Donné la définition d’une dyspnée', NULL, NULL, NULL, 1, '2017-06-22 11:10:54'),
(234, 27, 7, 'En vous aidant des noms de la liste , veuillez compléter le schéma du coeur :\n\nventricule gauche, veine cave supérieur, oreillette  droite, ventricule droit, aorte, oreillette gauche, valve aortique, valve pulmonaire, veine cave inférieure, valvule tricuspide, valve mitrale, artère pulmonaire', NULL, NULL, 'cœur.jpg', NULL, '2017-06-22 11:10:54'),
(235, 27, 1, 'A propos du pharynx cocher la ou les affirmations justes', NULL, NULL, NULL, NULL, '2017-06-22 11:11:08'),
(236, 27, 1, 'La digestion des aliments commence dans la bouche', NULL, NULL, NULL, NULL, '2017-06-22 11:11:08'),
(237, 27, 2, 'Quelles sont les deux hormones produites par le pancréas endocrine', 2, NULL, NULL, NULL, '2017-06-22 11:11:08'),
(238, 27, 2, 'Quels sont les trois nutriments de base assimilable par le corps', 3, NULL, NULL, NULL, '2017-06-22 11:11:09'),
(239, 27, 2, 'Citer deux fonctions de la langue', 2, NULL, NULL, NULL, '2017-06-22 11:11:09'),
(240, 27, 4, 'Compléter ce texte à trous avec les mots manquants. ', NULL, NULL, NULL, NULL, '2017-06-22 11:11:09'),
(241, 27, 6, 'Comment se font les déplacements du bol\nalimentaire le long du tube digestif ?', NULL, NULL, NULL, 1, '2017-06-22 11:11:09'),
(242, 27, 6, 'Comment appelle -t-on l\'accélération du transit intestinal ?', NULL, NULL, NULL, 1, '2017-06-22 11:11:09'),
(243, 27, 6, 'Quel est le nom du conduit qui relie le pharynx à\nl\'estomac ?', NULL, NULL, NULL, 1, '2017-06-22 11:11:09'),
(244, 27, 6, 'Dans quelle partie de l\'abdomen se trouve l\'estomac \net sous quel muscle ?', NULL, NULL, NULL, 1, '2017-06-22 11:11:09'),
(245, 27, 6, 'Quel est le nom du plus gros organe du tube diestif ?', NULL, NULL, NULL, 1, '2017-06-22 11:11:10'),
(246, 27, 6, 'Comment se nomme le réservoir à bile ?', NULL, NULL, NULL, 1, '2017-06-22 11:11:10'),
(247, 27, 6, 'Quel est le rôle du foie?', NULL, NULL, NULL, 1, '2017-06-22 11:11:10'),
(248, 27, 6, 'A quoi sert la bile?', NULL, NULL, NULL, 1, '2017-06-22 11:11:10'),
(249, 27, 7, 'Ecriver le nom des organes du tube digestif', NULL, NULL, 'AppDigestif.jpg', NULL, '2017-06-22 11:11:10'),
(250, 76, 1, 'Parmi les éléments suivants cocher ceux qui ne doivent PAS être présent dans l\'urine normale', NULL, NULL, NULL, NULL, '2017-06-29 13:46:23'),
(251, 76, 1, 'L\'odynurie signifie', NULL, NULL, NULL, NULL, '2017-06-29 13:46:23'),
(252, 76, 1, 'Hématurie signifie', NULL, NULL, NULL, NULL, '2017-06-29 13:46:24'),
(253, 76, 1, 'L\'anurie est urgence', NULL, NULL, NULL, NULL, '2017-06-29 13:46:24'),
(254, 76, 1, 'La cystite est une infection du rein', NULL, NULL, NULL, NULL, '2017-06-29 13:46:24'),
(255, 76, 4, 'Compléter ce texte à trous \navec les mots manquants.', NULL, NULL, NULL, NULL, '2017-06-29 13:46:24'),
(256, 76, 6, 'Comment se nomme le canal qui amène l\'urine de la vessie à l\'extérieur du corps ?', NULL, NULL, NULL, 1, '2017-06-29 13:46:25'),
(257, 76, 6, 'Par quel organe est produit l\'urine ?', NULL, NULL, NULL, 1, '2017-06-29 13:46:25'),
(258, 76, 6, 'Quel est le nom des conduits excréteurs qui poussent l\'urine des reins vers la vessie ?', NULL, NULL, NULL, 1, '2017-06-29 13:46:25'),
(259, 76, 6, 'Comment se nomme l\'organe musculo-membraneux qui est le réservoir d\'urine ?', NULL, NULL, NULL, 1, '2017-06-29 13:46:25'),
(260, 76, 6, 'Vrai ou faux : l\'urine est liquide non stérile ?', NULL, NULL, NULL, 1, '2017-06-29 13:46:25'),
(261, 76, 6, 'Que signifie la présence de globules blancs (leucocytes) en grande quantité dans l\'urine ?', NULL, NULL, NULL, 1, '2017-06-29 13:46:25'),
(262, 76, 6, 'Chez un homme, l\'urètre permet aussi le passage d\'un liquide biologique, lequel ?', NULL, NULL, NULL, 1, '2017-06-29 13:46:25'),
(263, 76, 6, 'Est-il possible de vivre normalement avec un seul rein?', NULL, NULL, NULL, 1, '2017-06-29 13:46:25'),
(264, 76, 6, 'Que signifie "dysurie"?', NULL, NULL, NULL, 1, '2017-06-29 13:46:25'),
(265, 76, 6, 'La pyélonéphrite est une infection de quel organe?', NULL, NULL, NULL, 1, '2017-06-29 13:46:25'),
(266, 76, 7, 'Compléter le schéma avec le nom des organes de l\'appareil urinaire.', NULL, NULL, 'AppUrinaire.jpg', NULL, '2017-06-29 13:46:25'),
(267, 3, 6, 'Définir une entorse ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:42'),
(268, 3, 6, 'Définir luxation articulaire ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:42'),
(269, 3, 6, 'Quelle est la première vertèbre qui supporte\n le crâne ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:42'),
(270, 3, 6, 'Quelle est le nom de la 2ème vertèbre qui \npermet l\'articulation de la tête ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:42'),
(271, 3, 6, 'Quel est le nom du liquide que le médecin peut retirer d\'une articulation enflée ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:42'),
(272, 3, 6, 'Quels types de muscles peuvent être contrôlés par la volonté ? Donné un exemple', NULL, NULL, NULL, 1, '2017-07-04 09:10:43'),
(273, 3, 6, 'Citer un muscle strié automatique ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:43'),
(274, 3, 6, 'Où trouve-t-on des muscles lisses ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:43'),
(275, 3, 6, 'Les mains, les bras sont reliés au thorax par quelle ceinture Où trouve-t-on des muscles lisses ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:43'),
(276, 3, 6, 'Les pieds, les jambes sont reliés au bassin par quelle ceinture ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:43'),
(277, 3, 6, 'Combien de vertèbres constituent la colonne vertébrale', NULL, NULL, NULL, 1, '2017-07-04 09:10:43'),
(278, 3, 6, 'Quelle est la différence entre l\'abduction et l\'adduction ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:43'),
(279, 3, 6, 'De quel minéral est principalement constitué de l’os ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:43'),
(280, 3, 6, 'Quels sont les os du bras et de l’avant-bras ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:43'),
(281, 3, 6, 'Combien a-t-on de vertèbres cervicales ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:43'),
(282, 3, 6, 'Combien a-t-on de vertèbres lombaires ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:43'),
(283, 3, 6, 'Quel est le rôle des bourses synoviales ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:43'),
(284, 3, 6, 'Comment appelle-t-on l’inflammation d’une capsule articulaire ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:44'),
(285, 3, 6, 'Comment appelle-t-on les muscles soumis à l’action de la volonté', NULL, NULL, NULL, 1, '2017-07-04 09:10:44'),
(286, 3, 6, 'Comment appelle-t-on deux muscles dont l’action est la même ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:44'),
(287, 3, 6, 'Comment appelle-t-on deux muscles dont l’action s’oppose ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:44'),
(288, 3, 6, 'Quel examen complémentaire est réalisé en première intention afin de rechercher une fracture ?', NULL, NULL, NULL, 1, '2017-07-04 09:10:44'),
(289, 3, 7, 'Ecriver le nom des os du squelette', NULL, NULL, 'squelette.jpg', NULL, '2017-07-04 09:10:44'),
(290, 3, 7, 'Nommer les points A, B et C', NULL, NULL, 'Coude.jpg', NULL, '2017-07-04 09:10:44'),
(291, 3, 7, 'Nommer les points A, B et C', NULL, NULL, 'Genou.jpg', NULL, '2017-07-04 09:10:45'),
(292, 3, 7, 'Nommer les points A et B', NULL, NULL, 'Hanche.jpg', NULL, '2017-07-04 09:10:45');

-- --------------------------------------------------------

--
-- Structure de la table `t_questionnaire`
--

CREATE TABLE IF NOT EXISTS `t_questionnaire` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Questionnaire_Name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `PDF` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Corrige_PDF` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Creation_Date` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_questionnaire`
--

TRUNCATE TABLE `t_questionnaire`;
--
-- Contenu de la table `t_questionnaire`
--

INSERT INTO `t_questionnaire` (`ID`, `Questionnaire_Name`, `PDF`, `Corrige_PDF`, `Creation_Date`) VALUES
(5, '2017 v4', '2017 v3pdf', '2017 v3pdf_corrige', NULL),
(6, 'questionnaire test', 'testpdf', 'testpdf_corrige', NULL),
(7, 'test 2', 'test 2pdf', 'test 2pdf_corrige', NULL),
(8, 'test', 'testpdf', 'testpdf_corrige', NULL),
(9, 'test 4', 'test 4pdf', 'test 4pdf_corrige', NULL),
(10, 'test 4', 'test 4pdf', 'test 4pdf_corrige', NULL),
(11, 'test X', 'test Xpdf', 'test Xpdf_corrige', NULL),
(12, 'questionnaire 1', 'questionnaire 1pdf', 'questionnaire 1pdf_corrige', NULL),
(13, 'questionnaire 1', 'questionnaire 1pdf', 'questionnaire 1pdf_corrige', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `t_question_questionnaire`
--

CREATE TABLE IF NOT EXISTS `t_question_questionnaire` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` int(11) NOT NULL,
  `FK_Questionnaire` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_t_question_has_t_questionnaire_t_questionnaire1_idx` (`FK_Questionnaire`),
  KEY `fk_t_question_has_t_questionnaire_t_question1_idx` (`FK_Question`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_question_questionnaire`
--

TRUNCATE TABLE `t_question_questionnaire`;
--
-- Contenu de la table `t_question_questionnaire`
--

INSERT INTO `t_question_questionnaire` (`ID`, `FK_Question`, `FK_Questionnaire`) VALUES
(1, 177, 5),
(2, 160, 5),
(3, 164, 5),
(4, 153, 5),
(5, 226, 5),
(6, 211, 5),
(7, 181, 5),
(8, 182, 5),
(9, 166, 5),
(10, 228, 5),
(11, 188, 5),
(12, 206, 5),
(13, 151, 5),
(14, 190, 5),
(15, 247, 5),
(16, 208, 5),
(17, 183, 5),
(18, 219, 5),
(19, 192, 5),
(20, 248, 5),
(21, 185, 5),
(22, 220, 5),
(23, 193, 5),
(24, 233, 5),
(25, 230, 5),
(26, 189, 5),
(27, 155, 5),
(28, 215, 5),
(29, 197, 5),
(30, 216, 5),
(31, 172, 5),
(32, 162, 5),
(33, 237, 5),
(34, 168, 5),
(35, 209, 5),
(36, 169, 5),
(37, 171, 5),
(38, 210, 5),
(39, 175, 5),
(40, 176, 5),
(41, 203, 5),
(42, 223, 5),
(43, 152, 5),
(44, 241, 5),
(45, 213, 5),
(46, 207, 5),
(47, 167, 5),
(48, 156, 5),
(49, 173, 5),
(50, 246, 5),
(51, 178, 5),
(52, 249, 5),
(53, 244, 5),
(54, 174, 5),
(55, 165, 5),
(56, 199, 5),
(57, 242, 5),
(58, 224, 5),
(59, 196, 5),
(60, 161, 5),
(61, 127, 6),
(62, 113, 7),
(63, 121, 7),
(64, 120, 7),
(65, 110, 7),
(66, 133, 7),
(67, 119, 7),
(68, 116, 7),
(69, 123, 7),
(70, 131, 7),
(71, 118, 7),
(72, 134, 7),
(73, 111, 7),
(74, 128, 7),
(75, 125, 7),
(76, 130, 7),
(77, 114, 7),
(78, 124, 7),
(79, 135, 7),
(80, 126, 7),
(81, 115, 7),
(82, 100, 8),
(83, 142, 8),
(84, 148, 8),
(85, 143, 9),
(86, 102, 9),
(87, 150, 9),
(88, 141, 10),
(89, 109, 10),
(90, 106, 10),
(91, 283, 11),
(92, 117, 11),
(93, 270, 11),
(94, 113, 12),
(95, 277, 12),
(96, 279, 12),
(97, 117, 12),
(98, 133, 12),
(99, 287, 12),
(100, 285, 12),
(101, 273, 12),
(102, 274, 12),
(103, 291, 12),
(104, 229, 12),
(105, 153, 12),
(106, 249, 12),
(107, 200, 12),
(108, 172, 12),
(109, 130, 13),
(110, 289, 13),
(111, 279, 13),
(112, 277, 13),
(113, 122, 13),
(114, 274, 13),
(115, 114, 13),
(116, 123, 13),
(117, 281, 13),
(118, 269, 13),
(119, 129, 13),
(120, 271, 13),
(121, 110, 13),
(122, 275, 13),
(123, 278, 13),
(124, 133, 13),
(125, 284, 13),
(126, 131, 13),
(127, 112, 13),
(128, 267, 13);

-- --------------------------------------------------------

--
-- Structure de la table `t_question_type`
--

CREATE TABLE IF NOT EXISTS `t_question_type` (
  `ID` int(11) NOT NULL,
  `Type_Name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_question_type`
--

TRUNCATE TABLE `t_question_type`;
--
-- Contenu de la table `t_question_type`
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

CREATE TABLE IF NOT EXISTS `t_table_cell` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` int(11) NOT NULL,
  `Content` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Column_Nb` int(11) DEFAULT NULL,
  `Row_Nb` int(11) DEFAULT NULL,
  `Header` tinyint(1) DEFAULT NULL,
  `Display_In_Question` tinyint(1) DEFAULT NULL,
  `Creation_Date` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_t_table_cell_t_question_idx` (`FK_Question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_table_cell`
--

TRUNCATE TABLE `t_table_cell`;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_temp_questionnaire`
--

TRUNCATE TABLE `t_temp_questionnaire`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_temp_questionnaire_answer`
--

TRUNCATE TABLE `t_temp_questionnaire_answer`;
-- --------------------------------------------------------

--
-- Structure de la table `t_topic`
--

CREATE TABLE IF NOT EXISTS `t_topic` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Parent_Topic` int(11) DEFAULT NULL,
  `Topic` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Creation_Date` datetime DEFAULT NULL,
  `Archive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_t_topic_t_topic1_idx` (`FK_Parent_Topic`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_topic`
--

TRUNCATE TABLE `t_topic`;
--
-- Contenu de la table `t_topic`
--

INSERT INTO `t_topic` (`ID`, `FK_Parent_Topic`, `Topic`, `Creation_Date`, `Archive`) VALUES
(1, NULL, 'SecretariatMedical', NULL, NULL),
(2, 1, 'Informatique', NULL, NULL),
(3, 1, 'AppLocomoteur', NULL, NULL),
(19, NULL, 'test 1', '2017-06-29 11:25:35', NULL),
(21, NULL, 'test 2', NULL, NULL),
(25, NULL, 'Informatique', NULL, NULL),
(27, 25, 'Algorithmie', NULL, NULL),
(41, NULL, 'test 3', NULL, NULL),
(76, 19, 'sujet 1 test 1', '2017-06-29 11:20:31', NULL),
(80, 19, 'sujet 2 test 1', '2017-06-29 12:58:40', NULL),
(81, 21, 'sujet 1 test 2', '2017-07-03 11:22:27', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `t_user`
--

CREATE TABLE IF NOT EXISTS `t_user` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FK_User_Type` int(11) NOT NULL,
  `User` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_t_user_t_user_type1_idx` (`FK_User_Type`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_user`
--

TRUNCATE TABLE `t_user`;
--
-- Contenu de la table `t_user`
--

INSERT INTO `t_user` (`ID`, `FK_User_Type`, `User`, `Password`) VALUES
(1, 2, 'admin', '$2y$10$aEqREuXq.4BgSq6ZJrfWre2FZEeOKvh8BIadXX3Hix0vzubqdA.ja'),
(2, 1, 'user', '$2y$10$aEqREuXq.4BgSq6ZJrfWre2FZEeOKvh8BIadXX3Hix0vzubqdA.ja');

-- --------------------------------------------------------

--
-- Structure de la table `t_user_type`
--

CREATE TABLE IF NOT EXISTS `t_user_type` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `access_level` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vider la table avant d'insérer `t_user_type`
--

TRUNCATE TABLE `t_user_type`;
--
-- Contenu de la table `t_user_type`
--

INSERT INTO `t_user_type` (`ID`, `name`, `access_level`) VALUES
(1, 'Administrator', 1),
(2, 'Member', 2);

--
-- Contraintes pour les tables exportées
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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
