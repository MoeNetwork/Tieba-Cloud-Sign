<?php
define('SYSTEM_FN','百度贴吧云签到');
define('SYSTEM_VER','1.0');
define('SYSTEM_ROOT',dirname(__FILE__));
define('SYSTEM_PAGE',isset($_REQUEST['mod']) ? strip_tags($_REQUEST['mod']) : 'default');
header("content-type:text/html; charset=utf-8");
require SYSTEM_ROOT.'/msg.php';

if (file_exists(SYSTEM_ROOT.'/install.lock')) {
	msg('错误：安装锁定，请删除以下文件后再安装：<br/><br/>/setup/install.lock');
}

	echo '<!DOCTYPE html><html><head>';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	echo '<title>安装向导 - '.SYSTEM_FN.'</title><meta name="generator" content="God.Kenvix\'s Blog (http://zhizhe8.net) and StusGame GROUP (http://www.stus8.com)" /></head><body>';
	echo '<script src="../js/jquery.min.js"></script>';
	echo '<link rel="stylesheet" href="../css/bootstrap.min.css">';
	echo '<script src="../js/bootstrap.min.js"></script>';
	echo '<style type="text/css">body { font-family:"微软雅黑","Microsoft YaHei";background: #eee; }</style>';

?>
<div class="navbar navbar-default" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
      <span class="sr-only">贴吧云签到</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="install.php">贴吧云签到安装</a>
  </div>
  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <ul class="nav navbar-nav">
          <li><a href="http://www.stus8.com" target="_blank">StusGame GROUP</a></li>
          <li><a href="http://zhizhe8.net" target="_blank">无名智者个人博客</a></li>
    </ul>
  </div><!-- /.navbar-collapse -->
</div>
<div style="width:90%;margin: 0 auto;overflow: hidden;position: relative;">
<?php
	if (!isset($_GET['step']) || $_GET['step'] == 0) {
		echo '<h2>准备安装</h2><br/><h4>你是在 BAE/SAE/JAE 上使用本程序吗？</h4><br/>';
		echo '<li><a href="install.php?step=2">不，我不是</a></li><br/>';
		echo '<li><a href="install.php?step=100">是的，我是</a></li>';
	} else {
		switch (strip_tags($_GET['step'])) {
			case '100':
				echo '<h2>(BAE/SAE/JAE) 手动修改配置</h2><br/>请按照注释修改 /<b>config.php</b><br/><br/><font color="red">警告：</font>切勿使用记事本修改；文件编码应该为 UTF-8 ( 无BOM )';
				echo '<br/><br/><div class="alert alert-success"><pre>
&lt?php if (!defined(\'SYSTEM_ROOT\')) { die(\'Insufficient Permissions\'); }

//BAE/SAE/JAE的数据库地址，用户名，密码请参考相关文档

//MySQL 数据库地址，普通主机一般为localhost
define(\'DB_HOST\',\'localhost\');
//MySQL 数据库用户名
define(\'DB_USER\',\'root\');
//MySQL 数据库密码
define(\'DB_PASSWD\',\'000000\');
//MySQL 数据库名称(存放百度贴吧云签到的)
define(\'DB_NAME\',\'tiebacloud\');
//MySQL 数据库前缀，建议保持默认
define(\'DB_PREFIX\',\'tc_\');
</pre></div>';
				echo '<b>参考文档：</b>BAE | SAE | JAE';
				echo '<br/><br/><br/><br/>修改完成后，请点击下一步<br/><br/><input type="button" onclick="location = \'install.php?step=2&bae\'" class="btn btn-success" value="下一步 >>">';
				break;

			case '2':
				echo '<h2>设置所需信息</h2><br/>';
				echo '<h4>数据库信息</h4><br/>';
				echo '<form action="install.php?step=3" method="post">';
				echo '<b>提示：</b>如果您的主机没有明确给出数据库信息 (例如SAE给出的是常量) 并且您已经写好了 config.php ，请选择 [ <b>自动获得数据库配置信息</b> ] 为 <b>是</b><input type="hidden" name="isbae" value="1"><br/><br/>';
				echo '<div class="input-group"><span class="input-group-addon">自动获得数据库配置信息</span><select name="from_config" class="form-control"  onchange="if(this.value == \'0\') { $(\'#db_config\').show(); } else { $(\'#db_config\').hide(); }"><option value="0">否</option><option value="1">是</option></select></div><br/>';
				echo '<div id="db_config"><div class="input-group"><span class="input-group-addon">数据库地址</span><input type="text" class="form-control" name="dbhost" value="localhost" placeholder=""></div><br/>';
				echo '<div class="input-group"><span class="input-group-addon">数据库用户名</span><input type="text" class="form-control" name="dbuser" placeholder=""></div><br/>';
				echo '<div class="input-group"><span class="input-group-addon">数据库密码</span><input type="text" class="form-control" name="dbpw" placeholder=""></div><br/>';
				echo '<div class="input-group"><span class="input-group-addon">数据库名称</span><input type="text" class="form-control" name="dbname" placeholder=""></div><br/>';
				echo '<div class="input-group"><span class="input-group-addon">数据表前缀</span><input type="text" class="form-control" name="dbprefix" value="tc_" placeholder=""></div><br/>';
				echo '</div><h4>站点创始人信息</h4><br/>';
				echo '<div class="input-group"><span class="input-group-addon">创始人用户名</span><input type="text" required class="form-control" name="user" placeholder=""></div><br/>';
				echo '<div class="input-group"><span class="input-group-addon">创始人邮箱</span><input type="email" required class="form-control" name="mail" placeholder=""></div><br/>';
				echo '<div class="input-group"><span class="input-group-addon">创始人密码</span><input type="password" required class="form-control" name="pw" placeholder=""></div><br/>';
				echo '<br/><br/><input type="submit" class="btn btn-success" value="下一步 >>"></form>';
				break;

			case '3':
				$errorhappen = '';
				preg_match("/^.*\//", $_SERVER['SCRIPT_NAME'], $sysurl);
				$sql  = str_ireplace('{VAR-PREFIX}', $_POST['dbprefix'], file_get_contents(SYSTEM_ROOT.'/install.template.sql'));
				$sql  = str_ireplace('{VAR-DB}', $_POST['dbname'], $sql);
				$sql  = str_ireplace('{VAR-SYSTEM-URL}', 'http://' . $_SERVER['HTTP_HOST'] . str_ireplace('setup/', '', $sysurl[0]), $sql);
				$sql .= "\n"."INSERT INTO `{$_POST['dbname']}`.`{$_POST['dbprefix']}users` (`id`, `name`, `pw`, `email`, `role`, `t`, `ck_bduss`, `options`) VALUES (NULL, '{$_POST['user']}', '".md5(md5(md5($_POST['pw'])))."', '{$_POST['mail']}', 'admin', 'tieba', '', NULL);";
				if (!isset($_POST['isbae'])) {
					$write_data = '<?php if (!defined(\'SYSTEM_ROOT\')) { die(\'Insufficient Permissions\'); }
//特别警告：请勿使用记事本编辑！！！如果你正在使用记事本并且还没有保存，赶紧关掉！！！
//如果你已经用记事本保存了，请立即下载最新版的云签到包解压并覆盖本文件

//BAE/SAE/JAE的数据库地址，用户名，密码请参考相关文档

//MySQL 数据库地址，普通主机一般为localhost
//MySQL 数据库地址，普通主机一般为localhost
define(\'DB_HOST\',\''.$_POST['dbhost'].'\');
//MySQL 数据库用户名
define(\'DB_USER\',\''.$_POST['dbuser'].'\');
//MySQL 数据库密码
define(\'DB_PASSWD\',\''.$_POST['dbpw'].'\');
//MySQL 数据库名称(存放百度贴吧云签到的)
define(\'DB_NAME\',\''.$_POST['dbname'].'\');
//MySQL 数据库前缀，建议保持默认
define(\'DB_PREFIX\',\''.$_POST['dbprefix'].'\');';
					if(file_put_contents(SYSTEM_ROOT.'/../config.php', $write_data) <= 0) {
						$errorhappen .= '<b>无法写入配置文件 config.php ，请打开本程序根目录的 config.php 并按照注释修改它</b>';
					}
				}
				if (class_exists("mysqli")) {
					if($_POST['from_config'] == 1) {
						require SYSTEM_ROOT.'/../config.php';
						$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
					} else {
						$conn = new mysqli($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpw'], $_POST['dbname']);
					}
					if ($conn->connect_error) {
						switch ($conn->connect_errno) {
							case 1044:
							case 1045:
								msg("连接数据库失败，数据库用户名或密码错误。错误编号：" . $conn->connect_errno);
								break;

			                case 1049:
								msg("连接数据库失败，未找到您填写的数据库。错误编号：1049");
								break;

							case 2003:
								msg("连接数据库失败，数据库端口错误。错误编号：2003");
								break;

							case 2005:
								msg("连接数据库失败，数据库地址错误或者数据库服务器不可用。错误编号：2005");
								break;

							case 2006:
								msg("连接数据库失败，数据库服务器不可用。错误编号：2006");
								break;

							default :
								msg("连接数据库失败，请检查数据库信息。错误编号：" . $conn->connect_errno);
								break;
						}
					}
					$conn->set_charset('utf8');
					$conn->multi_query($sql);
				} else {
					$errorhappen .= '由于您的服务器不支持MySQLi，请手动复制下列语句到数据库管理软件(例如phpmyadmin)并运行：<div class="alert alert-success"><pre>'.$sql.'</pre><br/><br/>';
				}

				if (!empty($errorhappen)) {
					echo '<h2>请手动安装</h2><br/>' . $errorhappen;
					echo '完成上述操作后，请点击下一步<br/><br/><input type="button" onclick="location = \'install.php?step=4\'" class="btn btn-success" value="下一步 >>">';
				} else {
					echo '<meta http-equiv="refresh" content="0;url=install.php?step=4"><h2>请稍候</h2><br/>正在完成安装...';
				}
				break;

			case '4':
				echo '<h2>安装完成</h2><br/>恭喜你，安装已经完成<br/><br/>请添加一个计划任务，文件为本程序根目录下的 <b>do.php</b><br/><br/><input type="button" onclick="location = \'../index.php\'" class="btn btn-success" value="进入我的云签到 >>">';
				break;

			default:
				msg('未定义操作');
				break;
		}
	}
?>
</div>