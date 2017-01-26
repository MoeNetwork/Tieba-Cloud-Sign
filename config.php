<?php 

//特别警告：禁止使用记事本编辑！

////////////////////////////以下选项只需在使用MySQL时填写////////////////////////////
//MySQL 数据库地址，普通主机一般为localhost
define('DB_HOST','127.0.0.1');
//MySQL 数据库用户名
define('DB_USER','root');
//MySQL 数据库密码
define('DB_PASSWD','');
//MySQL 数据库名称
define('DB_NAME','tiebacloud');

////////////////////////////以下选项使用任何数据库都需填写////////////////////////////
//数据库前缀，建议保持默认
define('DB_PREFIX','tc_');

///////////////////////////////////////其他设置///////////////////////////////////////
//停用CSRF防御
//说明在 http://git.oschina.net/kenvix/Tieba-Cloud-Sign/wikis/关于云签到CSRF防御
define('ANTI_CSRF',true);

//加密用盐，留空为不使用
define('SYSTEM_SALT','');