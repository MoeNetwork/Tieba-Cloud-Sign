<?php

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}

function ver4_review_nav()
{
    echo '<li ';
    if (isset($_GET['plugin']) && $_GET['plugin'] == 'ver4_review') {
        echo 'class="active"';
    }
    echo '><a href="index.php?plugin=ver4_review"><span class="glyphicon glyphicon-screenshot"></span> 贴吧云审查[吧务]</a></li>';
}

addAction('navi_1', 'ver4_review_nav');
addAction('navi_7', 'ver4_review_nav');

function dopost($tieba, $kw, $bduss)
{
    $a = 0;
    $tid = '';
    $kw = json_decode($kw, true);
    $tinfo = scanTieba($tieba);
    $fid = $tinfo['forum']['id'];
    if (!empty($fid)) {
        foreach ($tinfo['thread_list'] as $tv) {
            $c_title = 0;
            $c_content = 0;
            foreach ($kw as $v) {
                if (empty($c_title)) {
                    $c_title = is_numeric(strpos($tv['title'], $v));
                }
                foreach ($tv['abstract'] as $ttc) {
                    if (empty($c_content)) {
                        $c_content = is_numeric(strpos($ttc['text'], $v));
                    }
                }
            }
            if (!empty($c_title) || !empty($c_content)) {
                if (empty($a)) {
                    $tid = $tv['id'];
                } else {
                    $tid .= '_' . $tv['id'];
                }
                $a++;
            }
        }
        if (!empty($a)) {
            delPost($bduss, $tieba, $fid, $tid);
        }
    }
}

function scanTieba($kw)
{
    $tl = new wcurl('http://c.tieba.baidu.com/c/f/frs/page');
    $data = array(
        '_client_id'      => 'wappc_1470896832265_330',
        '_client_type'    => 2,
        '_client_version' => '5.1.3',
        '_phone_imei'     => '867600020777420',
        'from'            => 'baidu_appstore',
        'kw'              => $kw,
        'model'           => 'HUAWEI MT7-TL10',
        'pn'              => 1,
        'rn'              => 35,
        'st_type'         => 'tb_forumlist',
        'timestamp'       => time() . '525'
    );
    $sign_str = '';
    foreach ($data as $k => $v) {
        $sign_str .= $k . '=' . $v;
    }
    $sign = strtoupper(md5($sign_str . 'tiebaclient!!!'));
    $data['sign'] = $sign;
    $tl->set(CURLOPT_RETURNTRANSFER, true);
    $rt = $tl->post($data);
    $result = json_decode($rt, true);
    return $result;
}

function delPost($bduss, $kw, $fid, $tid)
{
    $t = new wcurl('http://tieba.baidu.com/f/commit/thread/batchDelete');
    $data = 'ie=utf-8&tbs=' . misc::getTbs(0, $bduss) . "&kw={$kw}&fid={$fid}&tid={$tid}&isBan=0";
    $t->addCookie(array('BDUSS' => $bduss));
    $t->post($data);
}
