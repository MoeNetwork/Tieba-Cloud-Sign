<?php
define('SYSTEM_DO_NOT_LOGIN', true);
require 'init.php';
set_time_limit(0);
global $m;
$return = '';
doAction('cron_1');

$today=date("Y-m-d");

if (option::get('cron_last_do_time') != $today) {
	option::set('cron_last_do_time',$today);
}

function GetCookie($uid) {
	global $m;
	$temp = $m->fetch_array($m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX."users` WHERE `id` = {$uid} LIMIT 1"));
	return $temp['ck_bduss'];
}

function Gettbs($uid){
	$ch = curl_init('http://tieba.baidu.com/dc/common/tbs');
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Referer: http://tieba.baidu.com/'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIE, GetCookie($uid));
	$x = json_decode(curl_exec($ch),true);
	return $x['tbs'];
}

function DoSign($uid,$kw) {
	$post = array(
		'BDUSS' => GetCookie($uid),
		'_client_id' => '03-00-DA-59-05-00-72-96-06-00-01-00-04-00-4C-43-01-00-34-F4-02-00-BC-25-09-00-4E-36',
		'_client_type' => '4',
		'_client_version' => '5.6.3',
		'_phone_imei' => '540b43b59d21b7a4824e1fd31b08e9a6',
		'fid' => '0',
		'kw' => $kw,
		'net_type' => '3',
		'tbs' => Gettbs($uid),
	);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, 'http://tieba.baidu.com/sign/add'); 
	curl_setopt($ch, CURLOPT_COOKIESESSION, true); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_COOKIE, "BDUSS=".GetCookie($uid));
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
	curl_setopt($ch, CURLOPT_POST, 1); 
 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post)); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-FORWARDED-FOR:183.185.2.".mt_rand(1,255)));
	curl_setopt($ch, CURLOPT_HEADER, false);  
	curl_exec($ch);
}

$count = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `lastdo` != '".$today."'"));
$count = $count[0];
//处理所有未签到的贴吧
if (option::get('cron_limit') == 0) {
	$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `no` = 0 AND `lastdo` != '".$today."'");
	$return .= '已直接处理所有未签到的贴吧';
} else {
	$limit=(option::get('cron_last_do') + option::get('cron_limit'));
	$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `no` = 0 AND `lastdo` != '".$today."' LIMIT ".option::get('cron_last_do').' , '.$limit);
	$return .= '分批签到模式下无状态报告';
}

while ($x=$m->fetch_array($q)) {
	DoSign($x['uid'],$x['tieba']);
	$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX."tieba` SET  `lastdo` =  '".$today."' WHERE `".DB_PREFIX."tieba`.`id` = '{$x['id']}'",true);
}

if ($count != 0 && option::get('cron_limit') != 0) {
	option::set('cron_last_do',option::get('cron_last_do'));
}
$count = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `lastdo` != '".$today."'"));
$count = $count[0];
$return .= '<br/>当前等待签到的贴吧数目为：'.$count;
doAction('cron_2');
msg('计划任务：'.$return);
?>