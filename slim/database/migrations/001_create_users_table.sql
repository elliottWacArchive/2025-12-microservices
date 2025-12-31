CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(255) NOT NULL,
    `last_name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- test data
INSERT INTO `users` (`username`, `first_name`, `last_name`) VALUES
('bill.gates', 'Bill', 'Gates'),
('steve.jobs', 'Steve', 'Jobs'),
('mark.zuckerberg', 'Mark', 'Zuckerberg'),
('evan.spiegel', 'Evan', 'Spiegel'),
('jack.dorsey', 'Jack', 'Dorsey'),
('elliott.bg', 'Elliott', 'Bg'),
('john.doe', 'John', 'Doe'),
('jane.doe', 'Jane', 'Doe'),
('jim.beam', 'Jim', 'Beam'),
('jill.doe', 'Jill', 'Doe'),
('jeff.bezos', 'Jeff', 'Bezos');