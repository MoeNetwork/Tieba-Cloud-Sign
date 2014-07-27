<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
/**
 * 环境准备
 */

$PluginHooks = array();
$today       = date("Y-m-d");
$i           = array();

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
$rs = $m->query("SELECT *  FROM `".DB_NAME."`.`".DB_PREFIX."cron`");
while ($rsr = $m->fetch_array($rs)) {
	$key = $rsr['name'];
	$i['cron'][$key] = $rsr;
}
//贴吧表列表
$i['table'][] = 'tieba';
//贴吧分表列表
$i['tabpart'] = unserialize($i['opt']['fb_tables']);
if (!empty($i['tabpart'])) {
	foreach ($i['tabpart'] as $value) {
		$i['table'][] = $value;
	}
}
//激活的插件列表
$i['plugin'] = unserialize($i['opt']['actived_plugins']);

//当前页面/模式, $i['mode'][0] 一般表示页面
if (!empty($_REQUEST['mod'])) {
	$i['mode'] = explode(':', strip_tags($_REQUEST['mod']));
} else {
	$i['mode'][0] = 'default';
}

//autoload
function class_autoload($c) {
	$c = strtolower($c);
	if (file_exists(SYSTEM_ROOT . '/lib/class.' . $c . '.php')) {
		include SYSTEM_ROOT . '/lib/class.' . $c . '.php';
	} else {
		msg("类 {$c} 加载失败");
	}
}
spl_autoload_register('class_autoload');

if (option::get('dev') != 1 || defined('NO_ERROR')) {
	define('SYSTEM_DEV', false);
} else {
	define('SYSTEM_DEV', true);
}
new option();
?>