CREATE TABLE IF NOT EXISTS `Events` (
   `ID` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Identifier for this event',
   `Name` varchar(64) COLLATE latin1_general_ci COMMENT 'Name for this event',
   `NameShort` varchar(16) COLLATE latin1_general_ci COMMENT 'Short Name',
   `Description` varchar(1024) COLLATE latin1_general_ci COMMENT 'Short Description',
   `Payload` varchar(16384) COLLATE latin1_general_ci COMMENT 'Long Text',
   `Link` varchar(256),
   `Status` tinyint(2) NOT NULL COMMENT '4: Upcoming, need registration, 3: Upcoming, 2: Active, open to participate, 1: Active, entrance is closed, 0: Ended, -1: Hidden',
   `Main` tinyint(1) NOT NULL COMMENT 'Boolean, show this event on the main page',
   `Start` timestamp DEFAULT '2010-01-01 00:00:00' COMMENT 'When the event starts/started',
   `End` timestamp DEFAULT '2010-01-01 00:00:00' COMMENT 'When the event ends/ended',
   `Host1` varchar(50) COMMENT 'userName for the 1st host; we dont need userId',
   `Host2` varchar(50) COMMENT 'userName for the 2nd host, if there is',
   `Host3` varchar(50) COMMENT 'userName for the 3rd host, if there is',
   `DisplayOrder` smallint(4) unsigned NOT NULL COMMENT 'Display Order',
   PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
