CREATE TABLE IF NOT EXISTS `{VAR-DB}`.`{VAR-TABLE}` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `uid` int(30) NOT NULL,
  `tieba` varchar(10000) DEFAULT NULL,
  `no` int(10) NOT NULL DEFAULT '0',
  `status` int(10) NOT NULL DEFAULT '0',
  `lastdo` varchar(200) DEFAULT '0',
  `last_error` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
