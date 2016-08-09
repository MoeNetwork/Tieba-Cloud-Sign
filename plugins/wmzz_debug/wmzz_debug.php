<?php
/**
 * 调试信息插件
 * @author Kenvix
 * 一旦插件被激活，此文件将被include
 * 获取开发文档：https://git.oschina.net/kenvix/Tieba-Cloud-Sign/wikis/
 */
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

function wmzz_debug_system1() {
	$GLOBALS['wmzz_debug_time'] = microtime();
}

addAction('header','wmzz_debug_system1');

function wmzz_debug_phpinfo() {
	if(ROLE == 'admin')
		echo '<li class="list-group-item"><a href="index.php?plugin=wmzz_debug" target="_blank">查看更多服务器信息 [ PHPInfo ]</a></li>';
}

addAction('index_p_3','wmzz_debug_phpinfo');

function wmzz_debug_system2() {
	global $m;
	echo '<br/>调试信息：执行 MySQL 查询 '. $m->queryCount . ' 次，PHP 运行耗时 '. round(microtime() - $GLOBALS['wmzz_debug_time'],9) . ' 秒';
}

addAction('footer','wmzz_debug_system2');
