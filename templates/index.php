<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } ?>
<div id="guide_page_1">
<span id="avatar" style="float:right;"><img src="<?php echo getGravatar(EMAIL,140) ?>" alt="您的Gravatar头像" title="您的Gravatar头像" class="img-thumbnail" onerror="$('#avatar').html('无法加载 Gravatar 头像');"></span>
<?php global $m,$i; doAction('index_1'); echo NAME ;?>，你好，欢迎使用 百度贴吧云签到<br><br>
点击上方导航栏的 功能菜单 可以列出所有功能
<br/><br/>
此程序作者为  <a href="http://zhizhe8.net" target="_blank">无名智者</a> (@ <a href="http://www.stus8.com/" target="_blank">StusGame GROUP</a>)
<br/><br/>本站 [ <?php echo SYSTEM_URL ?> ] 保留所有权利
</div>
<?php
doAction('index_3');
if (empty($i['user']['bduss'])) {
	echo '<br/><br/><b>配置状态：</b>无法自动签到 - 云签到未配置';
} else {
	$today = date('Y-m-d');
	echo '<br/><br/><b>配置状态：</b>云签到已配置，<a href="index.php?mod=log">点击查看签到日志</a>';
}
	echo '<br/><br/><div id="stat" onclick="view_status(this);"><button type="button" class="btn btn-info">点击查看签到状态统计信息</button></div>';
	echo '<br/><br/><b>权限：</b>'.getrole(ROLE);
	if (ROLE == 'admin') {
		echo '<br/><br/><b>计划任务上次执行日期：</b>'.option::get('cron_last_do_time');
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
		echo '<!--QQ邮件列表订阅嵌入代码--><br/><br/><script>var nId = "f752182ed774de32ef9ee39fbb5e44e38261368b16e7ea44",nWidth="auto",sColor="light",sText="请填写您的邮件地址，订阅 StusGame 云签到官方订阅，以便于及时接收关于云签到程序的更新与重要通知：" ;</script><script src="https://list.qq.com/zh_CN/htmledition/js/qf/page/qfcode.js" charset="gb18030"></script>';
	}
doAction('index_2');
?>
<br/><br/><br/>

<?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> // 作者: <a href="http://zhizhe8.net" target="_blank">无名智者</a>