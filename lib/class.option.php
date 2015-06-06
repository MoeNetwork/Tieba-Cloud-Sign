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
		global $m,$i;
		//用于兼容旧插件
		if (stripos($name, 'plugin_') === 0) {
			$plug = substr($name, '7');
            if(isset($i['opt'][$plug])) return $i['opt'][$plug];
			$set = $m->once_fetch_array("SELECT `options` FROM `".DB_PREFIX."plugins` WHERE `name` = '{$plug}' LIMIT 1;");
			return $set['options'];
		}
        return isset($i['opt'][$name]) ? $i['opt'][$name] : null;
	}

	/**
	 * 改变或添加一个设置 (不存在时自动添加)
	 * @param $name 设置项名称
	 * @param $value 值
	*/
	public static function set($name,$value) {
		global $m,$i;
		//用于兼容旧插件
		if (stripos($name, 'plugin_') === 0) {
			$plug = substr($name, '7');
			if($m->query("UPDATE `".DB_PREFIX."plugins` SET `options` = '" . $value . "' WHERE `name` = '{$plug}';")){
                $i['opt'][$plug] = $value;
                return true;
            } else {
                return false;
            }
		}
        $name = sqladds($name);
        $value = sqladds($value);
		if($m->query("INSERT INTO `".DB_PREFIX."options` (`name`, `value`) VALUES ('{$name}','{$value}') ON DUPLICATE KEY UPDATE `value` = '{$value}';")){
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
		global $m,$i;
		$name = sqladds($name);
		$value = sqladds($value);
		if($m->query("INSERT IGNORE INTO  `".DB_PREFIX."options` (`name`, `value`) VALUES ('{$name}', '{$value}');")){
            $i['opt'][$name] = $value;
            return true;
        } else {return false;}
	}

	/**
	 * 删除一个设置
	 * @param @name 设置名称
	*/
	public static function del($name) {
		global $m;
		return $m->query("DELETE FROM `".DB_PREFIX."options` WHERE `name` = '{$name}'") ? true : false;
	}

	/**
	 * 获取用户的设置
	 * $name 设置项名称
	 * $uid 用户UID，默认当前用户的UID
	 * @return string|bool 不存在时返回false
	*/
	public static function uget($name, $uid = '') {
		global $m,$i;
		if (empty($uid)) {
            return isset($i['user']['opt'][$name]) ? $i['user']['opt'][$name] : null;
		} else {
			$name = sqladds($name);
			$q = $m->once_fetch_array("SELECT `value` FROM `".DB_PREFIX."users_options` WHERE `uid` = '{$uid}' AND `name` = '{$name}' LIMIT 1");
			return isset($q['value']) ? $q['value'] : null;
		}
	}

	/**
	 * 改变用户的设置
	 * $name 设置名
	 * $value 设置值
	 * $uid 用户UID，默认当前用户的UID
	*/
	public static function uset($name , $value , $uid = '') {
		global $m,$i;
		if (empty($uid)) {
			$uid = $i['user']['uid'];
		}

		$name = sqladds($name);
		$value = sqladds($value);
        if($m->once_fetch_array("SELECT uid FROM `".DB_PREFIX."users_options` WHERE `uid` = {$uid} AND `name` = '{$name}'")){
            $q = $m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX."users_options` SET `value` =  '{$value}' WHERE `name` = '{$name}' AND `uid` = ".$uid);
        } else {
            $q = $m->query("INSERT INTO `".DB_PREFIX."users_options` (`uid`, `name`, `value`) VALUES ({$uid},'{$name}','{$value}')");
        }
        if($q){
            if ($uid == $i['user']['uid']) $i['user']['opt'][$name] = $value;
            return true;
        } else {
            return false;
        }
	}

	/**
	 * 清除用户的所有设置
	 * $uid 用户UID，默认当前用户的UID
	 */
	public static function udel($uid = '') {
		global $m,$i;
		if (empty($uid)) {
			$uid = $i['user']['uid'];
		}
		if($m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."users_options` WHERE `uid` = ".$uid)){
            if ($uid == $i['user']['uid']) $i['user']['opt'] = null;
            return true;
        } else {
            return false;
        }
	}

	/**
	 * 清除用户的指定设置
	 * @param $name 设置项名称
	 * @param $uid 用户UID，默认当前用户的UID
	 */
	public static function udela($name , $uid = '') {
		global $m,$i;
		$name = sqladds($name);
		if (empty($uid)) {
			$uid = $i['user']['uid'];
		}
		if($m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."users_options` WHERE `name` = '{$name}' AND `uid` = ".$uid)){
            if ($uid == $i['user']['uid']) unset($i['user']['opt'][$name]);
            return true;
        } else {
            return false;
        }
	}

	/**
	 * 添加一个用户的设置
	 * 添加时会自动检查有关设置是否已存在
	 * @param $name 设置项名称
	 * @param $value 值
	 * @param $uid 用户UID，默认当前用户的UID
	 */
	public static function uadd($name , $value , $uid = '') {
		global $m,$i;
		if (empty($uid)) {
			$uid = $i['user']['uid'];
		}
		$name = sqladds($name);
		$value = sqladds($value);
        if($m->once_fetch_array("SELECT uid `".DB_PREFIX."users_options` WHERE `uid` = '{$uid}' AND `name` = '{$name}'") === false){
            if($m->query("INSERT INTO  `".DB_PREFIX."users_options` (`uid`, `name`, `value`) VALUES ({$uid}, '{$name}', '{$value}');")){
                if ($uid == $i['user']['uid']) $i['user']['opt'][$name] = $value;
                return true;
            } else {
                return false;
            }
        }
	}

	/**
	 * 获取插件的所有设置
	 * 其实我建议保存到options表
	 * @param 插件标识符
	 * @return array 设置数组
	*/
	public static function pget($plug) {
		global $i;
		return $i['plugins']['info'][$plug]['options'];
	}

	/**
	 * 保存插件的所有设置
	 * @param $plug 插件标识符
	 * @param $value array 设置数组
	*/
	public static function pset($plug , $value) {
		global $m,$i;
		if($m->query("UPDATE `".DB_PREFIX."plugins` SET `options` = '" . serialize($value) . "' WHERE `name` = '{$plug}';")){
            $i['plugins']['info'][$plug]['options'] = $value;
            return true;
        } else{
            return false;
        }
	}

	/**
	 * 删除插件的所有设置
	 * @param $plug 插件标识符
	*/
	public static function pdel($plug) {
		global $m,$i;
		if($m->query("UPDATE `".DB_PREFIX."plugins` SET `options` = '' WHERE `name` = '{$plug}';")){
            $i['plugins']['info'][$plug]['options'] = null;
            return true;
        } else {
            return false;
        }
	}

	/**
	 * 获取插件的一条设置
     * @param $plug 插件标识符
	 * @param $name 设置项名称
	 * @return string 设置值
	*/
	public static function xget($plug , $name) {
		global $i;
		return $i['plugins']['info'][$plug]['options'][$name];
	}

	/**
	 * 保存插件的一条设置，不存在则添加之
	 * 注意：需要大量修改的请直接将设置保存到options表
	 * @param $plug 插件标识符
	 * @param $name 设置项名称
	 * @param $value 值
	 */
	public static function xset($plug , $name , $value) {
		global $m,$i;
		$a = self::pget($plug);
		$a[$name] = $value;
		if($m->query("UPDATE `".DB_PREFIX."plugins` SET `options` = '" . serialize($a) . "' WHERE `name` = '{$plug}';")){
            $i['plugins']['info'][$plug]['options'] = $a;
            return true;
        } else {
            return false;
        }
	}

	/**
	 * 删除插件的一条设置
	 * @param $plug 插件标识符
	 * @param $name 设置项名称
	 */
	public static function xdel($plug , $name ) {
		global $m,$i;
		$a = self::pget($plug);
		unset($a[$name]);
		if($m->query("UPDATE `".DB_PREFIX."plugins` SET `options` = '" . serialize($a) . "' WHERE `name` = '{$plug}';")){
            $i['plugins']['info'][$plug]['options'] = $a;
            return true;
        } else {
            return false;
        }
	}

	/**
	 * 直接添加插件的一条设置，已存在则跳过
	 * 注意：需要大量修改的请直接将设置保存到options表
	 * @param $plug 插件标识符
	 * @param $name 设置项名称
	 * @param $value 值
	 */
	public static function xadd($plug , $name , $value) {
		global $m,$i;
		$a = self::pget($plug);
		if (!isset($a[$name])) {
			$a[$name] = $value;
		} else {
			return;
		}
		if($m->query("UPDATE `".DB_PREFIX."plugins` SET `options` = '" . serialize($a) . "' WHERE `name` = '{$plug}';")){
            $i['plugins']['info'][$plug]['options'] = $a;
            return true;
        } else {
            return false;
        }
	}
}