SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `queue` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `token` int(32) NOT NULL,
  `status` enum('playing','queued','played','skipped','deleted','error') NOT NULL DEFAULT 'queued',
  `priority` enum('high','med','low') NOT NULL DEFAULT 'low',
  `position` int(12) NOT NULL,
  `ts_added` datetime DEFAULT NULL,
  `ts_played` datetime DEFAULT NULL,
  `played_by` int(12) DEFAULT NULL,
  `promoted_by` int(12) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `songs` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `token` int(32) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `artist` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(35) DEFAULT NULL,
  `hash` varchar(32) DEFAULT NULL,
  `ts_created` datetime DEFAULT NULL,
  `ts_lastlogin` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
