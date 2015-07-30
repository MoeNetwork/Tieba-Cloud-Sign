<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足!'); }
global $m,$i;

if (isset($_GET['add'])) {
?>
<form action="setting.php?mod=admin:cron&add" method="post">
<div class="table-responsive">
<table class="table table-hover">
	<thead>
		<tr>
			<th style="width:25%">参数</th>
			<th>值</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>任务名称<br/>唯一，不能有中文</td>
			<td><input type="text" name="name" class="form-control" required=""></td>
		</tr>
		<tr>
			<td>任务文件<br/>基准目录为云签到根目录，开头不需要带/</td>
			<td><input type="text" name="file" class="form-control" required=""></td>
		</tr>
		<tr>
			<td>任务描述<br/>描述这个任务</td>
			<td><textarea class="form-control" name="desc"></textarea></td>
		</tr>
		<tr>
			<td>忽略任务</td>
			<td><input type="radio" name="no" value="0" required="" checked> 否&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="no" value="1" required=""> 是</td>
		</tr>
		<tr>
			<td>执行间隔<br/>单位为秒，0为始终执行</td>
			<td><input type="number" name="freq" class="form-control" required="" value="0"></td>
		</tr>
		<tr>
			<td>上次执行<br/>Unix 时间戳</td>
			<td><input type="number" name="lastdo" class="form-control" required="" value="<?php echo time(); ?>"></td>
		</tr>
		<tr>
			<td>执行日志<br/><br/>系统会自动写入</td>
			<td><textarea name="log" class="form-control" style="height:100px"></textarea></td>
		</tr>
	</tbody>
</table>
</div>
<br/><button type="submit" class="btn btn-primary">提交更改</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<button type="button" class="btn btn-default" onclick="location = 'index.php?mod=admin:cron'">取消</button>
</form>
<?php
} else {
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
	$status .= ' | <a href="setting.php?mod=admin:cron&uninst='.$cs['name'].'" onclick="return confirm(\'你确实要卸载此计划任务吗？\');">卸载</a>';
	if (empty($cs['log'])) {
		$status .= '<br/>没有日志可查看';
	} else {
		$status .= '<script type="text/javascript">var system_cron_log = "'.addslashes($cs['log']).'"</script>';
		$status .= '<br/><a href="javascript:;" onclick="alert(system_cron_log);">点击查看此任务的日志</a>';
	}
	$status .= '<br/>运行顺序：<input required style="width:30%" type="number" name="order['.$cs['name'].']" value="'.$cs['orde'].'">';
	$cron .= '<input type="hidden" value="'.$cs['name'].'" name="ids[]"><tr><td style="width:30%"><b>'. $cs['name'] . '</b><br/>'.$cs['file'].'<br/>'. str_replace("\n", '<br/>', $cs['desc']) .'</td><td style="width:30%">'.$freq.'<br/>上次执行：'.$lastdo.'</td><td style="width:40%">'.$status.'</td></tr>';
}

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">计划任务操作成功</div>';
}

$crount = $m->once_fetch_array("SELECT COUNT(*) AS ffffff FROM `".DB_NAME."`.`".DB_PREFIX."cron` ");
?>
<div class="alert alert-info" id="tb_num">当前共有 <?php echo $crount['ffffff'] ?> 个计划任务，您需要添加根目录下 do.php 到您主机的计划任务后，下面的任务才能被执行<br/><a href="index.php?mod=admin:cron&add">添加新计划任务</a> | <a href="do.php?pw=<?php echo option::get('cron_pw') ?>">运行全部计划任务</a></div>
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
<input type="submit" class="btn btn-primary" value="提交更改">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-info" onclick="location = 'index.php?mod=admin:cron&add'">添加计划任务</button>
</form>
<br/><?php } ?>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER  . ' ' . SYSTEM_VER_NOTE ?> // 作者: <a href="http://zhizhe8.net" target="_blank">Kenvix</a> @ <a href="http://www.stus8.com" target="_blank">StusGame GROUP</a> &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a> &amp; <a href="http://fyy.l19l.com/" target="_blank">FYY</a>