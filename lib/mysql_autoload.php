<?php
define('SQLMODE', 'mysqli');
if (class_exists("mysqli") && SQLMODE != 'mysql') {
	require SYSTEM_ROOT.'/lib/class.mysqli.php';
} else {
	require SYSTEM_ROOT.'/lib/class.mysql.php';
}
require SYSTEM_ROOT.'/lib/class.sql.php';
$m = new sql(DB_HOST, DB_USER, DB_PASSWD, DB_NAME); //以后直接使用$m->函数()即可操作数据库