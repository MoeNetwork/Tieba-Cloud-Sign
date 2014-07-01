<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
global $m,$today;
if (isset($i['user']['bduss'])) {
	echo '<div class="alert alert-danger">无法列出签到日志，因为当前没有绑定百度账号</div>';	
}
	$count1 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `lastdo` = '".$today."' AND `uid` = ".UID));
	$count1 = $count1[0];
	$count2 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `lastdo` != '".$today."' AND `uid` = ".UID));
	$count2 = $count2[0];
	$q=$m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `uid` = ".UID);
	echo "<div class=\"alert alert-info\">已签到 {$count1} 个贴吧，还有 {$count2} 个贴吧等待签到。<a href=\"index.php?mod=showtb\">设置云签到</a><br/>如果某个贴吧签到状态为异常，可点击该链接查看详情</div>";
	echo '<table class="table"><thead><tr><th>ID</th><th>PID</th><th style="width:52%">贴吧名称</th><th style="width:16%">状态</th><th style="width:24%">上次签到</th></thead><tbody>';
while ($x=$m->fetch_array($q)) {
	if ($x['no'] == 1) {
		$s = '<font color="blue">忽略</font>';
	}
	elseif ($x['status'] != 0) {
		$s = '<font color="red">异常</font>';
	}
	elseif ($x['lastdo'] != $today) {
		$s = '<font color="black">待签</font>';
	}
	else {
		$s = '<font color="green">正常</font>';
	}
	if ($x['lastdo'] == 0) {
		$lastdo = '从未';
	} else {
		$lastdo = $x['lastdo'];
	}
	echo '<tr><td>'.$x['id'].'</td><td>'.$x['pid'].'</td><td>'.$x['tieba'].'</td><td>'.$s.'</td><td>'.$lastdo.'</td></tr>';
}

?></tbody></table>

<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>