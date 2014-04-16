<?php
define('SQLMODE', 'mysqli');
if (class_exists("mysqli") && SQLMODE != 'mysql') {
	require SYSTEM_ROOT.'/mysqli.php';
} else {
	require SYSTEM_ROOT.'/mysql.php';
}
$mysql_conncet_var = new wmysql();
$m                 = $mysql_conncet_var->con(); //以后直接使用$m->函数()即可操作数据库
?>