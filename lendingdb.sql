-- Add availability column in main items table
-- 1 = available; 0 = not available; set default to 0 for safety
ALTER TABLE items ADD COLUMN available TINYINT NOT NULL DEFAULT 0;

-- Create transaction table
CREATE TABLE trans(
	tranID INT NOT NULL AUTO_INCREMENT ,
	userID VARCHAR( 50 ) NOT NULL ,
	itemID INT NOT NULL NOT NULL,
	checkOutDate DATETIME NOT NULL,
	dueDate DATETIME NOT NULL,
	checkInDate DATETIME,
	PRIMARY KEY ( tranID ) ,
	UNIQUE (userID, itemID, checkOutDate),
	FOREIGN KEY ( userID ) REFERENCES userdb( userid ) ,
	FOREIGN KEY ( itemID ) REFERENCES items( itemID )
) ENGINE = INNODB;

