<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }

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

/* UCenter设置，说明：
UC_CONNECT	 连接 UCenter 的方式
mysql:MySQL 方式，空:远程方式
UC_DBHOST	UCenter 数据库主机
UC_DBUSER	UCenter 数据库用户名
UC_DBPW	UCenter 数据库密码
UC_DBNAME	UCenter 数据库名称
UC_DBCHARSET	UCenter 数据库字符集
UC_DBTABLEPRE	UCenter 数据库表前缀
UC_DBCONNECT	UCenter 数据库持久连接 0=关闭, 1=打开
UC_KEY	与 UCenter 的通信密钥, 要与 UCenter 保持一致
UC_API	UCenter 服务端的 URL 地址
UC_IP	UCenter 的 IP, 当 UC_CONNECT 为非 mysql 方式时, 并且当前应用服务器解析域名有问题时, 请设置此值
UC_CHARSET	UCenter 的字符集
UC_APPID	当前应用的 ID
*/
define('UC_CONNECT','mysql');
define('UC_DBHOST',DB_HOST);
define('UC_DBUSER',DB_USER);
define('UC_DBPW',DB_PASSWD);
define('UC_DBNAME','ultrax');
define('UC_DBCHARSET','utf-8');
define('UC_DBTABLEPRE','pre_ucenter_');
define('UC_DBCONNECT','1');
define('UC_KEY','DG1WER51G1G815F21F51384E5R');
define('UC_API','http://localhost/dz/uc_server/');
define('UC_APPID','2');