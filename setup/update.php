<?php
if (empty($_COOKIE['uid']) || empty($_COOKIE['pwd'])) {
  header('Location: ../');
  die;
}
define('SYSTEM_NO_ERROR', true);
define('SYSTEM_DO_NOT_REMIND_INSTALL', true);
require '../init.php';
global $i;
if (ROLE != 'admin') {
  msg('您需要先登录旧版本的云签到，才能继续升级');
}
$x = scandir(dirname(__FILE__));
$v = '';
foreach ($x as $value) {
  if ($value == '.' || $value == '..') {
    continue;
  }
  preg_match('/update(.*)to(.*).php/', $value, $g);
  if (!isset($g[2])) {
    continue;
  }
  if (SYSTEM_VER > $g[2] || SYSTEM_VER == $i['opt']['core_version']) {
    $other = '[ <font color="red">已安装</font> ] ';
  } else {
    $other = '';
  }
  $v .= "<li>{$other}<a href=\"{$value}\" onclick=\"return confirm('你确定要升级到此版本吗？');\">从 {$g[1]} 升级到 {$g[2]} [ {$value} ]</a></li><br/>";
}
?>
<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>软件升级 - 百度贴吧云签到</title><meta name="generator" content="God.Kenvix's Blog (http://zhizhe8.net) and StusGame GROUP (http://www.stus8.com)" /></head><body><script src="../source/js/jquery.min.js"></script><link rel="stylesheet" href="../source/css/bootstrap.min.css"><script src="../source/js/bootstrap.min.js"></script><style type="text/css">body { font-family:"微软雅黑","Microsoft YaHei";background: #eee; }</style><div class="navbar navbar-default" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
      <span class="sr-only">贴吧云签到</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="update.php">贴吧云签到升级</a>
  </div>
  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <ul class="nav navbar-nav">
          <li><a href="http://www.stus8.com" target="_blank">StusGame GROUP</a></li>
    </ul>
  </div><!-- /.navbar-collapse -->
</div>
<div style="width:90%;margin: 0 auto;overflow: hidden;position: relative;">
<h2>请选择要升级的版本</h2><b>提示：</b>请务必逐步升级<br/><br/>
<?php echo $v ?></div>