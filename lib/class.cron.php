<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

/**
 * cron 计划任务操作类
 */
class cron {
	/**
	 * 获取计划任务所有数据
	 * @param string $name 计划任务名称
	 * @return array
	*/
	public static function get($name) {
		global $i;
		if (isset($i['cron'][$name])) {
			return $i['cron'][$name];
		}
	}

	/**
	 * 获取计划任务指定数据
	 * @param string $name 计划任务名称
	 * @param string $set 设置项名
	 */
	public static function sget($name,$set) {
		global $i;
		if (isset($i['cron'][$name][$set])) {
			return $i['cron'][$name][$set];
		}
	}

	/**
	 * 通过数组改变或添加计划任务 (不存在时自动添加)
	 * @param $name string 全局唯一计划任务名称
	 * @param $set  array 设置项
	 */
	public static function aset($name, $set) {
		global $m;

		$set = adds($set);

		$sql = "INSERT INTO  `".DB_PREFIX."cron` (`name`";
		$a = '';
		$b = "'{$name}'";
		$c = "`name` = '{$name}'";

		if (isset($set['file'])) {
			$a .= ', `file`';
			$b .= ", '{$set['file']}'";
			$c .= ", `file` = '{$set['file']}'";
		}
		if (isset($set['no'])) {
			$a .= ', `no`';
			$b .= ", '{$set['no']}'";
			$c .= ", `no` = '{$set['no']}'";
		}
		if (isset($set['desc'])) {
			$a .= ', `desc`';
			$b .= ", '{$set['desc']}'";
			$c .= ", `desc` = '{$set['desc']}'";
		}
		if (isset($set['freq'])) {
			$a .= ', `freq`';
			$b .= ", '{$set['freq']}'";
			$c .= ", `freq` = '{$set['freq']}'";
		}
		if (isset($set['lastdo'])) {
			$a .= ', `lastdo`';
			$b .= ", '{$set['lastdo']}'";
			$c .= ", `lastdo` = '{$set['lastdo']}'";
		}
		if (isset($set['orde'])) {
			$a .= ', `orde`';
			$b .= ", '{$set['orde']}'";
			$c .= ", `orde` = '{$set['orde']}'";
		}
		if (isset($set['log'])) {
			$a .= ', `log`';
			$b .= ", '{$set['log']}'";
			$c .= ", `log` = '{$set['log']}'";
		}

		$sql .= $a . ' ) VALUES (' . $b . ') ON DUPLICATE KEY UPDATE '. $c . ';';
		$m->query($sql);

	}

	/**
	 * 改变或添加计划任务 (不存在时自动添加)
	 * WARNING:请使用更先进的 aset() 代替他
	 * $name 全局唯一计划任务名称
	 * $file 计划任务文件，执行时以include方式执行function，function名称为cron_计划任务名称
	 * $no 忽略任务
	 * $desc 计划任务描述
	 * $freq 执行频率
	 *       -1：一次性任务，执行完毕后系统会删除
	 *       0 ：默认，当do.php被执行时，该任务始终被运行
	 *       其他正整数：运行时间间隔，单位秒（$lastdo - $freq）
	 * $lastdo 上次执行，系统会写入
	 * $log 执行日志，系统会写入
	*/
	public static function set($name, $file = '', $no = 0, $desc = '', $freq = 0, $lastdo = '', $log = '') {
		global $m;
		$set = array();

		if (!empty($file)) {
			$set['file'] = $file;
		}
		if (!empty($no)) {
			$set['no'] = $no;
		}
		if (!empty($desc)) {
			$set['desc'] = $desc;
		}
		if (!empty($freq)) {
			$set['freq'] = $freq;
		}
		if (!empty($lastdo)) {
			$set['lastdo'] = $lastdo;
		}
		if (!empty($log)) {
			$set['log'] = $log;
		}

		self::aset($name , $set);
	}

	/**
	 * 直接添加一个计划任务
	 * @param string $name 计划任务名
	 * @param string $set  任务设置
	 */
	public static function add($name , $set) {
		global $m;

		$set = adds($set);

		$sql = "INSERT IGNORE INTO  `".DB_PREFIX."cron` (`name`";
		$a = '';
		$b = "'{$name}'";

		if (isset($set['file'])) {
			$a .= ', `file`';
			$b .= ", '{$set['file']}'";
		}
		if (isset($set['no'])) {
			$a .= ', `no`';
			$b .= ", '{$set['no']}'";
		}
		if (isset($set['desc'])) {
			$a .= ', `desc`';
			$b .= ", '{$set['desc']}'";
		}
		if (isset($set['freq'])) {
			$a .= ', `freq`';
			$b .= ", '{$set['freq']}'";
		}
		if (isset($set['lastdo'])) {
			$a .= ', `lastdo`';
			$b .= ", '{$set['lastdo']}'";
		}
		if (isset($set['log'])) {
			$a .= ', `log`';
			$b .= ", '{$set['log']}'";
		}

		$sql .= $a . ' ) VALUES (' . $b . ')';
		$m->query($sql);
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
	 * @param string $file 计划任务文件
	 * @param string $name 计划任务名称
	 * @return mixed 执行成功给出日志，否则false
	 */

	public static function run($file,$name) {
		$GLOBALS['in_cron'] = true;
		if (file_exists(SYSTEM_ROOT.'/'.$file)) {
			include_once SYSTEM_ROOT.'/'.$file;
			if (function_exists('cron_'.$name)) {
				return call_user_func('cron_'.$name);
			} else {
				self::aset($name , array('log' => '['.date('Y-m-d H:m:s').']计划任务启动失败，处理此任务的函数不存在'));
				return false;
			}
		}  else {
			self::aset($name , array('log' => '['.date('Y-m-d H:m:s').']计划任务启动失败，任务文件不存在'));
			return false;
		}
	}

	/**
	 * 异步执行计划任务（伪）
	 * @param string  $name 计划任务名称
	 */
	public static function arun($name) {
		$url = SYSTEM_URL . 'do.php?mod=runcron&cron=' . $name;
		$cpw = option::get('cron_pw');
		if (!empty($cpw)) {
			$url .= '&pw=' . $cpw;
		}

		if(!sendRequest($url)) {
			self::aset($name , array('log' => '['.date('Y-m-d H:m:s').']计划任务启动失败，在调用 fsockopen() 时失败，请检查主机是否支持此函数'));
		}
	}

	/**
	 * 按运行顺序运行所有计划任务
	 *
	 */
	public static function runall() {
		global $m;
		$cron = $m->query("SELECT *  FROM `".DB_NAME."`.`".DB_PREFIX."cron` ORDER BY  `orde` ASC ");
		while ($cs = $m->fetch_array($cron)) {
			if ($cs['no'] != '1') {
				if (option::get('cron_asyn')) {
					self::arun($cs['name']);
				} else {
					if ($cs['freq'] == '-1') {
						self::run($cs['file'],$cs['name']);
						self::del($cs['name']);
					}
					elseif ( empty($cs['freq']) || empty($cs['lastdo']) || (time() - $cs['lastdo']) >= $cs['freq'] ) {
						$return=self::run($cs['file'],$cs['name']);
						cron::aset($cs['name'] , 
							array(
								'lastdo' => time(),
								'log'    => $return
							)
						);
					}
				}
			}
		}
	}
}
