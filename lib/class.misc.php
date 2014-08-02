<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

/**
 * 其他功能类
 */

class misc {
	/**
	 * 快捷发送一封邮件
	 * @param $to 收件人
	 * @param $sub 邮件主题
	 * @param $msg 邮件内容
	 * @param $att 数组，附件的路径，可以多个附件，例如array('/plugins/wmzz_mailer/demo.jpg','/plugins/wmzz_mailer/f.jpg')
	 * @return 成功:true 失败：错误消息
	 */
	public static function mail($to, $sub = '无主题', $msg = '无内容', $att = array()) {
        if (defined("SAE_MYSQL_DB") && class_exists('SaeMail')){
            $mail = new SaeMail();
            $mail->setOpt(array(
              'from'=>option::get('mail_name'),//来源
              'to'=>$to,//接收者Mail
              'smtp_host'=>option::get('mail_host'),
              'smtp_port'=>option::get('mail_port'),//端口号（默认为25，一般不需修改）
              'smtp_username'=>option::get('mail_smtpname'),//发送账号
              'smtp_password'=>option::get('mail_smtppw'),//发送密码
              'subject'=>$sub,//邮件标题
              'content'=>$msg,//邮件内容
              'content_type'=>"HTML",//HTML格式发送
           ));
            $mail->setAttach( $att );
            $mail->send();
            if ($ret === false)
                return (var_dump($mail->errno(), $mail->errmsg()));
            else return true;
        }
        else {
		$mail = new PHPMailer();
		if (option::get('mail_mode') == 'SMTP') {
			$mail->isSMTP();
			$mail->Mailer = 'SMTP';
			$mail->SMTPDebug = 0;
			$mail->Debugoutput = 'html';
			$mail->Host = option::get('mail_host');
			$mail->Port = option::get('mail_port');
			$mail->SMTPAuth = (boolean) option::get('mail_auth');
			$mail->Username = option::get('mail_smtpname');
			$mail->Password = option::get('mail_smtppw');
		} else {
			$mail->Mailer = 'MAIL';
		}
			$mail->CharSet = "UTF-8"; //核心代码，可以解决乱码问题
			$mail->setFrom(option::get('mail_name'), option::get('mail_yourname'));
			$mail->addReplyTo(option::get('mail_name'), option::get('mail_yourname'));
			$mail->addAddress($to, $to);
			$mail->Subject = $sub;
			$mail->Body = $msg;
			$mail->msgHTML = $msg;
			$mail->AltBody = $msg;
			foreach ($att as $value) {
				$mail->addAttachment($value);
			}
		    if(!$mail->Send()) {
		        return $mail->ErrorInfo;
		    } else {
		       	return true;
		    }
        }
	}

	/**
	 * 寻找已缓存的贴吧 FID
	 * @param $kw 贴吧名
	 * @return string|boolean FID，如果没有缓存则返回false
	 */

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
		$f  = misc::findFid($kw);
		if ($f) {
			return $f; 
		} else {
			$ch = new wcurl('http://tieba.baidu.com/mo/m?kw='.urlencode($kw), array('User-Agent: fuck phone','Referer: http://wapp.baidu.com/','Content-Type: application/x-www-form-urlencoded'));
			$s  = $ch->exec();
			preg_match('/\<input type=\"hidden\" name=\"fid\" value=\"(.*?)\"\/\>/', $s, $fid);
			self::mSetFid($kw,$fid[1]);
			return $fid[1];
		}
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
		$temp = $m->fetch_array($m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX."baiduid` WHERE `id` = {$pid} LIMIT 1"));
		return $temp['bduss'];
	}

	/**
	 * 手机网页签到
	 */
	public static function DoSign_Mobile($uid,$kw,$id,$pid,$fid) {
		//没问题了
		$ck = misc::getCookie($pid);
		$ch = new wcurl('http://tieba.baidu.com/mo/q/sign?tbs='.misc::getTbs($uid,$ck).'&kw='.urlencode($kw).'&is_like=1&fid='.$fid ,array('User-Agent: fuck phone','Referer: http://tieba.baidu.com/f?kw='.$kw , 'Host: tieba.baidu.com','X-Forwarded-For: 115.28.1.'.mt_rand(1,255), 'Origin: http://tieba.baidu.com', 'Connection: Keep-Alive'));
		$ch->addcookie("BDUSS=".$ck);
		return $ch->exec();
	}

	/**
	 * 网页签到
	 */
	public static function DoSign_Default($uid,$kw,$id,$pid,$fid) {
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

	/**
	 * 客户端签到
	 */
	public static function DoSign_Client($uid,$kw,$id,$pid,$fid){
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

	/**
	 * 对一个UID执行完整的签到任务
	 */
	public static function DoSign_All($uid,$kw,$id,$table,$sign_mode,$pid,$fid) {
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
	 * 执行一个表的签到任务
	 * @param $table 表
	 * @param $sign_mode option::get('sign_mode')
	 */
	public static function DoSign($table,$sign_mode) {
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
}