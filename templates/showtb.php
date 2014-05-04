<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
global $m;

if (BDUSS != null) {
	if (isset($_GET['ok'])) {
		echo '<div class="alert alert-success">设置保存成功</div>';
	}
	$ex=$m->query('SELECT * FROM  `'.DB_NAME.'`.`'.DB_PREFIX.TABLE.'` WHERE  `uid` = '.UID.'');
	$f = '';
	$num=0;
	while($x=$m->fetch_array($ex)) {
		$num++;
		if ($x['no'] == 1) {
			$no = '<input type="radio" name="no['.$x['id'].']" value="1['.$x['id'].']" checked> 是 <input type="radio" name="no['.$x['id'].']" value="0['.$x['id'].']"> 否';
		} else {
			$no = '<input type="radio" name="no['.$x['id'].']" value="1['.$x['id'].']"> 是 <input type="radio" name="no['.$x['id'].']" value="0['.$x['id'].']" checked> 否';
		}
		$f .= '<tr><td>'.$x['id'].'</td><td>'.$x['tieba'].'</td><td>'.$no.'</td></tr>';
	}
	echo '<div class="alert alert-info" id="tb_num">当前已列出 '.$num.' 个贴吧，<a href="setting.php?mod=showtb&ref" onclick="$(\'#tb_num\').html(\'正在刷新贴吧列表，可能需要较长时间，请耐心等待...\')">点击这里可以刷新贴吧列表</a> | <a href="setting.php?mod=showtb&clean">清空列表</a>';
	if (option::get('enable_addtieba') == 1) {
		echo '<br/><br/><form action="setting.php?mod=showtb" method="post"><div class="input-group"><span class="input-group-addon">手动添加贴吧</span><input type="text" class="form-control" name="add" placeholder="若要手动添加贴吧，请输入贴吧名称"><span class="input-group-btn"><button type="submit" class="btn btn-default">提交贴吧</button></form></div>';
	}
	echo '</div>';
	echo '<form action="setting.php?mod=showtb&set" method="post">';
	echo '<table class="table"><thead><tr><th style="width:8%">ID</th><th style="width:62%">贴吧名称</th><th style="width:30%">忽略签到</th></thead><tbody>';
	echo $f.'</tbody></table><input type="submit" class="btn btn-primary" value="提交更改"></form>';
} else {
	echo '<div class="alert alert-danger">无法列出贴吧列表，因为当前没有绑定百度账号</div>';
}

?>

<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>