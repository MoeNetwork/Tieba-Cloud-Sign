<?php
/*
Plugin Name: 调试信息
Version: 1.0
Plugin URL: http://zhizhe8.net
Description: 在底部显示调试信息
Author: 无名智者
Author Email: kenvix@vip.qq.com
Author URL: http://zhizhe8.net
For: 不限
*/
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

function wmzz_debug_system() {
	echo 'fuck';
}

addAction('footer','wmzz_debug_system');
?>