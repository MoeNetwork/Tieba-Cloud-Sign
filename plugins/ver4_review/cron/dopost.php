<?php

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
global $m;
$now = time();
$id = option::get('ver4_review_id');
$max = $m->fetch_array($m->query("SELECT max(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_review_list`")); //获取ID最大值
if ($id < $max['c']) {
    $b = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_review_list` WHERE `id` > {$id} ORDER BY `id` ASC"));
    $open = (int)option::uget('ver4_review_crv', $b['uid']);
    if (!empty($open)) {
        $jg = time() - $b['date'];
        if ($jg >  $b['space']) {
            $u = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = {$b['pid']}"));
            if (!empty($u['id'])) {
                dopost($b['tname'], $b['kw'], $u['bduss']);
                $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_review_list` SET `date` = {$now} WHERE `id` = {$b['id']}");
            }
        }
    }
    option::set('ver4_review_id', $b['id']);
} else {
    option::set('ver4_review_id', 0);
}


//清理所有已经解除绑定用户设置的信息
$q = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_review_list`");
while ($x = $m->fetch_array($q)) {
    $b = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = {$x['pid']}"));
    if (empty($b['id'])) {
        $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_review_list` WHERE `id` = {$x['id']}");
    }
}
