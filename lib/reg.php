<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
/**
 * 环境准备
 */
$today       = date("Y-m-d");
$i           = array();

$i['PluginHooks'] = array(); //挂载列表
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
//贴吧表列表
$i['table'][] = 'tieba';
//贴吧分表列表
$i['tabpart'] = unserialize($i['opt']['fb_tables']);
if (!empty($i['tabpart'])) {
	foreach ($i['tabpart'] as $value) {
		$i['table'][] = $value;
	}
}
//所有插件列表
$i['plugins'] = array('all' => array() , 'actived' => array() , 'info' => array());
$plugin_all_query = $m->query("SELECT * FROM `".DB_PREFIX."plugins`");
$plugin_active_query = $m->query("SELECT * FROM `".DB_PREFIX."plugins` WHERE `status` = '1'");
while ($plugin_all_var = $m->fetch_array($plugin_all_query)) {
	$i['plugins']['all'][] = $plugin_all_var['name']; 
	$i['plugins']['info'][$plugin_all_var['name']] = $plugin_all_var; 
	$i['plugins']['info'][$plugin_all_var['name']]['options'] = empty($plugin_all_var['options']) ? array() : unserialize($plugin_all_var['options']);
}
while ($plugin_active_var = $m->fetch_array($plugin_active_query)) {
	$i['plugins']['actived'][] = $plugin_active_var['name'];
}

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

/**
 * Framework 错误处理函数
 */

function sfc_error($errno, $errstr, $errfile, $errline) {
	switch ($errno) {
		    case E_USER_ERROR:          $errnoo = 'User Error'; break;
		    case E_USER_WARNING:        $errnoo = 'User Warning'; break;
		    case E_ERROR:               $errnoo = 'Error'; break;
	        case E_WARNING:             $errnoo = 'Warning'; break;
	        case E_PARSE:               $errnoo = 'Parse Error'; break;
			case E_USER_NOTICE:         $errnoo = 'User Notice';	    break;     
 			case E_CORE_ERROR:          $errnoo = 'Core Error'; break;
	        case E_CORE_WARNING:        $errnoo = 'Core Warning'; break;
	        case E_COMPILE_ERROR:       $errnoo = 'Compile Error'; break;
	        case E_COMPILE_WARNING:     $errnoo = 'Compile Warning'; break;
	        case E_STRICT:              $errnoo = 'Strict Warning'; break;
		    default:                    $errnoo = 'Unknown Error [ #'.$errno.' ]';  break;
   	}
	if (SYSTEM_DEV == true && !defined('SYSTEM_NO_ERROR')) {
		echo '<div class="alert alert-danger alert-dismissable"><strong>[ StusGame Framework ] '.$errnoo.':</strong> [ Line: '.$errline.' ]<br/>'.$errstr.'<br/>File: '.$errfile.'</div>';
	}
	doAction('error', $errno, $errstr, $errfile, $errline, $errnoo);
}

set_error_handler('sfc_error');