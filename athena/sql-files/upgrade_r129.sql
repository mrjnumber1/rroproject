ALTER TABLE `guild`  ADD `storage_password` CHAR( 32 ) NOT NULL AFTER  `skill_point`;
ALTER TABLE `login`  ADD `storage_password` CHAR( 32 ) NOT NULL AFTER `user_pass`;
ALTER TABLE `members`ADD `storage_password` CHAR( 32 ) NOT NULL AFTER `mpass`;

