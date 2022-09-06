<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}

function ver4_zdwk_nav()
{
    echo '<li ';
    if (isset($_GET['plugin']) && $_GET['plugin'] == 'ver4_zdwk') {
        echo 'class="active"';
    }
    echo '><a href="index.php?plugin=ver4_zdwk"><span class="glyphicon glyphicon-book"></span> 百度知道签到</a></li>';
}

addAction('navi_1', 'ver4_zdwk_nav');
addAction('navi_7', 'ver4_zdwk_nav');

/*function zdsign($bduss)
{
    $c = new wcurl('https://zhidao.baidu.com/msubmit/signin');
    $c->addCookie(array('BDUSS' => $bduss));
    $c->get();
}*/
function zdsign($bduss){
    $boxType = [128 => "CopperChest",129 => "SilverChest",130 => "GoldChest",131 => "DiamondChest"];//宝箱类型
    $signinfo = json_decode((new wcurl('https://zhidao.baidu.com/mmisc/ajaxsigninfo'))->addCookie('BDUSS=' . $bduss)->get(), true);
    if ($signinfo) {
        $c = (new wcurl('https://zhidao.baidu.com/msubmit/signin', ['referer: https://zhidao.baidu.com/mmisc/signinfo', 'X-ik-ssl: 1', "X-ik-token: {$signinfo["user"]["stoken"]}"]))->addCookie('BDUSS=' . $bduss)->post(array("ssid" => null, "cifr" => null));//签到
        if($signinfo["data"]["signBoxId"]){
            $c = (new wcurl("https://zhidao.baidu.com/shop/submit/chest?type={$boxType[$signinfo["data"]["signBoxId"]]}", ["referer: https://zhidao.baidu.com/mmisc/signinfo", "X-ik-ssl: 1", "X-ik-token: {$signinfo["user"]["stoken"]}"]))->addCookie('BDUSS=' . $bduss)->post(array("itemId" => $signinfo["data"]["signBoxId"], "stoken" => $signinfo["user"]["stoken"]));//开宝箱
        }
    }
}

/*function wksign($bduss){
    $head = array();
    $head[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';
    $head[] = 'Referer: http://wenku.baidu.com/task/browse/daily';
    $c = new wcurl('http://wenku.baidu.com/task/submit/signin',$head);
    $c->addCookie('BDUSS=' . $bduss);
    $c->get();
}*/
