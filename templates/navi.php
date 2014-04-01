<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } ?>
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
          <li><a href="index.php">首页</a></li>
          <li><a href="index.php?mod=showtb">设置云签到</a></li>
          <li><a href="index.php?mod=log">签到执行日志</a></li>
          <li><a href="index.php?mod=baiduid">绑定百度账号</a></li>
        </ul>
      </li>
      <li class="dropdown" class="active">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">管理菜单 <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li><a href="index.php?mod=admin:tools">工具箱</a></li>
          <li><a href="index.php?mod=admin:set">全局设置</a></li>
          <li><a href="index.php?mod=admin:plugins">插件管理</a></li>
        </ul>
      </li>
      <?php doAction('navi'); ?>
      <li class="dropdown" class="active">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">关于 <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li><a href="http://www.stus8.com" target="_blank">StusGame GROUP</a></li>
          <li><a href="http://zhizhe8.net" target="_blank">无名智者个人博客</a></li>
        </ul>
      </li>
    </ul>
  </div><!-- /.navbar-collapse -->
</div>
<div style="margin: 0 auto;overflow: hidden;position: relative;width: 95%;">