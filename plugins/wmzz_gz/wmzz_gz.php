<?php
/*
Plugin Name: GZip 压缩页面
Version: 1.0
Plugin URL: http://zhizhe8.net
Description: 将页面直接 GZip 压缩后传输给用户，可大幅减少流量使用，增加加载速度
Author: 无名智者
Author Email: kenvix@vip.qq.com
Author URL: http://zhizhe8.net
For: 不限
*/
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

if(extension_loaded('zlib')) {
	ob_start('ob_gzhandler');
}
?>