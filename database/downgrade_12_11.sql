--
-- Removes the column `Questionnaire_Subtitle` from `t_questionnaire`
--

ALTER TABLE `t_questionnaire` DROP COLUMN `Questionnaire_Subtitle`
-- Delete `t_questionnaire_model_topic`
--
DROP TABLE `t_questionnaire_model_topic`;

--
-- Delete `t_questionnaire_model`
--
DROP TABLE `t_questionnaire_model`;
