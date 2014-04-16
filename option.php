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
	 * $name 计划任务名称
	 * @return array
	*/
	public static function get($name) {
		global $m;
		return $m->once_fetch_array("SELECT *  FROM `".DB_NAME."`.`".DB_PREFIX."cron` WHERE `name` = '{$name}'");
	}

	/**
	 * 改变或添加计划任务 (不存在时自动添加)
	 * $name 计划任务名称
	 * $file 计划任务文件，执行时以include方式执行
	 * $no 忽略任务
	 * $status 计划任务状态，系统会写入
	 * $freq 执行频率
	 * $lastdo 上次执行，系统会写入
	 * $log 执行日志，系统会写入
	*/
	public static function set($name, $file = '', $no = 0, $status = 0, $freq = 0, $lastdo = '', $log = '') {
		global $m;
		$x = $m->once_fetch_array("SELECT COUNT(*) AS ffffff FROM `".DB_NAME."`.`".DB_PREFIX."options` WHERE `name` = '{$name}'");
		if ($x['ffffff'] <= 0) {
			$m->query("INSERT INTO  `".DB_NAME."`.`".DB_PREFIX."cron` (`id`, `name`, `file`, `no`, `status`, `freq`, `lastdo`, `log`) VALUES (NULL, '{$name}', '{$file}', '{$no}', '{$status}', '{$freq}', '{$lastdo}', '{$log}');");	
		} else {
			$m->query("UPDATE  `".DB_NAME."`.`".DB_PREFIX."cron` SET  `name` =  '{$name}',`file` =  '{$file}',`no` =  '{$no}',`status` =  '{$status}',`freq` =  '{$freq}',`lastdo` =  '{$lastdo}',`log` =  '{$log}'  WHERE `name` = '{$name}'");
		}
	}
}


?>