-- Migration pour ajouter le champ password à la table users
ALTER TABLE `users` 
ADD COLUMN `password` VARCHAR(255) NULL AFTER `last_name`;

