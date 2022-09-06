<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
global $m;

$now = time();
$id = option::get('ver4_zdwk_pid');
$sql = "`uid` IN (SELECT `uid` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "users_options` WHERE `name` = 'ver4_zdwk_czd' AND `value` = 1)";
$max = $m->fetch_array($m->query("SELECT max(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE {$sql}")); //获取ID最大值
if ($id < $max['c']) {
    $b = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` > {$id} AND {$sql} ORDER BY `id` ASC"));
    $td = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    $ad = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'));
    $rc = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_zdwk_log` WHERE `date` > {$td} AND `date` < {$ad} AND `pid` ={$b['id']}"));
    if ($rc['c'] < 1) {
        zdsign($b['bduss']);
        $m->query("INSERT INTO `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_zdwk_log` (`uid`,`pid`,`result`,`date`) VALUES ({$b['uid']},{$b['id']},'签到完成 ',{$now})");
    }
    option::set('ver4_zdwk_pid', $b['id']);
} else {
    option::set('ver4_zdwk_pid', 0);
}


//清理所有已经解除绑定用户设置的信息
$q = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_zdwk_log`");
while ($x = $m->fetch_array($q)) {
    $b = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = {$x['pid']}"));
    if (empty($b['id'])) {
        $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_zdwk_log` WHERE `id` = {$x['id']}");
    }
}

/*
 * 删除30天之前的历史记录
 * */
$d = option::get('ver4_zdwk_day');
if ($d != date('d')) {
    global $m;
    $thirty = time() - 2592000;
    $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_zdwk_log` WHERE  `date` <= {$thirty}");
}
option::set('ver4_zdwk_day', date('d'));
