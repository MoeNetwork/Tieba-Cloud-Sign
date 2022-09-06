<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}

function ver4_post_nav()
{
    echo '<li ';
    if (isset($_GET['plugin']) && $_GET['plugin'] == 'ver4_post') {
        echo 'class="active"';
    }
    echo '><a href="index.php?plugin=ver4_post"><span class="glyphicon glyphicon-check"></span> 贴吧云灌水</a></li>';
}


addAction('navi_1', 'ver4_post_nav');
addAction('navi_7', 'ver4_post_nav');

/*
 * 生成时间间隔随机数
 * */
function randNum($n)
{
    return rand($n, $n + ceil($n / 4));
}

/*
 * 从url中分离tid
 * */
function getTid($url)
{
    preg_match('/\.com\/p\/(?<tid>\d+)/', $url, $tids);
    return $tids ['tid'];
}

/*
 * 贴吧post参数整合
 * */
function getParameter($data)
{
    $sign_str = '';
    foreach ($data as $k => $v) {
        $sign_str .= $k . '=' . $v;
    }
    $sign = strtoupper(md5($sign_str . 'tiebaclient!!!'));
    $data['sign'] = $sign;
    return $data;
}

/*
 * 根据BDUSS生成固定位数数字
 * */
function findNum($str='')
{
    $str = sha1(md5($str));
    $str = trim($str);
    if (empty($str)) {
        return '';
    }
    $temp=array('1','2','3','4','5','6','7','8','9','0');
    $result='';
    for ($i=0;$i<strlen($str);$i++) {
        if (in_array($str[$i], $temp)) {
            $result.=$str[$i];
        }
    }
    if (strlen($result) < 10) {
        return (int)$result + 1000000000;
    } else {
        return $result;
    }
}

/*
 * 获取fid
 * */
function getFid($tname)
{
    $x = wcurl::xget("http://tieba.baidu.com/i/data/get_fid_by_fname?fname={$tname}");
    $r = json_decode($x, true);
    return $r['data']['fid'];
}


/*
 * 获取帖子详细内容
 * 返回fid、tid、tname、pname
 * */
function getPage($tid)
{
    $tl = new wcurl('http://c.tieba.baidu.com/c/f/pb/page');
    $data = array(
        '_client_type'    => 2,
        '_client_version' => '6.0.0',
        '_phone_imei'     => '867600020777420',
        'from'            => 'tiebawap_bottom',
        'kz'              => $tid,
        'pn'              => 1,
        'rn'              => 10,
        'timestamp'       => time() . '516'
    );
    $tl->set(CURLOPT_RETURNTRANSFER, true);
    $rt = $tl->post(getParameter($data));
    $result = json_decode($rt, true);
    $r = array(
        "fid"   => $result['forum']['id'],
        "tid"   => $tid,
        "tname" => $result['forum']['name'],
        "pname" => $result['post_list'][0]['title'],
    );
    return $r;
}


/*
 * 获得帖子第一页内容
 * */
function getFirstPageTid($name)
{
    $tid = array();
    $tl = new wcurl('http://c.tieba.baidu.com/c/f/frs/page');
    $data = array(
        '_client_id'      => 'wappc_1470896832265_330',
        '_client_type'    => 2,
        '_client_version' => '5.1.3',
        '_phone_imei'     => '867600020777420',
        'from'            => 'baidu_appstore',
        'kw'              => $name,
        'model'           => 'HUAWEI MT7-TL10',
        'pn'              => 1,
        'rn'              => 33,
        'st_type'         => 'tb_forumlist',
        'timestamp'       => time() . '516'
    );
    $tl->set(CURLOPT_RETURNTRANSFER, true);
    $rt = $tl->post(getParameter($data));
    $result = json_decode($rt, true)['thread_list'];
    foreach ($result as $v) {
        $tid[] = $v['id'];
    }
    unset($tid[0],$tid[1],$tid[2]);
    return $tid;
}

/*
 * 发表回复(支持楼中楼)
 * */
function sendIt($b, $u, $t, $c)
{
    $tp = new wcurl('http://c.tieba.baidu.com/c/c/post/add');
    $data = array(
        'BDUSS'           => $b,
        '_client_id'      => 'wappc_147' . substr(findNum($b), 0, 10) . '_' . substr(findNum($b), 5, 3),
        '_client_type'    => $u['cat'] == 5 ? rand(1, 4) : $u['cat'],
        '_client_version' => '7.9.2',
        '_phone_imei'     => md5($b),
        'anonymous'       => 1,
        'content'         => $c,
        'fid'             => $t ['fid'],
        'from'            => 'appstore',
        'is_ad'           => 0,
        'kw'              => $t ['tname'],
        'model'           => 'HUAWEI MT7-TL10',
        'new_vcode'       => 1,
        'quote_id'        => !empty($t['qid']) ? $t['qid'] : '',
        'tbs'             => misc::getTbs(0, $b),
        'tid'             => $t['tid'],
        'timestamp'       => time() . '516',
        'vcode_tag'       => 12,
    );
    $tp->set(CURLOPT_RETURNTRANSFER, true);
    $rt = $tp->post(getParameter($data));
    $re = json_decode($rt, true);
    if (!$re) {
        return array(0, 'JSON 解析错误');
    }
    if ($re ['error_code'] == 0) {
        return array(2, "使用第" . $u['cat'] . '种客户端发帖成功');
    } elseif ($re ['error_code'] == 5) {
        return array(5, "需要输入验证码，请检查你是否已经关注该贴吧。");
    } elseif ($re ['error_code'] == 220034) {
        return array(220034, "您的操作太频繁了！");
    } elseif ($re ['error_code'] == 340016) {
        return array(340016, "您已经被封禁");
    } elseif ($re ['error_code'] == 232007) {
        return array(232007, "您输入的内容不合法，请修改后重新提交。");
    } else {
        return array($re ['error_code'], "未知错误，错误代码：" . $re ['error_code']);
    }
}

/*
 * 获取帖子指定楼层信息
 * */
function getFloorInfo($tid, $pn, $floor)
{
    $tl = new wcurl('http://c.tieba.baidu.com/c/f/pb/page');
    $data = array(
        '_client_type'    => 2,
        '_client_version' => '6.0.0',
        '_phone_imei'     => '867600020777420',
        'from'            => 'tiebawap_bottom',
        'kz'              => $tid,
        'pn'              => $pn,
        'rn'              => 30,
        'timestamp'       => time() . '516'
    );
    $tl->set(CURLOPT_RETURNTRANSFER, true);
    $rt = $tl->post(getParameter($data));
    $result = json_decode($rt, true)['post_list'];
    $pid = 0;
    foreach ($result as $v) {
        if ($v['floor'] == $floor) {
            $pid = $v['id'];
        }
    }
    return $pid;
}

/*
 * 获取图灵机器人内容
 * */
function getTuLing()
{
    $re = wcurl::xget('http://tuling123.tbsign.cn/index.php?mod=index');
    $r = json_decode($re, true);
    if ($r['code'] != 100000) {
        $content = getJuZiMi();
    } else {
        $content = $r['text'];
    }
    return $content;
}

/*
 * 从m.juzimi.com/ju获取随机内容
 * By n0099 四叶重工
 * */
function getJuZiMi()
{
    //Note7 (￣▽￣)~*
    $curl = new wcurl('http://m.juzimi.com/ju/', array('User-Agent: Mozilla/5.0 (Linux; Android 6.0; SM-N930F Build/MMB29K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.81 Mobile Safari/537.36'));
    $curl -> set(CURLOPT_FOLLOWLOCATION, true);
    preg_match('/<h1 class="sentence" id="xqtitle">(.+)<\/h1>/', $curl -> exec(), $curl_result);
    return $curl_result[1];
}


//获取指定贴吧第一页tie
/*function allTid($name)
{
    $head = array();
    $head[] = 'Content-Type: application/x-www-form-urlencoded';
    $head[] = 'User-Agent: Mozilla/5.0 (SymbianOS/9.3; Series60/3.2 NokiaE72-1/021.021; Profile/MIDP-2.1 Configuration/CLDC-1.1 ) AppleWebKit/525 (KHTML, like Gecko) Version/3.0 BrowserNG/7.1.16352';
    $tl = new wcurl('http://c.tieba.baidu.com/c/f/frs/page', $head);
    $data = array(
        '_client_id'      => 'wappc_1470896832265_330',
        '_client_type'    => 2,
        '_client_version' => '5.1.3',
        '_phone_imei'     => '867600020777420',
        'from'            => 'baidu_appstore',
        'kw'              => $name,
        'model'           => 'HUAWEI MT7-TL10',
        'pn'              => 1,
        'rn'              => 33,
        'st_type'         => 'tb_forumlist',
        'timestamp'       => time() . '516'
    );
    $sign_str = '';
    foreach ($data as $k => $v) $sign_str .= $k . '=' . $v;
    $sign = strtoupper(md5($sign_str . 'tiebaclient!!!'));
    $data['sign'] = $sign;
    $tl->set(CURLOPT_RETURNTRANSFER, true);
    $rt = $tl->post($data);
    $result = json_decode($rt, true)['thread_list'];
    $tid = array();
    foreach ($result as $v) {
        $tid[] = $v['id'];
    }
    unset($tid[0],$tid[1],$tid[2]);
    return $tid;
}*/

//添加帖子URL时获取帖子信息
/*function get_tid($url)
{
    $tieurl = $url;
    preg_match('/\.com\/p\/(?<tid>\d+)/', $tieurl, $tids);
    $tid = $tids ['tid'];
    $ch = curl_init('http://tieba.baidu.com/p/' . $tid);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $contents = curl_exec($ch);
    curl_close($ch);

    preg_match('/fname="(.+?)"/', $contents, $fnames);
    preg_match('|<title>(.*?)</title>|s', $contents, $post_names);

    $fid = get_fid($fnames[1]);
    $tid = get_random_tid($url);

    $post_name = str_replace('_' . $fnames[1] . '吧_百度贴吧', '', $post_names[1]);

    $result = json_encode(array(
        "fid"   => $fid,
        "tid"   => $tid,
        "tname" => $fnames[1],
        "pname" => $post_name,
    ), JSON_UNESCAPED_UNICODE);
    return $result;
}*/

//获得fid
/*function get_fid($tname)
{
    $x = wcurl::xget("http://tieba.baidu.com/i/data/get_fid_by_fname?fname={$tname}");
    $r = json_decode();

    $info = file_get_contents('http://tieba.baidu.com/i/data/get_fid_by_fname?fname=' . $tname);
    preg_match('/fid":(.*?)},/', $info, $fids);
    return $fids[1];
}*/


//发表帖子回复
/*function client_rppost($bduss, $tieba, $content)
{
    global $m;
    $uid = $tieba['uid'];
    $s = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_userset` WHERE `uid` = {$uid}"));
    if ($s ['cat'] == 5) {
        $s ['cat'] = rand(1, 4);
    }
    $head = array();
    $head[] = 'Content-Type: application/x-www-form-urlencoded';
    $head[] = 'User-Agent: Mozilla/5.0 (SymbianOS/9.3; Series60/3.2 NokiaE72-1/021.021; Profile/MIDP-2.1 Configuration/CLDC-1.1 ) AppleWebKit/525 (KHTML, like Gecko) Version/3.0 BrowserNG/7.1.16352';
    $tp = new wcurl('http://c.tieba.baidu.com/c/c/post/add',$head);
    $formdata = array(
        'BDUSS'           => $bduss,
        '_client_id'      => 'wappc_147' . substr(findNum($bduss),0,10) . '_' . substr(findNum($bduss),5,3),
        '_client_type'    => $s['cat'],
        '_client_version' => '6.6.9',
        '_phone_imei'     => md5($bduss),
        'anonymous'       => 1,
        'content'         => $content,
        'fid'             => $tieba ['fid'],
        'from'            => 'appstore',
        'is_ad'           => 0,
        'kw'              => $tieba ['tname'],
        'model'           => 'HUAWEI MT7-TL10',
        'new_vcode'       => 1,
        'quote_id'        => !empty($tieba ['qid']) ? $tieba ['qid'] : '',
        'tbs'             => misc::getTbs(0,$bduss),
        'tid'             => $tieba ['tid'],
        'vcode_tag'       => 11,
    );
    $adddata = '';
    foreach ($formdata as $k => $v)
        $adddata .= $k . '=' . $v;
    $sign = strtoupper(md5($adddata . 'tiebaclient!!!'));
    $formdata ['sign'] = $sign;
    $tp->set(CURLOPT_RETURNTRANSFER, true);
    $rt = $tp->post($formdata);
    $re = json_decode($rt, true);
    switch ($s ['cat']) {
        case '1' :
            $client_res = "iphone";
            break;
        case '2' :
            $client_res = "android";
            break;
        case '3' :
            $client_res = "WindowsPhone";
            break;
        case '4' :
            $client_res = "Windows8";
            break;
    }
    if (!$re) return array(0, 'JSON 解析错误');
    if ($re ['error_code'] == 0) return array(2, "使用" . $client_res . '客户端发帖成功');
    else if ($re ['error_code'] == 5) return array(5, "需要输入验证码，请检查你是否已经关注该贴吧。");
    else if ($re ['error_code'] == 7) return array(7, "您的操作太频繁了！");
    else if ($re ['error_code'] == 8) return array(8, "您已经被封禁");
    else return array($re ['error_code'], "未知错误，错误代码：" . $re ['error_code']);
}*/

//获取指定帖子的楼层信息
/*function getQid($tid,$pn,$floor){
    $tl = new wcurl('http://c.tieba.baidu.com/c/f/pb/page');
    $data = array(
        '_client_type'    => 2,
        '_client_version' => '6.0.0',
        '_phone_imei'     => '867600020777420',
        'from'            => 'tiebawap_bottom',
        'kz'              => $tid,
        'pn'              => $pn,
        'rn'              => 30,
        'timestamp'       => time() . '516'
    );
    $sign_str = '';
    foreach ($data as $k => $v) $sign_str .= $k . '=' . $v;
    $sign = strtoupper(md5($sign_str . 'tiebaclient!!!'));
    $data['sign'] = $sign;
    $tl->set(CURLOPT_RETURNTRANSFER, true);
    $rt = $tl->post($data);
    $result = json_decode($rt,true)['post_list'];
    $pid = 0;
    foreach ($result as $v){
        if ($v['floor'] == $floor) $pid = $v['id'];
    }
    return $pid;
}*/


//获得贴吧帖子tid
/*function get_random_tid($url)
{
    $cu = explode('/p/', $url);
    if (strpos($cu[1], '?')) {
        $tid = textMiddle($url, '/p/', '?');
    } else {
        $tid = $cu[1];
    }
    return $tid;
}*/



/*
 * 随机抽取内容获取接口
 * */
/*function get_random_content()
{
    $ac = rand_array(array(0));
    switch ($ac){
        case 0:
            $content = tuLing();
            break;
        case 1:
            $content = moLi();
            break;
        default:
            $content = tuLing();
            break;
    }
    return $content;
}*/

/*
 * 图灵API接口函数
 * */
/*function tuLing(){
    $apikey = option::get('ver4_post_apikey');
    if (!empty($apikey)){
        $tl = new wcurl('http://www.tuling123.com/openapi/api');
        $info = array('讲个笑话');
        $data = array('key' => $apikey, 'info' => rand_array($info));
        $re = $tl->post($data);
        $r = json_decode($re,true);
    } else {
        $r['code'] = -1;
    }
    if ($r['code'] != 100000) {
        $content = getContent();
    } else {
        $content = $r['text'];
    }
    return $content;
}*/

/*
 * 茉莉机器人API接口函数
 * */
/*function moLi(){
    $ml = new wcurl('http://www.itpk.cn/jsonp/api.php?question=%E7%AC%91%E8%AF%9D');
    $re = $ml->get();
    $r = json_decode($re,true);
    return $r['content'];
}*/
