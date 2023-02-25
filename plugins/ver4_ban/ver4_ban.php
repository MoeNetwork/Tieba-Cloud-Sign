<?php

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}

function ver4_ban_nav()
{
    echo '<li ';
    if (isset($_GET['plugin']) && $_GET['plugin'] == 'ver4_ban') {
        echo 'class="active"';
    }
    echo '><a href="index.php?plugin=ver4_ban"><span class="glyphicon glyphicon-ban-circle"></span> 贴吧云封禁[吧务]</a></li>';
}

addAction('navi_1', 'ver4_ban_nav');
addAction('navi_7', 'ver4_ban_nav');


/*
 * 执行封禁操作 网页
 * */
function ver4_ban($pid, $portrait, $name, $name_show, $tieba, $reason, int $day = 1)
{
    $bduss = misc::getCookie($pid);
    $r = empty($reason) ? '您因为违反吧规，已被吧务封禁，如有疑问请联系吧务' : $reason;
    $tl = new wcurl('https://tieba.baidu.com/pmc/blockid', [
        'Connection: keep-alive',
        'Accept: application/json, text/javascript, */*; q=0.01',
        'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
        'Origin: https://tieba.baidu.com',
        'Referer: https://tieba.baidu.com/',
        'X-Requested-With: XMLHttpRequest',
    ]);
    $data = array(
        'day'    => $day, // 1 3 10 封禁时长
        'fid'    => misc::getFid($tieba),
        'tbs'    => misc::getTbs(0, $bduss),
        'ie'     => 'utf8',
        'nick_name[]' => $name_show ?: '',
        'pid'    => mt_rand(100000000000, 150000000000),
        'reason' => $r
    );
    $tl->addCookie('BDUSS=' . $bduss);
    $portrait !== null ? $data['portrait[]'] = $portrait : $data['user_name[]'] = $name;
    $tl->set(CURLOPT_RETURNTRANSFER, true);
    $rt = $tl->post($data);
    return $rt;
}

/*
 * 执行封禁操作 客户端
 * */
function ver4_ban_client($pid, $portrait, $name, $tieba, $reason, int $day = 1)
{
    $bduss = misc::getCookie($pid);
    $r = empty($reason) ? '您因为违反吧规，已被吧务封禁，如有疑问请联系吧务！' : $reason;
    $tl = new wcurl('http://c.tieba.baidu.com/c/c/bawu/commitprison');
    $data = array(
        'BDUSS'  => $bduss,
        'day'    => $day,//1 7 10//封禁时长
        'fid'    => misc::getFid($tieba),
        'ntn'    => 'banid',
        'portrait' => $portrait,
        'reason' => $r,
        'tbs'    => misc::getTbs(0, $bduss),
        'un'     => $name,
        'word'   => $tieba,
        'z'      => rand(1000000000, 9999999999)//随便打的, 不要应该也行
    );
    $sign_str = '';
    foreach ($data as $k => $v) {
        $sign_str .= $k . '=' . $v;
    }
    $sign = strtoupper(md5($sign_str . 'tiebaclient!!!'));
    $data['sign'] = $sign;
    $tl->set(CURLOPT_RETURNTRANSFER, true);
    $rt = $tl->post($data);
    return $rt;
}
/**
 * 获取任职信息
 *
 * @param $pid
 * @param $tieba_name
 * @return bool|string
 */
function ver4_get_manager_web_backstage($pid, string $tieba_name)
{
    $cookies = misc::getCookie($pid, true);
    try {
        $tl = new Wcurl('http://tieba.baidu.com/bawu2/platform/index?ie=utf-8&word=' . $tieba_name);
        $tl->addCookie('BDUSS=' . $cookies["bduss"] . ";STOKEN=" . $cookies["stoken"]);
        $tl->set(CURLOPT_RETURNTRANSFER, true);
        $tl->set(CURLOPT_CONNECTTIMEOUT, 3);
        $rt = $tl->get();
        $tl->close();

        //遍码转换
        $rt = mb_convert_encoding($rt, "utf-8", "gbk");

        return $rt;
    } catch (Exception $exception) {
        return '';
    }
}

//某个pid下帐号是否为吧务
function ver4_is_manager($pid, string $tieba_name): array
{
    return [
        "isManager" => (bool)preg_match('/<p class="forum_list_position">([^<]+)<\/p>/', ver4_get_manager_web_backstage($pid, $tieba_name), $managerType),
        "managerType" => empty($managerType[1]) ? "" : $managerType[1],
    ];
}
function ver4_ban_get_userinfo_by_words($word): array
{
    $getInfo = json_decode((new wcurl("https://tieba.baidu.com/mo/q/search/user?word={$word}", ['User-Agent: tieba/12.5.1']))->get(), true);
    $userInfo = [];
    if (isset($getInfo["data"]["exactMatch"]["id"])) {
        $userInfo[] = [
            "name" => $getInfo["data"]["exactMatch"]["name"],
            "show_name" => $getInfo["data"]["exactMatch"]["user_nickname"],
            "portrait" => $getInfo["data"]["exactMatch"]["encry_uid"],
            "tieba_uid" => $getInfo["data"]["exactMatch"]["tieba_uid"],
            "baidu_uid" => (string)$getInfo["data"]["exactMatch"]["id"],
            "exact_match" => true,
        ];
    }
    foreach ($getInfo["data"]["fuzzyMatch"] as $fuzzyMatchItem) {
        $userInfo[] = [
            "name" => $fuzzyMatchItem["name"],
            "show_name" => $fuzzyMatchItem["user_nickname"],
            "portrait" => $fuzzyMatchItem["encry_uid"],
            "tieba_uid" => $fuzzyMatchItem["tieba_uid"],
            "baidu_uid" => (string)$fuzzyMatchItem["id"],
            "exact_match" => false,
        ];
    }
    return $userInfo;
}

//生成封禁列表
function ver4_ban_global_ban_list_generate(array $i, $m): array
{
    $globalBanList = [];
    foreach ($i["user"]["baidu"] as $userId => $userBaiduName) {
        $globalBanList[$userId] = [
            "name" => $userBaiduName,//$i["user"]["baidu_name_show"][$userId]??$i["user"]["baidu_portrait"][$userId],//注释部分用于banka版, 原版使用此插件请不要取消注释
            "list" => [],
        ];
        $api_ban_list = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` WHERE `pid` = {$userId}");
        while ($api_ban_user_info = $m->fetch_array($api_ban_list)) {
            $globalBanList[$userId]["list"][] = [
                "id" => $api_ban_user_info['id'],
                "name" => $api_ban_user_info['name'],
                "name_show" => $api_ban_user_info['name_show'],//TODO 修改为贴吧uid
                "tieba_uid" => "0",
                "portrait" => $api_ban_user_info['portrait'],
                "tieba" => $api_ban_user_info["tieba"],
                "stime" => $api_ban_user_info["stime"],
                "etime" => $api_ban_user_info["etime"],
                "date" => $api_ban_user_info["date"],
                "log" => $api_ban_user_info["log"],
            ];
        }
    }
    return $globalBanList;
}
