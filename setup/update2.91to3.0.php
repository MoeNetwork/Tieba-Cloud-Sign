<?php
define('SYSTEM_NO_ERROR', true);
define('SYSTEM_ONLY_CHECK_LOGIN', true);
require '../init.php';
global $m;
error_reporting(0);
if (ROLE == 'admin') {
	$fbs = unserialize(option::get('fb_tables'));
	if(!isset($_GET['doing']) && !isset($_GET['next'])) {
		$m->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."baiduid` ( `id` int(30) NOT NULL AUTO_INCREMENT COMMENT 'pid', `uid` int(30) NOT NULL, `bduss` text, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		foreach ($fbs as $value) {
			$m->query("ALTER TABLE  `".DB_PREFIX.$value."` ADD  `pid` INT( 30 ) NOT NULL DEFAULT  '0' AFTER  `uid` ;");
			$m->query("ALTER TABLE  `".DB_PREFIX.$value."` ADD  `fid` INT( 30 ) NOT NULL DEFAULT  '0' AFTER  `pid` ;");
		}
		$m->query("ALTER TABLE  `".DB_PREFIX."tieba` ADD  `pid` INT( 30 ) NOT NULL DEFAULT  '0' AFTER  `uid` ;");
		$m->query("ALTER TABLE  `".DB_PREFIX."tieba` ADD  `fid` INT( 30 ) NOT NULL DEFAULT  '0' AFTER  `pid` ;");
		msg('<meta http-equiv="refresh" content="0; url=update2.91to3.0.php?doing" />即将开始更新用户贴吧数据，程序将自动继续<br/><br/><a href="update2.91to3.0.php?doing">如果你的浏览器没有自动跳转，请点击这里</a>',false);
	}

	if(isset($_GET['doing'])) {
		//扫出所有百度账号并插入到baiduid表
		$val = $m->once_fetch_array("SELECT * FROM `".DB_PREFIX."users` WHERE `ck_bduss` IS NOT NULL ORDER BY `id` ASC");
		if (empty($val['id'])) {
			msg('<meta http-equiv="refresh" content="0; url=update2.91to3.0.php?next" />所有贴吧数据更新成功，程序将自动继续<br/><br/><a href="update2.91to3.0.php?next">如果你的浏览器没有自动跳转，请点击这里</a>',false);
		}
		$m->query("INSERT INTO `".DB_PREFIX."baiduid` (`id`, `uid`, `bduss`) VALUES (NULL, '{$val['id']}', '{$val['ck_bduss']}');");
		$bsid = $m->insert_id();
		//捕获自增ID作为PID，更新贴吧数据表
		$m->query("UPDATE `".DB_PREFIX."tieba` SET  `pid` =  '{$bsid}' WHERE `".DB_PREFIX."tieba`.`uid` = {$val['id']};");
		foreach ($fbs as $value) {
			$m->query("UPDATE `".DB_PREFIX.$value."` SET  `pid` =  '{$bsid}' WHERE `".DB_PREFIX.$value."`.`uid` = {$val['id']};");
		}
		$m->query("UPDATE `".DB_PREFIX."users` SET `ck_bduss` = NULL WHERE `".DB_PREFIX."users`.`id` = {$val['id']};");

		$count = $m->once_fetch_array("SELECT COUNT(*) FROM `".DB_PREFIX."users` WHERE `ck_bduss` IS NOT NULL");
		if($count > 0) {
			msg('<meta http-equiv="refresh" content="0; url=update2.91to3.0.php?doing" />用户 [ UID: '.$val['id'].' ] 的贴吧数据更新成功，程序将自动继续<br/><br/><a href="update2.91to3.0.php?doing">如果你的浏览器没有自动跳转，请点击这里</a>',false);
		} else {
			msg('<meta http-equiv="refresh" content="0; url=update2.91to3.0.php?next" />所有贴吧数据更新成功，程序将自动继续<br/><br/><a href="update2.91to3.0.php?next">如果你的浏览器没有自动跳转，请点击这里</a>',false);
		}
	}

	if(isset($_GET['next'])) {
		//更新用户表，删除ck_bduss列
		$m->query("ALTER TABLE `".DB_PREFIX."users` DROP `ck_bduss`;");
		//新的设置
		option::set('cktime','999999');
		option::set('bduss_num','0');
		unlink(__FILE__);
		msg('您的云签到已成功升级到 V3.0 版本，请立即删除此文件，谢谢');
	}
} else {
	msg('您需要先登录旧版本的云签到，才能继续升级');
}
?>