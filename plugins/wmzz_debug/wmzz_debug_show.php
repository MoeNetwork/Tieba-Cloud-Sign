<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

if (ROLE == 'admin') {
	loadhead('PHPInfo');
	echo '<style>body { font-family: "微软雅黑","Microsoft Yahei" !important;</style><div id="phpinfo_display">';
	phpinfo();
	echo '</div>';
	loadfoot();
} else {
	msg('权限不足');
}
?>