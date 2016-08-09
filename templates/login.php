<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } loadhead(); ?>
<div class="panel panel-primary" style="margin:1% 1% 1% 1%;">
	<div class="panel-heading">
          <h3 class="panel-title">请输入您的账号信息</h3>
    </div>
  <div style="margin:0% 5% 5% 5%;">
	<div class="login-top"></div><br/><?php doAction('login_page_1'); ?>
	<b>您需要输入账户和密码才能继续使用 <?php echo SYSTEM_NAME ?>，请输入您的账号信息</b><br/><br/>
     <?php if (isset($_GET['error_msg'])): ?><div class="alert alert-danger alert-dismissable">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  错误：<?php echo strip_tags($_GET['error_msg']); ?></div><?php endif;?>
      <?php if (isset($_GET['msg'])): ?><div class="alert alert-info alert-dismissable">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <?php echo strip_tags($_GET['msg']); ?></div><?php endif;?>
  <form name="f" method="post" action="index.php?mod=admin:login" onsubmit="//$('#pw').val($.md5($('#pw').val()));">
	<div class="input-group">
  <span class="input-group-addon">账户</span>
  <input type="text" class="form-control" name="user" placeholder="账户可以为用户名或者邮箱地址" required>
</div><br/>
<div class="input-group">
  <span class="input-group-addon">密码</span>
  <input type="password" class="form-control" name="pw" id="pw" required>
</div>
	<div class="login-button"><br/>
  <input type="checkbox" name="ispersis" id="ispersis" value="1" />&nbsp;<label for="ispersis">记住密码及账户</label><br/><br/>
	<?php doAction('login_page_2'); ?>
  <button type="submit" class="btn btn-primary" style="width:100%;float:left;">登陆</button>
  <?php doAction('login_page_3'); ?>
	</div><br/><br/><br/>
	<?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> <?php echo SYSTEM_VER_NOTE ?> // 作者: <a href="http://zhizhe8.net" target="_blank">Kenvix</a> @ <a href="http://www.stus8.com/forum.php" target="_blank">StusGame GROUP</a> &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a> &amp; <a href="http://fyy.l19l.com/" target="_blank">FYY</a> 
	<?php
  $icp=option::get('icp');
    if (!empty($icp)) {
      echo ' | <a href="http://www.miitbeian.gov.cn/" target="_blank">'.$icp.'</a>';
    }
    echo '<br/>'.option::get('footer');
    doAction('footer');
  ?>
  <div style=" clear:both;"></div>
	<div class="login-ext"></div>
	<div class="login-bottom"></div>
</div>
