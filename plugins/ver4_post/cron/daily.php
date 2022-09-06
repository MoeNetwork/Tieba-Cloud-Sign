<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
/*
 * 生成每天要回复的剩余列表
 * */
global $m;
$dtime = (int)option::get('ver4_post_daily');
if ($dtime != date('d')) {
    $q = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba`");
    while ($x = $m->fetch_array($q)) {
        $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` SET `remain` = {$x['all']},`success` = 0,`error` = 0,`nextdo` = 0 WHERE `id` = {$x['id']}");
    }
    $td = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    $pinfo = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid`");
    while ($rb = $m->fetch_array($pinfo)) {
        for ($a=0;$a<24;$a++) {
            $b = 0;
            $tinfo = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `pid` = {$rb['id']} AND `rts` = {$a}");
            while ($rt = $m->fetch_array($tinfo)) {
                $nt = $td + ($a * 3600) + $b;
                $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` SET `nextdo` = {$nt} WHERE `id` = {$rt['id']}");
                $b = $b + 30;
            }
        }
    }
    option::set('ver4_post_daily', date('d'));
}

/*
 * 每3天清理日志
 * */
$lt = (int)option::get('ver4_post_loglast');
if (time() - $lt > 259200) {
    $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` SET `log` = ''");
    option::set('ver4_post_loglast', time());
}


//清理所有已经解除绑定用户设置的信息
$q = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba`");
while ($x = $m->fetch_array($q)) {
    $b = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = {$x['pid']}"));
    if (empty($b['id'])) {
        $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `id` = {$x['id']}");
        $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_content` SET `tid` = 0 WHERE `tid` = {$x['id']}");
        $nu = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "users` WHERE `id` = {$x['uid']}"));
        if (empty($nu['id'])) {
            $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_userset` WHERE `uid` = {$x['uid']}");
        }
    }
}
