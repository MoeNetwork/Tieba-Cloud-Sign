<?php
define('SYSTEM_FN','百度贴吧云签到');
define('SYSTEM_VER','1.0');
define('SYSTEM_ROOT',dirname(__FILE__));
define('SYSTEM_PAGE',isset($_REQUEST['mod']) ? strip_tags($_REQUEST['mod']) : 'default');

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
    <a class="navbar-brand" href="index.php">贴吧云签到</a>
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
		echo '<h2>准备安装</h2><br/><h4>你是在 BAE/SAE 上使用本程序吗？</h4><br/>';
		echo '<li><a href="install.php?step=1">不，我不是</a></li><br/>';
		echo '<li><a href="install.php?step=1">是的，我是</a></li>';
	}
?>
</div>