<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足'); }

if(extension_loaded('zlib')) {
	echo '<div class="alert alert-success">环境检查：您的服务器支持 GZip 压缩，可以正常使用本插件</div>';
} else {
	echo '<div class="alert alert-danger">环境检查：您的服务器不支持 GZip 压缩，不能使用本插件</div>';
}

?>