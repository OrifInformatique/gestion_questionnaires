--
-- Create `t_questionnaire_model`
--

CREATE TABLE `gestion_questionnaires`.`t_questionnaire_model` (
	`ID` int NOT NULL,
	`Base_Name` varchar(100) NOT NULL,
	`Questionnaire_Name` varchar(100) NOT NULL,
	PRIMARY KEY (`ID`)
) ENGINE = InnoDB;

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

--
-- Update key relations
--
ALTER TABLE `t_questionnaire_model_topic` ADD CONSTRAINT `t_questionnaire_model_topic_ibfk_1`
FOREIGN KEY (`FK_Topic`) REFERENCES `t_topic`(`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `t_questionnaire_model_topic` ADD CONSTRAINT `t_questionnaire_model_topic_ibfk_2`
FOREIGN KEY (`FK_Questionnaire_Model`) REFERENCES `t_questionnaire_model`(`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION; 