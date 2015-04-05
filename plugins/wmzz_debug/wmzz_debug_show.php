<?php
/**
 * 插件展示页面
 * 访问 index.php?plugin=插件名
 * 会自动加载此文件
 */
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