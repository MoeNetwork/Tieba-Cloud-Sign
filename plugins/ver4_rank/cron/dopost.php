<?php

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
global $m;
$now = time();
$result = '';
$id = option::get('ver4_rank_id');
$max = $m->fetch_array($m->query("SELECT max(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_rank_log`")); //获取ID最大值
if ($id < $max['c']) {
    $b = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_rank_log` WHERE `id` > {$id} ORDER BY `id` ASC"));
    $p = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = '{$b['pid']}'"));  //获取bduss信息
    $td = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    $ad = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'));
    $rc = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_rank_log` WHERE `date` > {$td} AND `date` < {$ad} AND `id` ={$b['id']}"));
    if ($rc['c'] < 1) {
        $ck = (int)option::uget('ver4_rank_check', $b['uid']);
        if ($ck == 1) {
            $re = dorank($p['bduss'], $b['fid'], $b['nid']);
            $r = json_decode($re, true);
            switch ($r['no']) {
                case 0:
                    $error = '助攻成功啦~明天记得继续呦~';
                    break;
                case 3110004:
                    $error = '你还未关注当前吧哦, 快去关注吧~';
                    break;
                case 2280006:
                    $error = '今日已助攻过了，或者度受抽风了~';
                    break;
                default:
                    $error = '助攻失败，发生了一些未知错误~';
                    break;
            }
            $result .= '<br/>' . date('Y-m-d') . ' #' . $r['no'] . ',' . $error . $b['log'];
            $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_rank_log` SET `log` = '{$result}',`date` = {$now} WHERE `id` = {$b['id']}");
        }
    }
    option::set('ver4_rank_id', $b['id']);
} else {
    option::set('ver4_rank_id', 0);
}


//清理所有已经解除绑定用户设置的信息
$q = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_rank_log`");
while ($x = $m->fetch_array($q)) {
    $b = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = {$x['pid']}"));
    if (empty($b['id'])) {
        $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_rank_log` WHERE `id` = {$x['id']}");
    }
}
