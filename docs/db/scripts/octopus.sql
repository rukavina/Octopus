-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 02, 2011 at 05:47 PM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-1ubuntu9.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `octopus`
--

-- --------------------------------------------------------

--
-- Table structure for table `device`
--

CREATE TABLE IF NOT EXISTS `device` (
  `device_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `address` varchar(10) NOT NULL,
  `widget_class` varchar(50) NOT NULL,
  `status_data` text NOT NULL,
  `settings_data` text NOT NULL,
  PRIMARY KEY (`device_id`),
  KEY `address` (`type`,`address`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `device`
--

INSERT INTO `device` (`device_id`, `name`, `type`, `address`, `widget_class`, `status_data`, `settings_data`) VALUES
(1, 'Kamera', 'x10', 'A4', 'Octopus.Web.Device.X10.Switch', '{"on":"1"}', '{"top":20,"left":520,"width":100,"height":100,"x10_address":"A4"}'),
(2, 'Lampa', 'x10', 'A3', 'Octopus.Web.Device.X10.Lamp', '{"on":"0","intesity":"30"}', '{"top":220,"left":520,"width":100,"height":100,"x10_address":"A3"}'),
(3, 'Prisutan', 'x10', 'A5', 'Octopus.Web.Device.X10.Switch', '{"on":"1"}', '{"top":120,"left":520,"width":100,"height":100,"x10_address":"A5"}');

-- --------------------------------------------------------

--
-- Table structure for table `device_scene`
--

CREATE TABLE IF NOT EXISTS `device_scene` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` int(10) unsigned NOT NULL,
  `scene_id` int(10) unsigned NOT NULL,
  `settings_data` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_id` (`device_id`,`scene_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `device_scene`
--

INSERT INTO `device_scene` (`id`, `device_id`, `scene_id`, `settings_data`) VALUES
(1, 1, 1, ''),
(2, 2, 1, ''),
(3, 3, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `scene`
--

CREATE TABLE IF NOT EXISTS `scene` (
  `scene_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `settings_data` text NOT NULL,
  PRIMARY KEY (`scene_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `scene`
--

INSERT INTO `scene` (`scene_id`, `name`, `settings_data`) VALUES
(1, 'Dnevna Soba', '{\r\n  "top": 0,\r\n  "left": 0,\r\n  "width": 640,\r\n  "height": 480,\r\n  "background_image": "/camera",\r\n  "background_autoupdate": false,\r\n  "update_interval": 6000\r\n}');

-- --------------------------------------------------------

--
-- Table structure for table `trigger`
--

CREATE TABLE IF NOT EXISTS `trigger` (
  `trigger_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `type` varchar(20) NOT NULL,
  `class` varchar(50) NOT NULL,
  `status_data` text NOT NULL,
  `settings_data` text NOT NULL,
  `enabled` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`trigger_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `trigger`
--

INSERT INTO `trigger` (`trigger_id`, `name`, `type`, `class`, `status_data`, `settings_data`, `enabled`) VALUES
(1, 'Prisutan', 'X10', 'Octopus.X10.Trigger.Custom.Present', '', '{\r\n  "address":"A5",\r\n  "camera":"A4"\r\n}', 'yes'),
(2, 'Senzor', 'X10', 'Octopus.X10.Trigger.Custom.Sensor', '{"executed":1294258343}', '{"address":"A1","lamp":"A3"}', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `variable`
--

CREATE TABLE IF NOT EXISTS `variable` (
  `name` varchar(20) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `variable`
--

INSERT INTO `variable` (`name`, `value`) VALUES
('present', '');
