<?php
/*
Plugin Name: Flat UI
Version: 1.0
Plugin URL: http://zhizhe8.net
Description: 提供Metro风格云签到
Author: 无名智者
Author Email: kenvix@vip.qq.com
Author URL: http://zhizhe8.net
For: V3.0+
*/

if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

function wmzz_flatui_core() {
	echo '<link rel="stylesheet" href="'.SYSTEM_URL.'plugins/wmzz_flatui/css/flat-ui.min.css">';
}

addAction('header','wmzz_flatui_core');