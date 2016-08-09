CREATE TABLE IF NOT EXISTS `{VAR-DB}`.`{VAR-TABLE}` (
  `id` int(30) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(30) unsigned NOT NULL,
  `pid` int(30) unsigned NOT NULL DEFAULT '0',
  `fid` int(30) unsigned NOT NULL DEFAULT '0',
  `tieba` varchar(200) DEFAULT NULL,
  `no` tinyint(1) NOT NULL DEFAULT '0',
  `status`  mediumint(8) UNSIGNED NOT NULL DEFAULT '0' ,
  `latest` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `last_error` text,
  PRIMARY KEY (`id`),
  INDEX `uid` (`uid`) USING BTREE ,
  INDEX `latest` (`latest`) USING BTREE 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;