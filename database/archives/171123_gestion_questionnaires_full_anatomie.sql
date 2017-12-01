-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  jeu. 23 nov. 2017 à 15:26
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

--
-- Déchargement des données de la table `t_cloze_text`
--

INSERT INTO `t_cloze_text` (`ID`, `FK_Question`, `Cloze_Text`, `Creation_Date`) VALUES
(1, 1, 'L\'appareil génital masculin est constitué de deux […..] qui se trouvent dans un sac cutané fibro-musculaire appelé le […..] ou […..], il possède des glandes annexes qui sont les […..] et la […..] qui produisent le [.....] et ce sont les [.....] qui acheminent le sperme vers […..] qui traverse le pénis.', '2017-11-17 08:12:16'),
(2, 26, 'Le bol alimentaire est d\'abord mastiqué dans [...] puis lorsque on avale, il descend par [...] avant d\'arriver dans […]', '2017-11-17 08:12:54'),
(3, 41, 'L\'appareil urinaire est constitué de 2 organes principaux qui sont les […..], ils sont constitués d\'unités fonctionnelles qui se nomment  les [.....] capables de filtrer le [.....] pour produire de [.....], ils possèdent chacun un canal excréteur qui sont les […..] qui conduisent l\'urine jusque dans la […..] pour ensuite être éliminée à l\'extérieur par un canal qui s\'appelle [.....]', '2017-11-17 08:13:06'),
(4, 56, 'Le cœur est un organe [...], il est aussi appelé [...], logé au centre du [...], il fonctionne comme [...] pour faire circuler [...] dans notre corps.', '2017-11-17 08:19:48'),
(5, 57, 'La phase de contraction du cœur s’appelle la […] et la phase de remplissage s\'appelle la […]', '2017-11-17 08:19:48'),
(6, 58, 'Le rétressissement d\'une artère s\'appelle une […]', '2017-11-17 08:19:48'),
(7, 59, 'Une coloration bleutée de la peau s\'appelle une [...]', '2017-11-17 08:19:48'),
(8, 60, 'La maladie créant un rétressissement et un durcissement progressif de la paroi des artères s’appelle [...]', '2017-11-17 08:19:48'),
(9, 80, 'Les poumons sont logés dans […..], ils reposent sur un muscle plat, appelé le […..]. Ils sont découpés en […..] par des […..]', '2017-11-17 08:19:58'),
(10, 81, 'Au moment des échanges gazeux respiratoire, […] sort de l’artériol pulmonnaire puis est expiré et [….] est inspiré avant de pénétrer dans le sang.', '2017-11-17 08:19:58'),
(11, 82, 'La difficulté à respirer s\'appelle […]. Elle survient en premier à […] puis également […] lorsqu\'elle s\'aggrave', '2017-11-17 08:19:58'),
(12, 104, 'Le cerveau d\'un adulte pèse environ […..] g, il est divisé en deux […..] gauche et droite, ses lobes sont séparés par des […..] et il est le siège des fonctions […..] supérieures, de la conscience et des facultés cognitives.', '2017-11-17 08:20:11'),
(13, 165, 'Le corps humain contient environ 4 à litres de [….] contenu dans les [….], les […..], les […..] et le […..], il est formé d\'un liquide qu\'on appelle le […..] et des cellules ou éléments figurés qui sont les […..], les [….] et les […..].', '2017-11-17 08:22:44'),
(14, 166, 'Les fonctions du sang sont le transport de […..]  vers les cellules de nos tissus et organes, il maintient la composition du milieu intérieur pour éviter un déséquilibre, il a donc un rôle d\'[…..]. Il régule et maintient la […..] par absorption et répartition de la chaleur et il s\'occupe de l\'équilibre [.....] pour maintenir un [.....] sanguin à 7,4.', '2017-11-17 08:22:44'),
(15, 189, 'le système endocrinien est formé de […..] qui sont des organes constitués de cellules épithéliales dont la fonction est de produire des […..] et des […..]. Le produit des glandes […..] est rejeté à l\'extérieur du corps ou dans le tube digestif et les sécrétions ou hormones des glandes [.....] seront déversées dans le sang.', '2017-11-17 08:22:54');

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

--
-- Déchargement des données de la table `t_cloze_text_answer`
--

INSERT INTO `t_cloze_text_answer` (`ID`, `FK_Cloze_Text`, `Answer`, `Answer_Order`, `Creation_Date`) VALUES
(1, 1, 'testicules', 1, '2017-11-17 08:12:16'),
(2, 1, 'scrotum', 2, '2017-11-17 08:12:16'),
(3, 1, 'bourse', 3, '2017-11-17 08:12:16'),
(4, 1, 'vésicule séminale', 4, '2017-11-17 08:12:16'),
(5, 1, 'prostate', 5, '2017-11-17 08:12:16'),
(6, 1, 'sperme', 6, '2017-11-17 08:12:16'),
(7, 1, 'voies spermatiques', 7, '2017-11-17 08:12:16'),
(8, 1, 'l\'urètre', 8, '2017-11-17 08:12:16'),
(9, 2, 'la bouche', 1, '2017-11-17 08:12:54'),
(10, 2, 'l\'œsophage', 2, '2017-11-17 08:12:54'),
(11, 2, 'l\'estomac', 3, '2017-11-17 08:12:54'),
(12, 3, 'reins', 1, '2017-11-17 08:13:06'),
(13, 3, 'néphrons', 2, '2017-11-17 08:13:06'),
(14, 3, 'sang', 3, '2017-11-17 08:13:06'),
(15, 3, 'l\'urine', 4, '2017-11-17 08:13:06'),
(16, 3, 'uretères', 5, '2017-11-17 08:13:06'),
(17, 3, 'vessie', 6, '2017-11-17 08:13:06'),
(18, 3, 'l\'urètre', 7, '2017-11-17 08:13:06'),
(19, 4, 'musculaire creux', 1, '2017-11-17 08:19:48'),
(20, 4, 'myocarde', 2, '2017-11-17 08:19:48'),
(21, 4, 'thorax', 3, '2017-11-17 08:19:48'),
(22, 4, 'une pompe', 4, '2017-11-17 08:19:48'),
(23, 4, 'le sang', 5, '2017-11-17 08:19:48'),
(24, 5, 'systole', 1, '2017-11-17 08:19:48'),
(25, 5, 'dyastole', 2, '2017-11-17 08:19:48'),
(26, 6, 'sténose', 1, '2017-11-17 08:19:48'),
(27, 7, 'cyanose', 1, '2017-11-17 08:19:48'),
(28, 8, 'artériosclérose', 1, '2017-11-17 08:19:48'),
(29, 9, 'la cage thoracique', 1, '2017-11-17 08:19:58'),
(30, 9, 'diaphragme', 2, '2017-11-17 08:19:58'),
(31, 9, 'lobes', 3, '2017-11-17 08:19:58'),
(32, 9, 'scissures', 4, '2017-11-17 08:19:58'),
(33, 10, 'Gaz carbonique', 1, '2017-11-17 08:19:58'),
(34, 10, 'Oxygène', 2, '2017-11-17 08:19:58'),
(35, 11, 'dyspnée', 1, '2017-11-17 08:19:58'),
(36, 11, 'repos', 2, '2017-11-17 08:19:58'),
(37, 11, 'effort', 3, '2017-11-17 08:19:58'),
(38, 12, '1300', 1, '2017-11-17 08:20:11'),
(39, 12, 'hémisphères', 2, '2017-11-17 08:20:11'),
(40, 12, 'scissures', 3, '2017-11-17 08:20:11'),
(41, 12, 'nerveuses', 4, '2017-11-17 08:20:11'),
(42, 13, 'sang', 1, '2017-11-17 08:22:44'),
(43, 13, 'artères', 2, '2017-11-17 08:22:44'),
(44, 13, 'veines', 3, '2017-11-17 08:22:44'),
(45, 13, 'capillaires', 4, '2017-11-17 08:22:44'),
(46, 13, 'cœur', 5, '2017-11-17 08:22:44'),
(47, 13, 'plasma', 6, '2017-11-17 08:22:44'),
(48, 13, 'globules rouges', 7, '2017-11-17 08:22:44'),
(49, 13, 'globules blancs', 8, '2017-11-17 08:22:44'),
(50, 13, 'plaquettes', 9, '2017-11-17 08:22:44'),
(51, 14, 'l\'oxygène', 1, '2017-11-17 08:22:44'),
(52, 14, 'homéostasie', 2, '2017-11-17 08:22:44'),
(53, 14, 'température', 3, '2017-11-17 08:22:44'),
(54, 14, 'acide-base', 4, '2017-11-17 08:22:44'),
(55, 14, 'pH', 5, '2017-11-17 08:22:44'),
(56, 15, 'glandes', 1, '2017-11-17 08:22:54'),
(57, 15, 'substances', 2, '2017-11-17 08:22:54'),
(58, 15, 'sécrétions', 3, '2017-11-17 08:22:54'),
(59, 15, 'exocrines', 4, '2017-11-17 08:22:54'),
(60, 15, 'endocrines', 5, '2017-11-17 08:22:54');

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

--
-- Déchargement des données de la table `t_free_answer`
--

INSERT INTO `t_free_answer` (`ID`, `FK_Question`, `Answer`, `Creation_Date`) VALUES
(1, 2, 'Le pénis ou la verge', '2017-11-17 08:12:16'),
(2, 3, 'Adénome prostatique, obstruction de la sortie de la vessie, miction difficile (dysurie)', '2017-11-17 08:12:16'),
(3, 4, 'Le spermogramme', '2017-11-17 08:12:16'),
(4, 5, 'Une fonction exocrine, la production de gamètes mâles, les spermatozoïdes. Une fonction endocrine, la production de la testostérone, principale hormone masculine', '2017-11-17 08:12:16'),
(5, 6, 'Les spermatozoïdes', '2017-11-17 08:12:16'),
(6, 7, 'La testosterone', '2017-11-17 08:12:16'),
(7, 8, 'Faux, c\'est un muscle lisse', '2017-11-17 08:12:16'),
(8, 9, 'Vrai, production de gamètes, les ovules et des hormones féminines, oestrogènes et progestérone', '2017-11-17 08:12:16'),
(9, 10, '28 jours', '2017-11-17 08:12:16'),
(10, 11, 'La phase folliculaire, la phase lutéale', '2017-11-17 08:12:16'),
(11, 12, 'Croissance d\'un œuf à l\'extérieur de la cavité utérine souvent dans une trompe utérine (grossesse tubaire)', '2017-11-17 08:12:16'),
(12, 13, 'Primipare : femme qui accouche pour la première fois\nprimigeste : femme dont c\'est la première grossesse\nmultigeste : femme dont la présente grossesse n\'est pas la première', '2017-11-17 08:12:16'),
(13, 14, 'Les trompes de Fallope', '2017-11-17 08:12:16'),
(14, 15, 'L\'ovogenèse', '2017-11-17 08:12:16'),
(15, 16, 'L\'endomètre', '2017-11-17 08:12:16'),
(16, 17, 'Les oestrogènes, la progestérone et les androgènes', '2017-11-17 08:12:16'),
(17, 18, 'Le score d\'Apgar', '2017-11-17 08:12:16'),
(18, 27, 'Grâce aux mouvements péristaltiques', '2017-11-17 08:12:54'),
(19, 28, 'Diarrhées, émission de selles liquides et fréquentes.', '2017-11-17 08:12:54'),
(20, 29, 'L\'œsophage.', '2017-11-17 08:12:54'),
(21, 30, 'La partie supérieure de l\'abdomen et sous le diaphragme.', '2017-11-17 08:12:54'),
(22, 31, 'le foie', '2017-11-17 08:12:54'),
(23, 32, 'La vésicule biliaire', '2017-11-17 08:12:54'),
(24, 33, 'Sécrétion de la bile, nombreuses synthèses de \nprotéines par exemple des facteurs de coagulation\nmétabolisme des graisses, détoxification', '2017-11-17 08:12:54'),
(25, 34, 'A la digestion des graisses', '2017-11-17 08:12:54'),
(26, 42, 'L\'urètre', '2017-11-17 08:13:06'),
(27, 43, 'Les reins', '2017-11-17 08:13:06'),
(28, 44, 'Les uretères', '2017-11-17 08:13:06'),
(29, 45, 'La vessie', '2017-11-17 08:13:06'),
(30, 46, 'Faux, l\'urine est un liquide stérile', '2017-11-17 08:13:06'),
(31, 47, 'Une infection urinaire', '2017-11-17 08:13:06'),
(32, 48, 'Le sperme', '2017-11-17 08:13:06'),
(33, 49, 'Oui il est possible de vivre avec un seul rein', '2017-11-17 08:13:06'),
(34, 50, 'Difficulté à la miction, vidange incomplète avec résidu vésical après la miction', '2017-11-17 08:13:06'),
(35, 51, 'Rein (bassinet, néphrons)', '2017-11-17 08:13:06'),
(36, 61, 'Une fonction de pompe, circulation du sang dans les vaisseaux', '2017-11-17 08:19:48'),
(37, 62, '2 oreillettes ou atrium et 2 ventricules', '2017-11-17 08:19:48'),
(38, 63, 'Artères', '2017-11-17 08:19:48'),
(39, 64, 'Transport du sang oxygéné du cœur vers tous les organes du corps, appelée aussi circulation systémique', '2017-11-17 08:19:48'),
(40, 65, 'Transport du sang du cœur dans les alvéoles des poumons pour recharger le sang en oxygène et pour éliminer le dioxyde de carbone, appelée circulation pulmonaire', '2017-11-17 08:19:48'),
(41, 66, 'La catégorie des muscles striés et il est le seul de à ne pas dépendre de la volonté', '2017-11-17 08:19:48'),
(42, 67, 'Distribuer le sang oxygéné dans tout le corps', '2017-11-17 08:19:48'),
(43, 68, 'Ramener le sang chargé en gaz carbonique (CO2) vers les poumons', '2017-11-17 08:19:48'),
(44, 69, 'L\'aorte, elle part du ventricule gauche', '2017-11-17 08:19:48'),
(45, 70, 'La contraction du cœur et l\'expulsion du sang ', '2017-11-17 08:19:48'),
(46, 71, 'La dilatation et l\'aspiration du sang', '2017-11-17 08:19:48'),
(47, 72, 'C\'est la force exercée par le flux sanguin contre la paroi des artères du corps', '2017-11-17 08:19:48'),
(48, 73, '140/90 mmHg', '2017-11-17 08:19:48'),
(49, 74, 'coronaires', '2017-11-17 08:19:48'),
(50, 75, 'Nécrose d’une partie du muscle cardiaque', '2017-11-17 08:19:48'),
(51, 76, 'Respriation subjectivement difficile', '2017-11-17 08:19:48'),
(52, 83, 'Le diaphragme', '2017-11-17 08:19:58'),
(53, 84, '8-12/14 fois par minute, moins vite au repos', '2017-11-17 08:19:58'),
(54, 85, 'L\' asthme, BPCO, une pneumonie, emphysème, une obstruction des voies  respiratoires, une intoxication au monoxyde de carbone, etc.', '2017-11-17 08:19:58'),
(55, 86, 'Vrai le poumon gauche est plus petit que le poumon droit', '2017-11-17 08:19:58'),
(56, 87, 'La plèvre', '2017-11-17 08:19:58'),
(57, 88, 'Le médiastin', '2017-11-17 08:19:58'),
(58, 89, 'Les alvéoles', '2017-11-17 08:19:58'),
(59, 90, 'Vrai ', '2017-11-17 08:19:58'),
(60, 91, 'Faux, le diaphragme se relâche et s\'élève lors de l\'expiration.', '2017-11-17 08:19:58'),
(61, 92, 'Les globules rouges ou hématies', '2017-11-17 08:19:58'),
(62, 93, 'Obstruction aiguë, accès de dyspnée expiratoire, spasmes, congestion et hypersécrétion du mucus des bronches', '2017-11-17 08:19:58'),
(63, 94, 'Difficulté de la respiration (ressentie par le malade donc symptôme survenant d’abord à l’effort, puis même au repos).', '2017-11-17 08:19:58'),
(64, 105, '12 paires', '2017-11-17 08:20:11'),
(65, 106, 'Le liquide céphalo-rachidien (cérébrospinal)', '2017-11-17 08:20:11'),
(66, 107, 'Lobe occipital', '2017-11-17 08:20:11'),
(67, 108, 'Une neurone ou cellule nerveuse', '2017-11-17 08:20:11'),
(68, 109, 'Les synapses', '2017-11-17 08:20:11'),
(69, 110, 'L\'encéphale', '2017-11-17 08:20:11'),
(70, 111, 'Le tronc cérébral', '2017-11-17 08:20:11'),
(71, 112, 'L\'hypothalamus', '2017-11-17 08:20:11'),
(72, 113, 'Il est situé à la base du crâne sur le tronc cérébral\nLa régulation de l\'équilibre du corps, l\'harmonie et la coordination des mouvements', '2017-11-17 08:20:11'),
(73, 114, 'Les lobes frontaux, les lobes pariétaux, les lobes temporaux, les lobes occipitaux', '2017-11-17 08:20:11'),
(74, 115, 'La capacité du système nerveux de provoquer la contraction musculaire volontaire', '2017-11-17 08:20:11'),
(75, 116, 'La peau', '2017-11-17 08:20:11'),
(76, 117, 'Le réflexe', '2017-11-17 08:20:11'),
(77, 118, 'Le système sympathique et le système parasympathique', '2017-11-17 08:20:11'),
(78, 119, 'La vue, l\'ouie, le goût, le toucher, l\'odorat', '2017-11-17 08:20:11'),
(79, 120, 'Vrai, la rétine est une membrane constituant l\'organe de réception des sensations visuelles, grâce à ses cônes et ses bâtonnets sensibles à la lumière', '2017-11-17 08:20:11'),
(80, 121, 'L\'iris', '2017-11-17 08:20:11'),
(81, 122, 'Le tympan', '2017-11-17 08:20:11'),
(82, 123, 'Olfaction', '2017-11-17 08:20:11'),
(83, 127, 'Un neurone', '2017-11-17 08:20:26'),
(84, 128, 'Respiration cellulaire, production d\'énergie (ATP)', '2017-11-17 08:20:26'),
(85, 129, 'Assemblée de cellules ayant une fonction \nidentique', '2017-11-17 08:20:26'),
(86, 130, 'l\'ADN qui est du matériel génétique', '2017-11-17 08:20:26'),
(87, 131, 'Science qui a pour objet l’étude des maladies', '2017-11-17 08:20:26'),
(88, 132, 'Renseingement que fourni le malade sur lui-même ou son entourage sur le début de sa maladie jusqu\'au moment où il se trouve soumis à l\'observation du médecin', '2017-11-17 08:20:26'),
(89, 133, 'Il s’agit des analyses médicales ou imageries médicales demandées pour un patient présentant une maladie ou afin d’affiner un diagnostic', '2017-11-17 08:20:26'),
(90, 134, 'Acte par lequel le médecin, groupant les symptômes qu’offre son patient, les rattache à une maladie', '2017-11-17 08:20:26'),
(91, 135, 'La cellule', '2017-11-17 08:20:26'),
(92, 136, 'La mitose', '2017-11-17 08:20:26'),
(93, 139, 'Etirement des ligaments articulaires à la suite d\'une distortion brusque d\'une articulation', '2017-11-17 08:22:32'),
(94, 140, 'Déplacement permanent de deux surfaces articulaires', '2017-11-17 08:22:32'),
(95, 141, 'L\'atlas, première vertèbre du rachis', '2017-11-17 08:22:32'),
(96, 142, 'L\'axis, qui s\'articule avec l\'atlas\n', '2017-11-17 08:22:32'),
(97, 143, 'La synovie', '2017-11-17 08:22:32'),
(98, 144, 'Muscles striés squelettiques, par exemple : biceps, triceps, quadriceps, abducteurs, adducteurs, extenseurs,\nfléchisseurs des membres, muscles du dos, du tronc, fessiers, muscles oculaires, de la main', '2017-11-17 08:22:32'),
(99, 145, 'Le myocarde ou muscle cardiaque', '2017-11-17 08:22:32'),
(100, 146, 'Dans la paroi des viscères, des organes du tube digestif, de l\'appareil urogénital, de l\'appareil respiratoire, des vaisseaux etc.\n', '2017-11-17 08:22:32'),
(101, 147, 'La ceinture scapulaire', '2017-11-17 08:22:32'),
(102, 148, 'La ceinture pelvienne', '2017-11-17 08:22:32'),
(103, 149, '33 vertèbres', '2017-11-17 08:22:32'),
(104, 150, 'Lors d\'un mouvement, l\'abduction s\'éloigne du corps et l\'adduction se rapproche du corps', '2017-11-17 08:22:32'),
(105, 151, 'Calcium', '2017-11-17 08:22:32'),
(106, 152, 'Humérus, Radius, Ulna (=cubitus)', '2017-11-17 08:22:32'),
(107, 153, '7', '2017-11-17 08:22:32'),
(108, 154, '5', '2017-11-17 08:22:32'),
(109, 155, 'Facilite le glissement entre certains tendons, muscles et os / évite les frottements', '2017-11-17 08:22:32'),
(110, 156, 'Capsulite', '2017-11-17 08:22:32'),
(111, 157, 'Muscle strillé', '2017-11-17 08:22:32'),
(112, 158, 'Agoniste', '2017-11-17 08:22:32'),
(113, 159, 'Antagoniste', '2017-11-17 08:22:32'),
(114, 160, 'Radiographie', '2017-11-17 08:22:32'),
(115, 167, 'Le globule rouge transporte de l\'oxygène pour les tissus et reprend le gaz carbonique qui est éliminé\ndans les alvéoles pulmonaires', '2017-11-17 08:22:44'),
(116, 168, '1', '2017-11-17 08:22:44'),
(117, 169, '0', '2017-11-17 08:22:44'),
(118, 170, 'Vrai ', '2017-11-17 08:22:44'),
(119, 171, 'Faux, car la diminution des plaquettes sanguines fait augmenter le risque d\'hémorragies', '2017-11-17 08:22:44'),
(120, 172, 'L\'érythropoïèse', '2017-11-17 08:22:44'),
(121, 173, 'Une leucémie', '2017-11-17 08:22:44'),
(122, 174, 'D\'assurer la défense de l\'organisme contre les infections et agressions', '2017-11-17 08:22:44'),
(123, 175, 'Les anticorps', '2017-11-17 08:22:44'),
(124, 176, 'Les lymphocytes', '2017-11-17 08:22:44'),
(125, 177, 'Les leucocytes', '2017-11-17 08:22:44'),
(126, 178, 'L\'hémostase', '2017-11-17 08:22:44'),
(127, 179, 'Les thrombocytes ou plaquettes', '2017-11-17 08:22:44'),
(128, 180, 'Dans les globules rouges et elle transporte l\'oxygène', '2017-11-17 08:22:44'),
(129, 181, 'La moelle osseuse rouge', '2017-11-17 08:22:44'),
(130, 182, 'L\'hématopoïèse', '2017-11-17 08:22:44'),
(131, 183, 'Des anticorps', '2017-11-17 08:22:44'),
(132, 184, 'Un antigène du groupe A, un antigène du groupe B, les 2 antigènes AB ou aucun le groupe O', '2017-11-17 08:22:44'),
(133, 185, 'Présence dans l\'organisme d\'anticorps et de cellules capables d\'assurer une première défense contre les \nagents pathogènes.', '2017-11-17 08:22:44'),
(134, 186, 'Les bactéries, les virus, les champignons, les parasites, les toxines, les cellules malades ou cancéreuses', '2017-11-17 08:22:44'),
(135, 187, 'D\'immunité acquise', '2017-11-17 08:22:44'),
(136, 188, 'Réponse immunitaire', '2017-11-17 08:22:44'),
(137, 190, 'Les glandes endocrines.', '2017-11-17 08:22:54'),
(138, 191, 'Ce sont des messagers chimiques qui influencent le fonctionnement cellulaire.', '2017-11-17 08:22:54'),
(139, 192, 'Les parathyroïdes, la thyroïde, les surrénales, l\'hypophyse, les ovaires, les testicules, l\'hypothalamus', '2017-11-17 08:22:54'),
(140, 193, 'le pancréas, le foie, les gonades.', '2017-11-17 08:22:54'),
(141, 194, 'L\'insuline et le glucagon.', '2017-11-17 08:22:54'),
(142, 195, 'A la base du cerveau dans la selle turcique.', '2017-11-17 08:22:54'),
(143, 196, 'Hyperthyroïdie, hypothyroïdie, maladie de Basedow, le goitre.', '2017-11-17 08:22:54'),
(144, 197, 'La thyroïde.', '2017-11-17 08:22:54'),
(145, 198, 'A la base du cou de chaque côté de la trachée.', '2017-11-17 08:22:54'),
(146, 199, 'La régulation du calcium.', '2017-11-17 08:22:54'),
(147, 200, 'La corticosurrénale et la médullosurrénale.', '2017-11-17 08:22:54'),
(148, 201, 'Les testicules.', '2017-11-17 08:22:54'),
(149, 202, 'Une fonction exocrine qui produit les gamètes mâles ou spermatozoïdes, la spermatogenèse.\nUne fonction endocrine qui produit la testostérone, hormone androgène.', '2017-11-17 08:22:54'),
(150, 203, 'Les ovaires.', '2017-11-17 08:22:54'),
(151, 204, 'Une fonction exocrine, la production des ovules ou gamètes femelles, l\'ovogenèse.\nUne fonction endocrine, la production des oestrogènes et la progestérone.', '2017-11-17 08:22:54'),
(152, 206, 'Faux : trop longs, l\'image se forme devant la rétine', '2017-11-17 08:24:07'),
(153, 207, 'Myopie, presbytie, hypermétropie ', '2017-11-17 08:24:07'),
(154, 208, 'dyschromatopsies, daltonisme ', '2017-11-17 08:24:07'),
(155, 209, 'Dégénérescence maculaire liée à l\'âge: principale cause de cécité rétinienne chez le sujet âgé', '2017-11-17 08:24:07'),
(156, 210, 'La vue, l\'ouie, le goût, le toucher, l\'odorat', '2017-11-17 08:24:07'),
(157, 211, 'Vrai, la rétine est une membrane constituant l\'organe de réception des sensations visuelles, grâce à ses cônes et ses bâtonnets sensibles à la lumière', '2017-11-17 08:24:07'),
(158, 212, 'Un fond d\'œil', '2017-11-17 08:24:07'),
(159, 213, 'L\'iris', '2017-11-17 08:24:07'),
(160, 214, 'La trompe d\'Eustache', '2017-11-17 08:24:07'),
(161, 215, 'Le tympan', '2017-11-17 08:24:07'),
(162, 216, 'Le marteau, l\'enclume et l\'étrier', '2017-11-17 08:24:07'),
(163, 217, 'Le fond d\'œil', '2017-11-17 08:24:07'),
(164, 218, 'L\'audiogramme', '2017-11-17 08:24:07'),
(165, 221, 'Un neurone', '2017-11-17 08:24:20'),
(166, 222, 'Globules rouges ou érythrocytes, globules blancs ou leucocytes, plaquettes ou thrombocytes.\n\n', '2017-11-17 08:24:20'),
(167, 223, 'Les myocytes', '2017-11-17 08:24:20'),
(168, 224, 'C\'est une cellule graisseuse', '2017-11-17 08:24:20'),
(169, 225, 'Une cellule osseuse', '2017-11-17 08:24:20'),
(170, 226, 'Respiration cellulaire, production d\'énergie', '2017-11-17 08:24:20'),
(171, 227, 'Assemblée de cellules ayant une fonction \nidentique', '2017-11-17 08:24:20'),
(172, 228, 'l\'ADN qui est du matériel génétique', '2017-11-17 08:24:20'),
(173, 229, 'Division cellulaire: une cellule donne deux cellules semblables dont le noyau contient 46 chromosomes', '2017-11-17 08:24:20');

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

--
-- Déchargement des données de la table `t_multiple_answer`
--

INSERT INTO `t_multiple_answer` (`ID`, `FK_Question`, `Answer`, `Creation_Date`) VALUES
(1, 23, 'Insuline', '2017-11-17 08:12:54'),
(2, 23, 'Glucagon', '2017-11-17 08:12:54'),
(3, 24, 'Glucide', '2017-11-17 08:12:54'),
(4, 24, 'Lipide', '2017-11-17 08:12:54'),
(5, 24, 'Protéine', '2017-11-17 08:12:54'),
(6, 25, 'Goût', '2017-11-17 08:12:54'),
(7, 25, 'Déglutition', '2017-11-17 08:12:54'),
(8, 25, 'Phonation', '2017-11-17 08:12:54'),
(9, 25, 'Malaxage des aliments', '2017-11-17 08:12:54'),
(10, 54, 'Ischémie', '2017-11-17 08:19:48'),
(11, 54, 'Angine de poitrine', '2017-11-17 08:19:48'),
(12, 54, 'Infarctus du myocarde', '2017-11-17 08:19:48'),
(13, 54, 'athérosclérose', '2017-11-17 08:19:48'),
(14, 54, 'hypertension artierielle', '2017-11-17 08:19:48'),
(15, 55, 'Tabac', '2017-11-17 08:19:48'),
(16, 55, 'Cholestérol', '2017-11-17 08:19:48'),
(17, 55, 'Diabète', '2017-11-17 08:19:48'),
(18, 55, 'Hypertension artérielle', '2017-11-17 08:19:48'),
(19, 55, 'Sédentarité', '2017-11-17 08:19:48'),
(20, 79, 'Toux', '2017-11-17 08:19:58'),
(21, 79, 'Fièvre', '2017-11-17 08:19:58'),
(22, 79, 'Fatigue', '2017-11-17 08:19:58'),
(23, 79, 'Expectoration', '2017-11-17 08:19:58'),
(24, 79, 'Douleur thoracique', '2017-11-17 08:19:58'),
(25, 126, 'Observation', '2017-11-17 08:20:26'),
(26, 126, 'Palpation', '2017-11-17 08:20:26'),
(27, 126, 'Percussion', '2017-11-17 08:20:26'),
(28, 126, 'Auscultation', '2017-11-17 08:20:26');

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

--
-- Déchargement des données de la table `t_multiple_choice`
--

INSERT INTO `t_multiple_choice` (`ID`, `FK_Question`, `Answer`, `Valid`, `Creation_Date`) VALUES
(1, 21, 'Il communique avec la bouche', 1, '2017-11-17 08:12:54'),
(2, 21, 'Il ne sert qu\'au passage de l\'air', 0, '2017-11-17 08:12:54'),
(3, 21, 'C\'est le carrefour aéro-digestif', 1, '2017-11-17 08:12:54'),
(4, 21, 'Il est situé au dessus de l\'œsophage', 1, '2017-11-17 08:12:54'),
(5, 22, '1', 1, '2017-11-17 08:12:54'),
(6, 36, 'eau', 0, '2017-11-17 08:13:06'),
(7, 36, 'bactérie', 1, '2017-11-17 08:13:06'),
(8, 36, 'créatinine', 0, '2017-11-17 08:13:06'),
(9, 36, 'sucre', 1, '2017-11-17 08:13:06'),
(10, 36, 'sang', 1, '2017-11-17 08:13:06'),
(11, 37, 'Difficulté à uriner', 0, '2017-11-17 08:13:06'),
(12, 37, 'Miction trop fréquente', 0, '2017-11-17 08:13:06'),
(13, 37, 'Douleur en urinant', 1, '2017-11-17 08:13:06'),
(14, 38, 'Volume urinaire trop important', 0, '2017-11-17 08:13:06'),
(15, 38, 'Sang dans les urines', 1, '2017-11-17 08:13:06'),
(16, 38, 'Absence d\'urine', 0, '2017-11-17 08:13:06'),
(17, 39, '1', 1, '2017-11-17 08:13:06'),
(18, 40, '1', 0, '2017-11-17 08:13:06'),
(19, 53, '1', 0, '2017-11-17 08:19:48'),
(20, 78, 'Fréquente', 1, '2017-11-17 08:19:58'),
(21, 78, 'réversible', 0, '2017-11-17 08:19:58'),
(22, 78, 'influencée par le tabagisme', 1, '2017-11-17 08:19:58'),
(23, 78, 'une maladie cardiaque', 0, '2017-11-17 08:19:58'),
(24, 97, '1', 1, '2017-11-17 08:20:11'),
(25, 98, '1', 0, '2017-11-17 08:20:11'),
(26, 99, '1', 0, '2017-11-17 08:20:11'),
(27, 100, '1', 1, '2017-11-17 08:20:11'),
(28, 101, '...sortent directement du cerveau', 0, '2017-11-17 08:20:11'),
(29, 101, '… sortent de la moelle épinière', 1, '2017-11-17 08:20:11'),
(30, 101, '… sont toujours moteurs', 0, '2017-11-17 08:20:11'),
(31, 102, '1', 1, '2017-11-17 08:20:11'),
(32, 103, '1', 1, '2017-11-17 08:20:11'),
(33, 125, 'observation', 1, '2017-11-17 08:20:26'),
(34, 125, 'anamnèse', 0, '2017-11-17 08:20:26'),
(35, 125, 'radiographie', 0, '2017-11-17 08:20:26'),
(36, 125, 'auscultation', 1, '2017-11-17 08:20:26'),
(37, 125, 'palpation', 1, '2017-11-17 08:20:26');

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

--
-- Déchargement des données de la table `t_picture_landmark`
--

INSERT INTO `t_picture_landmark` (`ID`, `FK_Question`, `Symbol`, `Answer`, `Creation_Date`) VALUES
(1, 19, '1', 'canal déférent', '2017-11-17 08:12:16'),
(2, 19, '2', 'vessie', '2017-11-17 08:12:16'),
(3, 19, '3', 'vésicule séminale', '2017-11-17 08:12:16'),
(4, 19, '4', 'os pubien', '2017-11-17 08:12:16'),
(5, 19, '5', 'prostate', '2017-11-17 08:12:16'),
(6, 19, '6', 'urètre', '2017-11-17 08:12:16'),
(7, 19, '7', 'anus', '2017-11-17 08:12:16'),
(8, 19, '8', 'épipidyme', '2017-11-17 08:12:16'),
(9, 19, '9', 'testicule', '2017-11-17 08:12:16'),
(10, 19, '10', 'méat urinaire', '2017-11-17 08:12:16'),
(11, 19, '11', 'bourse ou scrotum', '2017-11-17 08:12:16'),
(12, 20, '1', 'trompe de Fallope', '2017-11-17 08:12:16'),
(13, 20, '2', 'endomètre', '2017-11-17 08:12:16'),
(14, 20, '3', 'ovaire', '2017-11-17 08:12:16'),
(15, 20, '4', 'utérus', '2017-11-17 08:12:16'),
(16, 20, '5', 'col de l\'utérus', '2017-11-17 08:12:16'),
(17, 20, '6', 'vagin', '2017-11-17 08:12:16'),
(18, 35, '1', 'pharynx', '2017-11-17 08:12:54'),
(19, 35, '2', 'œsophage', '2017-11-17 08:12:54'),
(20, 35, '3', 'foie', '2017-11-17 08:12:54'),
(21, 35, '4', 'estomac', '2017-11-17 08:12:54'),
(22, 35, '5', 'vésicule biliaire', '2017-11-17 08:12:54'),
(23, 52, '1', 'veine cave sup.', '2017-11-17 08:13:06'),
(24, 52, '2', 'aorte', '2017-11-17 08:13:06'),
(25, 52, '3', 'glandes surrénales', '2017-11-17 08:13:06'),
(26, 52, '4', 'néphron', '2017-11-17 08:13:06'),
(27, 52, '5', 'rein', '2017-11-17 08:13:06'),
(28, 52, '6', 'uretère', '2017-11-17 08:13:06'),
(29, 52, '7', 'vessie', '2017-11-17 08:13:06'),
(30, 52, '8', 'urètre', '2017-11-17 08:13:06'),
(31, 77, '1', 'veine cave supérieur', '2017-11-17 08:19:48'),
(32, 77, '2', 'aorte', '2017-11-17 08:19:48'),
(33, 77, '3', 'artère pulmonaire', '2017-11-17 08:19:48'),
(34, 77, '4', 'oreillette gauche', '2017-11-17 08:19:48'),
(35, 77, '5', 'oreilette droite', '2017-11-17 08:19:48'),
(36, 77, '6', 'valve pulmonaire', '2017-11-17 08:19:48'),
(37, 77, '7', 'valve aortique', '2017-11-17 08:19:48'),
(38, 77, '8', 'valve mitrale', '2017-11-17 08:19:48'),
(39, 77, '9', 'valve tricuspide', '2017-11-17 08:19:48'),
(40, 77, '10', 'ventricule droit', '2017-11-17 08:19:48'),
(41, 77, '11', 'ventricule gauche', '2017-11-17 08:19:48'),
(42, 77, '12', 'veine cave inférieur', '2017-11-17 08:19:48'),
(43, 95, '1', 'cavité nasale', '2017-11-17 08:19:58'),
(44, 95, '2', 'cavité buccale', '2017-11-17 08:19:58'),
(45, 95, '3', 'pharynx', '2017-11-17 08:19:58'),
(46, 95, '4', 'épiglotte', '2017-11-17 08:19:58'),
(47, 95, '5', 'larynx', '2017-11-17 08:19:58'),
(48, 95, '6', 'trachée', '2017-11-17 08:19:58'),
(49, 95, '7', 'bronchioles', '2017-11-17 08:19:58'),
(50, 95, '8', 'poumon gauche', '2017-11-17 08:19:58'),
(51, 95, '9', 'bronche', '2017-11-17 08:19:58'),
(52, 95, '10', 'scissures', '2017-11-17 08:19:58'),
(53, 95, '11', 'alvéoles', '2017-11-17 08:19:58'),
(54, 95, '12', 'diaphragme', '2017-11-17 08:19:58'),
(55, 95, '13', 'œsophage', '2017-11-17 08:19:58'),
(56, 96, 'A', 'Nez', '2017-11-17 08:19:58'),
(57, 96, 'B', 'Pharynx', '2017-11-17 08:19:58'),
(58, 96, 'C', 'Larynx', '2017-11-17 08:19:58'),
(59, 96, 'D', 'Trachée', '2017-11-17 08:19:58'),
(60, 124, 'A', 'Cornée', '2017-11-17 08:20:11'),
(61, 124, 'B', 'Pupille', '2017-11-17 08:20:11'),
(62, 124, 'C', 'Iris', '2017-11-17 08:20:11'),
(63, 124, 'D', 'Cristallin', '2017-11-17 08:20:11'),
(64, 124, 'E', 'Rétine', '2017-11-17 08:20:11'),
(65, 124, 'F', 'Nerf optique', '2017-11-17 08:20:11'),
(66, 137, '1', 'Rotation externe', '2017-11-17 08:20:26'),
(67, 138, '1', 'Epigastre', '2017-11-17 08:20:26'),
(68, 138, '2', 'Ombilic', '2017-11-17 08:20:26'),
(69, 138, '3', 'Hypogastre', '2017-11-17 08:20:26'),
(70, 138, '4', 'Hypochondre droit', '2017-11-17 08:20:27'),
(71, 138, '5', 'Flanc droit', '2017-11-17 08:20:27'),
(72, 138, '6', 'Fosse iliaque droite', '2017-11-17 08:20:27'),
(73, 138, '7', 'Hypochondre gauche', '2017-11-17 08:20:27'),
(74, 138, '8', 'Flanc gauche', '2017-11-17 08:20:27'),
(75, 138, '9', 'Fosse iliaque gauche', '2017-11-17 08:20:27'),
(76, 161, '1', 'os du crâne', '2017-11-17 08:22:32'),
(77, 161, '2', 'os de la face', '2017-11-17 08:22:32'),
(78, 161, '3', 'colonne cervicale, cou ', '2017-11-17 08:22:32'),
(79, 161, '4', 'clavicule', '2017-11-17 08:22:32'),
(80, 161, '5', 'omoplate', '2017-11-17 08:22:32'),
(81, 161, '6', 'sternum', '2017-11-17 08:22:32'),
(82, 161, '7', 'thorax, côte', '2017-11-17 08:22:32'),
(83, 161, '8', 'humérus', '2017-11-17 08:22:32'),
(84, 161, '9', 'radius', '2017-11-17 08:22:32'),
(85, 161, '10', 'cubitus ou ulna', '2017-11-17 08:22:32'),
(86, 162, 'A', 'Humérus', '2017-11-17 08:22:32'),
(87, 162, 'B', 'Radius', '2017-11-17 08:22:32'),
(88, 162, 'C', 'Ulna (=Cubitus)', '2017-11-17 08:22:32'),
(89, 163, 'A', 'Fémur', '2017-11-17 08:22:32'),
(90, 163, 'B', 'Tibia', '2017-11-17 08:22:32'),
(91, 163, 'C', 'Rotule (=Patella)', '2017-11-17 08:22:32'),
(92, 164, 'A', 'Fémur', '2017-11-17 08:22:32'),
(93, 164, 'B', 'Iliaque (=ischion, = coxal)', '2017-11-17 08:22:32'),
(94, 205, '1', 'hypothalamus', '2017-11-17 08:22:54'),
(95, 205, '2', 'hypophyse', '2017-11-17 08:22:54'),
(96, 205, '3', 'glande thyroïde', '2017-11-17 08:22:54'),
(97, 205, '4', 'parathyroïdes', '2017-11-17 08:22:54'),
(98, 205, '5', 'thymus', '2017-11-17 08:22:54'),
(99, 205, '6', 'glandes surrénales', '2017-11-17 08:22:54'),
(100, 205, '7', 'pancréas', '2017-11-17 08:22:54'),
(101, 205, '8', 'ovaires', '2017-11-17 08:22:54'),
(102, 205, '9', 'testicules', '2017-11-17 08:22:54'),
(103, 219, '1', 'iris', '2017-11-17 08:24:07'),
(104, 219, '2', 'pupille', '2017-11-17 08:24:07'),
(105, 219, '3', 'cornée', '2017-11-17 08:24:07'),
(106, 219, '4', 'humeur acqueuse', '2017-11-17 08:24:07'),
(107, 219, '5', 'cristallin', '2017-11-17 08:24:07'),
(108, 219, '6', 'humeur vitrée', '2017-11-17 08:24:07'),
(109, 219, '7', 'nerf optique', '2017-11-17 08:24:07'),
(110, 219, '8', 'rétine', '2017-11-17 08:24:07'),
(111, 220, '1', 'marteau', '2017-11-17 08:24:07'),
(112, 220, '2', 'enclume', '2017-11-17 08:24:07'),
(113, 220, '3', 'étrier', '2017-11-17 08:24:07'),
(114, 220, '4', 'canal semi-circulaire\n(équilibre)', '2017-11-17 08:24:07'),
(115, 220, '5', 'conduit auditif\nexterne', '2017-11-17 08:24:07'),
(116, 220, '6', 'tympan', '2017-11-17 08:24:07'),
(117, 220, '7', 'cochlée', '2017-11-17 08:24:07'),
(118, 220, '8', 'trompe \nd\'Eustache', '2017-11-17 08:24:07'),
(119, 230, '1', 'cytoplasme', '2017-11-17 08:24:20'),
(120, 230, '2', 'mitochondrie', '2017-11-17 08:24:20'),
(121, 230, '3', 'noyau', '2017-11-17 08:24:20'),
(122, 230, '4', 'membrane cellulaire', '2017-11-17 08:24:20');

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

--
-- Déchargement des données de la table `t_question`
--

INSERT INTO `t_question` (`ID`, `FK_Topic`, `FK_Question_Type`, `Question`, `Nb_Desired_Answers`, `Table_With_Definition`, `Picture_Name`, `Points`, `Creation_Date`) VALUES
(1, 85, 4, 'Compléter ce texte à trous de l\'apparail génital masculin avec les mots manquants ', NULL, NULL, NULL, NULL, '2017-11-17 08:12:16'),
(2, 85, 6, 'Dans quel organe se situe l\'urètre chez l\'homme ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(3, 85, 6, 'Quelle est la tumeur fréquente causant une dysurie chez l\'homme dès la cinquantaine ? ', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(4, 85, 6, 'Quel examen permet de quantifier l\'infertilité d\'un homme ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(5, 85, 6, 'Quelles sont les deux fonctions principales des testicules ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(6, 85, 6, 'Comment se nomment les cellules reproductrices chez l\'homme ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(7, 85, 6, 'Quel est l\'hormone sexuelle mâle?', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(8, 85, 6, 'Vrai ou Faux : le muscle utérin est un muscle strié ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(9, 85, 6, 'Vrai ou Faux : les ovaires sont des glandes exocrines et endocrines ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(10, 85, 6, 'Quelle est la durée moyenne d\'un cycle menstruel ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(11, 85, 6, 'Citer les deux phases du cycle menstruel ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(12, 85, 6, 'Définir grossesse extra-utérine ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(13, 85, 6, 'Définir primipare, primigeste et multigeste ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(14, 85, 6, 'Quel est l\'autre nom des trompes utérines ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(15, 85, 6, 'Comment se nomme la production des ovules ?  ', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(16, 85, 6, 'Quel est le nom de la muqueuse utérine qui se modifie au cours du cycle menstruel ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(17, 85, 6, 'Citer deux hormones sécrétées par l\'ovaire', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(18, 85, 6, 'Quel est le nom de l\'évaluation clinique du nouveau-né dans les 10 premières minutes de sa vie extra-utérine', NULL, NULL, NULL, 1, '2017-11-17 08:12:16'),
(19, 85, 7, 'Compléter avec les noms des organes le schéma de l\'appareil génital masculin.', NULL, NULL, 'AppGenitalMasculin.jpg', NULL, '2017-11-17 08:12:16'),
(20, 85, 7, 'Compléter le schéma de l\'appareil génital féminin.', NULL, NULL, 'AppGenitalFeminin.jpg', NULL, '2017-11-17 08:12:16'),
(21, 86, 1, 'A propos du pharynx cocher la ou les affirmations justes', NULL, NULL, NULL, NULL, '2017-11-17 08:12:54'),
(22, 86, 1, 'La digestion des aliments commence dans la bouche', NULL, NULL, NULL, NULL, '2017-11-17 08:12:54'),
(23, 86, 2, 'Quelles sont les deux hormones produites par le pancréas endocrine', 2, NULL, NULL, NULL, '2017-11-17 08:12:54'),
(24, 86, 2, 'Quels sont les trois nutriments de base assimilable par le corps', 3, NULL, NULL, NULL, '2017-11-17 08:12:54'),
(25, 86, 2, 'Citer deux fonctions de la langue', 2, NULL, NULL, NULL, '2017-11-17 08:12:54'),
(26, 86, 4, 'Compléter ce texte à trous avec les mots manquants. ', NULL, NULL, NULL, NULL, '2017-11-17 08:12:54'),
(27, 86, 6, 'Comment se font les déplacements du bol\nalimentaire le long du tube digestif ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:54'),
(28, 86, 6, 'Comment appelle -t-on l\'accélération du transit intestinal ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:54'),
(29, 86, 6, 'Quel est le nom du conduit qui relie le pharynx à\nl\'estomac ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:54'),
(30, 86, 6, 'Dans quelle partie de l\'abdomen se trouve l\'estomac \net sous quel muscle ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:54'),
(31, 86, 6, 'Quel est le nom du plus gros organe du tube diestif ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:54'),
(32, 86, 6, 'Comment se nomme le réservoir à bile ?', NULL, NULL, NULL, 1, '2017-11-17 08:12:54'),
(33, 86, 6, 'Quel est le rôle du foie?', NULL, NULL, NULL, 1, '2017-11-17 08:12:54'),
(34, 86, 6, 'A quoi sert la bile?', NULL, NULL, NULL, 1, '2017-11-17 08:12:54'),
(35, 86, 7, 'Ecriver le nom des organes du tube digestif', NULL, NULL, 'AppDigestif.jpg', NULL, '2017-11-17 08:12:54'),
(36, 87, 1, 'Parmi les éléments suivants cocher ceux qui ne doivent PAS être présent dans l\'urine normale', NULL, NULL, NULL, NULL, '2017-11-17 08:13:06'),
(37, 87, 1, 'L\'odynurie signifie', NULL, NULL, NULL, NULL, '2017-11-17 08:13:06'),
(38, 87, 1, 'Hématurie signifie', NULL, NULL, NULL, NULL, '2017-11-17 08:13:06'),
(39, 87, 1, 'L\'anurie est urgence', NULL, NULL, NULL, NULL, '2017-11-17 08:13:06'),
(40, 87, 1, 'La cystite est une infection du rein', NULL, NULL, NULL, NULL, '2017-11-17 08:13:06'),
(41, 87, 4, 'Compléter ce texte à trous \navec les mots manquants.', NULL, NULL, NULL, NULL, '2017-11-17 08:13:06'),
(42, 87, 6, 'Comment se nomme le canal qui amène l\'urine de la vessie à l\'extérieur du corps ?', NULL, NULL, NULL, 1, '2017-11-17 08:13:06'),
(43, 87, 6, 'Par quel organe est produit l\'urine ?', NULL, NULL, NULL, 1, '2017-11-17 08:13:06'),
(44, 87, 6, 'Quel est le nom des conduits excréteurs qui poussent l\'urine des reins vers la vessie ?', NULL, NULL, NULL, 1, '2017-11-17 08:13:06'),
(45, 87, 6, 'Comment se nomme l\'organe musculo-membraneux qui est le réservoir d\'urine ?', NULL, NULL, NULL, 1, '2017-11-17 08:13:06'),
(46, 87, 6, 'Vrai ou faux : l\'urine est liquide non stérile ?', NULL, NULL, NULL, 1, '2017-11-17 08:13:06'),
(47, 87, 6, 'Que signifie la présence de globules blancs (leucocytes) en grande quantité dans l\'urine ?', NULL, NULL, NULL, 1, '2017-11-17 08:13:06'),
(48, 87, 6, 'Chez un homme, l\'urètre permet aussi le passage d\'un liquide biologique, lequel ?', NULL, NULL, NULL, 1, '2017-11-17 08:13:06'),
(49, 87, 6, 'Est-il possible de vivre normalement avec un seul rein?', NULL, NULL, NULL, 1, '2017-11-17 08:13:06'),
(50, 87, 6, 'Que signifie \"dysurie\"?', NULL, NULL, NULL, 1, '2017-11-17 08:13:06'),
(51, 87, 6, 'La pyélonéphrite est une infection de quel organe?', NULL, NULL, NULL, 1, '2017-11-17 08:13:06'),
(52, 87, 7, 'Compléter le schéma avec le nom des organes de l\'appareil urinaire.', NULL, NULL, 'AppUrinaire.jpg', NULL, '2017-11-17 08:13:06'),
(53, 88, 1, 'Les veines qui sortent des poumons contiennent du sang desoxygéné', NULL, NULL, NULL, NULL, '2017-11-17 08:19:48'),
(54, 88, 2, 'Citer deux pathologies cardiaques fréquentes', 2, NULL, NULL, NULL, '2017-11-17 08:19:48'),
(55, 88, 2, 'Citer deux facteurs de risque cardiovasculaire', 2, NULL, NULL, NULL, '2017-11-17 08:19:48'),
(56, 88, 4, 'Compléter ce texte à trous avec les mots manquants.', NULL, NULL, NULL, NULL, '2017-11-17 08:19:48'),
(57, 88, 4, 'Compléter ce texte à trous avec les mots manquants.', NULL, NULL, NULL, NULL, '2017-11-17 08:19:48'),
(58, 88, 4, 'Compléter la phrase', NULL, NULL, NULL, NULL, '2017-11-17 08:19:48'),
(59, 88, 4, 'Compléter la phrase', NULL, NULL, NULL, NULL, '2017-11-17 08:19:48'),
(60, 88, 4, 'Compléter la phrase', NULL, NULL, NULL, NULL, '2017-11-17 08:19:48'),
(61, 88, 6, 'Quelle est la fonction du cœur ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(62, 88, 6, 'Nommer les quatres cavités cardiaques ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(63, 88, 6, 'Quels noms donne-t-on aux vaisseaux qui\nquittent le cœur ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(64, 88, 6, 'Fonction de la grande circulation ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(65, 88, 6, 'Fonction de la petite circulation ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(66, 88, 6, 'De quelle catégorie de muscles fait partie le cœur et pour quelle raison est-il l\'exception de sa catégorie ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(67, 88, 6, 'Quelle est la fonction principale des artères ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(68, 88, 6, 'Quelle est la fonction principale des veines ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(69, 88, 6, 'Quel est le nom de la plus grande artère du cœur\net de quel ventricule elle part ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(70, 88, 6, 'Quel est le rôle de la systole ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(71, 88, 6, 'Quel est le rôle de la diastale?', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(72, 88, 6, 'Donner la définition de la tension artérielle ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(73, 88, 6, 'Quelles sont les valeurs normales de la tension artérielle données par l\'OMS ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(74, 88, 6, 'Comment s\'appelle les artères qui nourissent le cœur?', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(75, 88, 6, 'Quelle est la définition d\'un infarctus du myocarde?', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(76, 88, 6, 'Donné la définition d’une dyspnée', NULL, NULL, NULL, 1, '2017-11-17 08:19:48'),
(77, 88, 7, 'En vous aidant des noms de la liste , veuillez compléter le schéma du coeur :\n\nventricule gauche, veine cave supérieur, oreillette  droite, ventricule droit, aorte, oreillette gauche, valve aortique, valve pulmonaire, veine cave inférieure, valvule tricuspide, valve mitrale, artère pulmonaire', NULL, NULL, 'coeur.jpg', NULL, '2017-11-17 08:19:48'),
(78, 89, 1, 'La Bronchopneumopathie chronique obstructive est (plusieurs réponses possible) :', NULL, NULL, NULL, NULL, '2017-11-17 08:19:58'),
(79, 89, 2, 'Donner trois symptômes d\'une pneumonie', 3, NULL, NULL, NULL, '2017-11-17 08:19:58'),
(80, 89, 4, 'Compléter ce texte à trous avec les mots manquants.', NULL, NULL, NULL, NULL, '2017-11-17 08:19:58'),
(81, 89, 4, 'Compléter ce texte à trous avec les mots manquants.', NULL, NULL, NULL, NULL, '2017-11-17 08:19:58'),
(82, 89, 4, 'Compléter ce texte à trous avec les mots manquants.', NULL, NULL, NULL, NULL, '2017-11-17 08:19:58'),
(83, 89, 6, 'Quel est le muscle principal de la respiration ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:58'),
(84, 89, 6, 'A quelle fréquence respirez-vous ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:58'),
(85, 89, 6, 'Citer deux maladies qui peuvent provoquer une détresse respiratoire ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:58'),
(86, 89, 6, 'Vrai ou faux : le poumon gauche est plus petit que le poumon droit.', NULL, NULL, NULL, 1, '2017-11-17 08:19:58'),
(87, 89, 6, 'Comment se nomme la membrane séreuse qui entoure et recouvre les poumons ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:58'),
(88, 89, 6, 'Quel est le nom de la partie centrale du thorax ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:58'),
(89, 89, 6, 'Comment se nomme les petits sacs où se font les échanges gazeux ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:58'),
(90, 89, 6, 'Vrai ou faux : lors de l\'inspiration, le diaphragme se contracte et s\'abaisse', NULL, NULL, NULL, 1, '2017-11-17 08:19:58'),
(91, 89, 6, 'Vrai ou faux : lors de l\'expiration, le diaphragme se contracte et s\'abaisse ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:58'),
(92, 89, 6, 'Quelles est le nom des globules qui transportent l\'oxygène ?', NULL, NULL, NULL, 1, '2017-11-17 08:19:58'),
(93, 89, 6, 'Donner la définition d\'une crise d\'asthme', NULL, NULL, NULL, 1, '2017-11-17 08:19:58'),
(94, 89, 6, 'Qu\'est ce qu\'une dyspnée', NULL, NULL, NULL, 1, '2017-11-17 08:19:58'),
(95, 89, 7, 'Compléter le schéma de l\'appareil respiratoire avec le nom des organes.', NULL, NULL, 'appRespiratoire.jpg', NULL, '2017-11-17 08:19:58'),
(96, 89, 7, 'Compléter le schéma de l\'appareil respiratoire avec le nom des organes.', NULL, NULL, 'Appareil_respiratoire.jpg', NULL, '2017-11-17 08:19:58'),
(97, 90, 1, 'La nevroglie sert à nourrir le neurone', NULL, NULL, NULL, NULL, '2017-11-17 08:20:11'),
(98, 90, 1, 'Un neurone n\'est connecté qu\'à un seul autre neurone', NULL, NULL, NULL, NULL, '2017-11-17 08:20:11'),
(99, 90, 1, 'Le cerveau d\'un adulte pèse un peu moins d\'un kilo', NULL, NULL, NULL, NULL, '2017-11-17 08:20:11'),
(100, 90, 1, 'La moelle épinière fait partie du sytème nerveux central', NULL, NULL, NULL, NULL, '2017-11-17 08:20:11'),
(101, 90, 1, 'Cocher la bonne réponse: les nerfs périphériques…', NULL, NULL, NULL, NULL, '2017-11-17 08:20:11'),
(102, 90, 1, 'Vrai ou faux : la rétine est la partie de l\'œil sur laquelle se forme une image projetée réelle mais inversée de l\'objet', NULL, NULL, NULL, NULL, '2017-11-17 08:20:11'),
(103, 90, 1, 'La cornée est la membrane transparente située en avant de l\'œil?', NULL, NULL, NULL, NULL, '2017-11-17 08:20:11'),
(104, 90, 4, 'Compléter ce texte à trous : la structure du cerveau', NULL, NULL, NULL, NULL, '2017-11-17 08:20:11'),
(105, 90, 6, 'Combien possèdons-nous de pairs de nerfs crâniens?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(106, 90, 6, 'Comment s\'appelle le liquide qui entoure le cerveau et la moelle épinière ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(107, 90, 6, 'Quelle lobe du cerveau gère la vision ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(108, 90, 6, 'Comment se nomme la cellule du système\nnerveux ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(109, 90, 6, 'Quel est le nom du lieu de connexion entre\n2 neurones ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(110, 90, 6, 'Quel est le nom \"scientifique\" du cerveau?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(111, 90, 6, 'Quel est le nom du centre des fonctions \nessentielles à la survie, comme la respiration,\nle rythme cardiaue, la TA, la digestion… ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(112, 90, 6, 'Comment se nomme la glande, centre de contrôle des\nfonctions végétatives, le chef d\'orchestre \n(hormones, faim, soif, douleur … ) ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(113, 90, 6, 'A quel endroit est situé le cervelet et quel est\nson rôle ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(114, 90, 6, 'Citer les 4 paires de lobes du cerveau ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(115, 90, 6, 'Donner la définition de motricité.', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(116, 90, 6, 'Quel est l\'organe de la sensibilité tactile, thermique et douloureuse ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(117, 90, 6, 'Comment se nomme la réaction motrice déclenchée par le système nerveux sans l\'intervention de la conscience ou de la volonté ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(118, 90, 6, 'Quel est le nom des 2 systèmes qui forment le système nerveux végétatif ou autonome ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(119, 90, 6, 'Quels sont les 5 sens du corps humain ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(120, 90, 6, 'Vrai ou faux : la rétine est la partie de l\'œil sur laquelle se forme une image projetée réelle mais inversée de l\'objet', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(121, 90, 6, 'Quelle est la partie de l\'œil qui lui donne sa couleur ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(122, 90, 6, 'Quel est le nom de la membrane fibreuse qui vibre et transmet les mouvements aux osselets de l\'oreille moyenne ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(123, 90, 6, 'Comment nomme-t-on le sens de l\'odorat?', NULL, NULL, NULL, 1, '2017-11-17 08:20:11'),
(124, 90, 7, 'En vous aidant de la liste ci-dessous, veuiller compléter le schéma de l\'œil :\n\nnerf optique, iris, cornée, cristallin, rétine, pupille', NULL, NULL, 'oeil.jpg', NULL, '2017-11-17 08:20:11'),
(125, 91, 1, 'Cocher les éléments qui font partie de l\'examen clinique du patient', NULL, NULL, NULL, NULL, '2017-11-17 08:20:26'),
(126, 91, 2, 'Citer les quatres temps de l\'examen clinique du patient', 4, NULL, NULL, NULL, '2017-11-17 08:20:26'),
(127, 91, 6, 'Quel est le nom de la cellule nerveuse ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:26'),
(128, 91, 6, 'Quel est le rôle et la  fonction des mitochondries ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:26'),
(129, 91, 6, 'Définir un tissu ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:26'),
(130, 91, 6, 'Que contient le noyau cellulaire ?', NULL, NULL, NULL, 1, '2017-11-17 08:20:26'),
(131, 91, 6, 'Donner la définition de : pathologie', NULL, NULL, NULL, 1, '2017-11-17 08:20:26'),
(132, 91, 6, 'Donner la définition d\': anamnèse', NULL, NULL, NULL, 1, '2017-11-17 08:20:26'),
(133, 91, 6, 'Donner la définition d\': examen complémentaire', NULL, NULL, NULL, 1, '2017-11-17 08:20:26'),
(134, 91, 6, 'Donner la définition de : Diagnostic', NULL, NULL, NULL, 1, '2017-11-17 08:20:26'),
(135, 91, 6, 'Quel élément de base constitue tous les tissus du corps humain?', NULL, NULL, NULL, 1, '2017-11-17 08:20:26'),
(136, 91, 6, 'Comment s\'appelle la division cellulaire qui permet à une celle de se répliquer?', NULL, NULL, NULL, 1, '2017-11-17 08:20:26'),
(137, 91, 7, 'Comment s\'appelle le mouvement de la hanche droite sur ce schéma', NULL, NULL, 'Re_hanche.jpg', NULL, '2017-11-17 08:20:26'),
(138, 91, 7, 'Nommer les cadrans de l\'abdomen', NULL, NULL, 'abdomen.jpg', NULL, '2017-11-17 08:20:26'),
(139, 92, 6, 'Définir une entorse ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(140, 92, 6, 'Définir luxation articulaire ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(141, 92, 6, 'Quelle est la première vertèbre qui supporte\n le crâne ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(142, 92, 6, 'Quelle est le nom de la 2ème vertèbre qui \npermet l\'articulation de la tête ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(143, 92, 6, 'Quel est le nom du liquide que le médecin peut retirer d\'une articulation enflée ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(144, 92, 6, 'Quels types de muscles peuvent être contrôlés par la volonté ? Donné un exemple', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(145, 92, 6, 'Citer un muscle strié automatique ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(146, 92, 6, 'Où trouve-t-on des muscles lisses ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(147, 92, 6, 'Les mains, les bras sont reliés au thorax par quelle ceinture Où trouve-t-on des muscles lisses ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(148, 92, 6, 'Les pieds, les jambes sont reliés au bassin par quelle ceinture ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(149, 92, 6, 'Combien de vertèbres constituent la colonne vertébrale', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(150, 92, 6, 'Quelle est la différence entre l\'abduction et l\'adduction ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(151, 92, 6, 'De quel minéral est principalement constitué de l’os ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(152, 92, 6, 'Quels sont les os du bras et de l’avant-bras ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(153, 92, 6, 'Combien a-t-on de vertèbres cervicales ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(154, 92, 6, 'Combien a-t-on de vertèbres lombaires ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(155, 92, 6, 'Quel est le rôle des bourses synoviales ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(156, 92, 6, 'Comment appelle-t-on l’inflammation d’une capsule articulaire ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(157, 92, 6, 'Comment appelle-t-on les muscles soumis à l’action de la volonté', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(158, 92, 6, 'Comment appelle-t-on deux muscles dont l’action est la même ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(159, 92, 6, 'Comment appelle-t-on deux muscles dont l’action s’oppose ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(160, 92, 6, 'Quel examen complémentaire est réalisé en première intention afin de rechercher une fracture ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:32'),
(161, 92, 7, 'Ecriver le nom des os du squelette', NULL, NULL, 'squelette.jpg', NULL, '2017-11-17 08:22:32'),
(162, 92, 7, 'Nommer les points A, B et C', NULL, NULL, 'Coude.jpg', NULL, '2017-11-17 08:22:32'),
(163, 92, 7, 'Nommer les points A, B et C', NULL, NULL, 'Genou.jpg', NULL, '2017-11-17 08:22:32'),
(164, 92, 7, 'Nommer les points A et B', NULL, NULL, 'Hanche.jpg', NULL, '2017-11-17 08:22:32'),
(165, 93, 4, 'Compléter ce texte à trous : la composition du sang', NULL, NULL, NULL, NULL, '2017-11-17 08:22:44'),
(166, 93, 4, 'Compléter ce texte à trous : les fonctions et rôles principaux du sang', NULL, NULL, NULL, NULL, '2017-11-17 08:22:44'),
(167, 93, 6, 'Quelle est la fonction principale de\nl\'érythrocyte ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(168, 93, 6, 'Vrai ou Faux : le plasma donne du sérum après coagulation ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(169, 93, 6, 'Vrai ou Faux : la moelle jaune des os longs \nest le lieu de formation des palquettes ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(170, 93, 6, 'Vrai ou Faux : une leucocytose est souvent  expliquée par une infection ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(171, 93, 6, 'Vrai ou Faux : une thrombopénie augmente\nle risque de thrombose ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(172, 93, 6, 'Comment appelle-t-on la formation des\nglobules rouges ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(173, 93, 6, 'Comment appelle-t-on une prolifération maligne des leucocytes ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(174, 93, 6, 'Quel est le rôle du système immunitaire ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(175, 93, 6, 'Comment se nomme les protéines ou \nimmunoglobulines qui s\'attaquent aux \nantigènes ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(176, 93, 6, 'Quel est le nom des gendarmes qui assurent\nles défenses immunitaires de notre corps ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(177, 93, 6, 'Quelles est le nom des cellules qui assurent les défenses naturelles de notre corps ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(178, 93, 6, 'Comment se nomme l\'ensemble des mécanismes de coagulation qui permet d\'éviter les hémorragies mais aussi les thromboses ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(179, 93, 6, 'Quel est le nom des cellules qui agissent en\npremier lors d\'une blessure et dont la fonction est diminuée en cas de prise d\'Aspirine ou d\'antiagrégants ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(180, 93, 6, 'Dans quelles cellules se trouvent l\'hémoglobine\net quel est son rôle ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(181, 93, 6, 'Dans quel endroit sont fabriqués les globules\nrouges ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(182, 93, 6, 'Comment se nomme le processus de fabrication\ndes globules rouges, des globules blancs et des\nplaquettes ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(183, 93, 6, 'Que va déclencher un antigène face à des agents\npathogènes ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(184, 93, 6, 'Quels sont les 4 antigèes que les globules rouges\npeuvent portés à leur surface ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(185, 93, 6, 'Donner la défintion de l\'immunité naturelle.', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(186, 93, 6, 'Le système immunitaire est chargé de défendre l\'organisme contre divers agents pathogènes, citez-en 3.', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(187, 93, 6, 'Après une vaccination, on parle d\'immunité ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(188, 93, 6, 'Lorsque l\'organisme répond à la présence d\'antigène par la formation d\'anticorps et la simulation des lymphocytes, on parle de ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:44'),
(189, 94, 4, 'Compléter ce texte à trous :\ndéfinition du système endocrinien', NULL, NULL, NULL, NULL, '2017-11-17 08:22:54'),
(190, 94, 6, 'Quelle sorte de glande produit les hormones ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:54'),
(191, 94, 6, 'Quel est le rôle des hormones ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:54'),
(192, 94, 6, 'Quelles sont les glandes qui forment le système hormonal ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:54'),
(193, 94, 6, 'Quelles sont les glandes endocrines mixtes ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:54'),
(194, 94, 6, 'Quelle hormone produit le pancréas ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:54'),
(195, 94, 6, 'A quel endroit se trouve l\'hypophyse ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:54'),
(196, 94, 6, 'Citer 2 pathologies thyroïdiennes.', NULL, NULL, NULL, 1, '2017-11-17 08:22:54'),
(197, 94, 6, 'Quelle est la plus volumineuse des glandes endocrines ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:54'),
(198, 94, 6, 'A quel endroit se trouve la thyroïde ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:54'),
(199, 94, 6, 'Quel est le rôle des glandes parathyroïde ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:54'),
(200, 94, 6, 'Comment se nomme les 2 glandes qui forment une glande surrénale ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:54'),
(201, 94, 6, 'Comment se nomme les glandes génitales masculines ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:54'),
(202, 94, 6, 'Quelles sont les 2 fonctions et rôles des glandes génitales masculines ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:54'),
(203, 94, 6, 'Comment se nomme les glandes génitales féminines ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:54'),
(204, 94, 6, 'Expliquer la fonction et le rôle des glandes\ngénitales féminines ?', NULL, NULL, NULL, 1, '2017-11-17 08:22:54'),
(205, 94, 7, 'En vous aidant de la liste ci-dessous, placer le nom des organes au bon endroit : \nhypophyse, glandes surrénales, thyroïde, hypothalamus, parathyroïdes, thymus, ovaires, glande thyroïde, testicules', NULL, NULL, 'syst_endocrinien.jpg', NULL, '2017-11-17 08:22:54'),
(206, 95, 6, 'Vrai ou Faux : les yeux myopes sont trop courts ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:07'),
(207, 95, 6, 'Citer des anomalies de réfraction (accomodation) de l\'œil ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:07'),
(208, 95, 6, 'Citer des troubles de la vision des couleurs ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:07'),
(209, 95, 6, 'Que signifie DMLA ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:07'),
(210, 95, 6, 'Quels sont les 5 sens du corps humain ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:07'),
(211, 95, 6, 'Vrai ou faux : la rétine est la partie de l\'œil sur laquelle se forme une image projetée réelle mais inversée de l\'objet', NULL, NULL, NULL, 1, '2017-11-17 08:24:07'),
(212, 95, 6, 'Comment se nomme l\'examen qui permet de contôler la rétine et les vaisseaux qui l\'irriguent ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:07'),
(213, 95, 6, 'Quelle est la partie de l\'œil qui lui donne sa couleur ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:07'),
(214, 95, 6, 'Comment se nomme le conduit reliant la cavité du tympan au nasopharynx et qui permet de rétablir l\'équilibre des pressions ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:07'),
(215, 95, 6, 'Quel est le nom de la membrane fibreuse qui vibre et transmet les mouvements aux osselets de l\'oreille moyenne ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:07'),
(216, 95, 6, 'Quel est le nom des osselets contenus dans l\'oreille moyenne ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:07'),
(217, 95, 6, 'Comment se nomme l\'examen qui permet de contôler la rétine et les vaisseaux qui l\'irriguent ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:07'),
(218, 95, 6, 'Quel est le nom de l\'examen qui permet l\'exploration de l\'audition, la mesure du seuil d\'audibilité ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:07'),
(219, 95, 7, 'En vous aidant de la liste ci-dessous, veuiller compléter le schéma de l\'œil :\nnerf optique, iris, cornée, cristallin, rétine, pupille, humeur acqueuse, humeur vitrée', NULL, NULL, 'oeil.jpg', NULL, '2017-11-17 08:24:07'),
(220, 95, 7, 'En vous aidant de la liste ci-desous, veuiler compléter le schéma de l\'oreille :\nEnclume, étrier, trompe d\'Eustache, conduit auditif externe, cochlée, canal semi-circulaire, tympan, marteau\n', NULL, NULL, 'oreille.jpg', NULL, '2017-11-17 08:24:07'),
(221, 96, 6, 'Quel est le nom de la cellule nerveuse ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:20'),
(222, 96, 6, 'Donner les noms des cellules du sang ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:20'),
(223, 96, 6, 'Comment appelle-t-on les cellules musculaires ?\n', NULL, NULL, NULL, 1, '2017-11-17 08:24:20'),
(224, 96, 6, 'Définir un adipocyte ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:20'),
(225, 96, 6, 'Définition d\'un ostéoblaste ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:20'),
(226, 96, 6, 'Quel est le rôle et la  fonction des mitochondries ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:20'),
(227, 96, 6, 'Définir un tissu ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:20'),
(228, 96, 6, 'Que contient le noyau cellulaire ?', NULL, NULL, NULL, 1, '2017-11-17 08:24:20'),
(229, 96, 6, 'Définition de la mitose?', NULL, NULL, NULL, 1, '2017-11-17 08:24:20'),
(230, 96, 7, 'Compléter le schéma de la structure d\'une cellule.', NULL, NULL, 'structurecellule.jpg', NULL, '2017-11-17 08:24:20');

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `t_cloze_text_answer`
--
ALTER TABLE `t_cloze_text_answer`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT pour la table `t_free_answer`
--
ALTER TABLE `t_free_answer`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT pour la table `t_multiple_answer`
--
ALTER TABLE `t_multiple_answer`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `t_multiple_choice`
--
ALTER TABLE `t_multiple_choice`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT pour la table `t_picture_landmark`
--
ALTER TABLE `t_picture_landmark`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT pour la table `t_question`
--
ALTER TABLE `t_question`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;

--
-- AUTO_INCREMENT pour la table `t_questionnaire`
--
ALTER TABLE `t_questionnaire`
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
