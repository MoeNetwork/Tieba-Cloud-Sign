<?php
define('SQLMODE', 'mysqli');
if (class_exists("mysqli") && SQLMODE != 'mysql') {
	require SYSTEM_ROOT.'/lib/class.mysqli.php';
} else {
	require SYSTEM_ROOT.'/lib/class.mysql.php';
}
$m = new wmysql(); //以后直接使用$m->函数()即可操作数据库
?>