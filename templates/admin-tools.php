<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
global $m;

if (isset($_GET['setting'])) {
	switch (strip_tags($_GET['setting'])) {
		case 'optim':
			global $m;
			$m->query('OPTIMIZE TABLE  `'.DB_NAME.'`.`'.DB_PREFIX.'options` ,`'.DB_NAME.'`.`'.DB_PREFIX.'tieba` ,`'.DB_NAME.'`.`'.DB_PREFIX.'users`');
			header("Location: ".SYSTEM_URL.'index.php?mod=admin:tools&ok='.'优化数据表');
			break;
		
		default:
			msg('未定义操作');
			break;
	}
}
elseif (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">'.strip_tags($_GET['ok']).' : 应用成功</div>';
}

doAction('admin_tools');
?>
<input type="button" onclick="location = '<?php echo SYSTEM_URL ?>index.php?mod=admin:tools&setting=optim'" class="btn btn-primary" value="优化所有数据表">

<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>