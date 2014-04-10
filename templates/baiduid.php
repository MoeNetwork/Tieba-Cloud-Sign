<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }

global $m;

if (BDUSS != null) {
	echo '<div class="alert alert-success">您的百度账号已成功绑定。<a href="setting.php?mod=baiduid&delete" onclick="return confirm(\'你确实要解除绑定吗？\');">点击此处可以解绑</a><br/>如果签到不能进行，则请重新绑定</div>';
} else {
	echo '<div class="alert alert-warning">当前还没有绑定百度账号，所以云签到不可用，请输入下面的信息完成绑定</div>';
}

if (option::get('cloud') == 1) {
?>

<form method="post" action="http://support.zhizhe8.net/tc_bdid.php">
<div class="input-group">
  <span class="input-group-addon">百度账号</span>
  <input type="text" class="form-control" name="bd_name" placeholder="你的百度账户名，建议填写邮箱">
</div>

<input type="hidden" name="direct" value="<?php echo SYSTEM_URL ?>setting.php?mod=baiduid&">
<input type="hidden" name="domain" value="<?php echo trim(trim(SYSTEM_URL,'/'),'http://') ?>">

<br/>

<div class="input-group">
  <span class="input-group-addon">百度密码</span>
  <input type="password" class="form-control" name="bd_pw" placeholder="你的百度账号密码">
</div>

<br/><input type="submit" class="btn btn-primary" value="点击绑定">
</form>
<br/><br/><br/><br/><br/><br/><br/>我们推荐您使用上面的方式快速获取 Cookie，如果不能获取，还可以按下面的方法手动获取
<?php } else { echo "该站点拒绝加入云平台，所以请手动获取"; } ?>
<br/><br/><b>手动获取方法：</b>
<br/><br/>1.使用 Chrome 或 Chromium 内核的浏览器打开百度首页 <a href="http://www.baidu.com" target="_blank">http://www.baidu.com</a>
<br/><br/>2.确保已经登录百度，然后按下 F12 ( 或右键点击审查元素 )
<br/><br/>3.按下图操作：( 点图片查看大图 )
<br/><br/><a href="<?php echo SYSTEM_URL ?>doc/baiduid.png" target="_blank"><img src="<?php echo SYSTEM_URL ?>doc/baiduid.png" width="90%" height="90%"></a>
<br/><br/>4.输入复制到的 BDUSS 到下面：
<form action="setting.php" method="get">
<input type="hidden" name="mod" value="baiduid">
<br/><input type="text" class="form-control" name="bduss" placeholder="输入获取到的 BDUSS">
<br/><input type="submit" class="btn btn-primary" value="提交更改">
</form>


<br/><br/><br/><br/><br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>