<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足!'); }
global $m,$i;
$cron  = '';
foreach ($i['cron'] as $cs) {
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
		$lastdo = date('Y-m-d H:i:s',$cs['lastdo']);
	} else {
		$lastdo = '从未运行';
	}

	if ($cs['no'] != 1) {
		$status = '<font color="green">有效</font>';
		$status .= ' | <a href="setting.php?mod=admin:cron&dis='.$cs['name'].'">忽略任务</a>';
	} else {
		$status = '<font color="blue">忽略</font> | <a href="setting.php?mod=admin:cron&act='.$cs['name'].'">取消忽略</a>';
	}
	$status .= '<br/><a href="setting.php?mod=admin:cron&run='.$cs['name'].'&file='.$cs['file'].'">运行</a>';
	$status .= ' | <a href="index.php?mod=admin:editcron&edit='.$cs['name'].'">编辑</a>';
	$status .= ' | <a href="setting.php?mod=admin:cron&uninst='.$cs['name'].'" style="color:red; " onclick="return confirm(\'你想要卸载此计划任务吗？\');">卸载</a>';
	if (empty($cs['log'])) {
		$status .= '<br/>没有日志可查看';
	} else {
		$status .= '<br/><a href="javascript:;" onclick="alert(\''.str_replace('\'','\\\'',str_replace('"', '&quot;', str_replace("\n", '<br/>', str_replace("\r", '',  $cs['log'])))) . '\');">点击查看此任务的日志</a>';
	}
	$status .= '<br/>运行顺序：<input required class="form-control input-sm" style="width:30%; display:inline" type="number" name="order['.$cs['name'].']" value="'.$cs['orde'].'">';
	$cron .= '<input type="hidden" value="'.$cs['name'].'" name="ids[]"><tr><td style="width:30%"><b>'. $cs['name'] . '</b><br/>'.$cs['file'].'<br/>'. str_replace(array("\n", "\r\n"), '<br/>', $cs['desc']) .'</td><td style="width:30%">'.$freq.'<br/>上次执行：'.$lastdo.'</td><td style="width:40%">'.$status.'</td></tr>';
}

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">计划任务操作成功</div>';
}

$count = $m->once_fetch_array("SELECT COUNT(*) AS c FROM `".DB_NAME."`.`".DB_PREFIX."cron`");
?>
<div class="alert alert-info" id="tb_num">当前共有 <?php echo $count['c'] ?> 个计划任务，您需要添加根目录下 do.php 到您主机的计划任务后，下面的任务才能被执行<br/><a href="index.php?mod=admin:editcron">添加新计划任务</a> | <a href="do.php?pw=<?php echo option::get('cron_pw') ?>">运行全部计划任务</a></div>
<form action="setting.php?mod=admin:cron&xorder" method="post">
<div class="table-responsive">
<table class="table table-hover">
	<thead>
		<tr>
			<th>任务描述/文件</th>
			<th>其他信息</th>
			<th>状态/操作</th>
		</tr>
	</thead>
	<tbody>
		<?php echo $cron ?>
	</tbody>
</table>
</div>
<input type="submit" class="btn btn-primary" value="提交更改">
</form>
<br/>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER  . ' ' . SYSTEM_VER_NOTE ?> // 作者: <a href="https://kenvix.com" target="_blank">Kenvix</a>  &amp; <a href="http://www.mokeyjay.com/" target="_blank">mokeyjay</a> &amp;  <a href="http://fyy1999.lofter.com/" target="_blank">FYY</a> &amp; <a href="http://www.stusgame.com/" target="_blank">StusGame</a>
