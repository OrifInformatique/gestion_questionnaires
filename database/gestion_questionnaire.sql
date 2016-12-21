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
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_topic`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_topic` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FK_Parent_Topic` INT(11) NOT NULL,
  `Topic` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Creation_Date` INT(11) NOT NULL,
  `Archive` INT(11) NOT NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_t_topic_t_topic1_idx` (`FK_Parent_Topic` ASC))
ENGINE = MyISAM
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
  `Nb_Desired_Answers` INT(11) NOT NULL,
  `Table_With_Definition` TINYINT(1) NOT NULL,
  `Picture_Name` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Points` INT(11) NOT NULL,
  `Creation_Date` DATETIME NOT NULL,
  PRIMARY KEY (`ID`, `FK_Topic`, `FK_Question_Type`),
  INDEX `fk_t_question_t_question_type1_idx` (`FK_Question_Type` ASC),
  INDEX `fk_t_question_t_topic1_idx` (`FK_Topic` ASC))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_answer_distribution`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_answer_distribution` (
  `ID` INT(11) NOT NULL,
  `FK_Question` INT(11) NOT NULL,
  `Question_Part` VARCHAR(250) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Answer_Part` VARCHAR(250) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Creation_Date` INT(11) NOT NULL,
  PRIMARY KEY (`ID`, `FK_Question`))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_cloze_text`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_cloze_text` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` INT(11) NOT NULL,
  `Cloze_Text` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Creation_Date` DATETIME NOT NULL,
  PRIMARY KEY (`ID`, `FK_Question`),
  INDEX `fk_t_cloze_text_t_question1_idx` (`FK_Question` ASC))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_cloze_text_answer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_cloze_text_answer` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FK_Cloze_Text` INT(11) NOT NULL,
  `Answer` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Answer_Order` INT(11) NOT NULL,
  `Creation_Date` DATETIME NOT NULL,
  PRIMARY KEY (`ID`, `FK_Cloze_Text`),
  INDEX `fk_t_cloze_text_answer_t_cloze_text1_idx` (`FK_Cloze_Text` ASC))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_free_answer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_free_answer` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` INT(11) NOT NULL,
  `Answer` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Creation_Date` DATETIME NOT NULL,
  PRIMARY KEY (`ID`, `FK_Question`),
  INDEX `fk_t_free_answer_t_question1_idx` (`FK_Question` ASC))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_multiple_answer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_multiple_answer` (
  `ID` INT(11) NOT NULL,
  `FK_Question` INT(11) NOT NULL,
  `Answer` VARCHAR(250) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Creation_Date` DATETIME NOT NULL,
  PRIMARY KEY (`FK_Question`, `ID`))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_multiple_choice`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_multiple_choice` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` INT(11) NOT NULL,
  `Answer` VARCHAR(250) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Valid` TINYINT(1) NOT NULL,
  `Creation_Date` DATETIME NOT NULL,
  PRIMARY KEY (`ID`, `FK_Question`),
  INDEX `fk_t_multiple_choice_t_question1_idx` (`FK_Question` ASC))
ENGINE = MyISAM
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
  `Creation_Date` DATETIME NOT NULL,
  PRIMARY KEY (`ID`, `FK_Question`),
  INDEX `fk_t_picture_landmark_t_question1_idx` (`FK_Question` ASC))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_questionnaire`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_questionnaire` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `Questionnaire_Name` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `PDF` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Corrige_PDF` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Creation_Date` DATETIME NOT NULL,
  PRIMARY KEY (`ID`))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_table_cell`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_table_cell` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FK_Question` INT(11) NOT NULL,
  `Content` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Column_Nb` INT(11) NOT NULL,
  `Row_Nb` INT(11) NOT NULL,
  `Header` TINYINT(1) NOT NULL,
  `Display_In_Question` TINYINT(1) NOT NULL,
  `Creation_Date` DATETIME NOT NULL,
  PRIMARY KEY (`ID`, `FK_Question`),
  INDEX `fk_t_table_cell_t_question_idx` (`FK_Question` ASC))
ENGINE = MyISAM
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
  PRIMARY KEY (`ID`, `FK_Question`, `FK_Questionnaire`),
  INDEX `fk_t_question_has_t_questionnaire_t_questionnaire1_idx` (`FK_Questionnaire` ASC),
  INDEX `fk_t_question_has_t_questionnaire_t_question1_idx` (`FK_Question` ASC))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_user_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_user_type` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `gestion_questionnaires`.`t_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_questionnaires`.`t_user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_type` INT NOT NULL,
  `user` VARCHAR(45) NULL,
  `password` VARCHAR(45) NULL,
  PRIMARY KEY (`id`, `user_type`),
  INDEX `fk_t_user_t_user_type1_idx` (`user_type` ASC),
  CONSTRAINT `fk_t_user_t_user_type1`
    FOREIGN KEY (`user_type`)
    REFERENCES `gestion_questionnaires`.`t_user_type` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
