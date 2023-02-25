<?php

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
function callback_init()
{
    global $m;
    //create connect tab
    $m->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "weltolk_backup_qq_connect` (
        `id`  int(255) NOT NULL AUTO_INCREMENT ,
        `uid`  int(255) NOT NULL ,
        `client`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
        `connect_type`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
        `address`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
        `access_token`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
        PRIMARY KEY (`id`)
        )
        ENGINE=MyISAM
        DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
        AUTO_INCREMENT=12
        CHECKSUM=0
        ROW_FORMAT=DYNAMIC
        DELAY_KEY_WRITE=0;");
    //create target tab
    $m->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "weltolk_backup_qq_target` (
        `id`  int(255) NOT NULL AUTO_INCREMENT ,
        `uid`  int(255) NOT NULL ,
        `connect_id`  int(255) NOT NULL ,
        `hour`  int(255) NOT NULL ,
        `type`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
        `type_id`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
        `path`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
        `nextdo`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
        PRIMARY KEY (`id`)
        )
        ENGINE=MyISAM
        DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
        AUTO_INCREMENT=12
        CHECKSUM=0
        ROW_FORMAT=DYNAMIC
        DELAY_KEY_WRITE=0;");
    // plugin_option
    option::set('weltolk_backup_qq_limit', "10");
    option::set('weltolk_backup_qq_enable', "on");
    option::set('weltolk_backup_qq_log', "init");
    //cron_tab setting
    cron::set('weltolk_backup_qq', 'plugins/weltolk_backup_qq/weltolk_backup_qq_cron.php', 0, '每日数据库备份qq推送定时任务', 0);
}

function callback_inactive()
{
    //cron_tab setting
    cron::del('weltolk_backup_qq');
}

function callback_remove()
{
    // plugin_option
    option::del('weltolk_backup_qq_limit');
    option::del('weltolk_backup_qq_enable');
    option::del('weltolk_backup_qq_log');
    //user setting
    global $m;
    $m->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "weltolk_backup_qq_connect`");
    $m->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "weltolk_backup_qq_target`");
}
