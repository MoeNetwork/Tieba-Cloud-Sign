<?php

//特别警告：禁止使用记事本编辑！

////////////////////////////以下选项只需在使用MySQL时填写////////////////////////////
//MySQL 数据库地址，普通主机一般为localhost
define('DB_HOST', '127.0.0.1');
//MySQL 数据库用户名
define('DB_USER', 'root');
//MySQL 数据库密码
define('DB_PASSWD', '');
//MySQL 数据库名称
define('DB_NAME', 'tiebacloud');
//MySQL 启用SSL连接，如需启用请将值改为1
define('DB_SSL', 0);

////////////////////////////以下选项使用任何数据库都需填写////////////////////////////
//数据库前缀，建议保持默认
define('DB_PREFIX', 'tc_');

///////////////////////////////////////其他设置///////////////////////////////////////
//停用CSRF防御
//说明在 https://github.com/MoeNetwork/Tieba-Cloud-Sign/wiki/%E5%85%B3%E4%BA%8E%E4%BA%91%E7%AD%BE%E5%88%B0CSRF%E9%98%B2%E5%BE%A1
define('ANTI_CSRF', true);

//加密用盐，留空为不使用
define('SYSTEM_SALT', '');
