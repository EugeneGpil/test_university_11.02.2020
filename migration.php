<?php

require_once "connection.php";

$migration = $DB->prepare(
    'USE `' . $CONFIG["database_name"] . '`;
    CREATE TABLE IF NOT EXISTS `news` ( 
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `participant_id` int(11) NOT NULL,
        `news_title` varchar(255) NOT NULL,
        `news_message` text NOT NULL,
        `likes_counter` int(11) NOT NULL DEFAULT "0",
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;
    INSERT INTO `news` (`id`, `participant_id`, `news_title`, `news_message`, `likes_counter`)
        VALUES (1, 1, "New agenda!", "Please visit our site!", 0) ON DUPLICATE KEY UPDATE `id` = 1;
    INSERT INTO `news` (`id`, `participant_id`, `news_title`, `news_message`, `likes_counter`)
        VALUES (2, 1, "Second agenda!", "Please visit our site again!", 0) ON DUPLICATE KEY UPDATE `id` = 2;

    CREATE TABLE IF NOT EXISTS `participant` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `email` varchar(255) NOT NULL UNIQUE,
        `name` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;
    INSERT INTO `participant` (`id`, `email`, `name`)
        VALUES (1, "user@example.com", "The first user") ON DUPLICATE KEY UPDATE `id` = 1;

    CREATE TABLE IF NOT EXISTS `session` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `time_of_event` datetime NOT NULL,
        `description` text NOT NULL,
        `number_of_seats`int(11) NOT NULL DEFAULT "20",
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
    INSERT INTO `session` (`id`, `name`, `time_of_event`, `description`)
        VALUES (1, "Introducing in HTML", "2020-02-15 10:00:00", "Start your new career!") 
        ON DUPLICATE KEY UPDATE `id` = 1;

    CREATE TABLE IF NOT EXISTS `speaker` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3;
    INSERT INTO `speaker` (`id`, `name`) VALUES (1, "Watson") ON DUPLICATE KEY UPDATE `id` = 1;
    INSERT INTO `speaker` (`id`, `name`) VALUES (2, "Arnold") ON DUPLICATE KEY UPDATE `id` = 2;
    
    CREATE TABLE IF NOT EXISTS `session_speaker` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `session` int(11) NOT NULL,
        `speaker` int(11) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `Relation` (`session`, `speaker`),
        CONSTRAINT `session_speaker_session` FOREIGN KEY (`session`) REFERENCES `session` (`id`) ON DELETE CASCADE,
        CONSTRAINT `session_speaker_speaker` FOREIGN KEY (`speaker`) REFERENCES `speaker` (`id`) ON DELETE CASCADE
    );
    INSERT INTO `session_speaker` (`id`, `session`, `speaker`) VALUES (1, 1, 1) ON DUPLICATE KEY UPDATE `id` = 1;
    INSERT INTO `session_speaker` (`id`, `session`, `speaker`) VALUES (2, 1, 2) ON DUPLICATE KEY UPDATE `id` = 2;

    CREATE TABLE IF NOT EXISTS `session_participant` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `session` int(11) NOT NULL,
        `participant` int(11) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `relation` (`session`, `participant`),
        CONSTRAINT `session_participant_session` FOREIGN KEY (`session`) REFERENCES `session` (`id`) ON DELETE CASCADE,
        CONSTRAINT `session_participant_participant` FOREIGN KEY (`participant`) REFERENCES `participant` (`id`) ON DELETE CASCADE
    );'
);
$migration->execute();
echo "DONE\n";
