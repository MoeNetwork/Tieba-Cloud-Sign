<?php
/**
 * 自动加载
 */
define('SYSTEM_FN','百度贴吧云签到');
define('SYSTEM_VER','2.2');
define('SYSTEM_ROOT',dirname(__FILE__));
define('SYSTEM_PAGE',isset($_REQUEST['mod']) ? strip_tags($_REQUEST['mod']) : 'default');
$PluginHooks = array();
$today       = date("Y-m-d");
header("content-type:text/html; charset=utf-8");
require SYSTEM_ROOT.'/setup/msg.php';
require SYSTEM_ROOT.'/config.php';
require SYSTEM_ROOT.'/mysql_autoload.php';
require SYSTEM_ROOT.'/lib/PHPMailerAutoload.php';
require SYSTEM_ROOT.'/class.php';
if (option::get('dev') != 1) {
	define('SYSTEM_DEV', false);
} else {
	define('SYSTEM_DEV', true);
}
new option();
define('SYSTEM_URL',option::get('system_url'));
error_reporting(0);
require SYSTEM_ROOT.'/sfc.functions.php';
require SYSTEM_ROOT.'/ui.php';
require SYSTEM_ROOT.'/globals.php';
if (option::get('protector') == 1) {
	require SYSTEM_ROOT.'/protector.php';
}
require SYSTEM_ROOT.'/plugins.php';
?>