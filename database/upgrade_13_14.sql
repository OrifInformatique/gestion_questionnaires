RENAME TABLE `t_user` TO `user`;
ALTER TABLE `user` CHANGE `ID` `id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `FK_User_Type` `fk_user_type` INT(11) NOT NULL, CHANGE `User` `username` VARCHAR(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `Password` `password` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL, CHANGE `Archive` `archive` TINYINT(4) NOT NULL DEFAULT '0';
ALTER TABLE `user` ADD `date_creation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `archive`;

RENAME TABLE `t_user_type` TO `user_type`;
ALTER TABLE `user_type` CHANGE `ID` `id` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `t_question_type` ADD `Archive` BOOLEAN NOT NULL DEFAULT FALSE AFTER `Type_Name`;
UPDATE `t_question_type` SET `Archive` = '1' WHERE `t_question_type`.`ID` = 3;
UPDATE `t_question_type` SET `Archive` = '1' WHERE `t_question_type`.`ID` = 5;

UPDATE `t_question_type` SET `Type_Name` = 'Choix multiples' WHERE `t_question_type`.`ID` = 1;
UPDATE `t_question_type` SET `Type_Name` = 'Réponses multiples' WHERE `t_question_type`.`ID` = 2;
UPDATE `t_question_type` SET `Type_Name` = 'Distribution de réponses' WHERE `t_question_type`.`ID` = 3;
UPDATE `t_question_type` SET `Type_Name` = 'Texte à trous' WHERE `t_question_type`.`ID` = 4;
UPDATE `t_question_type` SET `Type_Name` = 'Réponse libre' WHERE `t_question_type`.`ID` = 6;
UPDATE `t_question_type` SET `Type_Name` = 'Image avec repères' WHERE `t_question_type`.`ID` = 7;