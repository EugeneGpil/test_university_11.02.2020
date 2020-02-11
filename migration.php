<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/connection.php';

$migration = $DB->prepare(
    'USE `' . $CONFIG["database_name"] . '`;
    CREATE TABLE IF NOT EXISTS `News` ( `ID` int(11) NOT NULL AUTO_INCREMENT, `ParticipantId` int(11) NOT NULL, `NewsTitle` varchar(255) NOT NULL, `NewsMessage` text NOT NULL, `LikesCounter` int(11) NOT NULL DEFAULT "0", PRIMARY KEY (`ID`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;
    INSERT INTO `News` (`ID`, `ParticipantId`, `NewsTitle`, `NewsMessage`, `LikesCounter`) VALUES (1, 1, "New agenda!", "Please visit our site!", 0);
    CREATE TABLE IF NOT EXISTS `Participant` ( `ID` int(11) NOT NULL AUTO_INCREMENT, `Email` varchar(255) NOT NULL, `Name` varchar(255) NOT NULL, PRIMARY KEY (`ID`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;
    INSERT INTO `Participant` (`ID`, `Email`, `Name`) VALUES (1, "user@example.com", "The first user");
    CREATE TABLE IF NOT EXISTS `Session` ( `ID` int(11) NOT NULL AUTO_INCREMENT, `Name` varchar(255) NOT NULL, `TimeOfEvent` datetime NOT NULL, `Description` text NOT NULL, PRIMARY KEY (`ID`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
    CREATE TABLE IF NOT EXISTS `Speaker` ( `ID` int(11) NOT NULL AUTO_INCREMENT, `Name` varchar(255) NOT NULL, PRIMARY KEY (`ID`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3;
    INSERT INTO `Speaker` (`ID`, `Name`) VALUES (1, "Watson"), (2, "Arnold");'
);
$migration->execute();
echo "DONE\n";