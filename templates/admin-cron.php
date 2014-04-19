<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足!'); }
global $m;

$query = $m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX."cron`");
$cron  = '';
while ($cs = $m->fetch_array($query)) {
	if ($cs['freq'] == '-1') {
		$freq = '一次性任务';
	}
	elseif ($cs['freq'] == '0') {
		$freq = '始终运行的任务';
	}
	else {
		$freq = '执行间隔：'.$cs['freq'].' 秒';
	}

	if (!empty($cs['lastdo'])) {
		$lastdo = date('Y-m-d H:m:s',$cs['lastdo']);
	} else {
		$lastdo = '从未运行';
	}

	if ($cs['no'] != 1) {
		$status = '<font color="green">有效</font>';
		if ($cs['status'] != 0) {
			$status = '<font color="red">异常</font>';
		}
		$status .= ' | <a href="setting.php?mod=admin:cron&dis='.$cs['id'].'">忽略任务</a>';
	} else {
		$status = '<font color="blue">忽略</font> | <a href="setting.php?mod=admin:cron&act='.$cs['id'].'">取消忽略</a>';
	}

	$status .= ' | <a href="setting.php?mod=admin:cron&uninst='.$cs['id'].'" onclick="return confirm(\'你确实要卸载此计划任务吗？\');">卸载</a>';
	if (empty($cs['log'])) {
		$status .= '<br/>没有日志可查看';
	} else {
		$status .= '<script type="text/javascript">var system_cron_log = "\''.addslashes($cs['log']).'\'"</script>';
		$status .= '<br/><a href="javascript:;" onclick="alert(system_cron_log);">点击查看此任务的日志</a>';
	}

	$cron .= '<tr><td style="width:30%"><b>'.$cs['name'].'</b><br/>'.$cs['file'].'</td><td style="width:40%">'.$freq.'<br/>上次执行：'.$lastdo.'</td><td style="width:30%">'.$status.'</td></tr>';
}

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">计划任务操作成功</div>';
}

$crount = $m->once_fetch_array("SELECT COUNT(*) AS ffffff FROM `".DB_NAME."`.`".DB_PREFIX."cron` ");
?>
<div class="alert alert-info" id="tb_num">当前共有 <?php echo $crount['ffffff'] + 1 ?> 个计划任务，您需要添加根目录下 do.php 到您主机的计划任务后，下面的任务才能被执行<br/><a href="index.php?mod=admin:cron&add">点击这里可以添加一个计划任务到系统</a></div>
<table class="table table-striped">
	<thead>
		<tr>
			<th style="width:30%">任务描述/文件</th>
			<th style="width:40%">其他信息</th>
			<th style="width:30%">状态/操作</th>
		</tr>
	</thead>
	<tobdy>
		<?php echo $cron ?>
		<td style="width:30%"><b>签到所有贴吧</b><br/>do.php</td>
		<td style="width:40%">始终运行的任务<br/>上次执行：<?php echo option::get('cron_last_do_time') ?></td>
		<td style="width:30%">对系统任务不可用</td>
	</tbody>
</table>

<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>