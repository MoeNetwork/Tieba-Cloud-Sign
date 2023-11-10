<?php

define('SYSTEM_DEV', true);
define('SYSTEM_NO_CHECK_VER', true);
define('SYSTEM_NO_CHECK_LOGIN', true);
define('SYSTEM_NO_PLUGIN', true);
include __DIR__ . '/../init.php';
global $m,$i;
$cv = option::get('core_version');
if (!empty($cv) && $cv >= '5.01') {
    msg('您的云签到已升级到 V5.01 版本，请勿重复更新<br/><br/>请立即删除 /setup/update5.00to5.01.php');
}
$m->query("ALTER TABLE `" . DB_PREFIX . "users` CHANGE `pw` `pw` TEXT;", true);

option::set('core_version', '5.01');
unlink(__FILE__);
msg('您的云签到已成功升级到 V5.01 版本，请立即删除 /setup/update5.00to5.01.php，谢谢', SYSTEM_URL);
