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
		global $i;
		if (!isset($i['opt'][$name])) {
			self::set($name,'0');
			return 0;
		} else {
			return $i['opt'][$name];
		}
	}

	/**
	 * 改变或添加一个设置 (不存在时自动添加)
	 * @param $name 设置项名称
	 * @param $value 值
	*/
	public static function set($name,$value) {
		global $m;
		$name = sqladds($name);
		$value = sqladds($value);
		if($m->query("INSERT INTO `".DB_PREFIX."options` (`name`, `value`) VALUES ('{$name}','{$value}') ON DUPLICATE KEY UPDATE `value` = '{$value}';")){
			global $i;
			$i['opt'][$name] = $value;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 直接添加一个设置
	 * @param $name 设置项名称
	 * @param $value 值
	 */
	public static function add($name,$value) {
		global $m;
		$name = sqladds($name);
		$value = sqladds($value);
		$m->query("INSERT IGNORE INTO  `".DB_PREFIX."options` (`id`, `name`, `value`) VALUES (NULL, '{$name}', '{$value}');");
	}

	/**
	 * 删除一个设置
	 * @param @name 设置名称
	*/
	public static function del($name) {
		global $m;
		$m->query("DELETE FROM `".DB_PREFIX."options` WHERE `name` = '{$name}'");
	}

	/**
	 * 获取用户的设置
	 * $name 设置项名称
	 * $uid 用户UID，默认当前用户的UID
	 * @return string|bool 不存在时返回false
	*/
	public static function uget($name, $uid = '') {
		global $m;
		global $i;
		if (empty($uid)) {
			if (isset($i['user']['opt'][$name])) {
				return $i['user']['opt'][$name];
			} else {
				return false;
			}
		} else {
			$name = sqladds($name);
			$x = $m->once_fetch_array("SELECT * FROM `".DB_PREFIX."users_options` WHERE `uid` = '{$uid}' AND `name` = '{$name}' LIMIT 1");
			if (isset($x['value'])) {
				return $x['value'];
			} else {
				return false;
			}
		}
	}

	/**
	 * 改变用户的设置
	 * $name 设置名
	 * $value 设置值
	 * $uid 用户UID，默认当前用户的UID
	*/
	public static function uset($name , $value , $uid = '') {
		global $m;
		global $i;
		if (empty($uid)) {
			$uid = $i['user']['uid'];
		}

		$name = sqladds($name);
		$value = sqladds($value);
		$test = $m->once_fetch_array("SELECT COUNT(*) AS `x` FROM `".DB_PREFIX."users_options` WHERE `uid` = '{$uid}' AND `name` = '{$name}'");
		if($test['x'] > 0) {
			$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX."users_options` SET `value` =  '{$value}' WHERE `name` = '{$name}' AND `uid` = ".$uid);
		} else {
			$m->query("INSERT INTO `".DB_PREFIX."users_options` (`uid`, `name`, `value`) VALUES ('{$uid}','{$name}','{$value}')");
		}
	}

	/**
	 * 清除用户的所有设置
	 * $uid 用户UID，默认当前用户的UID
	 */
	public static function udel($uid = '') {
		global $m;
		global $i;
		if (empty($uid)) {
			$uid = $i['user']['uid'];
		}
		$m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."users_options` WHERE `uid` = ".$uid);
	}

	/**
	 * 清除用户的指定设置
	 * @param $name 设置项名称
	 * @param $uid 用户UID，默认当前用户的UID
	 */
	public static function udela($name , $uid = '') {
		global $m;
		global $i;
		$name = sqladds($name);
		if (empty($uid)) {
			$uid = $i['user']['uid'];
		}
		$m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."users_options` WHERE `name` = '{$name}' AND `uid` = ".$uid);
	}

	/**
	 * 添加一个用户的设置
	 * 添加时会自动检查有关设置是否已存在
	 * @param $name 设置项名称
	 * @param $value 值
	 * @param $uid 用户UID，默认当前用户的UID
	 */
	public static function uadd($name , $value , $uid = '') {
		global $m;
		global $i;
		if (empty($uid)) {
			$uid = $i['user']['uid'];
		}
		$name = sqladds($name);
		$value = sqladds($value);
		$test = $m->once_fetch_array("SELECT COUNT(*) AS `x` FROM `".DB_PREFIX."users_options` WHERE `uid` = '{$uid}' AND `name` = '{$name}'");
		if($test['x'] <= 0) {
			$m->query("INSERT INTO  `".DB_PREFIX."users_options` (`uid`, `name`, `value`) VALUES ('{$uid}', '{$name}', '{$value}');");
		}
	}

	/**
	 * 获取插件的所有设置
	 * @param 插件标识符
	 * @return array 设置数组
	*/
	public static function pget($plug) {
		return unserialize(self::get('plugin_'.$plug));
	}

	/**
	 * 保存插件的设置
	 * @param $plug 插件标识符
	 * @param $value array 设置数组
	*/
	public static function pset($plug , $value) {
		self::set('plugin_'.$plug , serialize($value));
	}

	/**
	 * 删除插件的设置
	 * @param $plug 插件标识符
	*/
	public static function pdel($plug) {
		self::del('plugin_'.$plug);
	}
}