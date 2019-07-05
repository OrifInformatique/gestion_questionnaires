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
-- Delete `t_questionnaire_model_topic`
--
DROP TABLE `t_questionnaire_model_topic`;

--
-- Delete `t_questionnaire_model`
--
DROP TABLE `t_questionnaire_model`;
