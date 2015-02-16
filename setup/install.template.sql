DROP TABLE IF EXISTS `{VAR-PREFIX}baiduid`;
CREATE TABLE `{VAR-PREFIX}baiduid` (
`id` int(30) NOT NULL COMMENT 'pid',
  `uid` int(30) NOT NULL,
  `bduss` text,
  `name` varchar(40) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{VAR-PREFIX}cron`;
CREATE TABLE `{VAR-PREFIX}cron` (
`id` int(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `orde` int(10) NOT NULL DEFAULT '0',
  `file` varchar(1000) DEFAULT NULL,
  `no` int(10) NOT NULL DEFAULT '0',
  `desc` text,
  `freq` int(10) NOT NULL DEFAULT '0',
  `lastdo` varchar(100) DEFAULT NULL,
  `log` text
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{VAR-PREFIX}options`;
CREATE TABLE `{VAR-PREFIX}options` (
`id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{VAR-PREFIX}plugins`;
CREATE TABLE `{VAR-PREFIX}plugins` (
`id` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `options` text
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{VAR-PREFIX}tieba`;
CREATE TABLE `{VAR-PREFIX}tieba` (
`id` int(30) NOT NULL,
  `uid` int(30) NOT NULL,
  `pid` int(30) NOT NULL DEFAULT '0',
  `fid` int(30) NOT NULL DEFAULT '0',
  `tieba` varchar(10000) DEFAULT NULL,
  `no` int(10) NOT NULL DEFAULT '0',
  `status` int(10) NOT NULL DEFAULT '0',
  `lastdo` varchar(200) DEFAULT '0',
  `last_error` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{VAR-PREFIX}users`;
CREATE TABLE `{VAR-PREFIX}users` (
`id` int(30) NOT NULL,
  `name` varchar(20) NOT NULL,
  `pw` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `role` enum('banned','vip','user','admin') NOT NULL DEFAULT 'user',
  `t` varchar(20) NOT NULL DEFAULT 'tieba'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{VAR-PREFIX}users_options`;
CREATE TABLE `{VAR-PREFIX}users_options` (
`id` int(30) NOT NULL,
  `uid` int(30) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `{VAR-PREFIX}baiduid`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `{VAR-PREFIX}cron`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`) USING BTREE;

ALTER TABLE `{VAR-PREFIX}options`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`), ADD UNIQUE KEY `name` (`name`), ADD FULLTEXT KEY `value` (`value`);

ALTER TABLE `{VAR-PREFIX}plugins`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `{VAR-PREFIX}tieba`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `{VAR-PREFIX}users`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`) USING BTREE, ADD UNIQUE KEY `id` (`id`) USING BTREE;

ALTER TABLE `{VAR-PREFIX}users_options`
 ADD PRIMARY KEY (`id`);


ALTER TABLE `{VAR-PREFIX}baiduid`
MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;
ALTER TABLE `{VAR-PREFIX}cron`
MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;
ALTER TABLE `{VAR-PREFIX}options`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `{VAR-PREFIX}plugins`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `{VAR-PREFIX}tieba`
MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;
ALTER TABLE `{VAR-PREFIX}users`
MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;
ALTER TABLE `{VAR-PREFIX}users_options`
MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;

INSERT INTO `{VAR-PREFIX}cron` (`name`, `orde`, `file`, `no`, `desc`, `freq`, `lastdo`, `log`) VALUES('system_sign', 0, 'lib/cron_system_sign.php', 0, '每天对所有贴吧进行签到\r\n忽略或卸载此任务会导致停止签到', 0, '0', '');
INSERT INTO `{VAR-PREFIX}cron` (`name`, `orde`, `file`, `no`, `desc`, `freq`, `lastdo`, `log`) VALUES('system_sign_retry', 1, 'lib/cron_system_sign_retry.php', 0, '对所有签到失败的贴吧进行复签\r\n忽略或卸载此任务会导致停止复签', 0, '0', '');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'system_url', '{VAR-SYSTEM-URL}');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'system_name', '贴吧云签到');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'protector', '1');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'cron_limit', '15');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'tb_max', '0');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'sign_mode', 'a:2:{i:0;s:1:"1";i:1;s:1:"3";}');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'footer', '');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'enable_reg', '1');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'protect_reg', '1');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'yr_reg', '');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'icp', '');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'trigger', '');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'mail_mode', 'MAIL');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'mail_name', '');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'mail_yourname', '');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'mail_host', '');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'mail_port', '');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'mail_auth', '0');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'mail_smtpname', '');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'mail_smtppw', '');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'fb', '0');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'cloud', '1');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'enable_addtieba', '1');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'pwdmode', 'md5(md5(md5($pwd)))');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'retry_max', '10');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'cron_order', '1');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'fb_tables', '');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'dev', '');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'cron_last_do_time', '2015-02-13');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'cron_last_do', '0');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'cron_isdoing', '0');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'cron_pw', '');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'sign_sleep', '0');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'cktime', '999999');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'bduss_num', '0');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'sign_multith', '1');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'sign_asyn', '0');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'cron_asyn', '');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'cron_sign_again', 'a:2:{s:3:"num";i:0;s:6:"lastdo";s:10:"2015-02-13";}');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'sign_hour', '0');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'mail_ssl', '0');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'freetable', 'tieba');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'core_version', '3.9');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'isapp', '{VAR-ISAPP}');
INSERT INTO `{VAR-PREFIX}options` ( `name`, `value`) VALUES( 'baidu_name', '1');
INSERT INTO `{VAR-PREFIX}plugins` ( `name`, `status`, `options`) VALUES( 'wmzz_debug', 1, '');