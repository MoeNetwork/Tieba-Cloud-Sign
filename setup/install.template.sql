SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `{VAR-PREFIX}baiduid`;
CREATE TABLE `{VAR-PREFIX}baiduid` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `uid` int(30) NOT NULL,
  `bduss` text,
  `name` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{VAR-PREFIX}cron`;
CREATE TABLE `{VAR-PREFIX}cron` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `orde` int(10) NOT NULL DEFAULT '0',
  `file` varchar(1000) DEFAULT NULL,
  `no` int(10) NOT NULL DEFAULT '0',
  `desc` text,
  `freq` int(10) NOT NULL DEFAULT '0',
  `lastdo` varchar(100) DEFAULT NULL,
  `log` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `{VAR-PREFIX}cron` VALUES ('1', 'system_sign', '0', 'lib/cron_system_sign.php', '0', '每天对所有贴吧进行签到\r\n忽略或卸载此任务会导致停止签到', '0', '0', '');
INSERT INTO `{VAR-PREFIX}cron` VALUES ('2', 'system_sign_retry', '1', 'lib/cron_system_sign_retry.php', '0', '对所有签到失败的贴吧进行复签\r\n忽略或卸载此任务会导致停止复签', '0', '0', '');

DROP TABLE IF EXISTS `{VAR-PREFIX}options`;
CREATE TABLE `{VAR-PREFIX}options` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

INSERT INTO `{VAR-PREFIX}options` VALUES ('1', 'system_url', '{VAR-SYSTEM-URL}');
INSERT INTO `{VAR-PREFIX}options` VALUES ('2', 'system_name', '贴吧云签到');
INSERT INTO `{VAR-PREFIX}options` VALUES ('3', 'protector', '1');
INSERT INTO `{VAR-PREFIX}options` VALUES ('4', 'cron_limit', '15');
INSERT INTO `{VAR-PREFIX}options` VALUES ('5', 'tb_max', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('6', 'sign_mode', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"3\";}');
INSERT INTO `{VAR-PREFIX}options` VALUES ('7', 'footer', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('8', 'ann', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('9', 'enable_reg', '1');
INSERT INTO `{VAR-PREFIX}options` VALUES ('10', 'protect_reg', '1');
INSERT INTO `{VAR-PREFIX}options` VALUES ('11', 'yr_reg', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('12', 'icp', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('13', 'trigger', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('14', 'mail_mode', 'MAIL');
INSERT INTO `{VAR-PREFIX}options` VALUES ('15', 'mail_name', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('16', 'mail_yourname', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('17', 'mail_host', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('18', 'mail_port', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('19', 'mail_auth', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('20', 'mail_smtpname', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('21', 'mail_smtppw', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('22', 'fb', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('23', 'cloud', '1');
INSERT INTO `{VAR-PREFIX}options` VALUES ('24', 'enable_addtieba', '1');
INSERT INTO `{VAR-PREFIX}options` VALUES ('25', 'pwdmode', 'md5(md5(md5($pwd)))');
INSERT INTO `{VAR-PREFIX}options` VALUES ('26', 'retry_max', '10');
INSERT INTO `{VAR-PREFIX}options` VALUES ('27', 'cron_order', '1');
INSERT INTO `{VAR-PREFIX}options` VALUES ('28', 'fb_tables', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('29', 'dev', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('30', 'cron_last_do_time', '从未执行');
INSERT INTO `{VAR-PREFIX}options` VALUES ('31', 'cron_last_do', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('32', 'cron_isdoing', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('33', 'cron_pw', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('34', 'sign_sleep', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('35', 'cktime', '999999');
INSERT INTO `{VAR-PREFIX}options` VALUES ('36', 'bduss_num', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('37', 'sign_multith', '1');
INSERT INTO `{VAR-PREFIX}options` VALUES ('38', 'sign_asyn', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('39', 'cron_asyn', '');
INSERT INTO `{VAR-PREFIX}options` VALUES ('40', 'cron_sign_again', 'a:2:{s:3:\"num\";i:0;s:6:\"lastdo\";s:10:\"从未执行\";}');
INSERT INTO `{VAR-PREFIX}options` VALUES ('41', 'sign_hour', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('42', 'mail_ssl', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('43', 'freetable', 'tieba');
INSERT INTO `{VAR-PREFIX}options` VALUES ('44', 'core_version', '3.9');
INSERT INTO `{VAR-PREFIX}options` VALUES ('45', 'core_revision', '0');
INSERT INTO `{VAR-PREFIX}options` VALUES ('46', 'isapp', '{VAR-ISAPP}');
INSERT INTO `{VAR-PREFIX}options` VALUES ('47', 'baidu_name', '1');

DROP TABLE IF EXISTS `{VAR-PREFIX}plugins`;
CREATE TABLE `{VAR-PREFIX}plugins` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `options` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `{VAR-PREFIX}plugins` VALUES ('1', 'wmzz_debug', '1', '');

DROP TABLE IF EXISTS `{VAR-PREFIX}tieba`;
CREATE TABLE `{VAR-PREFIX}tieba` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `uid` int(30) NOT NULL,
  `pid` int(30) NOT NULL DEFAULT '0',
  `fid` int(30) NOT NULL DEFAULT '0',
  `tieba` varchar(10000) DEFAULT NULL,
  `no` int(10) NOT NULL DEFAULT '0',
  `status` int(10) NOT NULL DEFAULT '0',
  `lastdo` varchar(200) DEFAULT '0',
  `last_error` text,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;DROP TABLE IF EXISTS `{VAR-PREFIX}users`;
CREATE TABLE `{VAR-PREFIX}users` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pw` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `role` enum('banned','vip','user','admin') NOT NULL DEFAULT 'user',
  `t` varchar(20) NOT NULL DEFAULT 'tieba',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{VAR-PREFIX}users_options`;
CREATE TABLE `{VAR-PREFIX}users_options` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `uid` int(30) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
