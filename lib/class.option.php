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
		$m->query("INSERT INTO `".DB_PREFIX."options` (`name`, `value`) VALUES ('{$name}','{$value}') ON DUPLICATE KEY UPDATE `value` = '{$value}';");
		return true;
	}

	/**
	 * 直接添加一个设置
	 * @param $name 设置项名称
	 * @param $value 值
	 */
	public static function add($name,$value) {
		global $m;
		$m->query("INSERT INTO  `".DB_PREFIX."options` (`id`, `name`, `value`) VALUES (NULL, '{$name}', '{$value}');");
	}

	/**
	 * 删除一个设置
	 * @param @name 设置名称
	*/
	public static function del($name) {
		global $m;
		$m->query("DELETE FROM `".DB_PREFIX."options` WHERE `name` = `{$name}`");
	}

	/**
	 * 获取用户的设置
	 * $name 设置项名称
	 * $uid 用户UID，默认当前用户的UID
	 * @return string
	*/
	public static function uget($name, $uid = '') {
		global $m;
		global $i;
		if (empty($uid)) {
			$uid = $GLOBALS['uid'];
		}
		if (isset($i['user']['opt'][$name])) {
			return $i['user']['opt'][$name];
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

	/**
	 * 获取插件的设置
	 * @param 插件标识符
	 * @return array 设置数组
	*/
	public static function pget($plug) {
		return unserialize(self::get('plugin_'.$plug));
	}
}
?>