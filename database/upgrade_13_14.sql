RENAME TABLE `gestion_questionnaires`.`t_user` TO `gestion_questionnaires`.`user`;
ALTER TABLE `user` CHANGE `ID` `id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `FK_User_Type` `fk_user_type` INT(11) NOT NULL, CHANGE `User` `username` VARCHAR(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `Password` `password` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL, CHANGE `Archive` `archive` TINYINT(4) NOT NULL DEFAULT '0';
ALTER TABLE `user` ADD `date_creation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `archive`;

RENAME TABLE `gestion_questionnaires`.`t_user_type` TO `gestion_questionnaires`.`user_type`;
ALTER TABLE `user_type` CHANGE `ID` `id` INT(11) NOT NULL AUTO_INCREMENT;
