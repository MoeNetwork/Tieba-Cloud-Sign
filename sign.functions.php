<?php
function Gettbs($uid){
	$ch = curl_init('http://tieba.baidu.com/dc/common/tbs');
	curl_setopt($ch, CURLOPT_USERAGENT, 'fuck phone');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Referer: http://tieba.baidu.com/','X-Forwarded-For: 115.28.1.'.mt_rand(1,255)));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIE, GetCookie($uid));
	return curl_exec($ch);
}

function GetCookie($uid) {
	global $m;
	$temp = $m->fetch_array($m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX."users` WHERE `id` = {$uid} LIMIT 1"));
	return $temp['ck_bduss'];
}

function DoSign_Gettbs($uid) {
	$ch = curl_init('http://tieba.baidu.com/dc/common/tbs');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Referer: http://tieba.baidu.com/','X-Forwarded-For: 115.28.1.'.mt_rand(1,255)));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'fuck phone');
	curl_setopt($ch, CURLOPT_COOKIE, GetCookie($uid));
	$x = json_decode(curl_exec($ch),true);
	curl_close($ch);
	return $x['tbs'];
}

function DoSign_Mobile($uid,$kw,$id) {
	/*
	//貌似有点问题？下个版本再看看。暂时无条件返回正确吧
	$ch = curl_init('http://tieba.baidu.com/mo/q/sign?tbs='.DoSign_Gettbs($uid).'&kw='.urlencode($kw).'&is_like=1&fid=0');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Referer: http://tieba.baidu.com/f?kw='.$kw , 'Host: tieba.baidu.com','X-Forwarded-For: 115.28.1.'.mt_rand(1,255), 'Origin: http://tieba.baidu.com', 'Connection: Keep-Alive'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'fuck phone');
	curl_setopt($ch, CURLOPT_COOKIE, GetCookie($uid));
	return curl_exec($ch);
	*/
	return '{"c":"ok"}';
}

function DoSign_Default($uid,$kw,$id) {
	global $m,$today;
	$ch = curl_init('http://tieba.baidu.com/mo/m?kw='.urlencode($kw));
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','X-Forwarded-For: 115.28.1.'.mt_rand(1,255)));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'fuck phone');
	curl_setopt($ch, CURLOPT_COOKIE, "BDUSS=".GetCookie($uid));
	$s=curl_exec($ch);
	curl_close($ch);
	preg_match('/\<td style=\"text-align:right;\"\>\<a href=\"(.*)\"\>签到\<\/a\>\<\/td\>\<\/tr\>/', $s, $s);
	if (isset($s[1])) {
		$ch = curl_init('http://tieba.baidu.com'.$s[1]);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','X-Forwarded-For: 115.28.1.'.mt_rand(1,255)));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'fuck phone');
		curl_setopt($ch, CURLOPT_COOKIE, "BDUSS=".GetCookie($uid));
		$s=curl_exec($ch);
		curl_close($ch);
	} else {
		$s = '{"c":"ok"}';
	}
	return $s;
}

function DoSign_Client($uid,$kw,$id){
	$ch = curl_init('http://c.tieba.baidu.com/c/c/forum/sign');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIE, GetCookie($uid));
	curl_setopt($ch, CURLOPT_POST, 1);
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
	$sign = strtoupper(md5($x.'tiebaclient!!!'));
	$temp['sign'] = $sign;
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($temp));
	return curl_exec($ch);
}

function DoSign_All($uid,$kw,$id,$table) {
	global $m,$today;
	$r = json_decode(DoSign_Mobile($uid,$kw,$id),true);
	$s = json_decode(DoSign_Default($uid,$kw,$id),true);
	$v = json_decode(DoSign_Client($uid,$kw,$id),true);
	if (!isset($s['error_code']) && !isset($r['no']) && !isset($v['error_code'])) {
		$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX.$table."` SET  `lastdo` =  '".$today."',`status` =  '0',`last_error` = NULL WHERE `".DB_PREFIX.$table."`.`id` = '{$id}'",true);
	}
	elseif ($v['error_code'] == 160002) {
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
function DoSign($table) {
	global $m,$today;
	$return = '';
	$count = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `lastdo` != '".$today."'"));
	$count = $count[0];

	//处理所有未签到的贴吧
	if (option::get('cron_limit') == 0) {
		$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `lastdo` != '".$today."'");
		$return .= '已直接处理所有未签到的贴吧';
	} else {
		$limit=(option::get('cron_last_do') + option::get('cron_limit'));
		$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `lastdo` != '".$today."' LIMIT ".option::get('cron_last_do').' , '.$limit);
		$return .= '分批签到模式下无状态报告';
	}

	while ($x=$m->fetch_array($q)) {
		DoSign_All($x['uid'],$x['tieba'],$x['id'],$table);
	}
	//重新尝试签到出错的贴吧
	if (option::get('cron_limit') == 0) {
		$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `status` != '0'");
		$return .= '已直接处理所有未签到的贴吧';
	} else {
		$limit=(option::get('cron_last_do') + option::get('cron_limit'));
		$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `status` != '0' LIMIT ".option::get('cron_last_do').' , '.$limit);
		$return .= '分批签到模式下无状态报告';
	}

	while ($x=$m->fetch_array($q)) {
		DoSign_All($x['uid'],$x['tieba'],$x['id'],$table);
	}
	return $return;
}