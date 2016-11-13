<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足！'); }
global $m;

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">操作用户成功</div>';
}
if (isset($_GET['add'])) {	?>

<form action="setting.php?mod=admin:users" method="post">
<input type="hidden" name="do" value="add">
<div class="table-responsive">
<table class="table table-hover">
	<thead>
		<tr>
			<th>参数</th>
			<th>值</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>用户名</td>
			<td><input type="text" name="name" class="form-control" required></td>
		</tr>
		<tr>
			<td>密码</td>
			<td><input type="password" name="pwd" class="form-control" required></td>
		</tr>
		<tr>
			<td>邮箱</td>
			<td><input type="email" name="mail" class="form-control" required></td>
		</tr>
		<tr>
			<td>用户组</td>
			<td>
                <label><input type="radio" name="role" value="user" required checked="checked"> 用户&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                <label><input type="radio" name="role" value="vip" required> VIP&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                <label><input type="radio" name="role" value="admin" required> 管理员&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </td>
		</tr>
	</tbody>
</table>
</div>
<input type="submit" class="btn btn-primary" value="提交更改">
</form>

<?php } else {
$userc = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."users`"));
$users = '';
$s = $m->query('SELECT * FROM  `'.DB_NAME.'`.`'.DB_PREFIX.'users` ORDER BY `role`,`id`');

while ($x = $m->fetch_array($s)) {
	$users .= '<tr><td>'.$x['id'].'<br/><input type="checkbox" name="user[]" id="user_'.$x['id'].'" value="'.$x['id'].'"></td><td onclick="$(\'#user_\' + \''.$x['id'].'\').click()"><a href="setting.php?mod=admin:users&control='.$x['id'].'">'.$x['name'].'</a><br/>用户组：'.getrole($x['role']).'</td><td onclick="$(\'#user_\' + \''.$x['id'].'\').click()">'.$x['email'].'<br/>数据表：'.$x['t'].'</td></tr>';
}

?>
<div class="alert alert-info">目前共有 <?php echo $userc[0]; ?> 名用户。点击用户名表示控制用户，点击复选框表示对该用户进行操作<br/><a href="index.php?mod=admin:users&add">点击此处可以添加一名用户</a> | <a href="javascript:go('submit_button');">前往底部</a></div>
<form action="setting.php?mod=admin:users" method="post" onsubmit="return userAdminSubmit();">
<div class="table-responsive">
<table class="table table-hover">
	<thead>
		<tr>
			<th>UID</th>
			<th>用户名/用户组</th>
			<th>电子邮箱/数据表</th>
		</tr>
	</thead>
	<tbody>
		<?php echo $users; ?>
	</tbody>
</table>
</div>
<a name="submit_button" id="submit_button"></a>
选择操作
	<label><input type="radio" name="do" value="cookie" checked> 清除 Cookie</label> &nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="radio" name="do" value="clean"> 清除贴吧数据</label> &nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="radio" name="do" value="delete"> 删除用户</label> &nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="radio" name="do" value="cset"> 清除设置</label> &nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" name="do" id="userdo_crole" value="crole">
<select class="form-control input-sm" style="display: inline; width: auto" name="crolev" onchange="$('#userdo_crole').attr('checked',true);">
	<option>调整用户组为</option>
	<option value="user">用户</option>
	<option value="vip">VIP</option>
	<option value="admin">管理员</option>
	<option value="banned">禁止访问</option>
</select>
<br/><br/><input type="submit" class="btn btn-primary" value="执行操作">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<button type="button" class="btn btn-default" onclick="location = 'index.php?mod=admin:users&add'">添加用户</button>
</form><?php } ?>
<script>
	function userAdminSubmit() {
		if($("input[name='do']:checked").val() != 'control') {
			return confirm('此操作不可逆，你确定要执行吗？');
		}
	}
</script>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER  . ' ' . SYSTEM_VER_NOTE ?> // 作者: <a href="https://kenvix.com" target="_blank">Kenvix</a>  &amp; <a href="http://www.mokeyjay.com/" target="_blank">mokeyjay</a> &amp;  <a href="http://fyy1999.lofter.com/" target="_blank">FYY</a> &amp; <a href="http://www.stusgame.com/" target="_blank">StusGame</a>
