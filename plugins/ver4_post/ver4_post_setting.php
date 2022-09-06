<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
} ?>
<h2>云灌水设置</h2>
<br>
<?php
if (isset($_GET['msg'])) {
    echo '<div class="alert alert-success">'.htmlspecialchars($_GET['msg']).'</div>';
}
if (isset($_GET['save'])) {
    option::set('ver4_post_dt', $_POST['vpdt']);
    option::set('ver4_post_all', $_POST['vpat']);
    option::set('ver4_post_ts', $_POST['vps']);
    option::set('ver4_post_suf', $_POST['suf']);
    option::set('ver4_post_apikey', $_POST['key']);
    redirect('index.php?mod=admin:setplug&plug=ver4_post&msg='.urlencode('设置已保存成功！'));
}
?>
<br>
<form method="post" action="index.php?mod=admin:setplug&plug=ver4_post&save">

	<div class="input-group">
		<span class="input-group-addon">最多可用次数(条)</span>
		<input type="text" class="form-control" name="vpat" placeholder="用户每天最多可以回复几次" value="<?= option::get('ver4_post_all') ?>" required="">
	</div>
	<br>

	<div class="input-group">
		<span class="input-group-addon">默认回复单帖(次)</span>
		<input type="text" class="form-control" name="vpdt" placeholder="单个帖子默认次数" value="<?= option::get('ver4_post_dt') ?>" required="">
	</div>
	<br>
	<div class="input-group">
		<span class="input-group-addon">相同PID间隔(秒)</span>
		<input type="number" class="form-control" name="vps" placeholder="用户上下两次相同PID间隔" value="<?= option::get('ver4_post_ts') ?>" required="">
	</div>
	<br>
	<div class="input-group">
		<span class="input-group-addon">添加广告后缀(内)</span>
		<input type="text" class="form-control" name="suf" placeholder="设置回帖必带的后缀" value="<?= option::get('ver4_post_suf') ?>">
	</div>
	<br>
	<div class="input-group">
		<span class="input-group-addon">图灵机器人APIKEY(未使用)</span>
		<input type="text" class="form-control" name="key" placeholder="输入你在图灵官网得到的APIKEY" value="<?= option::get('ver4_post_apikey') ?>">
	</div>
	<br>
	<br>
	<input type="submit" class="btn btn-primary" value="保存设置">
</form>