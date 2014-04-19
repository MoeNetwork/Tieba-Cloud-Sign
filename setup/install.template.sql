-- 百度云签到安装： MySQL 语句模板
--
-- 手工安装方法：
-- 1.替换所有{VAR-PREFIX}为你想要的数据表前缀
-- 2.替换所有{VAR-DB}为你要安装该程序使用的数据库的名称
-- 3.替换所有的{VAR-SYSTEM-URL}为站点地址，后面带上/
-- 4.修改并运行下列语句，并在运行的时候去除 -- 
-- INSERT INTO `{VAR-DB}`.`{VAR-PREFIX}users` (`id`, `name`, `pw`, `email`, `role`, `t`, `ck_bduss`, `options`) VALUES (1, '用户名', '密码', '邮箱', 'admin', 'tieba', '', NULL);
--
-- 特别注意：如果没有明确给出数据库名称，请直接删除所有的 `{VAR-DB}`.

--
-- 表的结构 `{VAR-DB}`.`{VAR-PREFIX}cron`
--

CREATE TABLE IF NOT EXISTS `{VAR-DB}`.`{VAR-PREFIX}cron` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(5000) NOT NULL,
  `file` varchar(10000) DEFAULT NULL,
  `no` int(10) DEFAULT '0',
  `lastdo` varchar(200) DEFAULT NULL,
  `log` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `{VAR-DB}`.`{VAR-PREFIX}options`
--

CREATE TABLE IF NOT EXISTS `{VAR-DB}`.`{VAR-PREFIX}options` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `name` (`name`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- 转存表中的数据 `{VAR-DB}`.`{VAR-PREFIX}options`
--

INSERT INTO `{VAR-DB}`.`{VAR-PREFIX}options` (`id`, `name`, `value`) VALUES
(1, 'system_url', '{VAR-SYSTEM-URL}'),
(2, 'cron_limit', '0'),
(3, 'cron_last_do', '10'),
(4, 'cron_last_do_time', '0'),
(5, 'tb_max', '0'),
(6, 'footer', ''),
(7, 'enable_reg', '1'),
(8, 'protect_reg', '1'),
(9, 'yr_reg', ''),
(10, 'icp', ''),
(11, 'actived_plugins', 'a:1:{i:0;s:10:"wmzz_debug";}'),
(12, 'trigger', ''),
(13, 'protector', '1'),
(14, 'fb', '4000'),
(15, 'fb_tables', ''),
(16, 'cloud', '1'),
(17, 'dev', ''),
(18, 'freetable', 'tieba'),
(19, 'cron_isdoing', '0'),
(20, 'pwdmode', 'md5(md5(md5($pwd)))');

-- --------------------------------------------------------

--
-- 表的结构 `{VAR-DB}`.`{VAR-PREFIX}tieba`
--

CREATE TABLE IF NOT EXISTS `{VAR-DB}`.`{VAR-PREFIX}tieba` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `uid` int(30) NOT NULL,
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
-- 表的结构 `{VAR-DB}`.`{VAR-PREFIX}users`
--

CREATE TABLE IF NOT EXISTS `{VAR-DB}`.`{VAR-PREFIX}users` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `pw` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `role` varchar(100) NOT NULL DEFAULT 'user',
  `t` varchar(200) NOT NULL DEFAULT 'tieba',
  `ck_bduss` varchar(1000) DEFAULT NULL,
  `options` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `id_3` (`id`),
  KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
