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
    $bduss = misc::getCookie($pid);
    $head = [
        "x-ik-ssl" => "1",
        "Referer" =>  "https://zhidao.baidu.com/shop/lottery",
        "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36",
    ];
    $pl = new wcurl("https://zhidao.baidu.com/shop/submit/lottery", $head);
    $pl->addCookie('BDUSS=' . $bduss);
    $re = $pl->post(["type" => "0", "token" => $token]);
    $result = json_decode($re, true);
    return $result;
}
