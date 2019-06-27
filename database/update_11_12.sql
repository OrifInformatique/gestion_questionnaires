--
-- Modifies `t_user` to have `is_inactive` for inactive users checking
--
ALTER TABLE `t_user` CHANGE `is_active` `is_inactive` TINYINT(4) NOT NULL DEFAULT '0';