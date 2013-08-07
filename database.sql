-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 06, 2013 at 10:49 PM
-- Server version: 5.5.29
-- PHP Version: 5.3.10-1ubuntu3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `nbar1_gs`
--

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE IF NOT EXISTS `queue` (
  `q_id` int(255) NOT NULL AUTO_INCREMENT,
  `q_song_id` varchar(255) NOT NULL,
  `q_song_title` varchar(255) NOT NULL,
  `q_song_artist` varchar(255) NOT NULL,
  `q_song_status` enum('playing','queued','played','skipped','deleted','error') NOT NULL DEFAULT 'queued',
  `q_song_priority` enum('high','med','low') NOT NULL DEFAULT 'low',
  `q_song_position` int(255) NOT NULL,
  `q_song_added_ts` datetime DEFAULT NULL,
  `q_song_played_ts` datetime DEFAULT NULL,
  `q_song_played_by` int(15) DEFAULT NULL,
  `q_song_promoted_by` int(15) DEFAULT NULL,
  `q_song_skip` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`q_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_nickname` varchar(35) DEFAULT NULL,
  `user_created` datetime DEFAULT NULL,
  `user_lastlogin` datetime DEFAULT NULL,
  `user_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
