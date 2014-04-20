<?php
require '../init.php';
global $m;
if (ROLE == 'admin') {
	$m->query('DROP TABLE IF EXISTS `'.DB_NAME.'`.`'.DB_PREFIX.'cron` ');
	$m->query("CREATE TABLE IF NOT EXISTS `".DB_NAME."`.`".DB_PREFIX."cron` (`id` int(30) NOT NULL AUTO_INCREMENT,`name` varchar(1000) NOT NULL,`file` varchar(1000) DEFAULT NULL,`no` int(10) NOT NULL DEFAULT '0',`status` int(10) NOT NULL DEFAULT '0', `freq` int(10) NOT NULL DEFAULT '0',`lastdo` varchar(100) DEFAULT NULL,`log` text,PRIMARY KEY (`id`), FULLTEXT KEY `name` (`name`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	unlink(__FILE__);
	msg('您的云签到已成功升级到 V2.0 版本，请立即删除此文件，谢谢');
} else {
	msg('您需要先登录旧版本的云签到，才能继续升级');
}
?>