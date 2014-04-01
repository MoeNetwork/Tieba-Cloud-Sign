<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } ?>
<div id="guide_page_1">
<span id="avatar" style="float:right;"><img src="<?php echo getGravatar(EMAIL,140) ?>" alt="您的Gravatar头像" title="您的Gravatar头像" class="img-thumbnail" onerror="$('#avatar').html('无法加载 Gravatar 头像');"></span>
<?php doAction('index_1'); echo NAME ;?>，你好，欢迎使用 百度贴吧云签到<br><br>
点击上方导航栏的 功能菜单 可以列出所有功能
<br/><br/>
此程序作者为  <a href="http://zhizhe8.net" target="_blank">无名智者</a> (@ <a href="http://www.stus8.com/" target="_blank">StusGame GROUP</a>)
<br><br/>如果有人向您出售此程序或利用此程序盈利，<a href="mailto:kenvix@vip.qq.com">请点击此处举报(有奖)</a>
</div>
<?php
if (BDUSS == null) {
	echo '<br/><br/><b>配置状态：</b>无法自动签到 - 云签到未配置';
} else {
	global $m;
	$today = date('Y-m-d');
	$count1 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `lastdo` = '".$today."'"));
	$count1 = $count1[0];
	$count2 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `lastdo` != '".$today."'"));
	$count2 = $count2[0];
	if (ROLE == 'admin') {
		echo '<br/><br/><b>权限：</b>管理员';
	}
	elseif (ROLE == 'user') {
		echo '<br/><br/><b>权限：</b>用户';
	}
	else {
		echo '<br/><br/><b>权限：</b>未定义';
	}
	echo '<br/><br/><b>配置状态：</b>云签到已配置，<a href="index.php?mod=log">点击查看签到日志</a>';
	echo "<br/><br/><b>签到状态：</b>已签到 {$count1} 个贴吧，还有 {$count2} 个贴吧等待签到";
}
doAction('index_2');
?>
<br/><br/><br/>

<?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>