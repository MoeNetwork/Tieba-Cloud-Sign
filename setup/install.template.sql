DROP TABLE IF EXISTS `{VAR-PREFIX}cron`;
DROP TABLE IF EXISTS `{VAR-PREFIX}options`;
DROP TABLE IF EXISTS `{VAR-PREFIX}tieba`;
DROP TABLE IF EXISTS `{VAR-PREFIX}users`;
DROP TABLE IF EXISTS `{VAR-PREFIX}users_options`;
DROP TABLE IF EXISTS `{VAR-PREFIX}baiduid`;
DROP TABLE IF EXISTS `{VAR-PREFIX}plugins`;

CREATE TABLE IF NOT EXISTS `{VAR-PREFIX}cron` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `orde` int(10) NOT NULL DEFAULT '0',
  `file` varchar(1000) DEFAULT NULL,
  `no` int(10) NOT NULL DEFAULT '0',
  `desc`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
  `freq` int(10) NOT NULL DEFAULT '0',
  `lastdo` varchar(100) DEFAULT NULL,
  `log` text,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name` (`name`) USING BTREE 
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `{VAR-PREFIX}options` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `name` (`name`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `{VAR-PREFIX}tieba` (
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
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `{VAR-PREFIX}users` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `pw` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `role`  enum('banned','vip','user','admin') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'user' ,
  `t` varchar(200) NOT NULL DEFAULT 'tieba',
  `options` text,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name` (`name`) USING BTREE ,
  UNIQUE INDEX `id` (`id`) USING BTREE 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `{VAR-PREFIX}baiduid` (
  `id` int(30) NOT NULL AUTO_INCREMENT COMMENT 'pid',
  `uid` int(30) NOT NULL,
  `bduss` text,
  `name`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `{VAR-PREFIX}users_options` (
`id`  int(30) NOT NULL AUTO_INCREMENT ,
`uid`  int(30) NOT NULL ,
`name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`value`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
CHECKSUM=0
ROW_FORMAT=DYNAMIC
DELAY_KEY_WRITE=0
;

CREATE TABLE IF NOT EXISTS `{VAR-PREFIX}plugins` (
`id`  int(10) NOT NULL AUTO_INCREMENT ,
`name`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`status`  tinyint(1) NOT NULL DEFAULT 0 ,
`options`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `name` (`name`) USING BTREE 
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
CHECKSUM=0
ROW_FORMAT=DYNAMIC
DELAY_KEY_WRITE=0
;


INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('system_url', '{VAR-SYSTEM-URL}');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('system_name', '贴吧云签到');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('protector', '1');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('cron_limit', '25');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('tb_max', '0');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('sign_mode', 'a:1:{i:0;s:1:\"1\";}');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('footer', '');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('enable_reg', '1');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('protect_reg', '1');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('yr_reg', '');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('icp', '');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('trigger', '');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('mail_mode', 'MAIL');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('mail_name', '');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('mail_yourname', '');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('mail_host', '');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('mail_port', '');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('mail_auth', '0');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('mail_smtpname', 'admin');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('mail_smtppw', 'admin');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('fb', '5000');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('cloud', '1');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('enable_addtieba', '1');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('pwdmode', 'md5(md5(md5($pwd)))');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('retry_max', '4');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('cron_order', '1');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('fb_tables', '');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('dev', '');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('cron_last_do_time', '0');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('cron_last_do', '0');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('cron_isdoing', '0');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('cron_pw', '');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('sign_sleep', '0');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('cktime', '999999');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('bduss_num', '0');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('sign_multith', '1');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('sign_asyn', '0');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('cron_asyn', '1');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('cron_sign_again', '');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('sign_hour', '0');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('mail_ssl', '0');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('freetable', '0');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('core_version', '3.8');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('isapp', '{VAR-ISAPP}');
INSERT INTO `{VAR-PREFIX}options` (`name`,`value`) VALUES ('baidu_name', '1');

INSERT INTO `{VAR-PREFIX}cron` (`name`, `orde`, `file`, `no`, `desc`, `freq`, `lastdo`, `log`) VALUES ('system_sign', 0, 'lib/cron_system_sign.php', 0, '每天对所有贴吧进行签到\n忽略或卸载此任务会导致停止签到', 0, '0', '');
INSERT INTO `{VAR-PREFIX}cron` (`name`, `orde`, `file`, `no`, `desc`, `freq`, `lastdo`, `log`) VALUES ('system_sign_retry', 1, 'lib/cron_system_sign_retry.php', 0, '对所有签到失败的贴吧进行复签\n忽略或卸载此任务会导致停止复签', 0, '0', '');