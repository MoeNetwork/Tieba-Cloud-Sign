<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足！'); }
global $m,$i;

if (isset($_GET['ok'])) {
    echo '<div class="alert alert-success">应用成功</div>';
}
doAction('admin_update_1');
if (isset($i['mode'][2])) {
	?>
<ul class="nav nav-tabs" role="tablist">
  <li><a href="index.php?mod=admin:update">检查更新</a></li>
  <li class="active"><a href="index.php?mod=admin:update:back">更新回滚</a></li>
</ul>
<br/>
	<?php
if (file_exists(SYSTEM_ROOT . '/setup/update_backup') && is_dir(SYSTEM_ROOT . '/setup/update_backup')) {
	$xc = scandir(SYSTEM_ROOT . '/setup/update_backup',1);
	$count = count($xc) - 2;
} else {
	$count = 0;
}
if($count <= 0) {
	echo '<div class="alert alert-danger" role="alert">现在无备份可供回滚，当您对云签到进行升级时，会自动生成备份以供回滚</div>';	
} else {
	echo '<div class="alert alert-info" role="alert">有 '.$count.' 个更新可供回滚，备份文件均位于 /setup/update_backup/实际名称 中</div>
	<table class="table table-striped">
	<thead>
		<tr>
			<th style="width:15%">实际名称</th>
			<th style="width:10%">版本</th>
			<th style="width:20%">备份时间</th>
			<th style="width:25%">操作</th>
		</tr>
	</thead>
	<tbody>';
	foreach ($xc as $v) {
		$ini = parse_ini_file(SYSTEM_ROOT . '/setup/update_backup/' . $v . '/__backup.ini');

		if ($ini !== false) {
			echo '<tr><td>'.$v.'</td>';
			echo '<td>'.$ini['ver'].'</td>';
			echo '<td>'.$ini['time'].'</td>';
			echo '<td><button type="button" class="btn btn-primary" onclick="if(confirm(\'你确实要将云签到回滚到此版本吗\')) location = \'setting.php?mod=admin:update:back&dir='.$v.'\';">回滚</button> ';
			echo '<button type="button" class="btn btn-default"  onclick="if(confirm(\'你确实要删除此备份吗\')) location = \'setting.php?mod=admin:update:back&del='.$v.'\';">删除</button></td>';
			echo '</tr>';
		}
	}
	echo '</tbody></table>';
}
} else {
?>
<ul class="nav nav-tabs" role="tablist">
  <li class="active"><a href="index.php?mod=admin:update">检查更新</a></li>
  <li><a href="index.php?mod=admin:update:back">更新回滚</a></li>
</ul>
<br/>
<div class="input-group">
	<span class="input-group-addon">更新服务器</span>
	<select id="server" class="form-control">
		<option value="0">Git@OSC [国内推荐]</option>
		<option value="1">Github [国外推荐]</option>
		<option value="2">Coding [国内]</option>
		<option value="3">Gitcafe [国外]</option>
	</select>
	<span class="input-group-btn">
		<input id="save_btn" type="button" value="保存并应用" class="btn btn-info" onclick="save_server()">
	</span>
</div>
<script type="text/javascript">
	<?php
		$server = option::get('update_server') === null ? 0 : option::get('update_server');
	?>
	$('#server').val('<?php echo $server; ?>');
	function save_server() {
		$('#save_btn').val('正在保存').attr("disabled","disabled");
		$.ajax({ 
		  async:true, 
		  url: 'ajax.php?mod=admin:update:changeServer&server=' + $('#server').val(), 
		  type: "GET", 
		  data : {},
		  dataType: 'html', 
		  timeout: 90000, 
		  success: function(data){
		    location.reload();
		 },
		  error: function(error){
		  	console.log(error);
		  	$('#save_btn').val('保存并应用').removeAttr("disabled");
		  	alert('保存失败！错误信息已记录到控制台，按F12打开控制台查看详细信息');
		  }
		});
	}
</script>
<br/>
<?php
//检测服务器是否支持写入
if(is_writable("setup")){
	echo '<div id="comsys2">
	<div class="alert alert-info"><span id="upd_info">正在检查更新......</span><br/><br/>
	<div class="progress progress-striped active">
	<div class="progress-bar progress-bar-success" id="upd_prog" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 30%">
	<span class="sr-only">正在检查更新</span></div></div></div>
</div>

<div id="comsys"></div>
<div id="comsys2"></div>
<div id="comsys3"></div>';
	$writable="1";
} else {
	echo '<div class="alert alert-danger" role="alert">你的服务器不支持文件写入，请 <a href="http://www.stus8.com/forum.php?mod=viewthread&tid=2141" target="_blank">手动更新</a></div>';
	$writable="0";
}
?>

<script type="text/javascript">
function update() {
	console.log(updata);
}
if(<?php echo $writable; ?>==1){
	$.ajax({ 
	  async:true, 
	  url: 'ajax.php?mod=admin:update&server=' + server, 
	  type: "GET", 
	  data : {},
	  dataType: 'html', 
	  timeout: 90000, 
	  success: function(data){
	    $("#upd_prog").css({'width':'70%'});
		$("#comsys3").html(data);
	    $("#upd_info").html('完毕');
	    $("#upd_prog").css({'width':'100%'});
	    $("#comsys2").delay(1000).slideUp(500);
	 },
	  error: function(error){
	  	console.log(error);
	  	 $("#upd_info").html('检查更新失败！');
	     $("#upd_prog").css({'width':'0%'});
	     $("#comsys").html('<div class="alert alert-danger">检查更新失败：无法连接到更新服务器<br/>错误已经记录到控制台，打开控制台查看详细<br/>你还可以尝试 <a href="http://www.stus8.com/forum.php?mod=viewthread&tid=2141" target="_blank">手动更新</a></div><br/>');
	  }
	});
}
</script>
<?php } doAction('admin_update_2'); ?>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> // 作者: <a href="http://zhizhe8.net" target="_blank">无名智者</a> &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a>