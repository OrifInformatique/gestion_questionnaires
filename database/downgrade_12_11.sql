--
-- Modifies `t_user` to have `is_active` back
--
ALTER TABLE `t_user` CHANGE `is_inactive` `is_active` TINYINT(4) NULL DEFAULT '1';