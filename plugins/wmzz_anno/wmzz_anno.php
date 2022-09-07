<?php
/*
Plugin Name: 公告栏插件
Version: 1.6
Plugin URL: http://zhizhe8.net
Description: 在 首页 显示公告栏
Author: 无名智者
Author Email: kenvix@vip.qq.com
Author URL: http://zhizhe8.net
For: V3.4+
*/
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

function wmzz_anno_show() {
	global $m;
	$s = option::get('wmzz_anno_set');
	if(!empty($s)) {
		$y = '';
		$x = explode("\n", $s);
		foreach ($x as $value) {
			$y .= $value.'<br/>';
		}
		echo str_replace('{$anno}', $y, option::get('wmzz_anno_tpl'));
	}
}

function wmzz_anno_addaction_navi() {
	?>
	<li><a href="index.php?mod=admin:setplug&plug=wmzz_anno"><span class="glyphicon glyphicon-bullhorn"></span> 公告栏管理</a></li>
	<?php
}

$wmzz_anno_doa = unserialize(option::get('wmzz_anno_doa'));

foreach ($wmzz_anno_doa as $wmzz_anno_doa_v) {
	addAction($wmzz_anno_doa_v,'wmzz_anno_show');
}
addAction('navi_3','wmzz_anno_addaction_navi');
?>