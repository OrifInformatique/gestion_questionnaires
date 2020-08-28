--
-- Make the user field not unique
--
ALTER TABLE `user` DROP INDEX `username_unique`;