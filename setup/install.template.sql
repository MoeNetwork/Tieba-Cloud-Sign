-- 百度云签到安装： MySQL 语句模板
--
-- 手工安装方法：
-- 1.替换所有{VAR-PREFIX}为你想要的数据表前缀
-- 2.替换所有的{VAR-SYSTEM-URL}为站点地址，后面带上/
-- 3.修改并运行下列语句，并在运行的时候去除 -- 
-- INSERT INTO `{VAR-PREFIX}users` (`id`, `name`, `pw`, `email`, `role`, `t`, `ck_bduss`, `options`) VALUES (1, '用户名', '密码', '邮箱', 'admin', 'tieba', '', NULL);
--
-- 特别注意：如果没有明确给出数据库名称，请直接删除所有的 
-- 如果这是安装向导提示你手动安装的，请无视这些注释！

--
-- 删除存在的表
--

DROP TABLE IF EXISTS `{VAR-PREFIX}cron`;
DROP TABLE IF EXISTS `{VAR-PREFIX}options`;
DROP TABLE IF EXISTS `{VAR-PREFIX}tieba`;
DROP TABLE IF EXISTS `{VAR-PREFIX}users`;
DROP TABLE IF EXISTS `{VAR-PREFIX}baiduid`;

--
-- 表的结构 `{VAR-PREFIX}cron`
--

CREATE TABLE IF NOT EXISTS `tc_cron` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(1000) NOT NULL,
  `orde` int(10) NOT NULL DEFAULT '0',
  `file` varchar(1000) DEFAULT NULL,
  `no` int(10) NOT NULL DEFAULT '0',
  `status` int(10) NOT NULL DEFAULT '0',
  `freq` int(10) NOT NULL DEFAULT '0',
  `lastdo` varchar(100) DEFAULT NULL,
  `log` text,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `{VAR-PREFIX}options`
--

CREATE TABLE IF NOT EXISTS `{VAR-PREFIX}options` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `name` (`name`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- 转存表中的数据 `{VAR-PREFIX}options`
--

INSERT INTO `{VAR-PREFIX}options` (`name`, `value`) VALUES
('system_url', '{VAR-SYSTEM-URL}'),
('system_name', '贴吧云签到'),
('protector', '1'),
('actived_plugins', 'a:1:{i:0;s:10:"wmzz_debug";}'),
('cron_limit', '0'),
('tb_max', '0'),
('sign_mode', 'a:1:{i:0;s:1:"1";}'),
('footer', ''),
('enable_reg', '1'),
('protect_reg', ''),
('yr_reg', ''),
('icp', ''),
('trigger', ''),
('mail_mode', 'MAIL'),
('mail_name', ''),
('mail_yourname', ''),
('mail_host', ''),
('mail_port', '21'),
('mail_auth', '0'),
('mail_smtpname', ''),
('mail_smtppw', ''),
('fb', '5000'),
('cloud', '1'),
('enable_addtieba', '1'),
('pwdmode', 'md5(md5(md5($pwd)))'),
('retry_max', '10'),
('cron_order', '1'),
('fb_tables', ''),
('dev', '0'),
('cron_last_do_time', '0'),
('cron_last_do', '0');

-- --------------------------------------------------------

--
-- 表的结构 `{VAR-PREFIX}tieba`
--

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `{VAR-PREFIX}users`
--

CREATE TABLE IF NOT EXISTS `{VAR-PREFIX}users` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `pw` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `role` varchar(100) NOT NULL DEFAULT 'user',
  `t` varchar(200) NOT NULL DEFAULT 'tieba',
  `options` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `id_3` (`id`),
  KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `{VAR-PREFIX}baiduid`
--

CREATE TABLE IF NOT EXISTS `{VAR-PREFIX}baiduid` (
  `id` int(30) NOT NULL AUTO_INCREMENT COMMENT 'pid',
  `uid` int(30) NOT NULL,
  `bduss` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
