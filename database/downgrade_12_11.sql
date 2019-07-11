--
-- Modifies `t_user` to have `is_active` back
-- Invert the values of this field
--
ALTER TABLE `t_user` CHANGE `Archive` `is_active` TINYINT(4) NULL DEFAULT '1';
UPDATE `t_user` SET `is_active`=2 WHERE `is_active`=1;
UPDATE `t_user` SET `is_active`=1 WHERE `is_active`=0;
UPDATE `t_user` SET `is_active`=0 WHERE `is_active`=2;

--
-- Removes the column `Questionnaire_Subtitle` from `t_questionnaire`
--
ALTER TABLE `t_questionnaire` DROP COLUMN `Questionnaire_Subtitle`;

--
-- Don't allow longer filenames
--
ALTER TABLE `t_questionnaire` CHANGE `PDF` `PDF` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
CHANGE `Corrige_PDF` `Corrige_PDF` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

--
-- Delete `t_questionnaire_model_topic`
--
DROP TABLE `t_questionnaire_model_topic`;

--
-- Delete `t_questionnaire_model`
--
DROP TABLE `t_questionnaire_model`;

--
-- Create `t_temp_questionnaire`
--
CREATE TABLE `gestion_questionnaires`.`t_temp_questionnaire` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`ID_Question` int(11) NOT NULL,
	`Question` text NOT NULL,
	`Picture_Path` varchar(100) NOT NULL,
	`Content` text NOT NULL,
	`Answer_Zone` text NOT NULL,
	`Points` int(11) NOT NULL,
	PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Create `t_temp_questionnaire_answer`
--
CREATE TABLE `gestion_questionnaires`.`t_temp_questionnaire_answer` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`ID_Question` int(11) NOT NULL,
	`Question` text NOT NULL,
	`Content` text NOT NULL,
	`Picture_Path` varchar(100) NOT NULL,
	`Picture_Answers` varchar(100) NOT NULL,
	`Answer` text NOT NULL,
	`Points` int(11) NOT NULL,
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Downgrade Topic's name length
--
ALTER TABLE `t_topic` CHANGE `Topic` `Topic` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

--
-- Downgrade User type
-- Delete the "Manager" user type
-- Change admin access_level to 2
--
DELETE FROM `t_user_type` WHERE `access_level` = 2;
UPDATE `t_user_type` SET `access_level` = '2' WHERE `access_level` = 4;
