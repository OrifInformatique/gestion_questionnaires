--
-- Adds the column `Questionnaire_Subtitle` to `t_questionnaire`
--
ALTER TABLE `t_questionnaire` ADD `Questionnaire_Subtitle` VARCHAR(255) NULL AFTER `Questionnaire_Name`;

--
-- Modifies `t_user` to have `Archive` for inactive users checking
--
ALTER TABLE `t_user` CHANGE `is_active` `Archive` TINYINT(4) NOT NULL DEFAULT '0';
UPDATE `t_user` SET `Archive`=2 WHERE `Archive`=1;
UPDATE `t_user` SET `Archive`=1 WHERE `Archive`=0;
UPDATE `t_user` SET `Archive`=0 WHERE `Archive`=2;

--
-- Create `t_questionnaire_model`
--
CREATE TABLE `gestion_questionnaires`.`t_questionnaire_model` (
	`ID` int NOT NULL,
	`Base_Name` varchar(100) NOT NULL,
	`Questionnaire_Name` varchar(100) NOT NULL,
	`Questionnaire_Subtitle` varchar(255) NULL,
	PRIMARY KEY (`ID`)
) ENGINE = InnoDB;
ALTER TABLE `t_questionnaire_model` CHANGE `ID` `ID` INT(11) NOT NULL AUTO_INCREMENT;

--
-- Create `t_questionnaire_model_topic`
--
CREATE TABLE `gestion_questionnaires`.`t_questionnaire_model_topic` (
	`ID` INT NOT NULL AUTO_INCREMENT,
	`FK_Questionnaire_Model` INT NOT NULL,
	`FK_Topic` INT NOT NULL,
	`Nb_Topic_Questions` INT NOT NULL,
	PRIMARY KEY (`ID`),
	INDEX (`FK_Questionnaire_Model`),
	INDEX (`FK_Topic`)
) ENGINE = InnoDB;
ALTER TABLE `t_questionnaire_model_topic` CHANGE `ID` `ID` INT(11) NOT NULL AUTO_INCREMENT;

--
-- Update key relations
--
ALTER TABLE `t_questionnaire_model_topic` ADD CONSTRAINT `t_questionnaire_model_topic_ibfk_1`
FOREIGN KEY (`FK_Topic`) REFERENCES `t_topic`(`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `t_questionnaire_model_topic` ADD CONSTRAINT `t_questionnaire_model_topic_ibfk_2`
FOREIGN KEY (`FK_Questionnaire_Model`) REFERENCES `t_questionnaire_model`(`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;
