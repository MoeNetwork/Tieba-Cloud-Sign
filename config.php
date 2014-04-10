<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }

//BAE/SAE的数据库地址，用户名，密码请参考相关文档

//MySQL 数据库地址，一般为localhost
define('DB_HOST','localhost');
//MySQL 数据库用户名
define('DB_USER','root');
//MySQL 数据库密码
define('DB_PASSWD','000000');
//MySQL 数据库名称(存放百度贴吧云签到的)
define('DB_NAME','tiebacloud');
//MySQL 数据库前缀，建议保持默认
define('DB_PREFIX','tc_');