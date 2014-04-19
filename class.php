<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
/**
 * option 设置类
 */
class option {
	/**
	 * 获取设置
	 * $name 设置项名称
	 * @return string
	*/
	public static function get($name) {
		global $m;
		$query=$m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX."options` WHERE `name` = '{$name}'");
		$temp=$m->fetch_array($query);
		return $temp['value'];
	}

	/**
	 * 改变或添加一个设置 (不存在时自动添加)
	 * $name 设置项名称
	*/
	public static function set($name,$value) {
		global $m;
		$x = $m->once_fetch_array("SELECT COUNT(*) AS ffffff FROM `".DB_NAME."`.`".DB_PREFIX."options` WHERE `name` = '{$name}'");
		if ($x['ffffff'] <= 0) {
			$m->query("INSERT INTO  `".DB_NAME."`.`".DB_PREFIX."options` (`id`, `name`, `value`) VALUES (NULL, '{$name}', '{$value}');");	
		} else {
			$m->query("UPDATE  `".DB_NAME."`.`".DB_PREFIX."options` SET  `value` =  '{$value}' WHERE `name` = '{$name}'");
		}
		return true;
	}

	/**
	 * 获取用户的设置
	 * $name 设置项名称
	 * $uid 用户UID，默认当前用户的UID
	 * @return string
	*/
	public static function uget($name, $uid = '') {
		global $m;
		if (empty($uid)) {
			$uid = $GLOBALS['uid'];
		}
		$query=$m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX."users` WHERE `id` = ".$uid);
		$temp=$m->fetch_array($query);
		$temp=unserialize($temp['options']);
		if (isset($temp[$name])) {
			return $temp[$name];
		}
	}

	/**
	 * 改变用户的设置
	 * $uid 用户UID，默认当前用户的UID
	 * $data array 各设置
	*/
	public static function uset($data, $uid = '') {
		global $m;
		if (empty($uid)) {
			$uid = $GLOBALS['uid'];
		}
		$x = serialize($data);
		$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX."users` SET `options` =  '{$x}' WHERE `id` = ".$uid);
	}
}

/**
 * cron 计划任务操作类
 */
class cron Extends option {
	/**
	 * 获取计划任务名称
	 * @param $name 计划任务名称
	 * @return array
	*/
	public static function get($name) {
		global $m;
		return $m->once_fetch_array("SELECT *  FROM `".DB_NAME."`.`".DB_PREFIX."cron` WHERE `name` = '{$name}'");
	}

	/**
	 * 改变或添加计划任务 (不存在时自动添加)
	 * $name 计划任务名称
	 * $file 计划任务文件，执行时以include方式执行function，function名称为cron_计划任务名称
	 * $no 忽略任务
	 * $status 计划任务状态，系统会写入
	 * $freq 执行频率
	 *       -1：一次性任务，执行完毕后系统会删除
	 *       0 ：默认，当do.php被执行时，该任务始终被运行
	 *       其他正整数：运行时间间隔，单位秒（$lastdo - $freq）
	 * $lastdo 上次执行，系统会写入
	 * $log 执行日志，系统会写入
	*/
	public static function set($name, $file = '', $no = 0, $status = 0, $freq = 0, $lastdo = '', $log = '') {
		global $m;
		$x = $m->once_fetch_array("SELECT COUNT(*) AS ffffff FROM `".DB_NAME."`.`".DB_PREFIX."cron` WHERE `name` = '{$name}'");
		if ($x['ffffff'] <= 0) {
			$m->query("INSERT INTO  `".DB_NAME."`.`".DB_PREFIX."cron` (`id`, `name`, `file`, `no`, `status`, `freq`, `lastdo`, `log`) VALUES (NULL, '{$name}', '{$file}', '{$no}', '{$status}', '{$freq}', '{$lastdo}', '{$log}');");	
		} else {
			$m->query("UPDATE  `".DB_NAME."`.`".DB_PREFIX."cron` SET  `name` =  '{$name}',`file` =  '{$file}',`no` =  '{$no}',`status` =  '{$status}',`freq` =  '{$freq}',`lastdo` =  '{$lastdo}',`log` =  '{$log}'  WHERE `name` = '{$name}'");
		}
	}

	/**
	 * 删除一个计划任务
	 * @param $name 计划任务名称
	*/
	public static function del($name) {
		global $m;
		$m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."cron` WHERE `name` = '{$name}'");
	}
}

/**
 * 杂项功能类
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
}
?>