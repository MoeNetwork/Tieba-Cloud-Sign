<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }
if (option::get('enable_reg') != 1) {
  msg('该站点已关闭注册');
}
loadhead();
?>
<div class="panel panel-success" style="margin:5% 15% 5% 15%;">
	<div class="panel-heading">
          <h3 class="panel-title">注册 <?php echo SYSTEM_FN ?></h3>
    </div>
    <div style="margin:0% 5% 5% 5%;">
	<div class="login-top"></div><br/><?php doAction('reg_page_1'); ?>
	<b>请输入您的账号信息以注册本站</b><br/><br/>
  <?php if (isset($_GET['error_msg'])): ?><div class="alert alert-danger alert-dismissable">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  错误：<?php echo strip_tags($_GET['error_msg']); ?></div><?php endif;?>
  <form name="f" method="post" action="<?php echo SYSTEM_URL ?>index.php?mod=admin:reg">
	<div class="input-group">
  <span class="input-group-addon">账户</span>
  <input type="text" class="form-control" name="user" required>
</div><br/>
<div class="input-group">
  <span class="input-group-addon">密码</span>
  <input type="password" class="form-control" name="pw" id="pw" required>
</div><br/>
<div class="input-group">
  <span class="input-group-addon">邮箱</span>
  <input type="email" class="form-control" name="mail" id="mail" required>
</div>
<?php 
$yr_reg = option::get('yr_reg');
if (!empty($yr_reg)) { ?>
<br/>
<div class="input-group">
  <span class="input-group-addon">邀请码</span>
  <input type="text" class="form-control" name="yr" id="yr" required>
</div>
<?php } ?>
	<div class="login-button"><br/>
	<?php doAction('reg_page_2'); if (option::get('enable_reg') == 1) { ?>
  <button type="submit" class="btn btn-primary" style="width:60%;float:left;">继续注册</button>
	<button type="button" class="btn btn-success" onclick="location = '<?php echo SYSTEM_URL ?>index.php?mod=login'" style="width:35%;float:right;">已有账号?</button>
  <?php } else { ?>
  <button type="submit" class="btn btn-primary" style="width:100%;float:left;">登陆</button>
  <?php } doAction('reg_page_3'); ?>
	</div><br/><br/><br/>
	<?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a> @ <a href="http://www.stus8.com/" target="_blank">StusGame GROUP</a>
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