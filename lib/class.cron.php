<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

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
		global $i;
		if (isset($i['cron'][$name])) {
			return $i['cron'][$name];
		}
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
	 * 删除计划任务
	 */
	public static function del($name) {
		global $m;
		$m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."cron` WHERE `name` = '{$name}'");
	}

	/**
	 * 执行一个计划任务
	 * 
	 * @param 计划任务文件
	 * @param 计划任务名称
	 * @return 执行成功true，否则false
	 */

	public static function run($file,$name) {
		$GLOBALS['in_cron'] = true;
		if (file_exists(SYSTEM_ROOT.'/'.$file)) {
			include_once SYSTEM_ROOT.'/'.$file;
			if (function_exists('cron_'.$name)) {
				return call_user_func('cron_'.$name);
			}
		}
	}

	/**
	 * 按运行顺序运行所有计划任务
	 *
	 */
	public static function runall() {
		global $m;
		$time = time();
		$cron = $m->query("SELECT *  FROM `".DB_NAME."`.`".DB_PREFIX."cron` ORDER BY  `orde` ASC ");
		while ($cs = $m->fetch_array($cron)) {
			if ($cs['no'] != '1') {
				if ($cs['freq'] == '-1') {
					self::run($cs['file'],$cs['name']);
					$m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."cron` WHERE `".DB_PREFIX."cron`.`id` = ".$cs['id']);
				}
				elseif ( empty($cs['freq']) || empty($cs['lastdo']) || $cs['lastdo'] - $cs['freq'] >= $cs['freq'] ) {
					$return=self::run($cs['file'],$cs['name']);
					$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX."cron` SET `lastdo` =  '{$time}',`log` = '{$return}' WHERE `".DB_PREFIX."cron`.`id` = ".$cs['id']);
				}
			}
		}
	}
}
