<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }

switch (SYSTEM_PAGE) {
	case 'baiduid':
		template('baiduid');
		break;
	case 'showtb':
		template('showtb');
		break;
	case 'log':
		template('log');
		break;
	case 'set':
		template('set');
		break;
	case 'admin:set':
		template('admin-set');
		break;
	case 'admin:tools':
		template('admin-tools');
		break;
	case 'admin:users':
		template('admin-users');
		break;
	case 'admin:plugins':
		template('admin-plugins');
		break;
	case 'admin:cron':
		template('admin-cron');
		break;
	case 'admin:update':
		template('admin-update');
		break;
	case 'admin:setplug':
		$plug = strip_tags($_GET['plug']);
		if (file_exists(SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_setting.php') && in_array($_GET['plug'], unserialize(option::get('actived_plugins')))) {
			require_once SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_setting.php';
		} else {
			echo '<b>插件设置页面不存在</b>';
		}
		echo '<br/><br/><br/><br/><br/>'.SYSTEM_FN.' V'.SYSTEM_VER.' By <a href="http://zhizhe8.net" target="_blank">无名智者</a>';
		break;
	default:
		template('index');
		break;
}
?>