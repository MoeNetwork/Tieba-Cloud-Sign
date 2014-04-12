<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足！'); }
global $m;

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">应用成功</div>';
}
doAction('admin_tools_1');
?>
<div id="comsys"></div>
<script type="text/javascript">
	function checkupd() {
		$("#comsys").html('<div class="alert alert-info">正在检查，请稍后... 正在尝试联系服务器...<br/>如果长时间没有响应，可能是无法连接到服务器</div><br/>');
		$.ajax({ 
		  async:true, 
		  url: 'http://support.zhizhe8.net/plugins_update.php?name=tc&callback=?', 
		  type: "GET", 
		  data : {},
		  dataType: 'jsonp', 
		  timeout: 90000, 
		  success: function(data){
		     if (data.version > <?php echo SYSTEM_VER ?>) {
		        $("#comsys").html('<div class="alert alert-warning">有新版本可用，请点击下面的按钮更新 [ 本地版本：<?php echo SYSTEM_VER ?> | 最新正式版本 '+data.version+' ]<br/><button type="button" onclick="location = \'http://zhizhe8.net/?post=85736\';">>>> 点击此处开始进行程序升级</button></div><br/>');
		     } else {
		        $("#comsys").html('<div class="alert alert-success">您当前正在使用的版本已为最新版本 [ 本地版本：<?php echo SYSTEM_VER ?> | 最新正式版本 '+data.version+' ]<br/><a href="javascript:;" onclick="alert(\''+data.upds+'\');">点击查看正式版本('+data.version+')更新内容</a></div><br/>');
		     }  
		     console.log(data);  },
		  error: function(error){
		  	console.log(error)
		     $("#comsys").html('<div class="alert alert-danger">检查更新失败：无法连接到更新服务器<br/>错误已经记录到控制台，打开控制台查看详细</div><br/>');
		  }
		});
	}
</script>

<input type="button" onclick="checkupd();" class="btn btn-success" value="检查云签到的更新">&nbsp;&nbsp;&nbsp;&nbsp;请经常检查软件更新

<br/><br/><input type="button" onclick="location = '<?php echo SYSTEM_URL ?>setting.php?mod=admin:tools&setting=optim'" class="btn btn-primary" value="优化所有的数据表">&nbsp;&nbsp;&nbsp;&nbsp;可清除所有数据表的多余数据

<br/><br/><input type="button" onclick="location = '<?php echo SYSTEM_URL ?>setting.php?mod=admin:tools&setting=fixdoing'" class="btn btn-primary" value="修复计划任务状态">&nbsp;&nbsp;&nbsp;&nbsp;可解决运行计划任务始终提示已经有一个计划任务正在运行中的问题

<br/><br/><input type="button" onclick="location = '<?php echo SYSTEM_URL ?>setting.php?mod=admin:tools&setting=reftable'" class="btn btn-primary" value="扫描空闲的签到表">&nbsp;&nbsp;&nbsp;&nbsp;扫描空闲的签到数据表，用户注册时系统会自动扫描
<?php doAction('admin_tools_2'); ?>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>