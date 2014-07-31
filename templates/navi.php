<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
global $i;
?>
<div class="navbar navbar-default" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
      <span class="sr-only"><?php echo SYSTEM_NAME ?></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="index.php"><?php echo SYSTEM_NAME ?></a>
  </div>

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <ul class="nav navbar-nav">
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-list-alt"></span> 功能菜单 <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li><a href="index.php"><span class="glyphicon glyphicon-home"></span> 首页</a></li>
          <li><a href="index.php?mod=set"><span class="glyphicon glyphicon-wrench"></span> 个人设置</a></li>
          <li><a href="index.php?mod=showtb"><span class="glyphicon glyphicon-cloud"></span> 设置云签到</a></li>
          <li><a href="index.php?mod=log"><span class="glyphicon glyphicon-calendar"></span> 签到执行日志</a></li>
          <li><a href="index.php?mod=baiduid"><span class="glyphicon glyphicon-link"></span> 百度账号管理</a></li>
          <?php doAction('navi_1'); ?>
        </ul>
      </li>
      <?php if (ROLE == 'admin') { ?>
      <li class="dropdown" class="active">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-th-list"></span> 管理菜单 <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li><a href="index.php?mod=admin:tools"><span class="glyphicon glyphicon-briefcase"></span> 工具箱</a></li>
          <li><a href="index.php?mod=admin:set"><span class="glyphicon glyphicon-cog"></span> 全局设置</a></li>
          <li><a href="index.php?mod=admin:users"><span class="glyphicon glyphicon-user"></span> 用户管理</a></li>
          <li><a href="index.php?mod=admin:update"><span class="glyphicon glyphicon-open"></span> 检查更新</a></li>
          <li><a href="index.php?mod=admin:stat"><span class="glyphicon glyphicon-stats"></span> 统计信息</a></li>
          <li><a href="index.php?mod=admin:cron"><span class="glyphicon glyphicon-time"></span> 计划任务</a></li>
          <li><a href="index.php?mod=admin:plugins"><span class="glyphicon glyphicon-tasks"></span> 插件管理</a></li>
          <?php doAction('navi_2'); ?>
        </ul>
      </li>
      <li class="dropdown" class="active">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-th"></span> 插件菜单 <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li><a href="index.php?mod=admin:plugins"><span class="glyphicon glyphicon-tasks"></span> 插件管理</a></li>
          <li><a href="http://www.stus8.com/forum.php?mod=forumdisplay&fid=163&filter=sortid&sortid=13" target="_blank"><span class="glyphicon glyphicon-shopping-cart"></span> 插件商城</a></li>
          <?php doAction('navi_3'); ?>
        </ul>
      </li>
      <?php } doAction('navi_4'); ?>
      <li class="dropdown" class="active">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-book"></span> 关于 <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li><a href="http://www.stus8.com" target="_blank">StusGame GROUP</a></li>
          <li><a href="http://zhizhe8.net" target="_blank">无名智者个人博客</a></li>
          <?php doAction('navi_5'); ?>
        </ul>
      </li>
    </ul>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="index.php?mod=admin:logout"><span class="glyphicon glyphicon-off"></span> 退出登录</a></li>
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
                <li <?php if($i['mode'][0] == 'default' && !isset($_GET['plugin'])) { echo 'class="active"'; } ?> ><a href="index.php"><span class="glyphicon glyphicon-home"></span> 首页</a></li>
                <li <?php if($i['mode'][0] == 'set' && !isset($_GET['plugin'])) { echo 'class="active"'; } ?> ><a href="index.php?mod=set"><span class="glyphicon glyphicon-wrench"></span> 个人设置</a></li>
                <li <?php if($i['mode'][0] == 'showtb' && !isset($_GET['plugin'])) { echo 'class="active"'; } ?> ><a href="index.php?mod=showtb"><span class="glyphicon glyphicon-cloud"></span> 设置云签到</a></li>
                <li <?php if($i['mode'][0] == 'log' && !isset($_GET['plugin'])) { echo 'class="active"'; } ?> ><a href="index.php?mod=log"><span class="glyphicon glyphicon-calendar"></span> 签到执行日志</a></li>
                <li <?php if($i['mode'][0] == 'baiduid' && !isset($_GET['plugin'])) { echo 'class="active"'; } ?> ><a href="index.php?mod=baiduid"><span class="glyphicon glyphicon-link"></span> 百度账号管理</a></li>
               <?php doAction('navi_7'); if (ROLE == 'admin') { ?>
               <br/>
                <li <?php if(SYSTEM_PAGE == 'admin:tools' && !isset($_GET['plugin'])) { echo 'class="active"'; } ?> ><a href="index.php?mod=admin:tools"><span class="glyphicon glyphicon-briefcase"></span> 工具箱</a></li>
                <li <?php if(SYSTEM_PAGE == 'admin:set' && !isset($_GET['plugin'])) { echo 'class="active"'; } ?> ><a href="index.php?mod=admin:set"><span class="glyphicon glyphicon-cog"></span> 全局设置</a></li>
                <li <?php if(SYSTEM_PAGE == 'admin:users' && !isset($_GET['plugin'])) { echo 'class="active"'; } ?> ><a href="index.php?mod=admin:users"><span class="glyphicon glyphicon-user"></span> 用户管理</a></li>
                <li <?php if(SYSTEM_PAGE == 'admin:update' && !isset($_GET['plugin'])) { echo 'class="active"'; } ?> ><a href="index.php?mod=admin:update"><span class="glyphicon glyphicon-open"></span> 检查更新</a></li>
                <li <?php if(isset($i['mode'][1]) && $i['mode'][1] == 'stat' && !isset($_GET['plugin'])) { echo 'class="active"'; } ?> ><a href="index.php?mod=admin:stat"><span class="glyphicon glyphicon-stats"></span> 统计信息</a></li>
                <li <?php if(SYSTEM_PAGE == 'admin:cron' && !isset($_GET['plugin'])) { echo 'class="active"'; } ?> ><a href="index.php?mod=admin:cron"><span class="glyphicon glyphicon-time"></span> 计划任务</a></li>
                <li <?php if(SYSTEM_PAGE == 'admin:plugins' && !isset($_GET['plugin'])) { echo 'class="active"'; } ?> ><a href="index.php?mod=admin:plugins"><span class="glyphicon glyphicon-tasks"></span> 插件管理</a></li>
               <?php doAction('navi_8'); ?>
               <br/>
               <li><a href="http://www.stus8.com/forum.php?mod=forumdisplay&fid=163&filter=sortid&sortid=13" target="_blank"><span class="glyphicon glyphicon-shopping-cart"></span> 插件商城</a></li>
               <?php doAction('navi_9'); } ?>
              </li>
            </ul>
          </div>
        </div>
<div class="col-md-9" role="main">