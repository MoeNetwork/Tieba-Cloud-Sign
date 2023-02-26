<?php

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}

function ver4_lottery_nav()
{
    echo '<li ';
    if (isset($_GET['plugin']) && $_GET['plugin'] == 'ver4_lottery') {
        echo 'class="active"';
    }
    echo '><a href="index.php?plugin=ver4_lottery"><span class="glyphicon glyphicon-gift"></span> 知道商城抽奖</a></li>';
}

addAction('navi_1', 'ver4_lottery_nav');
addAction('navi_7', 'ver4_lottery_nav');


function getToken($pid)
{
    $bduss = misc::getCookie($pid);
    $tc = new wcurl('https://zhidao.baidu.com/shop/lottery');
    $tc->addCookie('BDUSS=' . $bduss);
    $re = $tc->get();
    $token = textMiddle($re, '\'luckyToken\', \'', '\'');
    return $token;
}

function lottery($pid, $token)
{
    $nt = time();
    $bduss = misc::getCookie($pid);
    $head = array();
    $head[] = 'Referer: https://zhidao.baidu.com/shop/lottery';
    $head[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';
    $pl = new wcurl("https://zhidao.baidu.com/shop/submit/lottery?type=0&token={$token}&_={$nt}308", $head);
    $pl->addCookie('BDUSS=' . $bduss);
    $re = $pl->get();
    $result = json_decode($re, true);
    return $result;
}
