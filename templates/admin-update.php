<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足！'); }
global $m,$i;

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">应用成功</div>';
}
doAction('admin_tools_1');
?>
<div id="comsys2">
	<div class="alert alert-info"><span id="upd_info">正在检查更新......</span><br/><br/>
	<div class="progress progress-striped active">
	<div class="progress-bar progress-bar-success" id="upd_prog" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 30%">
	<span class="sr-only">正在检查更新</span></div></div></div>
</div>

<div id="comsys"></div>
<div id="comsys2"></div>
<div id="comsys3"></div>
<script type="text/javascript">
function update() {
	console.log(updata);
}
		$.ajax({ 
		  async:true, 
		  url: 'ajax.php?mod=admin:update', 
		  type: "GET", 
		  data : {},
		  dataType: 'html', 
		  timeout: 90000, 
		  success: function(data){
		    $("#upd_prog").css({'width':'70%'});
			updata    = data;
			if (updata.length <= 1) {
				$("#comsys3").html('<div class="alert alert-success">您当前正在使用最新版本的 <?php echo SYSTEM_FN ?>，无需更新</div>');
			} else {
				$("#comsys3").html('<div class="alert alert-warning"><form action="ajax.php?mod=admin:update:updnow" method="post"><b>发现有新版文件，以下文件可以更新</b>：<br/>更新前请确保服务器具有写的权限，文件将被临时下载到 <b>/setup/update_cache</b> 文件夹<br/>' + updata + '<br/><br/><input type="submit" class="btn btn-primary" value="更新上述文件到最新正式版本"></form></div>');
			}
			console.log(data);
		    $("#upd_info").html('完毕');
		    $("#upd_prog").css({'width':'100%'});
		    $("#comsys2").fadeOut(1500);
		 },
		  error: function(error){
		  	console.log(error)
		  	 $("#upd_info").html('检查更新失败......');
		     $("#upd_prog").css({'width':'0%'});
		     $("#comsys").html('<div class="alert alert-danger">版本检查结果：检查更新失败：无法连接到更新服务器<br/>错误已经记录到控制台，打开控制台查看详细</div><br/>');
		  }
		});
</script>
<?php doAction('admin_tools_2'); ?>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> // 作者: <a href="http://zhizhe8.net" target="_blank">无名智者</a>