<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

if (ROLE == 'admin') {
	phpinfo();
} else {
	msg('权限不足');
}
?>