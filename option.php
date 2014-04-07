<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

class option {
	public static function get($name) {
		global $m;
		$query=$m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX."options` WHERE `name` = '{$name}'");
		$temp=$m->fetch_array($query);
		return $temp['value'];
	}
	public static function set($name,$value) {
		global $m;
		$m->query("UPDATE  `".DB_NAME."`.`".DB_PREFIX."options` SET  `value` =  '{$value}' WHERE `name` = '{$name}'");
		return true;
	}
}
?>