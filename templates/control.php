<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }
/*
if ($x['']) {
	# code...
}
*/
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
	case 'admin:set':
		template('admin-set');
		break;
	case 'admin:tools':
		template('admin-tools');
		break;
	case 'admin:plugins':
		template('admin-plugins');
		break;
	default:
		template('index');
		break;
}
?>