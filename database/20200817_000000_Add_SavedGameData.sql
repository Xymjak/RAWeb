CREATE TABLE IF NOT EXISTS `gamedatasave` (
    ID INT(11) PRIMARY KEY,
    TopAchievers VARCHAR(435) DEFAULT NULL,
    TopAchieversDate TIMESTAMP NULL,
    UniquePlayers INT(7) UNSIGNED DEFAULT NULL,
    UniquePlayersDate TIMESTAMP NULL
);
