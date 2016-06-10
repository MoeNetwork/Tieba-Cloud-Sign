<?php
define('SYSTEM_NO_ERROR', true);
define('SYSTEM_NO_CHECK_VER', true);
define('SYSTEM_NO_CHECK_LOGIN', true);
define('SYSTEM_NO_PLUGIN', true);
include '../init.php';
global $m,$i;
$cv = option::get('core_version');
if (!empty($cv) && $cv >= '4.5') {
    msg('您的云签到已升级到 V4.5 版本，请勿重复更新<br/><br/>请立即删除 /setup/update4.4to4.5.php');
}
$i['tabpart'][] = 'tieba';
foreach ($i['tabpart'] as $value) {
    $m->query('ALTER TABLE `'.DB_PREFIX.$value.'` MODIFY COLUMN `status` mediumint(8) UNSIGNED NOT NULL DEFAULT 0 AFTER `no`;');
}
option::add('csrf','0');
option::set('core_version' , '4.5');
unlink(__FILE__);
msg('您的云签到已成功升级到 V4.5 版本，请立即删除 /setup/update4.4to4.5.php，谢谢', SYSTEM_URL);