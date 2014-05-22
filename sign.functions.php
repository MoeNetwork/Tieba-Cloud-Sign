<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
function Gettbs($uid){
	$ch = new wcurl('http://tieba.baidu.com/dc/common/tbs', array('User-Agent: fuck phone','Referer: http://tieba.baidu.com/','X-Forwarded-For: 115.28.1.'.mt_rand(1,255)));
	$ch->addcookie("BDUSS=".GetCookie($uid));
	return $ch->exec();
}

function GetCookie($uid) {
	global $m;
	$temp = $m->fetch_array($m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX."users` WHERE `id` = {$uid} LIMIT 1"));
	return $temp['ck_bduss'];
}

function DoSign_Gettbs($uid) {
	return Gettbs($uid);
}

function DoSign_Mobile($uid,$kw,$id) {
	//貌似有点问题
	$ch = new wcurl('http://tieba.baidu.com/mo/q/sign?tbs='.DoSign_Gettbs($uid).'&kw='.urlencode($kw).'&is_like=1&fid=0',array('User-Agent: fuck phone','Referer: http://tieba.baidu.com/f?kw='.$kw , 'Host: tieba.baidu.com','X-Forwarded-For: 115.28.1.'.mt_rand(1,255), 'Origin: http://tieba.baidu.com', 'Connection: Keep-Alive'));
	$ch->addcookie("BDUSS=".GetCookie($uid));
	return $ch->exec();
}

function DoSign_Default($uid,$kw,$id) {
	global $m,$today;
	$ch = new wcurl('http://tieba.baidu.com/mo/m?kw='.urlencode($kw), array('User-Agent: fuck phone','Referer: http://tieba.baidu.com/','Content-Type: application/x-www-form-urlencoded'));
	$ch->addcookie("BDUSS=".GetCookie($uid));
	$s  = $ch->exec();
	$ch->close();

	preg_match('/\<td style=\"text-align:right;\"\>\<a href=\"(.*)\"\>签到\<\/a\>\<\/td\>\<\/tr\>/', $s, $s);
	if (isset($s[1])) {
		$ch = new wcurl('http://tieba.baidu.com'.$s[1], array('User-Agent: Mozilla/5.0 (Linux; U; Android 4.1.2; zh-cn; MB526 Build/JZO54K) AppleWebKit/530.17 (KHTML, like Gecko) FlyFlow/2.4 Version/4.0 Mobile Safari/530.17 baidubrowser/042_1.8.4.2_diordna_458_084/alorotoM_61_2.1.4_625BM/1200a/39668C8F77034455D4DED02169F3F7C7%7C132773740707453/1','P3P: CP=" OTI DSP COR IVA OUR IND COM "','Referer: '.'http://tieba.baidu.com/mo/m?kw='.urlencode($kw)));
		$ch->addcookie("BDUSS=".GetCookie($uid));
		$ch->exec();
		$ch->close();
	} else {
		$s = '{"c":"ok"}';
	}
	return $s;
}

function DoSign_Client($uid,$kw,$id){
	$ch = new wcurl('http://c.tieba.baidu.com/c/c/forum/sign', array('Content-Type: application/x-www-form-urlencoded'));
	$ch->addcookie("BDUSS=".GetCookie($uid));
	$temp = array(
		'BDUSS' => GetCookie($uid),
		'_client_id' => '03-00-DA-59-05-00-72-96-06-00-01-00-04-00-4C-43-01-00-34-F4-02-00-BC-25-09-00-4E-36',
		'_client_type' => '4',
		'_client_version' => '1.2.1.17',
		'_phone_imei' => '540b43b59d21b7a4824e1fd31b08e9a6',
		'fid' => '0',
		'kw' => $kw,
		'net_type' => '3',
		'tbs' => DoSign_Gettbs($uid)
	);
	$x = '';
	foreach($temp as $k=>$v) $x .= $k.'='.$v;
	$temp['sign'] = strtoupper(md5($x.'tiebaclient!!!'));
	$ch->post($temp);
	return $ch->exec();
}

function DoSign_All($uid,$kw,$id,$table,$sign_mode) {
	global $m,$today;

	if(!empty($sign_mode) && in_array('1',$sign_mode)) {
		$v = json_decode(DoSign_Client($uid,$kw,$id),true);
	}
	if(!empty($sign_mode) && in_array('2',$sign_mode)) {
		$s = json_decode(DoSign_Default($uid,$kw,$id),true);
	}
	if(!empty($sign_mode) && in_array('3',$sign_mode)) {
		$r = json_decode(DoSign_Mobile($uid,$kw,$id),true);
	}

	if (!isset($s['error_code']) && !isset($r['no']) && !isset($v['error_code'])) {
		$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX.$table."` SET  `lastdo` =  '".$today."',`status` =  '0',`last_error` = NULL WHERE `".DB_PREFIX.$table."`.`id` = '{$id}'",true);
	}
	elseif ($v['error_code'] == '160002' || $v['error_code'] = '340003') {
		$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX.$table."` SET  `lastdo` =  '".$today."',`status` =  '0',`last_error` = NULL WHERE `".DB_PREFIX.$table."`.`id` = '{$id}'",true);
	}
	else {
		if (isset($s['error_code'])) {
			$error = $s['error_code'];
			$errorid = 2;
		}
		elseif (isset($r['no'])) {
			$error = $r['no'];
			$errorid = 3;
		}
		elseif (isset($v['error_code'])) {
			$error = $v['error_code'];
			$errorid = 4;
		}
		else {
			$errorid = '1';
			$error = '1';
		}
		$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX.$table."` SET  `lastdo` =  '".$today."',`status` =  '".$errorid."',`last_error` = '".$error."' WHERE `".DB_PREFIX.$table."`.`id` = '{$id}'",true);
	}
}

/**
 * 执行一次贴吧签到
 *
 * @param 贴吧数据表
 *
 *
 */
function DoSign($table,$sign_mode) {
	global $m,$today;
	$return = '';
	$count = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `lastdo` != '".$today."'"));
	$count = $count[0];

	//处理所有未签到的贴吧
	if (option::get('cron_limit') == 0) {
		$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `lastdo` != '".$today."'");
		$return .= '已直接处理所有未签到的贴吧';
	} else {
		$limit=option::get('cron_limit');
		$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `lastdo` != '".$today."' LIMIT 0 , ".$limit);
		$return .= '分批签到模式下无状态报告';
	}

	while ($x=$m->fetch_array($q)) {
		DoSign_All($x['uid'],$x['tieba'],$x['id'],$table,$sign_mode);
	}
	//重新尝试签到出错的贴吧
	if (option::get('cron_limit') == 0) {
		$sign_again = unserialize(option::get('cron_sign_again'));
		if (option::get('retry_max') == '0') {
			$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `status` != '0'");
		}
		elseif ($sign_again['lastdo'] == $today && $sign_again['num'] <= option::get('retry_max') && option::get('retry_max') != '-1') {
			$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `status` != '0'");
		}
		$return .= '已直接处理所有未签到的贴吧';
	} else {
		$limit=option::get('cron_limit');
		$sign_again = unserialize(option::get('cron_sign_again'));
		if (option::get('retry_max') == '0') {
			$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `status` != '0'");
		}
		elseif ($sign_again['lastdo'] == $today && $sign_again['num'] <= option::get('retry_max') && option::get('retry_max') != '-1') {
			$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `status` != '0'");
		}
		$return .= '分批签到模式下无状态报告';
	}

	while ($x=$m->fetch_array($q)) {
		DoSign_All($x['uid'],$x['tieba'],$x['id'],$table,$sign_mode);
	}
	return $return;
}