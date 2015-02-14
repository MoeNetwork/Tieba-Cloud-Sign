<?php
define('SYSTEM_NO_ERROR', true);
define('SYSTEM_NO_CHECK_VER', true);
define('SYSTEM_ONLY_CHECK_LOGIN', true);
define('SYSTEM_NO_PLUGIN', true);
require '../init.php';
global $m,$i;
error_reporting(0);
if (ROLE == 'admin') {
	$sql = '';
	foreach ($i['table'] as $tab) {
		$sql .= "ALTER TABLE `".DB_PREFIX.$tab."` DROP INDEX `id`, ADD INDEX `uid` (`uid`) USING BTREE;";
	}
	$sql .= "ALTER TABLE `".DB_PREFIX."baiduid` ADD INDEX `uid` (`uid`) USING BTREE ;";
	$sql .= "ALTER TABLE `".DB_PREFIX."options` DROP INDEX `id`, DROP INDEX `value`;";
	@$m->xquery($sql,true);
	option::set('core_version' , SYSTEM_VER);
	unlink(__FILE__);
	msg('升级完成，请删除 /setup/update3.8to3.9.php',SYSTEM_URL);
} else {
	msg('您需要先登录旧版本的云签到，才能继续升级');
}