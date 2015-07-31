<?php
define('SYSTEM_NO_ERROR', true);
define('SYSTEM_NO_CHECK_VER', true);
define('SYSTEM_NO_CHECK_LOGIN', true);
define('SYSTEM_NO_PLUGIN', true);
require '../init.php';
global $m,$i;

    $cv = option::get('core_version');
    if (!empty($cv) && $cv >= '4.0') {
        msg('您的云签到已升级到 V4.0 版本，请勿重复更新<br/><br/>请立即删除 /setup/update3.9to4.0.php');
    }
    //------------------------------------------------//
    option::add('toolpw','');
    option::add('sign_scan','1');
    option::add('system_keywords','贴吧云签到');
    option::add('system_description','贴吧云签到');
    option::add('bbs_us','');
    option::add('bbs_pw','');
    $i['tabpart'][] = 'tieba';
    foreach ($i['tabpart'] as $value) {
        $m->xquery('
        ALTER TABLE `'.DB_PREFIX.$value'`
MODIFY COLUMN `id`  int(30) UNSIGNED NOT NULL AUTO_INCREMENT FIRST ,
MODIFY COLUMN `uid`  int(30) UNSIGNED NOT NULL AFTER `id`,
MODIFY COLUMN `pid`  int(30) UNSIGNED NOT NULL DEFAULT 0 AFTER `uid`,
MODIFY COLUMN `fid`  int(30) UNSIGNED NOT NULL DEFAULT 0 AFTER `pid`;

ALTER TABLE `'.DB_PREFIX.$value'`
DROP COLUMN `lastdo`,
ADD COLUMN `latest`  tinyint(2) UNSIGNED NOT NULL DEFAULT 0 AFTER `status`;

ALTER TABLE `'.DB_PREFIX.$value'`
MODIFY COLUMN `status`  tinyint(2) UNSIGNED NOT NULL DEFAULT 0 AFTER `no`;

ALTER TABLE `'.DB_PREFIX.$value'`
ADD INDEX `latest` (`latest`) USING BTREE ;');
    }

    $m->xquery('
ALTER TABLE `'.DB_PREFIX.'baiduid`
MODIFY COLUMN `id`  int(30) UNSIGNED NOT NULL AUTO_INCREMENT FIRST ,
MODIFY COLUMN `uid`  int(30) UNSIGNED NOT NULL AFTER `id`;

ADD INDEX (`name`)
ALTER TABLE `'.DB_PREFIX.'cron`
ADD INDEX `name` (`name`) USING BTREE;

ALTER TABLE `'.DB_PREFIX.'cron`
MODIFY COLUMN `no` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `file`;

ALTER TABLE `'.DB_PREFIX.'users_options`
ADD INDEX `name` (`name`) USING BTREE ;

ALTER TABLE `'.DB_PREFIX.'plugins`
ADD `order` int(10) unsigned NOT NULL DEFAULT 0;
');

    //------------------------------------------------//
    unlink(__FILE__);
    option::set('core_version' , '4.0');
    msg('您的云签到已成功升级到 V4.0 版本，请立即删除 /setup/update3.9to4.0.php，谢谢<br/><br/>若要获取 V4.0 版本新特性，请前往 <a href="http://www.stus8.com/forum.php?mod=viewthread&tid=6411">StusGame GROUP</a> ', SYSTEM_URL);