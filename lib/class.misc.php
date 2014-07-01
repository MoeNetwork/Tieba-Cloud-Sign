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
}