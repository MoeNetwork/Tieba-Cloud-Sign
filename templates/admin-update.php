<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足！'); }
global $m,$i;
doAction('admin_update_1');
?>
<div class="input-group">
	<span class="input-group-addon">更新服务器</span>
	<select id="server" class="form-control">
		<option value="0">Github [默认,国外]</option>
		<option value="5">Coding [国内]</option>
	</select>
	<span class="input-group-btn">
		<input id="save_btn" type="button" value="保存并应用" class="btn btn-info" onclick="save_server()">
	</span>
</div>
<br/>
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
	echo '<div class="alert alert-danger" role="alert">你的服务器不支持文件写入，请手动更新</div>';
	$writable="0";
}
?>

<script type="text/javascript">
function waitup() {
	$("#comsys").html('<div class="alert alert-warning">开始更新，请不要离开此页面...</div>');
}
function update() {
	console.log(updata);
}
if(<?php echo $writable; ?>==1){
	$.ajax({
	  async:true,
	  url: 'ajax.php?mod=admin:update<?php if(isset($_GET['ok'])) echo '&ok' ?>',
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
	     $("#comsys").html('<div class="alert alert-danger">检查更新失败：无法连接到更新服务器<br/>错误已经记录到控制台，打开控制台查看详细<br/>你可以尝试手动更新</div><br/>');
	  }
	});
}
</script>
<?php doAction('admin_update_2'); ?>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> // 作者: <a href="https://kenvix.com" target="_blank">Kenvix</a> &amp; <a href="http://www.mokeyjay.com/" target="_blank">mokeyjay</a> &amp;  <a href="http://fyy1999.lofter.com/" target="_blank">FYY</a> &amp; <a href="http://www.stusgame.com/" target="_blank">StusGame</a>
