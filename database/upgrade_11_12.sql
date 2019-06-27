--
-- Adds the column `Questionnaire_Subtitle` to `t_questionnaire`
--

ALTER TABLE `t_questionnaire` ADD `Questionnaire_Subtitle` VARCHAR(255) NULL AFTER `Questionnaire_Name`;

--
-- Modifies `t_user` to have `is_inactive` for inactive users checking
--

ALTER TABLE `t_user` CHANGE `is_active` `is_inactive` TINYINT(4) NOT NULL DEFAULT '0';