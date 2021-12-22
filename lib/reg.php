<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
/**
 * 环境准备
 */
$today       = date("Y-m-d");
$i           = array();

$i['plugins']['hook'] = array(); //挂载列表
//注册全局信息变量 $i
$i['db']['host'] = DB_HOST;
$i['db']['user'] = DB_USER;
$i['db']['prefix'] = DB_PREFIX;
$i['db']['passwd'] = DB_PASSWD;
$i['db']['name'] = DB_NAME;

//_POST _GET _REQUEST
$i['post']    = $_POST;
$i['get']     = $_GET;
$i['request'] = $_REQUEST;
$ws = $m->query("SELECT * FROM ".DB_PREFIX."options");
while ($wsr = $m->fetch_array($ws)) {
	$key = $wsr['name'];
	$i['opt'][$key] = $wsr['value'];
}
$rs = $m->query("SELECT *  FROM `".DB_NAME."`.`".DB_PREFIX."cron` ORDER BY `orde` ASC");
while ($rsr = $m->fetch_array($rs)) {
	$key = $rsr['name'];
	$i['cron'][$key] = $rsr;
}

//贴吧分表列表
$i['tabpart'] = $i['table'] = unserialize($i['opt']['fb_tables']);
$i['table'] = 'tieba'; //贴吧表列表

//当前页面/模式, $i['mode'][0] 一般表示页面
if (!empty($_REQUEST['mod'])) {
	$i['mode'] = explode(':', strip_tags($_REQUEST['mod']));
} else {
	$i['mode'][0] = 'default';
}

if((empty($i['opt']['core_version']) || SYSTEM_VER != $i['opt']['core_version']) && !defined('SYSTEM_NO_CHECK_VER')) {
	if (empty($i['opt']['core_version'])) {
		$i['opt']['core_version'] = '3.45';
	}
	if (file_exists(SYSTEM_ROOT . '/setup/update' . $i['opt']['core_version'] . 'to' . SYSTEM_VER . '.php')) {
		$updatefile = '<a href="setup/update' . $i['opt']['core_version'] . 'to' . SYSTEM_VER . '.php">请点击运行: ' . 'update' . $i['opt']['core_version'] . 'to' . SYSTEM_VER . '.php</a>';
		msg('严重错误：数据库中的云签到版本与文件版本不符，是否已运行升级脚本？<br/><br/>' . $updatefile);
	} else {
		$m->query("INSERT INTO `".DB_PREFIX."options` (`name`, `value`) VALUES ('core_version','".SYSTEM_VER."') ON DUPLICATE KEY UPDATE `value` = '".SYSTEM_VER."';");
	}
}

if (!defined('SYSTEM_NO_PLUGIN')) {
	//所有插件列表
	$i['plugins'] = array('all' => array() , 'actived' => array() , 'info' => array());
	$plugin_all_query = $m->query("SELECT * FROM `".DB_PREFIX."plugins` ORDER BY `order` ASC");
	while ($plugin_all_var = $m->fetch_array($plugin_all_query)) {
		$i['plugins']['all'][] = $plugin_all_var['name']; 
		$i['plugins']['info'][$plugin_all_var['name']] = $plugin_all_var; 
		$i['plugins']['info'][$plugin_all_var['name']]['options'] = empty($plugin_all_var['options']) ? array() : unserialize($plugin_all_var['options']);
		if ($plugin_all_var['status'] == '1') {
			$i['plugins']['actived'][] = $plugin_all_var['name'];
		}
	}
}

//autoload
function class_autoload($c) {
	$c = strtolower($c);
	if (file_exists(SYSTEM_ROOT . '/lib/class.' . $c . '.php')) {
		include SYSTEM_ROOT . '/lib/class.' . $c . '.php';
	} else {
		throw new Exception('无法加载此类：' . $c , 10001);
	}
}
spl_autoload_register('class_autoload');

if (option::get('dev') != 1) {
	define('SYSTEM_DEV', false);
} else {
	define('SYSTEM_DEV', true);
}

set_exception_handler(array('E','exception'));
set_error_handler(array('E','error'));
