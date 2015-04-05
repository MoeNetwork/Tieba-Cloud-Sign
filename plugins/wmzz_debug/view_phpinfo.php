<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
/**
 * 已经定义的插件自定义页面
 * 用法参见 wmzz_debug_desc.php
 */
if (ROLE == 'admin') {
	phpinfo();
} else {
	msg('权限不足');
}