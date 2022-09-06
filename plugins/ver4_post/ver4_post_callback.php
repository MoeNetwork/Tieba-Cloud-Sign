<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
/**
 * 安装插件时会被调用
 */
function callback_install()
{
    //在这里做点事
    global $m;
    $m->query("
		CREATE TABLE IF NOT EXISTS `".DB_PREFIX."ver4_post_content` (
		  `id` int(10) NOT NULL AUTO_INCREMENT,
		  `tid` int(10) NOT NULL DEFAULT '0',
		  `uid` int(10) NOT NULL,
		  `content` text,
		  `date` int(10) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
	");
    $m->query("
		CREATE TABLE IF NOT EXISTS `".DB_PREFIX."ver4_post_tieba` (
		  `id` int(10) NOT NULL AUTO_INCREMENT,
		  `uid` int(10) NOT NULL,
		  `pid` int(10) NOT NULL,
		  `fid` int(10) NOT NULL,
		  `tid` varchar(15) NOT NULL,
		  `qid` varchar(20) NOT NULL DEFAULT '0',
		  `rts` int(10) NOT NULL DEFAULT '0',
		  `rte` int(10) NOT NULL DEFAULT '24',
		  `tname` varchar(255) NOT NULL,
		  `pname` varchar(255) NOT NULL,
		  `all` int(10) NOT NULL DEFAULT '0',
		  `space` int(10) NOT NULL DEFAULT '60',
		  `remain` int(10) NOT NULL DEFAULT '0',
		  `success` int(10) NOT NULL DEFAULT '0',
		  `error` int(10) NOT NULL DEFAULT '0',
		  `log` text,
		  `date` int(10) NOT NULL,
		  `nextdo` int(10) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
	");
    $m->query("
		CREATE TABLE IF NOT EXISTS `".DB_PREFIX."ver4_post_userset` (
		  `id` int(10) NOT NULL AUTO_INCREMENT,
		  `uid` int(10) NOT NULL,
		  `cat` int(10) NOT NULL DEFAULT '5',
		  `cs` varchar(255) DEFAULT NULL,
		  `ce` varchar(255) DEFAULT NULL,
		  `date` int(10) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
	");
}

/**
 * 激活插件时会被调用
 */
function callback_init()
{
    option::set('ver4_post_dt', 10);
    option::set('ver4_post_all', 200);
    option::set('ver4_post_ts', 300);
    option::set('ver4_post_daily', 0);
    option::set('ver4_post_do', 0);
    option::set('ver4_post_pid', 0);
    option::set('ver4_post_suf', 0);
    option::set('ver4_post_apikey', '');
    cron::set('ver4_post_daily', 'plugins/ver4_post/cron/daily.php', 0, 0, 0);
    cron::set('ver4_post_dopost', 'plugins/ver4_post/cron/dopost.php', 0, 0, 0);
}

/**
 * 禁用插件时会被调用
 */
function callback_inactive()
{
    option::del('ver4_post_dt');
    option::del('ver4_post_all');
    option::del('ver4_post_ts');
    option::del('ver4_post_daily');
    option::del('ver4_post_do');
    option::del('ver4_post_pid');
    option::del('ver4_post_suf');
    option::del('ver4_post_apikey');
    cron::del('ver4_post_daily');
    cron::del('ver4_post_dopost');
}

/**
 * 卸载插件时会被调用
 * 卸载插件前，如果插件是激活的，会自动禁用并调用 callback_inactive()
 */
function callback_remove()
{
    //在这里做点事
    global $m;
    $m->query("DROP TABLE IF EXISTS `".DB_PREFIX."ver4_post_userset`");
    $m->query("DROP TABLE IF EXISTS `".DB_PREFIX."ver4_post_tieba`");
    $m->query("DROP TABLE IF EXISTS `".DB_PREFIX."ver4_post_content`");
}

/**
 * 升级插件时会被调用
 * 系统会传入当前数据库的版本号、当前插件文件中说明的版本号
 * 必须有返回值，如果返回新的版本号，新版本号由系统记录到数据库；如果返回false，将终止操作且不记录到数据库
 */
function callback_update($ver1, $ver2)
{
    //ver1 是当前数据库的版本号
    //ver2 是当前插件文件中说明的版本号，即 插件名_desc.php 的 ['plugin']['version'] 的值
    //在这里做点事
    return false; //我不干了！
}

/**
 * 插件自定义保存设置函数
 * 插件调用方法：setting.php?mod=setplugin:插件名称
 * 然后系统会调用 插件名_callback.php 的 callback_setting()
 */
function callback_setting()
{
    //在这里做点事
    //dump($_POST); //看看前端给我POST了什么东西
}
