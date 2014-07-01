<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 



function DoSign_Mobile($uid,$kw,$id,$pid,$fid) {
	//没问题了
	$ck = misc::getCookie($pid);
	$ch = new wcurl('http://tieba.baidu.com/mo/q/sign?tbs='.misc::getTbs($uid,$ck).'&kw='.urlencode($kw).'&is_like=1&fid='.$fid ,array('User-Agent: fuck phone','Referer: http://tieba.baidu.com/f?kw='.$kw , 'Host: tieba.baidu.com','X-Forwarded-For: 115.28.1.'.mt_rand(1,255), 'Origin: http://tieba.baidu.com', 'Connection: Keep-Alive'));
	$ch->addcookie("BDUSS=".$ck);
	return $ch->exec();
}

function DoSign_Default($uid,$kw,$id,$pid,$fid) {
	global $m,$today;
	$ck = misc::getCookie($pid);
	$ch = new wcurl('http://tieba.baidu.com/mo/m?kw='.urlencode($kw).'&fid='.$fid, array('User-Agent: fuck phone','Referer: http://wapp.baidu.com/','Content-Type: application/x-www-form-urlencoded'));
	$ch->addcookie("BDUSS=".$ck);
	$s  = $ch->exec();
	$ch->close();
	preg_match('/\<td style=\"text-align:right;\"\>\<a href=\"(.*)\"\>签到\<\/a\>\<\/td\>\<\/tr\>/', $s, $s);
	if (isset($s[1])) {
		$ch = new wcurl('http://tieba.baidu.com'.$s[1], 
			array(
				'Accept: text/html, application/xhtml+xml, */*',
				'Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3',
				'User-Agent: Fucking Phone'
			));
		$ch->addcookie("BDUSS=".$ck);
		$ch->exec();
		$ch->close();
	} else {
		$s = '{"c":"err"}';
	}
	return $s;
}

function DoSign_Client($uid,$kw,$id,$pid,$fid){
	$ck = misc::getCookie($pid);
	$ch = new wcurl('http://c.tieba.baidu.com/c/c/forum/sign', array('Content-Type: application/x-www-form-urlencoded','User-Agent: Fucking iPhone/1.0 BadApple/99.1'));
	$ch->addcookie("BDUSS=".$ck);
	$temp = array(
		'BDUSS' => misc::getCookie($pid),
		'_client_id' => '03-00-DA-59-05-00-72-96-06-00-01-00-04-00-4C-43-01-00-34-F4-02-00-BC-25-09-00-4E-36',
		'_client_type' => '4',
		'_client_version' => '1.2.1.17',
		'_phone_imei' => '540b43b59d21b7a4824e1fd31b08e9a6',
		'fid' => $fid,
		'kw' => $kw,
		'net_type' => '3',
		'tbs' => misc::getTbs($uid,$ck)
	);
	$x = '';
	foreach($temp as $k=>$v) {
		$x .= $k.'='.$v;
	}
	$temp['sign'] = strtoupper(md5($x.'tiebaclient!!!'));
	return $ch->post($temp);
}

function DoSign_All($uid,$kw,$id,$table,$sign_mode,$pid,$fid) {
	global $m,$today;

	if (empty($fid)) {
		$fid = misc::getFid($kw);
		$m->query("UPDATE  `".DB_PREFIX.$table."` SET  `fid` =  '{$fid}' WHERE  `".DB_PREFIX.$table."`.`id` = '{$id}';",true);
	}

	if(!empty($sign_mode) && in_array('1',$sign_mode)) {
		$v = json_decode(DoSign_Client($uid,$kw,$id,$pid,$fid),true);
	}
	if(!empty($sign_mode) && in_array('2',$sign_mode)) {
		$s = json_decode(DoSign_Default($uid,$kw,$id,$pid,$fid),true);
	}
	if(!empty($sign_mode) && in_array('3',$sign_mode)) {
		$r = json_decode(DoSign_Mobile($uid,$kw,$id,$pid,$fid),true);
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
 */
function DoSign($table,$sign_mode) {
	global $m,$today,$i;

	//处理所有未签到的贴吧
	if (option::get('cron_limit') == 0) {
		$q = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `lastdo` != '".$today."' ORDER BY RAND() ");
	} else {
		$limit = option::get('cron_limit');
		$q = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `lastdo` != '".$today."' ORDER BY RAND() LIMIT 0 , ".$limit);
	}

	while ($x=$m->fetch_array($q)) {
		DoSign_All($x['uid'],$x['tieba'],$x['id'],$table,$sign_mode,$x['pid'],$x['fid']);
	}

	//重新尝试签到出错的贴吧
	if (option::get('cron_limit') == 0) {
		$sign_again = unserialize(option::get('cron_sign_again'));
		if (option::get('retry_max') == '0') {
			$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `status` != '0' ORDER BY RAND()");
		}
		elseif ($sign_again['lastdo'] == $today && $sign_again['num'] <= option::get('retry_max') && option::get('retry_max') != '-1') {
			$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `status` != '0' ORDER BY RAND()");
		}
	} else {
		$limit=option::get('cron_limit');
		$sign_again = unserialize(option::get('cron_sign_again'));
		if (option::get('retry_max') == '0') {
			$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `status` != '0' ORDER BY RAND()");
		}
		elseif ($sign_again['lastdo'] == $today && $sign_again['num'] <= option::get('retry_max') && option::get('retry_max') != '-1') {
			$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `status` != '0' ORDER BY RAND()");
		}
	}

	while ($x=$m->fetch_array($q)) {
		DoSign_All($x['uid'],$x['tieba'],$x['id'],$table,$sign_mode,$x['pid'],$x['fid']);
	}
}