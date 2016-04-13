SET NAMES utf8;

CREATE TABLE IF NOT EXISTS `elfchat_admins` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` char(255) NOT NULL DEFAULT '',
  `password` char(64) NOT NULL DEFAULT '',
  `hash` char(64) NOT NULL,
  `logined` int(1) NOT NULL DEFAULT '0',
  `ip` char(32) NOT NULL DEFAULT '',
  `admin_url_hash` char(64) NOT NULL,
  `time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `elfchat_adminslogs` (
  `key` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(10) NOT NULL,
  `id` int(10) NOT NULL,
  `doing` text NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `elfchat_ban` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` char(255) NOT NULL,
  `ban_id` tinyint(1) NOT NULL DEFAULT '0',
  `ban_ip` tinyint(1) NOT NULL DEFAULT '0',
  `ip` char(40) NOT NULL,
  `for_time` int(11) NOT NULL,
  `start_time` int(11) NOT NULL,
  `reason` text NOT NULL,
  PRIMARY KEY (`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `elfchat_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `settings` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `elfchat_groups` (`id`, `title`, `settings`) VALUES (1, 'Guests', '%default_group_settings%');
INSERT INTO `elfchat_groups` (`id`, `title`, `settings`) VALUES (2, 'Users', '%default_group_settings%');
INSERT INTO `elfchat_groups` (`id`, `title`, `settings`) VALUES (3, 'Moderators', '%default_group_settings%');
INSERT INTO `elfchat_groups` (`id`, `title`, `settings`) VALUES (4, 'Administrators', '%default_group_settings%');

CREATE TABLE IF NOT EXISTS `elfchat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room` int(11) NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `time` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `personal` int(11) NOT NULL DEFAULT '0',
  `type` varchar(255) NOT NULL,
  `except` int(11) NOT NULL,
  `delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `elfchat_moderslogs` (
  `key` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(10) NOT NULL,
  `id` int(10) NOT NULL,
  `doing` text NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `elfchat_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `default` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `elfchat_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ownuser` tinyint(1) NOT NULL DEFAULT '1',
  `outsider` char(40) DEFAULT '',
  `outid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `mask` varchar(255) NOT NULL DEFAULT '',
  `avatar` varchar(255) NOT NULL,
  `guest` tinyint(1) NOT NULL DEFAULT '0',
  `moder` tinyint(1) NOT NULL DEFAULT '0',
  `settings` text NOT NULL,
  `group` int(11) NOT NULL,
  `password` char(40) NOT NULL,
  `email` varchar(255) NOT NULL,
  `session` char(40) NOT NULL DEFAULT '',
  `online` tinyint(1) NOT NULL DEFAULT '0',
  `ip` char(32) NOT NULL,
  `room` int(11) NOT NULL,
  `status` char(255) NOT NULL,
  `time` int(11) NOT NULL,
  `connect` tinyint(1) NOT NULL DEFAULT '0',
  `remember` tinyint(1) NOT NULL,
  `silent_until` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `session` (`session`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
