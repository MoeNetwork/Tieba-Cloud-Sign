<?php

define('SYSTEM_DEV', true);
define('SYSTEM_NO_CHECK_VER', true);
define('SYSTEM_NO_CHECK_LOGIN', true);
define('SYSTEM_NO_PLUGIN', true);
include __DIR__ . '/../init.php';
global $m,$i;
$cv = option::get('core_version');
if (!empty($cv) && $cv >= '4.97') {
    msg('您的云签到已升级到 V4.97 版本，请勿重复更新<br/><br/>请立即删除 /setup/update4.96to4.97.php');
}
$m->query("ALTER TABLE `" . DB_PREFIX . "baiduid` DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;", true);
$m->query("ALTER TABLE `" . DB_PREFIX . "baiduid` DROP INDEX `name`;", true);
$m->query("ALTER TABLE `" . DB_PREFIX . "baiduid` CHANGE `name` `name` VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';", true);
$m->query("ALTER TABLE `" . DB_PREFIX . "baiduid` ADD `portrait` VARCHAR(40) DEFAULT '' NOT NULL AFTER `name`;", true);
$m->query("ALTER TABLE `" . DB_PREFIX . "baiduid` ADD INDEX(`portrait`);", true);

//update name_show and portrait
$result = $m->query("SELECT bduss FROM " . DB_PREFIX . "baiduid;");
while ($row = $result->fetch_assoc()) {
    $baiduUserInfo = getBaiduUserInfo($row["bduss"]);
    if (!empty($baiduUserInfo["portrait"])) {
        $baidu_name = sqladds($baiduUserInfo["name"]);
        $baidu_name_portrait = sqladds($baiduUserInfo["portrait"]);
        $m->query("UPDATE " . DB_PREFIX . "baiduid SET `portrait` = '{$baidu_name_portrait}' WHERE `bduss` = '{$row["bduss"]}';");
    }
}

option::set('core_version', '4.97');
unlink(__FILE__);
msg('您的云签到已成功升级到 V4.97 版本，请立即删除 /setup/update4.96to4.97.php，谢谢', SYSTEM_URL);
