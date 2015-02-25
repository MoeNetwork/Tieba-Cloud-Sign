<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
global $m,$i,$today;

$count1 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `lastdo` = '".$today."' AND `uid` = ".UID));
$count1 = $count1[0];
$count2 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `lastdo` != '".$today."' AND `uid` = ".UID));
$count2 = $count2[0];

if (!empty($i['user']['bduss'])) {
	if (isset($_GET['ok'])) {
		echo '<div class="alert alert-success">设置保存成功</div>';
	}
	$ex=$m->query('SELECT * FROM  `'.DB_NAME.'`.`'.DB_PREFIX.TABLE.'` WHERE  `uid` = '.UID.' ORDER BY `id` ASC');
	$f = '';
	$num=0;
	?>
<div class="modal fade" id="AddTieba" tabindex="-1" role="dialog" aria-labelledby="AddTieba" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">手动添加贴吧</h4>
      </div>
      <div class="modal-body">
        <form action="setting.php?mod=showtb" method="post">
        	<div class="input-group">
        		<span class="input-group-addon">请选择对应账号ID (PID)</span>
	        	<select name="pid" required class="form-control">
					<?php foreach ($i['user']['bduss'] as $key => $value) {
						echo '<option value="'.$key.'">'.$key.'</option>';
					}
					?>
				</select>
			</div>
			<br/>
			<div class="input-group">
				<span class="input-group-addon">请输入贴吧名称</span>
				<input type="text" class="form-control" name="add" required>
			</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">提交贴吧</button>
      </div></form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
	<?php
	while($x=$m->fetch_array($ex)) {
		$num++;
		if ($x['lastdo'] == 0) {
			$lastdo = '从未';
		} else {
			$lastdo = $x['lastdo'];
		}
		if ($x['no'] == '0') {
			$no = '<input type="radio" name="no['.$x['id'].']" value="1"> 是 <input type="radio" name="no['.$x['id'].']" value="0" checked> 否';
		} else {
			$no = '<input type="radio" name="no['.$x['id'].']" value="1" checked> 是 <input type="radio" name="no['.$x['id'].']" value="0"> 否';
		}
		$f .= '<tr><td>'.$x['id'].'</td><td>'.$x['pid'].'</td><td>'.$x['fid'].'</td>';
		$f .= '<td class="wrap"><a title="'.$x['tieba'].'" href="http://tieba.baidu.com/f?ie=utf-8&kw='.$x['tieba'].'" target="_blank">'. mb_substr($x['tieba'] , 0 , 30 , 'UTF-8') .'</a>';
		if ($x['status'] != 0) {
			$f .= '<br/><b>错误:</b>' . $x['last_error'] . '</td>';
			$f .= '<td><font color="red">异常</font><br/>#' . $x['status'];
		}
		elseif ($x['lastdo'] != $today) {
			$f .= '</td><td><font color="black">待签</font>';
		}
		else {
			$f .= '</td><td><font color="green">正常</font>';
		}
		$f .= '</td><td>'.$lastdo.'</td><td>'.$no.'</td></tr>';
	}
	echo '<div class="alert alert-info" id="tb_num">当前已列出 '.$num.' 个贴吧。已签到 '.$count1.' 个贴吧，还有 '.$count2.' 个贴吧等待签到<br/>PID 即为 账号ID';
	if (!ISVIP) {
		echo '，您最多可以添加 ' . option::get('tb_max') . ' 个贴吧';
	}
	echo '，移动设备可能需要左右滑动表格才能显示所有内容<br/>功能：<a href="setting.php?mod=showtb&ref" onclick="$(\'#tb_num\').html(\'正在刷新贴吧列表，可能需要较长时间，请耐心等待...\')">刷新贴吧列表</a> | <a href="setting.php?mod=showtb&clean" onclick="return confirm(\'你真的要清空所有贴吧吗？\');">清空列表</a>';
	if (option::get('enable_addtieba') == 1) {
		echo ' | <a href="javascript:;" data-toggle="modal" data-target="#AddTieba">手动添加贴吧</a>';
	}
	echo ' | <a href="javascript:;" onclick="go(\'submit_button\');">前往底部</a></div>';
	echo '<form action="setting.php?mod=showtb&set" method="post">';
	echo '<div class="table-responsive"><table class="table table-hover"><thead><tr>';
	echo '<th>ID</th>';
	echo '<th>PID</th><th>FID</th>';
	echo '<th>贴吧名称</th>';
	echo '<th>状态</th>';
	echo '<th>上次签到</th>';
	echo '<th>忽略签到</th></thead><tbody>';
	echo $f.'</tbody></table></div><input type="submit" id="submit_button" class="btn btn-primary" value="提交更改"></form>';
} else {
	echo '<div class="alert alert-danger">无法列出贴吧列表，因为当前没有绑定百度账号</div>';
}

?>

<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> // 作者: <a href="http://zhizhe8.net" target="_blank">无名智者</a> &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a>