<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } ?>
<div id="guide_page_1">
<span id="avatar" style="float:right;"><img src="<?php echo getGravatar() ?>" alt="您的头像" title="您的头像" class="img-thumbnail" onerror="$('#avatar').html('无法加载头像');"></span>
<?php global $m,$i; doAction('index_1'); echo NAME ;?>，你好，欢迎使用 百度贴吧云签到<br><br>
点击上方导航栏的 功能菜单 可以列出所有功能
<br/><br/>
此程序作者为  <a href="http://zhizhe8.net" target="_blank">无名智者</a> (@ <a href="http://www.stus8.com/" target="_blank">StusGame GROUP</a>)
<br/><br/>本站 [ <?php echo SYSTEM_URL ?> ] 保留所有权利
</div>

<?php
doAction('index_3');
	$today = date('Y-m-d');
	echo '<br/><br/><div id="stat" onclick="view_status(this);"><button type="button" class="btn btn-info">点击查看签到状态统计信息</button></div>';
	echo '<br/><br/><b>权限：</b>'.getrole(ROLE);
	if (ROLE == 'admin') {
		echo '<br/><br/><b>计划任务上次执行日期：</b>'.option::get('cron_last_do_time');
		if (time() - strtotime(option::get('cron_last_do_time')) > 86400) {
			echo '<br/><br/><font color="red"><span class="glyphicon glyphicon-warning-sign"></span> <b>警告：</b></font>计划任务今天尚未运行，是否已设置 <b>do.php</b> 到您的主机的计划任务？</font>';
		}
		echo '<br/><br/><b>关注贴吧配额限制：</b>无限制(管理员)';
	}
	elseif(option::get('tb_max') == 0) {
		echo '<br/><br/><b>关注贴吧配额限制：</b>无限制';
	}
	else {
		echo '<br/><br/><b>关注贴吧配额限制：</b>'.option::get('tb_max') .' 个';
	}
	echo '<br/><br/><b>您的签到数据表：</b>'.DB_PREFIX.TABLE;
	if (ROLE == 'admin') {
		echo '<br/><br/>';
		echo '<div class="well"><p class="info">请填写您的邮件地址，订阅 StusGame 云签到官方订阅，以便于及时接收关于云签到程序的更新与重要通知：</p><div class="mailInput"><form action="https://list.qq.com/cgi-bin/qf_compose_send" target="_blank" method="post"><input type="hidden" name="t" value="qf_booked_feedback"><input type="hidden" name="id" value="f752182ed774de32ef9ee39fbb5e44e38261368b16e7ea44"><div class="input-group">
  <input type="hidden" name="mod" value="baiduid">
  <span class="input-group-addon">输入邮箱地址</span>
  <input type="text" class="form-control" name="to">
  <span class="input-group-btn"><input type="submit" class="btn btn-primary" value="点击订阅"></span></div></form></div></div>';
	}
doAction('index_2');
?>
<br/><br/><br/>

<?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> // 作者: <a href="http://zhizhe8.net" target="_blank">无名智者</a> &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a>
