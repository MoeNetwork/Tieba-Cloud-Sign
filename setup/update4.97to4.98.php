<?php

define('SYSTEM_DEV', true);
define('SYSTEM_NO_CHECK_VER', true);
define('SYSTEM_NO_CHECK_LOGIN', true);
define('SYSTEM_NO_PLUGIN', true);
include __DIR__ . '/../init.php';
global $m,$i;
$cv = option::get('core_version');
if (!empty($cv) && $cv >= '4.98') {
    msg('您的云签到已升级到 V4.98 版本，请勿重复更新<br/><br/>请立即删除 /setup/update4.97to4.98.php');
}
$m->query("ALTER TABLE `" . DB_PREFIX . "baiduid` ADD `stoken` TEXT NULL DEFAULT NULL AFTER `bduss`;", true);

option::set('core_version', '4.98');
unlink(__FILE__);
msg('您的云签到已成功升级到 V4.98 版本，请立即删除 /setup/update4.97to4.98.php，谢谢', SYSTEM_URL);
