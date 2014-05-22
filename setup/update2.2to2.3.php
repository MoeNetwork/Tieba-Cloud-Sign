<?php
require '../init.php';
global $m;
if (ROLE == 'admin') {
	$m->query("INSERT INTO `".DB_PREFIX."options` (`id`, `name`, `value`) VALUES (NULL, 'system_name', '贴吧云签到');");
	$m->query("INSERT INTO `".DB_PREFIX."options` (`id`, `name`, `value`) VALUES (NULL, 'cron_sign_again', '');");
	$m->query("INSERT INTO `".DB_PREFIX."options` (`id`, `name`, `value`) VALUES (NULL, 'cron_order', '1');");
	$m->query("INSERT INTO `".DB_PREFIX."options` (`id`, `name`, `value`) VALUES (NULL, 'sign_mode', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}');");
	$m->query("ALTER TABLE `".DB_PREFIX."cron` ADD  `orde` INT( 10 ) NOT NULL DEFAULT  '0' AFTER  `name` ;");
	unlink(__FILE__);
	msg('您的云签到已成功升级到 V2.3 版本，请立即删除此文件，谢谢');
} else {
	msg('您需要先登录旧版本的云签到，才能继续升级');
}
?>