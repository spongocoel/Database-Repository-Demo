 CREATE TABLE `test`.`Contact` (
	`ID` 		INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`Active` 	TINYINT( 1 ) NOT NULL DEFAULT 1,
	`Name` 		VARCHAR( 50 ) NOT NULL ,
	`Address` 	VARCHAR( 50 ) NULL ,
	`City` 		VARCHAR( 50 ) NULL ,
	`State` 	CHAR( 2 ) NULL ,
	`Zip` 		CHAR( 5 ) NULL ,
	`Birthday` 	DATE NULL
) ENGINE = MYISAM 