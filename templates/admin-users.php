<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足！'); }
global $m;

if (isset($_POST['do'])) {
	Clean();
	switch (strip_tags($_POST['do'])) {
		case 'cookie':
			foreach ($_POST['user'] as $value) {
				$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX."users` SET  `ck_bduss` =  '' WHERE  `".DB_PREFIX."users`.`id` = ".$value);
			}
			break;
		
		case 'clean':
			foreach ($_POST['user'] as $value) {
				CleanUser($value);
			}
			break;

		case 'delete':
			foreach ($_POST['user'] as $value) {
				DeleteUser($value);
			}
			break;

		default:
			msg('未定义操作');
			break;
	}
	header("Location: ".SYSTEM_URL.'index.php?mod=admin:users&ok');
}

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">操作用户成功</div>';
}
$userc = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."users`"));
$users = '';
$s = $m->query('SELECT * FROM  `'.DB_NAME.'`.`'.DB_PREFIX.'users`');

while ($x = $m->fetch_array($s)) {
	$users .= '<tr><td>'.$x['id'].'<br/><input type="checkbox" name="user[]" value="'.$x['id'].'"></td><td>'.$x['name'].'<br/>用户组：'.getrole($x['role']).'</td><td>'.$x['email'].'<br/>数据表：'.$x['t'].'</td><td><input type="text" style="width:100%" class="form-control" onclick="this.select();" value="'.$x['ck_bduss'].'" readonly></td></tr>';
}

?>
<div class="alert alert-info">目前共有 <?php echo $userc[0]; ?> 名用户。点击 UID 下面的复选框表示对该用户进行操作</div>
<form action="index.php?mod=admin:users" method="post" onsubmit="return confirm('此操作不可逆，你确定要执行吗？');">
<table class="table table-striped">
	<thead>
		<tr>
			<th style="width:7%">UID</th>
			<th style="width:35%">用户名/用户组</th>
			<th style="width:20%">电子邮箱/数据表</th>
			<th style="width:23%">Cookie</th>
		</tr>
	</thead>
	<tbody>
		<?php echo $users; ?>
	</tbody>
</table>
选择操作：<input type="radio" name="do" value="cookie" required> 清除 Cookie &nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" name="do" value="clean"> 清除贴吧数据 &nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" name="do" value="delete"> 删除用户
<br/><br/><input type="submit" class="btn btn-primary" value="执行操作"></form>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>