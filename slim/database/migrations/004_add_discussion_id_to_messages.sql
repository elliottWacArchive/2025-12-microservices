ALTER TABLE `messages` 
ADD COLUMN `discussion_id` VARCHAR(24) NULL AFTER `user_id`,
ADD INDEX `discussion_id` (`discussion_id`);
