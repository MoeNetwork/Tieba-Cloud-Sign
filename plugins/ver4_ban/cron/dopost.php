<?php

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
$id = option::get('ver4_ban_id');
$id = empty($id) ? 0 : $id;
global $m;
$time = time();
$otime = $time - 86400;
$sql = "`date` < {$otime} AND `stime` < {$time} AND `etime` > {$time}  AND `uid` IN (SELECT `uid` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "users_options` WHERE `name` = 'ver4_ban_open' AND `value` = '1')";
$max = $m->fetch_array($m->query("SELECT max(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` WHERE {$sql}")); //获取ID最大值
if ($id < $max['c']) {
    $ls = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` WHERE `id` > {$id} AND {$sql} ORDER BY `id` ASC"));
    $us = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_userset` WHERE `uid` = {$ls['uid']}"));
    $re = ver4_ban_client($ls['pid'], $ls['portrait'], $ls['name'], $ls['tieba'], $us['c']);
    $re = json_decode($re, true);
    if (!$re['error_code']) {
        $con = $ls['log'] . date('Y-m-d H:i:s') . ' 执行结果：<font color="green">操作成功</font><br>';
    } else {
        $con = $ls['log'] . date('Y-m-d H:i:s') . " 执行结果：<font color=\"red\">操作失败</font>#{$re["error_code"]} {$re["error_msg"]}<br>";
    }
    $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` SET `date` = {$time},`log` = '{$con}' WHERE `id` = {$ls['id']}");
    option::set('ver4_ban_id', $ls['id']);
} else {
    option::set('ver4_ban_id', 0);
}


//清理所有已经解除绑定用户设置的信息
$q = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list`");
while ($x = $m->fetch_array($q)) {
    if ($x['etime'] - time() < -86400) {
        $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` WHERE `id` = {$x['id']}");
    }
    $b = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = {$x['pid']}"));
    if (empty($b['id'])) {
        $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` WHERE `id` = {$x['id']}");
        $nu = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "users` WHERE `id` = {$x['uid']}"));
        if (empty($nu['id'])) {
            $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_userset` WHERE `uid` = {$x['uid']}");
        }
    }
}
