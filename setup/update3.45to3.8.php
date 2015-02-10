<?php
define('SYSTEM_NO_ERROR', true);
define('SYSTEM_NO_CHECK_VER', true);
define('SYSTEM_ONLY_CHECK_LOGIN', true);
define('SYSTEM_NO_PLUGIN', true);
require '../init.php';
global $m;
error_reporting(0);
if (ROLE == 'admin') {
	$cv = option::get('core_version');
	if (!empty($cv) && $cv >= '3.8') {
		msg('您的云签到已升级到 V3.8 版本，请勿重复更新<br/><br/>请立即删除 /setup/update3.45to3.48.php');	
	}
	option::add('core_version','3.8');
	option::add('isapp','0');
	option::add('cron_asyn','0');
	option::add('mail_ssl','0');
	option::add('baidu_name','1');
	option::set('sign_sleep', option::get('sign_sleep') * 1000);
	$m->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."plugins` (
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
;");
	$plugins = unserialize(option::get('actived_plugins'));
	foreach ($plugins as $plug) {
		if (isset($i['opt']['plugin_' . $plug])) {
			$set = $i['opt']['plugin_' . $plug];
		} else {
			$set = '';
		}
		$m->query("INSERT IGNORE INTO `".DB_NAME."`.`".DB_PREFIX."plugins` (`name`,`status`,`options`) VALUES ('{$plug}','1','{$set}');");
	}
	$m->query("ALTER TABLE `".DB_PREFIX."cron` ADD COLUMN `desc` text NULL AFTER `no`;");
	$m->query("ALTER TABLE `".DB_PREFIX."cron` DROP COLUMN `status`;",true);
	cron::aset('system_sign' , array(
		'desc' => '每天对所有贴吧进行签到'. "\n" .'忽略或卸载此任务会导致停止签到'
	));
	cron::add('system_sign_retry' , array(
		'orde'    => '1',
		'file'    => 'lib/cron_system_sign_retry.php',
		'no'      => '0',
		'desc'    => '对所有签到失败的贴吧进行复签' . "\n" . '忽略或卸载此任务会导致停止复签',
		'freq'    => '0'
	));
	$m->query("ALTER TABLE `".DB_PREFIX."baiduid` ADD COLUMN `name`  varchar(40) NULL AFTER `bduss`;");
	unlink(__FILE__);
	msg('您的云签到已成功升级到 V3.8 版本，请立即删除 /setup/update3.45to3.8.php，谢谢<br/><br/>若要获取 V3.8 版本新特性，请前往 <a href="http://www.stus8.com/forum.php?mod=viewthread&tid=6411">StusGame GROUP</a> ', SYSTEM_URL);
} else {
	msg('您需要先登录旧版本的云签到，才能继续升级');
}