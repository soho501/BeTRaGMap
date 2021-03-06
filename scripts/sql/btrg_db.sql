CREATE TABLE IF NOT EXISTS `backer` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(100) NOT NULL,
  `URL` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `backergroup` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `BACKERID` int(11) NOT NULL,
  `GROUPID` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `category` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(100) NOT NULL,
  `DESCRIPTION` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `categorygroup` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CATEGORYID` int(11) NOT NULL,
  `GROUPID` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `group` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `URL` varchar(100) NOT NULL,
  `NAME` varchar(100) NOT NULL,
  `BACKERS` int(11) NOT NULL,
  `DAYS` int(11) NOT NULL,
  `RAISED` int(11) NOT NULL,
  `TARGET` int(11) NOT NULL,
  `FUNDEDDATE` date NOT NULL,
  `DESCRIPTION` longtext NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
