<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
$now = time();
$dtime = (int)option::get('ver4_post_daily');
if ($dtime == (int)date('d')) {  //判断daily是否已经完成，没完成则不执行
    global $m;
    $hr = date('H');
    $do = (int)option::get('ver4_post_do');  //上一次执行ID
    $sql = "`remain` > 0 AND `rts` <= {$hr} AND `rte` >= {$hr} AND `nextdo` < {$now} AND `uid` IN (SELECT `uid` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "users_options` WHERE `name` = 'ver4_post_open' AND `value` = 1) AND `pid` IN (SELECT `pid` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `date` < {$now})";
    $max = $m->fetch_array($m->query("SELECT max(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE {$sql}")); //获取ID最大值
    if ($do < (int)$max['c'] && !empty((int)$max['c'])) {
        $x = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `id` > {$do} AND {$sql} ORDER BY `id` ASC"));  //获取回复贴吧信息
        $p = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = '{$x['pid']}'"));  //获取bduss信息
        $s = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_userset` WHERE `uid` = {$x['uid']}"));//获取用户设置信息
        if (empty($x['tid'])) {
            $tid = getFirstPageTid($x['tname']);
            if (count($tid) > 0) {
                $x['tid'] = rand_array($tid);
                option::set('ver4_post_do', $x['id']);
                collect($p['bduss'], $s, $x);
            } else {
                option::set('ver4_post_do', $x['id']);
            }
        } else {
            option::set('ver4_post_do', $x['id']);
            collect($p['bduss'], $s, $x);
        }
    } else {
        option::set('ver4_post_do', 0);
    }
}


/*
 * 汇总资料完成整个发帖过程
 * */
function collect($b, $u, $t)
{
    global $m;
    $now = time();
    $pjn = $now + 60;
    $content = array();
    $ucontent = array();
    $nextdo = $now + $t['space'];
    $randtime = (int)option::uget('ver4_post_randtime', $t['uid']);
    if (!empty($randtime)) {
        $nextdo = $now + randNum($t['space']);
    }
    $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` SET `date` = {$pjn} WHERE `pid` = {$t['pid']}");
    $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` SET `nextdo` = {$nextdo} WHERE `id` = {$t['id']}");
    $c = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_content` WHERE `tid` = {$t['id']} AND `uid` = '{$t['uid']}'");  //查询用户设置的回帖内容
    while ($rc = $m->fetch_array($c)) {
        $content[] = $rc['content'];
    } //循环列出用户回帖内容以供随机筛选
    if (count($content) < 1) {
        $uc = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_content` WHERE `tid` = 0 AND `uid` = '{$t['uid']}'");  //查询用户设置的回帖内容
        while ($urc = $m->fetch_array($uc)) {
            $ucontent[] = $urc['content'];
        } //循环列出用户回帖内容以供随机筛选
        if (count($ucontent) < 1) {
            $con = getTuLing();
        } else {
            $con = rand_array($ucontent);
        }
    } else {
        $con = rand_array($content);  //从内容数据随机取一句
    }
    $sc = option::get('ver4_post_suf'); //获取系统定义的后缀
    $con = $u['cs'] . $con . $u['ce'] . $sc; //生成回复内容
    if (!empty($t['qid'])) {
        $con = substr($con, 0, 60);
    }
    $re = sendIt($b, $u, $t, $con);  //提交数据至贴吧进行发帖操作
    $log = date('Y-m-d H:i:s') . ' 执行结果：' . $re[1] . '<br/>' . $t['log'];
    if ($re[0] == 2) {
        $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` SET `remain` = `remain` - 1,`log` = '{$log}',`success` = `success` + 1,`allsuc` = `allsuc` + 1 WHERE `id` = {$t['id']}");
    } else {
        $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` SET `remain` = `remain` - 1,`log` = '{$log}',`error` = `error` + 1,`allerr` = `allerr` + 1 WHERE `id` = {$t['id']}");
    }
}
