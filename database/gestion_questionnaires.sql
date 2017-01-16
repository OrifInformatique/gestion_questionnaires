-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema gestion_questionnaires
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema gestion_questionnaires
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `gestion_questionnaires` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
USE `gestion_questionnaires` ;

-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_question_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_question_type` (
  `ID` INT(11) NOT NULL,
  `Type_Name` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`ID`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_topic`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_topic` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FK_Parent_Topic` INT(11) NULL,
  `Topic` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Creation_Date` DATETIME NULL,
  `Archive` TINYINT(1) NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_t_topic_t_topic1_idx` (`FK_Parent_Topic` ASC),
  CONSTRAINT `fk_t_topic_t_topic1`
    FOREIGN KEY (`FK_Parent_Topic`)
    REFERENCES `gestion_questionnaires`.`t_topic` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_question`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_question` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FK_Topic` INT(11) NOT NULL,
  `FK_Question_Type` INT(11) NOT NULL,
  `Question` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Nb_Desired_Answers` INT(11) NULL,
  `Table_With_Definition` TINYINT(1) NULL,
  `Picture_Name` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL,
  `Points` INT(11) NULL,
  `Creation_Date` DATETIME NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_t_question_t_question_type1_idx` (`FK_Question_Type` ASC),
  INDEX `fk_t_question_t_topic1_idx` (`FK_Topic` ASC),
  CONSTRAINT `fk_t_question_t_question_type1`
    FOREIGN KEY (`FK_Question_Type`)
    REFERENCES `gestion_questionnaires`.`t_question_type` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_t_question_t_topic1`
    FOREIGN KEY (`FK_Topic`)
    REFERENCES `gestion_questionnaires`.`t_topic` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_answer_distribution`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_answer_distribution` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` INT(11) NOT NULL,
  `Question_Part` VARCHAR(250) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Answer_Part` VARCHAR(250) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Creation_Date` DATETIME NULL,
  PRIMARY KEY (`ID`),
  CONSTRAINT `fk_t_answer_distribution_t_question1`
    FOREIGN KEY (`FK_Question`)
    REFERENCES `gestion_questionnaires`.`t_question` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_cloze_text`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_cloze_text` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` INT(11) NOT NULL,
  `Cloze_Text` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Creation_Date` DATETIME NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_t_cloze_text_t_question1_idx` (`FK_Question` ASC),
  CONSTRAINT `fk_t_cloze_text_t_question1`
    FOREIGN KEY (`FK_Question`)
    REFERENCES `gestion_questionnaires`.`t_question` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_cloze_text_answer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_cloze_text_answer` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FK_Cloze_Text` INT(11) NOT NULL,
  `Answer` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Answer_Order` INT(11) NULL,
  `Creation_Date` DATETIME NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_t_cloze_text_answer_t_cloze_text1_idx` (`FK_Cloze_Text` ASC),
  CONSTRAINT `fk_t_cloze_text_answer_t_cloze_text1`
    FOREIGN KEY (`FK_Cloze_Text`)
    REFERENCES `gestion_questionnaires`.`t_cloze_text` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_free_answer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_free_answer` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` INT(11) NOT NULL,
  `Answer` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Creation_Date` DATETIME NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_t_free_answer_t_question1_idx` (`FK_Question` ASC),
  CONSTRAINT `fk_t_free_answer_t_question1`
    FOREIGN KEY (`FK_Question`)
    REFERENCES `gestion_questionnaires`.`t_question` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_multiple_answer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_multiple_answer` (
  `ID` INT(11) NOT NULL,
  `FK_Question` INT(11) NOT NULL,
  `Answer` VARCHAR(250) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Creation_Date` DATETIME NULL,
  PRIMARY KEY (`ID`),
  CONSTRAINT `fk_t_multiple_answer_t_question1`
    FOREIGN KEY (`FK_Question`)
    REFERENCES `gestion_questionnaires`.`t_question` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_multiple_choice`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_multiple_choice` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` INT(11) NOT NULL,
  `Answer` VARCHAR(250) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Valid` TINYINT(1) NULL,
  `Creation_Date` DATETIME NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_t_multiple_choice_t_question1_idx` (`FK_Question` ASC),
  CONSTRAINT `fk_t_multiple_choice_t_question1`
    FOREIGN KEY (`FK_Question`)
    REFERENCES `gestion_questionnaires`.`t_question` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_picture_landmark`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_picture_landmark` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` INT(11) NOT NULL,
  `Symbol` VARCHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Answer` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Creation_Date` DATETIME NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_t_picture_landmark_t_question1_idx` (`FK_Question` ASC),
  CONSTRAINT `fk_t_picture_landmark_t_question1`
    FOREIGN KEY (`FK_Question`)
    REFERENCES `gestion_questionnaires`.`t_question` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_questionnaire`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_questionnaire` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `Questionnaire_Name` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `PDF` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL,
  `Corrige_PDF` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL,
  `Creation_Date` DATETIME NULL,
  PRIMARY KEY (`ID`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_table_cell`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_table_cell` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` INT(11) NOT NULL,
  `Content` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Column_Nb` INT(11) NULL,
  `Row_Nb` INT(11) NULL,
  `Header` TINYINT(1) NULL,
  `Display_In_Question` TINYINT(1) NULL,
  `Creation_Date` DATETIME NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_t_table_cell_t_question_idx` (`FK_Question` ASC),
  CONSTRAINT `fk_t_table_cell_t_question`
    FOREIGN KEY (`FK_Question`)
    REFERENCES `gestion_questionnaires`.`t_question` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_temp_questionnaire`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_temp_questionnaire` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `ID_Question` INT(11) NOT NULL,
  `Question` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Picture_Path` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Content` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Answer_Zone` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Points` INT(11) NOT NULL,
  PRIMARY KEY (`ID`))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_temp_questionnaire_answer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_temp_questionnaire_answer` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `ID_Question` INT(11) NOT NULL,
  `Question` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Content` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Picture_Path` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Picture_Answers` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Answer` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Points` INT(11) NOT NULL,
  PRIMARY KEY (`ID`))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_question_questionnaire`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_question_questionnaire` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `FK_Question` INT(11) NOT NULL,
  `FK_Questionnaire` INT(11) NOT NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_t_question_has_t_questionnaire_t_questionnaire1_idx` (`FK_Questionnaire` ASC),
  INDEX `fk_t_question_has_t_questionnaire_t_question1_idx` (`FK_Question` ASC),
  CONSTRAINT `fk_t_question_has_t_questionnaire_t_question1`
    FOREIGN KEY (`FK_Question`)
    REFERENCES `gestion_questionnaires`.`t_question` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_t_question_has_t_questionnaire_t_questionnaire1`
    FOREIGN KEY (`FK_Questionnaire`)
    REFERENCES `gestion_questionnaires`.`t_questionnaire` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_user_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_user_type` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `access_level` INT NOT NULL,
  PRIMARY KEY (`ID`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_user` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `FK_User_Type` INT NOT NULL,
  `User` VARCHAR(45) NOT NULL,
  `Password` VARCHAR(255) NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_t_user_t_user_type1_idx` (`FK_User_Type` ASC),
  CONSTRAINT `fk_t_user_t_user_type1`
    FOREIGN KEY (`FK_User_Type`)
    REFERENCES `gestion_questionnaires`.`t_user_type` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
