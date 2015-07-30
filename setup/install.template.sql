SET FOREIGN_KEY_CHECKS=0;




DROP TABLE IF EXISTS `{VAR-PREFIX}baiduid`;
CREATE TABLE `{VAR-PREFIX}baiduid` (
  `id` int(30) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(30) unsigned NOT NULL,
  `bduss` text,
  `name` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;








DROP TABLE IF EXISTS `{VAR-PREFIX}cron`;
CREATE TABLE `{VAR-PREFIX}cron` (
  `name` varchar(40) NOT NULL,
  `orde` int(10) NOT NULL DEFAULT '0',
  `file` varchar(100) DEFAULT NULL,
  `no` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `desc` text,
  `freq` int(10) NOT NULL DEFAULT '0',
  `lastdo`  int(10) UNSIGNED NOT NULL DEFAULT 0 ,
  `log` text,
  PRIMARY KEY (`name`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;




INSERT INTO `{VAR-PREFIX}cron` VALUES ('system_sign', '0', 'lib/cron_system_sign.php', '0', '每天对所有贴吧进行签到\r\n忽略或卸载此任务会导致停止签到', '0', '0', '');
INSERT INTO `{VAR-PREFIX}cron` VALUES ('system_sign_retry', '1', 'lib/cron_system_sign_retry.php', '0', '对所有签到失败的贴吧进行复签\r\n忽略或卸载此任务会导致停止复签', '0', '0', '');




DROP TABLE IF EXISTS `{VAR-PREFIX}options`;
CREATE TABLE `{VAR-PREFIX}options` (
  `name` varchar(255) NOT NULL,
  `value` text,
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




INSERT INTO `{VAR-PREFIX}options` VALUES ('system_url', '{VAR-SYSTEM-URL}');
INSERT INTO `{VAR-PREFIX}options` VALUES ('system_name', '贴吧云签到');
INSERT INTO `{VAR-PREFIX}options` VALUES ('cron_limit', '10');
INSERT INTO `{VAR-PREFIX}options` VALUES ('tb_max', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('sign_mode', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"3\";}');
INSERT INTO `{VAR-PREFIX}options` VALUES ('footer', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('ann', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('enable_reg', '1');
INSERT INTO `{VAR-PREFIX}options` VALUES ('protect_reg', '1');
INSERT INTO `{VAR-PREFIX}options` VALUES ('yr_reg', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('icp', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('trigger', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('mail_mode', 'MAIL');
INSERT INTO `{VAR-PREFIX}options` VALUES ('mail_name', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('mail_yourname', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('mail_host', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('mail_port', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('mail_auth', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('mail_smtpname', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('mail_smtppw', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('fb', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('cloud', '1');
INSERT INTO `{VAR-PREFIX}options` VALUES ('enable_addtieba', '1');
INSERT INTO `{VAR-PREFIX}options` VALUES ('pwdmode', 'md5(md5(md5($pwd)))');
INSERT INTO `{VAR-PREFIX}options` VALUES ('retry_max', '10');
INSERT INTO `{VAR-PREFIX}options` VALUES ('cron_order', '1');
INSERT INTO `{VAR-PREFIX}options` VALUES ('fb_tables', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('dev', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('cron_last_do_time', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('cron_last_do', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('cron_isdoing', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('cron_pw', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('sign_sleep', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('cktime', '999999');
INSERT INTO `{VAR-PREFIX}options` VALUES ('bduss_num', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('sign_multith', '1');
INSERT INTO `{VAR-PREFIX}options` VALUES ('sign_asyn', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('cron_asyn', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('cron_sign_again', 'a:2:{s:3:\"num\";i:0;s:6:\"lastdo\";s:10:\"2015-07-19\";}');
INSERT INTO `{VAR-PREFIX}options` VALUES ('sign_hour', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('mail_ssl', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('freetable', 'tieba');
INSERT INTO `{VAR-PREFIX}options` VALUES ('core_version', '4.0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('core_revision', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('isapp', '{VAR-ISAPP}');
#INSERT INTO `{VAR-PREFIX}options` VALUES ('toolpw', '{VAR-TOOLPW}');
INSERT INTO `{VAR-PREFIX}options` VALUES ('sign_scan', '2');
INSERT INTO `{VAR-PREFIX}options` VALUES ('system_keywords', '贴吧云签到');
INSERT INTO `{VAR-PREFIX}options` VALUES ('system_description', '贴吧云签到');
INSERT INTO `{VAR-PREFIX}options` VALUES ('bbs_us', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('bbs_pw', '');



DROP TABLE IF EXISTS `{VAR-PREFIX}plugins`;
CREATE TABLE `{VAR-PREFIX}plugins` (
  `name` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `ver` varchar(15) DEFAULT NULL,
  `options` text,
  `order` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;




INSERT INTO `{VAR-PREFIX}plugins` VALUES ('wmzz_debug', '1', '2.0', '', '0');




DROP TABLE IF EXISTS `{VAR-PREFIX}tieba`;
CREATE TABLE `{VAR-PREFIX}tieba` (
  `id` int(30) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(30) unsigned NOT NULL,
  `pid` int(30) unsigned NOT NULL DEFAULT '0',
  `fid` int(30) unsigned NOT NULL DEFAULT '0',
  `tieba` varchar(200) DEFAULT NULL,
  `no` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `latest` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `last_error` text,
  PRIMARY KEY (`id`),
  INDEX `uid` (`uid`) USING BTREE ,
  INDEX `latest` (`latest`) USING BTREE 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;








DROP TABLE IF EXISTS `{VAR-PREFIX}users`;
CREATE TABLE `{VAR-PREFIX}users` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pw` char(32) NOT NULL,
  `email` varchar(40) NOT NULL,
  `role` varchar(10) NOT NULL DEFAULT 'user',
  `t` varchar(20) NOT NULL DEFAULT 'tieba',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  UNIQUE KEY `email` (`email`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;








DROP TABLE IF EXISTS `{VAR-PREFIX}users_options`;
CREATE TABLE `{VAR-PREFIX}users_options` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `uid` int(30) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;