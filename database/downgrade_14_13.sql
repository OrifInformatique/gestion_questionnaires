RENAME TABLE `gestion_questionnaires`.`user` TO `gestion_questionnaires`.`t_user`;
ALTER TABLE `user` CHANGE `id` `ID` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `fk_user_type` `FK_User_Type` INT(11) NOT NULL, CHANGE `username` `User` VARCHAR(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `password` `Password` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL, CHANGE `archive` `Archive` TINYINT(4) NOT NULL DEFAULT '0';
ALTER TABLE `user` DROP COLUMN `date_creation`;

RENAME TABLE `gestion_questionnaires`.`user_type` TO `gestion_questionnaires`.`t_user_type`;
ALTER TABLE `user_type` CHANGE `id` `ID` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `t_question_type` ADD `Archive` BOOLEAN NOT NULL DEFAULT FALSE AFTER `Type_Name`;
UPDATE `t_question_type` SET `Archive` = '1' WHERE `t_question_type`.`ID` = 3;
UPDATE `t_question_type` SET `Archive` = '1' WHERE `t_question_type`.`ID` = 5;

ALTER TABLE `t_question_type` DROP COLUMN `Archive`;