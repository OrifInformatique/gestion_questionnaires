--
-- Add email to `user`
--
ALTER TABLE `user` ADD `email` VARCHAR(100) NULL DEFAULT NULL AFTER `password`;