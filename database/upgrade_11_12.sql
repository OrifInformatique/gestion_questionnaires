--
-- Adds the column `Questionnaire_Subtitle` to `t_questionnaire`
--

ALTER TABLE `t_questionnaire` ADD `Questionnaire_Subtitle` VARCHAR(255) NULL AFTER `Questionnaire_Name`;