--
-- Add email to `user`
--
ALTER TABLE `user` ADD `email` VARCHAR(100) NULL DEFAULT NULL AFTER `password`;

--
-- Make the user field unique
--
ALTER TABLE `user` ADD UNIQUE `username_unique` ( `username`);