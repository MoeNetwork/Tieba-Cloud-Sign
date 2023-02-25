<?php

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}

function ver4_rank_nav()
{
    echo '<li ';
    if (isset($_GET['plugin']) && $_GET['plugin'] == 'ver4_rank') {
        echo 'class="active"';
    }
    echo '><a href="index.php?plugin=ver4_rank"><span class="glyphicon glyphicon-plane"></span> 贴吧名人堂助攻</a></li>';
}

addAction('navi_1', 'ver4_rank_nav');
addAction('navi_7', 'ver4_rank_nav');

function dorank($bduss, $fid, $nid)
{
    $tbs = misc::getTbs(0, $bduss);
    $pz = new wcurl("http://tieba.baidu.com/celebrity/submit/support");
    $pz->addCookie(array('BDUSS' => $bduss));
    $data = array(
        'tbs'      => $tbs,
        'forum_id' => $fid,
        'npc_id'   => $nid
    );
    $result = $pz->post($data);
    return $result;
}
