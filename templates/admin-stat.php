<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足！'); }
global $m,$i;

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">设置保存成功</div>';
}

if (!isset($i['mode'][2])) $i['mode'][2] = 'sign';

switch ($i['mode'][2]) {

case 'sign' : 
?>
<ul class="nav nav-tabs" role="tablist">
  <li class="active"><a href="index.php?mod=admin:stat:sign">签到</a></li>
  <li><a href="index.php?mod=admin:stat:env">环境</a></li>
  <?php doAction('stat_navi'); ?>
</ul>
<h3>查看签到信息和用户信息</h3>
<table class="table table-striped">
	<thead>
		<th>UID</th>
		<th>用户名</th>
		<th>已绑定数</th>
		<th>签到忽略数</th>
		<th>签到出错数</th>
		<th>贴吧总数</th>
	</thead>
	<tbody>
		<?php 
		$uxsv = $m->query("SELECT * FROM `".DB_PREFIX."users`");
		while ($uxs = $m->fetch_array($uxsv)) {
			$uxsc = $m->once_fetch_array("SELECT COUNT(*) AS `c` FROM `".DB_PREFIX."baiduid` WHERE `uid` = ".$uxs['id']);
			$uxsb = $m->once_fetch_array("SELECT COUNT(*) AS `c` FROM `".DB_PREFIX.$uxs['t']."` WHERE `no` != '0' AND `uid` = ".$uxs['id']);
			$uxsn = $m->once_fetch_array("SELECT COUNT(*) AS `c` FROM `".DB_PREFIX.$uxs['t']."` WHERE `status` != '0' AND `uid` = ".$uxs['id']);
			$uxsm = $m->once_fetch_array("SELECT COUNT(*) AS `c` FROM `".DB_PREFIX.$uxs['t']."` WHERE `uid` = ".$uxs['id']);
			echo '<tr><td>'.$uxs['id'].'</td><td>'.$uxs['name'].'</td><td>'.$uxsc['c'].'</td><td>'.$uxsb['c'].'</td><td>'.$uxsn['c'].'</td><td>'.$uxsm['c'].'</td>';
		}
		?>
	</tbody>
</table>
<?php break; case 'env' : ?>
<ul class="nav nav-tabs" role="tablist">
  <li><a href="index.php?mod=admin:stat:sign">签到</a></li>
  <li class="active"><a href="index.php?mod=admin:stat:env">环境</a></li>
  <?php doAction('stat_navi'); ?>
</ul>
<h3>当前服务器软件环境</h3>
<?php 
define('DO_NOT_LOAD_UI', true);
require SYSTEM_ROOT.'/setup/check.php';
?>
<?php break; default:  ?>
<ul class="nav nav-tabs" role="tablist">
  <li><a href="index.php?mod=admin:stat:sign">签到</a></li>
  <li><a href="index.php?mod=admin:stat:env">环境</a></li>
  <?php doAction('stat_navi'); ?>
</ul>
<?php break;
} ?>
<br/><br/><br/><br/>
<?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> // 作者: <a href="http://zhizhe8.net" target="_blank">无名智者</a> &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a>