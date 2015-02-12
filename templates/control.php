<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }
global $i;

switch ($i['mode'][0]) {
	case 'baiduid':
		template('baiduid');
		break;
	case 'showtb':
		template('showtb');
		break;
	case 'log':
		//兼容老版本插件，重定向到showtb
		Clean();
		ReDirect('index.php?mod=showtb');
		break;
	case 'set':
		template('set');
		break;
	case 'admin':
		if (ROLE != 'admin') msg('权限不足！');
		switch($i['mode'][1]) {
			case 'set':
				template('admin-set');
				break;
			case 'tools':
				template('admin-tools');
				break;
			case 'users':
				template('admin-users');
				break;
			case 'plugins':
				template('admin-plugins');
				break;
			case 'cron':
				template('admin-cron');
				break;
			case 'update':
				template('admin-update');
				break;
			case 'setplug':
				$plug = strip_tags($_GET['plug']);
				if (file_exists(SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_setting.php') && in_array($_GET['plug'], $i['plugins']['actived'])) {
					require_once SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_setting.php';
				} else {
					echo '<b>插件设置页面不存在</b>';
				}
				echo '<br/><br/><br/><br/><br/>'.SYSTEM_FN.' V'.SYSTEM_VER.' By <a href="http://zhizhe8.net" target="_blank">无名智者</a>';
				break;
			case 'stat':
				template('admin-stat');
				break;
		}
		break;
	default:
		template('index');
		break;
}
?>