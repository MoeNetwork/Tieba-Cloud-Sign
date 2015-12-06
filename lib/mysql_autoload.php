<?php
/**
 * MySQL 自动按需加载和配置
 * @author Kenvix
 */

/**
 * 数据库连接模式
 * mysqli 或 mysql
 */
define('SQLMODE', 'mysqli');
/**
 * 是否开启数据库长连接
 * bool true=开启 | false=关闭
 */
define('LONGSQL',false);

if (class_exists("mysqli") && SQLMODE != 'mysql') {
	require SYSTEM_ROOT.'/lib/class.mysqli.php';
} else {
	require SYSTEM_ROOT.'/lib/class.mysql.php';
}
require SYSTEM_ROOT.'/lib/class.S.php';
$m = new S(DB_HOST, DB_USER, DB_PASSWD, DB_NAME, LONGSQL); //以后直接使用$m->函数()即可操作数据库