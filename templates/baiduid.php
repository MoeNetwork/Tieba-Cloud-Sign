<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }

global $m;
if (isset($_GET['delete'])) {
	$m->query("UPDATE  `".DB_NAME."`.`".DB_PREFIX."users` SET  `ck_bduss` =  NULL WHERE  `".DB_PREFIX."users`.`id` =".UID.";");
	header("Location: ".SYSTEM_URL."?mod=baiduid");
}
elseif (isset($_GET['bduss'])) {
	$m->query("UPDATE  `".DB_NAME."`.`".DB_PREFIX."users` SET  `ck_bduss` =  '".strip_tags($_GET['bduss'])."' WHERE  `".DB_PREFIX."users`.`id` =".UID.";");
	header("Location: ".SYSTEM_URL."?mod=baiduid");
}
elseif (BDUSS != null) {
	echo '<div class="alert alert-success">您的百度账号已成功绑定。<a href="index.php?mod=baiduid&delete" onclick="return confirm(\'你确实要解除绑定吗？\');">点击此处可以解绑</a></div>';
} else {
	echo '<div class="alert alert-warning">当前还没有绑定百度账号，所以云签到不可用</div>';
}
?>

<form method="post" action="http://support.zhizhe8.net/tc_bdid.php">
<div class="input-group">
  <span class="input-group-addon">百度账号</span>
  <input type="text" class="form-control" name="bd_name" placeholder="你的百度账户名，建议填写邮箱">
</div>

<input type="hidden" name="direct" value="<?php echo SYSTEM_URL ?>?mod=baiduid&">
<input type="hidden" name="domain" value="<?php echo trim(trim(SYSTEM_URL,'/'),'http://') ?>">

<br/>

<div class="input-group">
  <span class="input-group-addon">百度密码</span>
  <input type="password" class="form-control" name="bd_pw" placeholder="你的百度账号密码">
</div>

<br/><input type="submit" class="btn btn-primary" value="点击绑定">
</form>
<br/><br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>