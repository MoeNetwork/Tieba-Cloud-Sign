<?php
/**
 * 加载核心
 * HELLO GAY!
 */
define('SYSTEM_FN','百度贴吧云签到');
define('SYSTEM_VER','3.9');
define('SYSTEM_ROOT',dirname(__FILE__));
define('SYSTEM_PAGE',isset($_REQUEST['mod']) ? strip_tags($_REQUEST['mod']) : 'default');
define('SUPPORT_URL', 'http://support.zhizhe8.net/tcs/');
header("content-type:text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai'); 
require SYSTEM_ROOT.'/lib/msg.php';
require SYSTEM_ROOT.'/lib/class.E.php';
require SYSTEM_ROOT.'/config.php';
require SYSTEM_ROOT.'/lib/mysql_autoload.php';
require SYSTEM_ROOT.'/lib/class.smtp.php';
require SYSTEM_ROOT.'/lib/class.zip.php';
require SYSTEM_ROOT.'/lib/reg.php';
define('SYSTEM_URL',option::get('system_url'));
define('SYSTEM_NAME', option::get('system_name'));
//版本修订号
define('SYSTEM_REV',option::get('core_revision'));
//压缩包链接
define('UPDATE_SERVER_OSCGIT','https://git.oschina.net/kenvix/Tieba-Cloud-Sign/repository/archive?ref=master');
define('UPDATE_SERVER_GITHUB','https://github.com/kenvix/Tieba-Cloud-Sign/archive/master.zip');
define('UPDATE_SERVER_CODING','https://coding.net/u/kenvix/p/Tieba-Cloud-Sign/git/archive/master');
define('UPDATE_SERVER_GITCAFE','https://gitcafe.com/kenvix/Tieba-Cloud-Sign/archiveball/master/zip');
//压缩包内文件夹名
define('UPDATE_FNAME_OSCGIT','Tieba-Cloud-Sign');
define('UPDATE_FNAME_GITHUB','Tieba-Cloud-Sign-master');
define('UPDATE_FNAME_CODING','');
define('UPDATE_FNAME_GITCAFE','Tieba-Cloud-Sign');
//压缩包解压路径
define('UPDATE_CACHE',SYSTEM_ROOT.'/setup/update_cache/');
require SYSTEM_ROOT.'/lib/sfc.functions.php';
require SYSTEM_ROOT.'/lib/ui.php';
require SYSTEM_ROOT.'/lib/globals.php';
if (option::get('protector') == 1) {
	require SYSTEM_ROOT.'/lib/protector.php';
}
require SYSTEM_ROOT.'/lib/plugins.php';