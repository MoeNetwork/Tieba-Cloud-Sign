<?php
define('SYSTEM_FN','百度贴吧云签到');
define('SYSTEM_VER','1.0');
define('SYSTEM_ROOT',dirname(__FILE__));
define('SYSTEM_PAGE',isset($_REQUEST['mod']) ? strip_tags($_REQUEST['mod']) : 'default');

require SYSTEM_ROOT.'/msg.php';

if (file_exists(SYSTEM_ROOT.'/install.lock')) {
	msg('错误：安装锁定，请删除以下文件后再安装：<br/><br/>/setup/install.lock');
}
?>