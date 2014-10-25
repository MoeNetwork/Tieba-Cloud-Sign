<?php
define('SYSTEM_NO_ERROR', true);
define('SYSTEM_ONLY_CHECK_LOGIN', true);
require '../init.php';
global $m;
error_reporting(0);
if (ROLE == 'admin') {
	option::set('cron_pw','');
	option::set('sign_sleep','0');
	cron::add('system_sign', array(
		'file'    => 'lib/cron_system_sign.php',
		'no'      => 0,
		'status'  => 0,
		'freq'    => 0,
		'lastdo'  => 0
	));
	$m->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."users_options` (
`id`  int(30) NOT NULL AUTO_INCREMENT ,
`uid`  int(30) NOT NULL ,
`name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`value`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=2
CHECKSUM=0
ROW_FORMAT=DYNAMIC
DELAY_KEY_WRITE=0
;");
	$m->query("ALTER TABLE `".DB_PREFIX."cron`
MODIFY COLUMN `name`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `id`,
DROP INDEX `name` ,
ADD UNIQUE INDEX `name` (`name`) ;");
	$m->query("ALTER TABLE `".DB_PREFIX."users`
MODIFY COLUMN `role`  enum('banned','vip','user','admin') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'user' AFTER `email`;");
	unlink(__FILE__);
	msg('您的云签到已成功升级到 V3.4 版本，请立即删除 /setup/update3.3to3.4.php，谢谢');
} else {
	msg('您需要先登录旧版本的云签到，才能继续升级');
}