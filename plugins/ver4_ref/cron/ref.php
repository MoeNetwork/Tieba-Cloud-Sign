<?php

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
global $m;
$day = option::get('ver4_ref_day');
if ($day != date('d')) {
    $lastdo = (int)option::get('ver4_ref_lastdo');
    if ((time() - $lastdo > 90) && (date('H') > 18)) {
        $id = option::get('ver4_ref_id');
        $b = $m->fetch_array($m->query("SELECT max(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid`"));
        if ($id < $b['c']) {
            $bi = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` > {$id}"));
            $x = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "tieba` WHERE `pid` = {$bi['id']}"));
            if ($x['c'] <= 1000) {
                misc::scanTiebaByPid($bi['id']);
            }
            option::set('ver4_ref_id', $bi['id']);
            option::set('ver4_ref_lastdo', time());
        } else {
            option::set('ver4_ref_id', 0);
            option::set('ver4_ref_day', date('d'));
        }
    }
}
