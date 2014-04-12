<?php
define('SYSTEM_FN','百度贴吧云签到');
define('SYSTEM_VER','1.0');
define('SYSTEM_ROOT',dirname(__FILE__));
define('SYSTEM_PAGE',isset($_REQUEST['mod']) ? strip_tags($_REQUEST['mod']) : 'default');
$PluginHooks = array();
$today       = date("Y-m-d");
header("content-type:text/html; charset=utf-8");
require SYSTEM_ROOT.'/setup/msg.php';
require SYSTEM_ROOT.'/config.php';
if (class_exists("mysqli")) {
	require SYSTEM_ROOT.'/mysqli.php';
} else {
	require SYSTEM_ROOT.'/mysql.php';
}
$mysql_conncet_var = new wmysql();
$m                 = $mysql_conncet_var->con(); //以后直接使用$m->函数()即可操作数据库
require SYSTEM_ROOT.'/option.php';
new option();
define('SYSTEM_URL',option::get('system_url'));
if (option::get('dev') != 1) { error_reporting(0); }
require SYSTEM_ROOT.'/protector.php';
require SYSTEM_ROOT.'/sfc.functions.php';
require SYSTEM_ROOT.'/ui.php';
require SYSTEM_ROOT.'/globals.php';
require SYSTEM_ROOT.'/plugins.php';
?>