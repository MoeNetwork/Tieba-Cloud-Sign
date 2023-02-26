<?php

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
global $m;

if (date('H') >= 12) {
    $id = option::get('ver4_lottery_pid');
    $sql = "`uid` IN (SELECT `uid` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "users_options` WHERE `name` = 'ver4_lottery_check' AND `value` = '1')";
    $max = $m->fetch_array($m->query("SELECT max(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE {$sql}")); //获取ID最大值
    if ($id < $max['c']) {
        $b = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` > {$id} AND {$sql} ORDER BY `id` ASC"));
        $td = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $ad = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'));
        $rc = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_lottery_log` WHERE `date` > {$td} AND `date` < {$ad} AND `pid` = {$b['id']}"));
        if ($rc['c'] < 2) {
            $now = time();
            $md = $m->fetch_array($m->query("SELECT max(`date`) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_lottery_log` WHERE `pid` = {$b['id']}")); //获取ID最大值
            $tjg = $now - $md['c'];
            if ($tjg > 1200) {
                $token = getToken($b['id']);
                if (!empty($token)) {
                    $result = lottery($b['id'], $token);
                    if (empty($result['errno'])) {
                        $m->query("INSERT INTO `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_lottery_log` (`uid`,`pid`,`result`,`prize`,`date`) 
							VALUES ({$b['uid']},{$b['id']},'{$result['errmsg']}','{$result['data']['prizeList'][0]['goodsName']}',{$now})");
                    } else {
                        $m->query("INSERT INTO `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_lottery_log` (`uid`,`pid`,`result`,`prize`,`date`) 
							VALUES ({$b['uid']},{$b['id']},'{$result['errmsg']}','-',{$now})");
                    }
                }
            }
        }
        option::set('ver4_lottery_pid', $b['id']);
    } else {
        option::set('ver4_lottery_pid', 0);
    }
} else {
    $d = option::get('ver4_lottery_day');
    if ($d != date('d')) {
        global $m;
        $thirty = time() - 2592000;
        $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_lottery_log` WHERE  `date` <= {$thirty}");
    }
    option::set('ver4_lottery_day', date('d'));
}


//清理所有已经解除绑定用户设置的信息
$q = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_lottery_log`");
while ($x = $m->fetch_array($q)) {
    $b = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = {$x['pid']}"));
    if (empty($b['id'])) {
        $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_lottery_log` WHERE `id` = {$x['id']}");
    }
}
