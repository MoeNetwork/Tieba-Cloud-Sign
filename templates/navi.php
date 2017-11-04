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
      <?php if (ROLE != 'visitor' && ROLE != 'banned') { ?>
      <ul class="nav navbar-nav">
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-list-alt"></span> 功能菜单 <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li class="<?php checkIfActive('default') ?>" ><a href="index.php"><span class="glyphicon glyphicon-home"></span> 首页</a></li>
          <li class="<?php checkIfActive('set') ?>" ><a href="index.php?mod=set"><span class="glyphicon glyphicon-wrench"></span> 个人设置</a></li>
          <li class="<?php checkIfActive('baiduid') ?>" ><a href="index.php?mod=baiduid"><span class="glyphicon glyphicon-link"></span> 百度账号管理</a></li>
          <li class="<?php checkIfActive('showtb') ?>" ><a href="index.php?mod=showtb"><span class="glyphicon glyphicon-calendar"></span> 云签到设置和日志</a></li>
          <?php doAction('navi_1'); ?>
        </ul>
      </li>
      <?php if (ROLE == 'admin') { ?>
      <li class="dropdown" class="active">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-th-list"></span> 管理菜单 <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li class="<?php checkIfActive('admin:tools') ?>" ><a href="index.php?mod=admin:tools"><span class="glyphicon glyphicon-briefcase"></span> 工具箱</a></li>
          <li class="<?php checkIfActive('admin:set') ?>" ><a href="index.php?mod=admin:set"><span class="glyphicon glyphicon-cog"></span> 设置中心</a></li>
          <li class="<?php checkIfActive('admin:users') ?>" ><a href="index.php?mod=admin:users"><span class="glyphicon glyphicon-user"></span> 用户管理</a></li>
          <li class="<?php checkIfActive('admin:stat') ?>" ><a href="index.php?mod=admin:stat"><span class="glyphicon glyphicon-stats"></span> 统计信息</a></li>
          <li class="<?php checkIfActive('admin:cron') ?>" ><a href="index.php?mod=admin:cron"><span class="glyphicon glyphicon-time"></span> 计划任务</a></li>
		  <li class="<?php checkIfActive('admin:update') ?>" ><a href="index.php?mod=admin:update"><span class="glyphicon glyphicon-open"></span> 检查更新</a></li>
          <?php doAction('navi_2'); ?>
        </ul>
      </li>
      <li class="dropdown" class="active">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-th"></span> 插件菜单 <b class="caret"></b></a>
        <ul class="dropdown-menu">
            <?php doAction('navi_3'); ?>
            <li class="<?php checkIfActive('admin:plugins') ?>" ><a href="index.php?mod=admin:plugins"><span class="glyphicon glyphicon-tasks"></span> 插件管理</a></li>
            <li><a href="http://git.oschina.net/kenvix/Tieba-Cloud-Sign/wikis/%E8%B4%B4%E5%90%A7%E4%BA%91%E7%AD%BE%E5%88%B0%E6%8F%92%E4%BB%B6%E5%BA%93" target="_blank"><span class="glyphicon glyphicon-shopping-cart"></span> 插件库</a></li>
        </ul>
      </li>
      <?php doAction('navi_4'); } ?>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <?php if(defined('CON_UID')){?><li><a href="index.php?mod=usercontrolback"><span class="glyphicon glyphicon-eject"></span> 返回 <?php echo CON_NAME; ?></a></li><?php } ?>
      <li><a href="index.php?mod=admin:logout"><span class="glyphicon glyphicon-off"></span> 退出登录</a></li>
      <?php doAction('navi_6'); ?>
    </ul>
      <?php } else { ?>
    <ul class="nav navbar-nav">
      <li class="<?php checkIfActive('login') ?>" ><a href="index.php?mod=login"><span class="glyphicon glyphicon-play"></span> 登录</a></li>
      <li class="<?php checkIfActive('reg') ?>" ><a href="index.php?mod=reg"><span class="glyphicon glyphicon-user"></span> 注册</a></li>
	  <?php doAction('navi_10'); ?>
	</ul>
      <?php } ?>
    <ul class="nav navbar-nav">
      <li class="dropdown" class="active">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-book"></span> 关于 <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li><a href="http://lovelive.us" target="_blank">StusGame</a></li>
          <li><a href="https://kenvix.com" target="_blank">Kenvix's Blog</a></li>
          <?php doAction('navi_5'); ?>
        </ul>
      </li>
  	</ul>
  </div><!-- /.navbar-collapse -->
</div>

<!-- 侧边导航，宽屏设备可见 -->
<div class="container bs-docs-container">
      <div class="row">
        <div class="col-md-3">
          <div class="bs-sidebar hidden-print visible-lg visible-md" role="complementary" >
            <ul class="nav bs-sidenav">
              <?php if (ROLE != 'visitor' && ROLE != 'banned') { ?>
              <li>
                <li class="<?php checkIfActive('default') ?>" ><a href="index.php"><span class="glyphicon glyphicon-home"></span> 首页</a></li>
                <li class="<?php checkIfActive('set') ?>" ><a href="index.php?mod=set"><span class="glyphicon glyphicon-wrench"></span> 个人设置</a></li>
                <li class="<?php checkIfActive('baiduid') ?>" ><a href="index.php?mod=baiduid"><span class="glyphicon glyphicon-link"></span> 百度账号管理</a></li>
                <li class="<?php checkIfActive('showtb') ?>" ><a href="index.php?mod=showtb"><span class="glyphicon glyphicon-calendar"></span> 云签到设置和日志</a></li>
               <?php doAction('navi_7'); if (ROLE == 'admin') { ?>
               <br/>
                <li class="<?php checkIfActive('admin:tools') ?>" ><a href="index.php?mod=admin:tools"><span class="glyphicon glyphicon-briefcase"></span> 工具箱</a></li>
                <li class="<?php checkIfActive('admin:set') ?>" ><a href="index.php?mod=admin:set"><span class="glyphicon glyphicon-cog"></span> 设置中心</a></li>
                <li class="<?php checkIfActive('admin:users') ?>" ><a href="index.php?mod=admin:users"><span class="glyphicon glyphicon-user"></span> 用户管理</a></li>
                <li class="<?php checkIfActive('admin:stat') ?>" ><a href="index.php?mod=admin:stat"><span class="glyphicon glyphicon-stats"></span> 统计信息</a></li>
                <li class="<?php checkIfActive('admin:cron') ?>" ><a href="index.php?mod=admin:cron"><span class="glyphicon glyphicon-time"></span> 计划任务</a></li>
                <li class="<?php checkIfActive('admin:plugins') ?>" ><a href="index.php?mod=admin:plugins"><span class="glyphicon glyphicon-tasks"></span> 插件管理</a></li>
               <?php doAction('navi_8'); ?>
               <br/>
                  <li><a href="http://git.oschina.net/kenvix/Tieba-Cloud-Sign/wikis/%E8%B4%B4%E5%90%A7%E4%BA%91%E7%AD%BE%E5%88%B0%E6%8F%92%E4%BB%B6%E5%BA%93" target="_blank"><span class="glyphicon glyphicon-shopping-cart"></span> 插件库</a></li>
				  <li class="<?php checkIfActive('admin:update') ?>" ><a href="index.php?mod=admin:update"><span class="glyphicon glyphicon-open"></span> 检查更新</a></li>
                      <li><a href="http://www.stusgame.com/" target="_blank"><span class="glyphicon glyphicon-globe"></span> 问题反馈</a></li>
               <?php doAction('navi_9'); } ?>
              </li>
              <?php } else { ?>
				<li class="<?php checkIfActive('login') ?>" ><a href="index.php?mod=login"><span class="glyphicon glyphicon-play"></span> 登录</a></li>
				<li class="<?php checkIfActive('reg') ?>" ><a href="index.php?mod=reg"><span class="glyphicon glyphicon-user"></span> 注册</a></li>
			    <?php doAction('navi_11'); ?>
              <?php } ?>
            </ul>
          </div>
        </div>
<div class="col-md-9" role="main">
