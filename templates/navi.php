<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

function system_tc_tempfunction_1() {
  echo <<< DATA
  <li><a href="index.php">首页</a></li>
  <li><a href="index.php?mod=set">个人设置</a></li>
  <li><a href="index.php?mod=showtb">设置云签到</a></li>
  <li><a href="index.php?mod=log">签到执行日志</a></li>
  <li><a href="index.php?mod=baiduid">绑定百度账号</a></li>
DATA;
}

function system_tc_tempfunction_2() {
  echo <<< DATA
  <li><a href="index.php?mod=admin:tools">工具箱</a></li>
  <li><a href="index.php?mod=admin:set">全局设置</a></li>
  <li><a href="index.php?mod=admin:users">用户管理</a></li>
  <li><a href="index.php?mod=admin:cron">计划任务</a></li>
  <li><a href="index.php?mod=admin:plugins">插件管理</a></li>
DATA;
}

addAction('navi_1','system_tc_tempfunction_1');
addAction('navi_2','system_tc_tempfunction_2');
addAction('navi_7','system_tc_tempfunction_1');
addAction('navi_8','system_tc_tempfunction_2');

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

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <ul class="nav navbar-nav">
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">功能菜单 <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <?php doAction('navi_1'); ?>
        </ul>
      </li>
      <?php if (ROLE == 'admin') { ?>
      <li class="dropdown" class="active">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">管理菜单 <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <?php doAction('navi_2'); ?>
        </ul>
      </li>
      <li class="dropdown" class="active">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">插件菜单 <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li><a href="index.php?mod=admin:plugins">插件管理</a></li>
          <li><a href="http://www.stus8.com/forum.php?mod=forumdisplay&fid=163&filter=sortid&sortid=13" target="_blank">插件商城</a></li>
          <?php doAction('navi_3'); ?>
        </ul>
      </li>
      <?php } doAction('navi_4'); ?>
      <li class="dropdown" class="active">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">关于 <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li><a href="http://www.stus8.com" target="_blank">StusGame GROUP</a></li>
          <li><a href="http://zhizhe8.net" target="_blank">无名智者个人博客</a></li>
          <?php doAction('navi_5'); ?>
        </ul>
      </li>
    </ul>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="index.php?mod=admin:logout">退出登录</a></li>
      <?php doAction('navi_6'); ?>
    </ul>
  </div><!-- /.navbar-collapse -->
</div>

<!-- 侧边导航，宽屏设备可见 -->
<div class="container bs-docs-container">
      <div class="row">
        <div class="col-md-3">
          <div class="bs-sidebar hidden-print visible-lg visible-md" role="complementary" >
            <ul class="nav bs-sidenav">                 
              <li>
               <?php doAction('navi_7'); if (ROLE == 'admin') { ?>
               <br/>
               <?php doAction('navi_8'); ?>
               <br/>
               <li><a href="http://www.stus8.com/forum.php?mod=forumdisplay&fid=163&filter=sortid&sortid=13" target="_blank">插件商城</a></li>
               <?php doAction('navi_9'); } ?>
              </li>
            </ul>
          </div>
        </div>
<div class="col-md-9" role="main">