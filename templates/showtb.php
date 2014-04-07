<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
global $m;

if (isset($_GET['set'])) {
	$x=$m->fetch_array($m->query('SELECT * FROM  `'.DB_NAME.'`.`'.DB_PREFIX.TABLE.'` WHERE  `uid` = '.UID.' LIMIT 1'));
	$f=$x['tieba'];
	foreach ($_POST['no'] as $x) {
		preg_match('/(.*)\[(.*)\]/', $x, $v);
		$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX.TABLE."` SET `no` =  '{$v[1]}' WHERE  `".DB_PREFIX.TABLE."`.`id` = {$v[2]} ;");
	}
	header("Location: ".SYSTEM_URL.'index.php?mod=showtb&ok');
}
elseif (isset($_GET['ref'])) {
	  $ch = curl_init(); 
	  curl_setopt($ch, CURLOPT_URL, 'http://tieba.baidu.com/mo/?tn=bdFBW&tab=favorite'); //登陆地址 
	  curl_setopt($ch, CURLOPT_COOKIESESSION, true); 
	  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	  curl_setopt($ch, CURLOPT_COOKIE, "BDUSS=".BDUSS);
	  curl_setopt($ch, CURLOPT_USERAGENT, 'Phone '.mt_rand());
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
	  curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-FORWARDED-FOR:183.185.2.".mt_rand(1,255)));
	  curl_setopt($ch, CURLOPT_HEADER, false);  
	  $ch = curl_exec($ch);
	  preg_match_all('/<td>(.*?).\<a href=\"\/mo\/(.*?)\"\>(.*?)\<\/a\>\<\/td\>/', $ch, $list);
	  $f = '';
	  foreach ($list[3] as $v) {
	  	$osq = $m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `uid` = ".UID." AND `tieba` = '{$v}';");
		if($m->num_rows($osq) == 0) {
			$m->query("INSERT INTO `".DB_NAME."`.`".DB_PREFIX.TABLE."` (`id`, `uid`, `tieba`, `no`, `lastdo`) VALUES (NULL, '".UID."', '{$v}', 0, 0);");
		}
	  }
	  header("Location: ".SYSTEM_URL.'index.php?mod=showtb');
}
elseif (BDUSS != null) {
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
	echo '<div class="alert alert-info" id="tb_num">当前已列出 '.$num.' 个贴吧，<a href="index.php?mod=showtb&ref" onclick="$(\'#tb_num\').html(\'正在刷新贴吧列表，可能需要较长时间，请耐心等待...\')">点击这里可以刷新贴吧列表</a></div>';
	echo '<form action="index.php?mod=showtb&set" method="post">';
	echo '<table class="table"><thead><tr><th style="width:8%">ID</th><th style="width:62%">贴吧名称</th><th style="width:30%">忽略签到</th></thead><tbody>';
	echo $f.'</tbody></table><input type="submit" class="btn btn-primary" value="提交更改"></form>';
} else {
	echo '<div class="alert alert-danger">无法列出贴吧列表，因为当前没有绑定百度账号</div><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>';
	die;	
}

?>

<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>