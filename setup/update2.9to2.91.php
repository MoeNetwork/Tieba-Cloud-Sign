<?php
define('NO_ERROR', true);
require '../init.php';
global $m;
error_reporting(0);
if (ROLE == 'admin') {
	$fbs = unserialize(option::get('fb_tables'));
	//捕获自增ID作为PID，更新贴吧数据表
	$m->query("UPDATE `".DB_PREFIX."tieba` SET  `lastdo` =  '2010-10-10' WHERE `".DB_PREFIX."tieba`.`lastdo` != '2010-10-10';");
	foreach ($fbs as $value) {
		$m->query("UPDATE `".DB_PREFIX."tieba` SET  `lastdo` =  '2010-10-10' WHERE `".DB_PREFIX.$value."`.`lastdo` != '2010-10-10';");
	}
	unlink(__FILE__);
	msg('您的云签到已成功升级到 V2.91 版本，请立即删除此文件，谢谢');
} else {
	msg('您需要先登录旧版本的云签到，才能继续升级');
}
?>