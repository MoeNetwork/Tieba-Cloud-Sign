<?php
	define('TOOLKIT_ROOT', dirname(__FILE__));
	define('SYSTEM_ROOT', dirname(__FILE__).'/..');
	define('SYSTEM_PAGE', isset($_REQUEST['mod']) ? strip_tags($_REQUEST['mod']) : 'default');
	define('TOOLKIT_FILE', basename(__FILE__));
	
	if (!file_exists(SYSTEM_ROOT.'/config.php')) {
		echo '未找到 config.php 请确认是否已经将本工具箱放入 setup 文件夹<br/><br/>尝试在此目录寻找失败：'.SYSTEM_ROOT.'/';
		die();
	}

	include TOOLKIT_ROOT.'/msg.php';
	include SYSTEM_ROOT.'/config.php';

	function err() {
		echo '</div></body></html>';
		die;
	}

	switch (SYSTEM_PAGE) {
		case 'login':
			if ($_POST['pw'] == DB_PASSWD) {
				setcookie("toolkit_pw",md5(md5(md5($_POST['pw']))));
			}
			header("Location: ".TOOLKIT_FILE);
			die;
			break;
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>工具箱 - 贴吧云签到</title>
	<script src="../js/jquery.min.js"></script>
	<link rel="stylesheet" href="../css/bootstrap.min.css">
	<script src="../js/bootstrap.min.js"></script>
	<style type="text/css">body { font-family:"微软雅黑","Microsoft YaHei";background: #eee; }</style>
</head>
<body>
<div class="navbar navbar-default" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
      <span class="sr-only">贴吧云签到工具箱</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="toolkit.php">贴吧云签到工具箱</a>
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
	if (!isset($_COOKIE['toolkit_pw']) || $_COOKIE['toolkit_pw'] != md5(md5(md5(DB_PASSWD)))) {
		echo '<h2>请先登录工具箱</h2><br/>如果多次登录仍显示此页面，则表示密码错误<br/><br/><form method="post" action="'.TOOLKIT_FILE.'?mod=login"><div class="input-group"><span class="input-group-addon">数据库密码</span><input type="text" class="form-control" name="pw" placeholder=""></div><br/><input type="submit" class="btn btn-success" value="下一步 >>"></form>';
		err();
	}
	echo $source;
	?><li>错误检查工具</li><br/>
			<li>手动添加账户</li>
</div>
</body>
</html>