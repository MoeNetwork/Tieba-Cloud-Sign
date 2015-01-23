<?php
/**
 * 加载核心
 * HELLO GAY!
 */
define('SYSTEM_FN','百度贴吧云签到');
define('SYSTEM_VER','3.8');
define('SYSTEM_ROOT',dirname(__FILE__));
define('SYSTEM_PAGE',isset($_REQUEST['mod']) ? strip_tags($_REQUEST['mod']) : 'default');
define('SUPPORT_URL', 'http://support.zhizhe8.net/tcs/');
header("content-type:text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai'); 
require SYSTEM_ROOT.'/setup/msg.php';
require SYSTEM_ROOT.'/config.php';
require SYSTEM_ROOT.'/lib/mysql_autoload.php';
require SYSTEM_ROOT.'/lib/class.smtp.php';
require SYSTEM_ROOT.'/lib/reg.php';
define('SYSTEM_URL',option::get('system_url'));
define('SYSTEM_NAME', option::get('system_name'));
require SYSTEM_ROOT.'/lib/sfc.functions.php';
require SYSTEM_ROOT.'/lib/ui.php';
require SYSTEM_ROOT.'/lib/globals.php';
if (option::get('protector') == 1) {
	require SYSTEM_ROOT.'/lib/protector.php';
}
require SYSTEM_ROOT.'/lib/plugins.php';
