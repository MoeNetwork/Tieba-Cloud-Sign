<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

/**
 * 其他功能类
 */

class misc {
	/**
	 * 快捷发送一封邮件
	 * @param string $to 收件人
	 * @param string $sub 邮件主题
	 * @param string $msg 邮件内容(HTML)
	 * @param array $att 附件，每个键为文件名称，值为附件内容（可以为二进制文件），例如array('a.txt' => 'abcd' , 'b.png' => file_get_contents('x.png'))
	 * @return 成功:true 失败：错误消息
	 */
	public static function mail($to, $sub = '无主题', $msg = '无内容', $att = array()) {
        if (defined("SAE_MYSQL_DB") && class_exists('SaeMail')){
            $mail = new SaeMail();
            $options = Array(
				'from'          => option::get('mail_name'),
				'to'            => $to,
				'smtp_host'     => option::get('mail_host'),
				'smtp_port'     => option::get('mail_port'), //端口号（默认为25，一般不需修改）
				'smtp_username' => option::get('mail_smtpname'), //smtp账号
				'smtp_password' => option::get('mail_smtppw'), //smtp密码
				'subject'       => $sub, //邮件标题
				'content'       => $msg, //邮件内容
				'content_type'  => 'HTML' //HTML格式发送
            );
            $mail->setOpt($options);
            $ret = $mail->send();
            if ($ret === false) {
                return 'Mail Send Error: #'.$mail->errno().' - '.$mail->errmsg();
            } else {
            	return true;
            }
        } else {
        	$From = option::get('mail_name');
			if (option::get('mail_mode') == 'SMTP') {
				$Host = option::get('mail_host');
				$Port = intval(option::get('mail_port'));
				$SMTPAuth = (boolean) option::get('mail_auth');
				$Username = option::get('mail_smtpname');
				$Password = option::get('mail_smtppw');
                $Nickname = option::get('mail_yourname');
				if (option::get('mail_ssl') == '1') {
					$SSL = true;
				} else {
					$SSL = false;
				}
				$mail = new SMTP($Host , $Port , $SMTPAuth , $Username , $Password , $SSL);
				$mail->att = $att;
				if($mail->send($to , $From , $sub , $msg, $Nickname)) {
					return true;
				} else {
					return $mail->log;
				}
			} else {
				$header .= "MIME-Version:1.0\r\n";
		        $header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		        $header .= "To: " . $to . "\r\n";
		        $header .= "From: " . $From . "\r\n";
		        $header .= "Subject: " . $sub . "\r\n";
		        $header .= 'Reply-To: ' . $From . "\r\n";
		        $header .= "Date: " . date("r") . "\r\n";
				return mail(
					$to,
					$sub,
					$msg,
					$header
				);
			}
	    }
	}

	/** 
	 * 通过UID判断某个用户是不是VIP
	 * @param $uid UID
	 * @return bool VIP=true
	 */
	public static function isvip($uid) {
		global $m;
		$x = $m->once_fetch_array("SELECT * FROM `".DB_PREFIX."users` WHERE `id` = '{$uid}';");
		if ($x['role'] == 'vip' || $x['role'] == 'admin') {
			return true;
		} else {
			return false;
		}
	}

	/** 
	 * 通过UID获得指定用户的贴吧数据表
	 * @param $uid UID
	 */
	public static function getTable($uid) {
		global $m;
		$x = $m->once_fetch_array("SELECT * FROM `".DB_PREFIX."users` WHERE `id` = '{$uid}';");
		return $x['t'];
	}

	/**
	 * 寻找已缓存的贴吧 FID
	 * @param $kw 贴吧名
	 * @return string|boolean FID，如果没有缓存则返回false
	 */
	/*
	public static function findFid($kw) {
		global $i;
		global $m;
		foreach ($i['table'] as $v) {
			$r = $m->once_fetch_array("SELECT * FROM `".DB_PREFIX.$v."` WHERE `tieba` = '{$kw}' AND `fid` IS NOT NULL LIMIT 1");
			if (!empty($r['fid'])) {
				return $r['fid'];
				break;
			}
		}
		return false;
	}
	*/

	/**
	 * 批量设置贴吧 FID
	 * @param $kw 贴吧名
	 * @param $fid FID
	 */

	public static function mSetFid($kw,$fid) {
		global $m,$i;
		if (empty($fid)) {
			return false;
		}
		foreach ($i['table'] as $v) {
			$r = $m->query("UPDATE  `".DB_PREFIX.$v."` SET  `fid` =  '{$fid}' WHERE  `".DB_PREFIX.$v."`.`tieba` = '{$kw}';");
		}
	}

	/**
	 * 得到贴吧 FID
	 * @param $kw 贴吧名
	 * @return string FID
	 */

	public static function getFid($kw) {
		global $m;
		/*
		$f  = misc::findFid($kw);
		if ($f) {
			return $f; 
		} else {
		*/
			$ch = new wcurl('http://tieba.baidu.com/mo/m?kw='.urlencode($kw), array('User-Agent: fuck phone','Referer: http://wapp.baidu.com/','Content-Type: application/x-www-form-urlencoded'));
			$s  = $ch->exec();
			//self::mSetFid($kw,$fid[1]);
			$x  = easy_match('<input type="hidden" name="fid" value="*"/>',$s);
			if (isset($x[1])) {
				return $x[1];
			} else {
				return false;
			}
		//}
	}

	/**
	 * 得到TBS
	 */

	public static function getTbs($uid,$bduss){
		$ch = new wcurl('http://tieba.baidu.com/dc/common/tbs', array('User-Agent: fuck phone','Referer: http://tieba.baidu.com/','X-Forwarded-For: 115.28.1.'.mt_rand(1,255)));
		$ch->addcookie("BDUSS=". $bduss);
		$x = json_decode($ch->exec(),true);
		return $x['tbs'];
	}

	/**
	 * 得到BDUSS 
	 * @param $pid 用户PID
	 */
	public static function getCookie($pid) {
		global $m;
		if (empty($pid)) {
			return false;
		}
		$temp = $m->fetch_array($m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX."baiduid` WHERE `id` = {$pid} LIMIT 1"));
		return $temp['bduss'];
	}

	/**
	 * 50贴吧客户端一键签到
	 */
	public static function DoSign_Onekey($uid,$kw,$id,$pid,$fid,$ck) {
		$ch = new wcurl('http://c.tieba.baidu.com/c/c/forum/msign', array(
			'User-Agent: bdtb for Android 6.5.8'
		));	
		$ch->addcookie(array('BDUSS' => $ck));
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

	/**
	 * 手机网页签到
	 */
	public static function DoSign_Mobile($uid,$kw,$id,$pid,$fid,$ck) {
		//没问题了
		$ch = new wcurl('http://tieba.baidu.com/mo/q/sign?tbs='.misc::getTbs($uid,$ck).'&kw='.urlencode($kw).'&is_like=1&fid='.$fid ,array('User-Agent: fuck phone','Referer: http://tieba.baidu.com/f?kw='.$kw , 'Host: tieba.baidu.com','X-Forwarded-For: 115.28.1.'.mt_rand(1,255), 'Origin: http://tieba.baidu.com', 'Connection: Keep-Alive'));
		$ch->addcookie("BDUSS=".$ck);
		return $ch->exec();
	}

	/**
	 * 网页签到
	 */
	public static function DoSign_Default($uid,$kw,$id,$pid,$fid,$ck) {
		global $m,$today;
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
			//临时判断解决方案
			$ch = new wcurl('http://tieba.baidu.com/mo/m?kw='.urlencode($kw).'&fid='.$fid, array('User-Agent: fuck phone','Referer: http://wapp.baidu.com/','Content-Type: application/x-www-form-urlencoded'));
			$ch->addcookie("BDUSS=".$ck);
			$s = $ch->exec();
			$ch->close();
			//如果找不到这段html则表示没有签到则stripos()返回false，同时is_bool()返回true，最终返回false
			return !is_bool(stripos($s,'<td style="text-align:right;"><span >已签到</span></td>'));
		} else {
			return true;
		}
	}

	/**
	 * 客户端签到
	 */
	public static function DoSign_Client($uid,$kw,$id,$pid,$fid,$ck){
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

	/**
	 * 对一个贴吧执行完整的签到任务
	 */
	public static function DoSign_All($uid,$kw,$id,$table,$sign_mode,$pid,$fid) {
		global $m;
		$again_error_id     = 160002; //重复签到错误代码
		$again_error_id_2   = 1101; //特殊的重复签到错误代码！！！签到过快=已签到
		$again_error_id_3   = 1102; //特殊的重复签到错误代码！！！签到过快=已签到
		$status_succ    = false;

		$ck = misc::getCookie($pid);
		$kw = addslashes($kw);
		$today = date('d');

		if (empty($fid)) {
			$fid = misc::getFid($kw);
			$m->query("UPDATE  `".DB_PREFIX.$table."` SET  `fid` =  '{$fid}' WHERE  `".DB_PREFIX.$table."`.`id` = '{$id}';",true);
		}

		//dump(json_decode(self::DoSign_Client($uid,$kw,$id,$pid,$fid,$ck),true),true);die;

		if(!empty($sign_mode) && in_array('1',$sign_mode) && $status_succ === false) {
			$r = self::DoSign_Client($uid,$kw,$id,$pid,$fid,$ck);
			$v = json_decode($r,true);
			if($v != $r && $v != NULL){//decode失败时会直接返回原文或NULL
				if (empty($v['error_code']) || $v['error_code'] == $again_error_id) {
					$status_succ = true;
				} else {
					$error_code = $v['error_code'];
					$error_msg  = $v['error_msg'];
				}
			}
		}

		if(!empty($sign_mode) && in_array('3',$sign_mode) && $status_succ === false) {
			$r = self::DoSign_Mobile($uid,$kw,$id,$pid,$fid,$ck);
			$v = json_decode($r,true);
			if($v != $r && $v != NULL){//decode失败时会直接返回原文或NULL
				if (empty($v['no']) || $v['no'] == $again_error_id_2 || $v['no'] == $again_error_id_3) {
					$status_succ = true;
				} else {
					$error_code  = $v['no'];
					$error_msg   = $v['error'];
				}
			}
		}

		if(!empty($sign_mode) && in_array('2',$sign_mode) && $status_succ === false) {
			if(self::DoSign_Default($uid,$kw,$id,$pid,$fid,$ck) === true) {
				$status_succ = true;
			}
		}

		if ($status_succ === true) {
			$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX.$table."` SET  `latest` =  '".$today."',`status` =  '0',`last_error` = NULL WHERE `".DB_PREFIX.$table."`.`id` = '{$id}'",true);
		} else {
			$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX.$table."` SET  `latest` =  '".$today."',`status` =  '".$error_code."',`last_error` = '".$error_msg."' WHERE `".DB_PREFIX.$table."`.`id` = '{$id}'",true);
		}

		usleep(option::get('sign_sleep') * 1000);
	}

	/**
	 * 执行一个表的签到任务
	 * @param $table 表
	 */
	public static function DoSign($table) {
		global $m;
		$sign_mode = unserialize(option::get('sign_mode'));
		$today = date('d');

		if (date('H') <= option::get('sign_hour')) {
			return option::get('sign_hour').'点时忽略签到';	
		}

		$limit = option::get('cron_limit');

		//处理所有未签到的贴吧
		if ($limit == 0) {
			$q  = array();
			$qs = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `latest` != '".$today."'");
			while ($qss = $m->fetch_array($qs)) {
				$q[] = array(
					'id'     => $qss['id'],
					'uid'    => $qss['uid'],
					'pid'    => $qss['pid'],
					'fid'    => $qss['fid'],
					'tieba'  => $qss['tieba'],
					'no'     => $qss['no'],
					'status' => $qss['status'],
					'latest' => $qss['latest'],
					'last_error'    => $qss['last_error']
				);
			}
			shuffle($q);
		} else {
			$q = rand_row( DB_PREFIX.$table , 'id' , $limit , "`no` = 0 AND `latest` != '{$today}'" , true );
		}
		
		foreach ($q as $x) {
			self::DoSign_All($x['uid'] , $x['tieba'] , $x['id'] , $table , $sign_mode , $x['pid'] , $x['fid']);
		}
	}

	/**
	 * 执行一个表的签到重試任务
	 * @param $table 表
	 */
	public static function DoSign_retry($table) {
		global $m,$i;
		$today = date('d');
		if (date('H') <= option::get('sign_hour')) {
			return option::get('sign_hour').'点时忽略签到';	
		}
		$limit = option::get('cron_limit');
		$sign_mode = unserialize(option::get('sign_mode'));
		$sign_again = unserialize(option::get('cron_sign_again'));
		$retry_max  = option::get('retry_max');
		$q = array();
		$x = array();
		//重新尝试签到出错的贴吧
		if ($limit == 0) {
			if ($retry_max == '0' || ($sign_again['latest'] == $today && $sign_again['num'] <= $retry_max && $retry_max != '-1') ) {
				$qs = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `no` = 0 AND `status` != '0'");
				while ($qss = $m->fetch_array($qs)) {
					$q[] = array(
						'id'     => $qss['id'],
						'uid'    => $qss['uid'],
						'pid'    => $qss['pid'],
						'fid'    => $qss['fid'],
						'tieba'  => $qss['tieba'],
						'no'     => $qss['no'],
						'status' => $qss['status'],
						'latest' => $qss['latest'],
						'last_error'    => $qss['last_error']
					);
				}
				shuffle($q);
			}
		} else {
			if ($retry_max == '0' || ($sign_again['lastdo'] == $today && $sign_again['num'] <= $retry_max && $retry_max != '-1') ) {
				$q = rand_row( DB_PREFIX.$table , 'id' , $limit , "`no` = 0 AND `status` != '0' AND `latest` = '{$today}'" , true );
			}
		}

		foreach ($q as $x) {
			self::DoSign_All($x['uid'] , $x['tieba'] , $x['id'] , $table , $sign_mode , $x['pid'] , $x['fid']);
		}
	}

	/**
	 * 登录百度
	 * @param 百度用户名
	 * @param 百度密码
	 * @param 验证码
	 * @param $vcodestr
	 * @return 登录完成的页面
	 */
	public static function loginBaidu( $bd_name , $bd_pw , $verifycode , $vcodestr ) {
		$x = new wcurl('http://wappass.baidu.com/passport/?verifycode=' . $verifycode , array('User-Agent: Phone'.mt_rand()));
		$x->set(CURLOPT_HEADER , true);
		return $x->post(array(
			'username'       => $bd_name ,
			'password'       => $bd_pw ,
			'verifycode'     => $verifycode , 
			'login_save'     => '3' ,
			'vcodestr'       => $vcodestr , 
			'aaa'            => '%E7%99%BB%E5%BD%95' ,
			'login'          => 'yes' ,
			'can_input'      => '0' ,
			'u'              => 'http%3A%2F%2Fm.baidu.com%2F%3Faction%3Dlogin' ,
			'tn' ,
			'tpl' ,
			'ssid'           => '000000' ,
			'form'           => '0' ,
			'bd_page_type'   => '1' ,
			'uid'            => 'wiaui_1316933575_9548' , 
			'isPhone'        => 'isPhone' ,
		));
	}
	/**
	 * 扫描指定PID的所有贴吧
	 * @param $pid PID
	 */
	public static function scanTiebaByPid($pid) {
		global $i;
		global $m;
		$cma    = $m->once_fetch_array("SELECT * FROM `".DB_PREFIX."baiduid` WHERE `id` = '{$pid}';");	
		$uid    = $cma['uid'];
		$ubduss = $cma['bduss'];
		$isvip  = self::isvip($uid);
		$pid    = $cma['id'];
		$table  = self::getTable($uid);
		$n3     = 1;
		$n      = 0;
		$n2     = 0;
		$addnum = 0; 
		$list   = array();
		$o      = option::get('tb_max');
		while(true) {
			$url = 'http://tieba.baidu.com/f/like/mylike?&pn='.$n3;
			$n3++;
			$addnum = 0;
			$c      = new wcurl($url, array('User-Agent: Phone XXX')); 
			$c->addcookie("BDUSS=".$ubduss);
			$ch = $c->exec();
			$c->close();
			preg_match_all('/\<td\>(.*?)\<a href=\"\/f\?kw=(.*?)\" title=\"(.*?)\">(.*?)\<\/a\>\<\/td\>/', $ch, $list);
			foreach ($list[3] as $v) {
				$n++;
				if (!empty($o) && $isvip == false && $n > $o) {
					break 2;
				}
				$v = addslashes(htmlspecialchars(mb_convert_encoding($v, "UTF-8", "GBK")));
				$osq = $m->once_fetch_array("SELECT COUNT(*) AS `C` FROM `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `uid` = ".$uid." AND `pid` = '{$pid}' AND `tieba` = '{$v}';");
				if($osq['C'] == '0') {
					$m->query("INSERT INTO `".DB_NAME."`.`".DB_PREFIX.$table."` (`id`, `pid`, `uid`, `tieba`, `no`, `latest`) VALUES (NULL, {$pid}, ".$uid.", '{$v}', 0, 0);");
				}
				$addnum++;
			}
            if($n3 > 100){
                break; //100页后重复
            }
			if (!isset($list[3][0])) {
				break; //完成
			} elseif($o != 0 && $n2 >= $o && $isvip == false) {
				break; //超限
			}
			$n2 = $n2 + $addnum;
		}
	}

	/**
	 * 扫描指定用户的所有贴吧并储存
	 * @param UID，如果留空，表示当前用户的UID
	 */
	public static function scanTiebaByUser($uid = '') {
		global $i;
		global $m;
		set_time_limit(0);
		if (empty($uid)) {
			$bduss = $i['user']['bduss'];
		} else {
			$bx = $m->query("SELECT * FROM `".DB_PREFIX."baiduid` WHERE `uid` = '{$uid}';");
			while ($by = $m->fetch_array($bx)) {
				$upid         = $by['id'];
				$bduss[$upid] = $by['bduss'];
			}
		}
		$n      = 0;
		foreach ($bduss as $pid => $ubduss) {
			$t = self::scanTiebaByPid($pid);
		}
	}
}