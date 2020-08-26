--
-- Make the user field unique
--
ALTER TABLE `user` ADD UNIQUE `username_unique` ( `username`); 