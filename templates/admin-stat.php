<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足！'); }
global $m,$i;
$day = date('d');

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
<?php /* 数据缓存于 <?php echo date('Y-m-d H:i:s' , C::getTime('admin_stat')) ?>，点击刷新 */ ?>
<table class="table table-striped">
	<thead>
		<th>UID</th>
		<th>用户名</th>
		<th>已绑定数</th>
		<th>等待签到数</th>
		<th>签到成功数</th>
		<th>签到出错数</th>
		<th>签到忽略数</th>
		<th>贴吧总数</th>
	</thead>
	<tbody>
		<?php
		$uxsv = $m->query("SELECT * FROM `".DB_PREFIX."users` ORDER BY `id`");
		$uxsg = $m->once_fetch_array("SELECT COUNT(*) AS `c` FROM `".DB_PREFIX."baiduid`");
		$alls = $alle = $alln = $allm = $allw = 0;
		while ($uxs = $m->fetch_array($uxsv)) {
			$uxsc = $m->once_fetch_array("SELECT COUNT(*) AS `c` FROM `".DB_PREFIX."baiduid` WHERE `uid` = ".$uxs['id']);
			$list = $m->query("SELECT id,no,status,latest FROM `".DB_PREFIX.$uxs['t']."` WHERE `uid` = ".$uxs['id']);
			$success = $error = $no = $all = $waiting = 0;
			$num = $m->num_rows($list);
			while ($x = $m->fetch_array($list)) {
				if ($x['no'] == '1') {
					$no++;
				} elseif ($x['latest'] != $day) {
					$waiting++;
				} elseif ($x['status'] == '0') {
					$success++;
				} elseif ($x['status'] != '0') {
					$error++;
				}
			}
			$allw = $allw + $waiting;
			$alls = $alls + $success;
			$alln = $alln + $no;
			$allm = $allm + $num;
			$alle = $alle + $error;
			echo '<tr><td>'.$uxs['id'].'</td><td>'.$uxs['name'].'</td><td>'.$uxsc['c'].'</td><td>'.$waiting.'</td><td>'.$success.'</td><td>'.$error.'</td><td>'.$no.'</td><td>'.$num.'</td>';
		}
		echo '<tr><td colspan="2"><strong>总计数据</strong></td><td>'.$uxsg['c'].'</td><td>'.$allw.'</td><td>'.$alls.'</td><td>'.$alle.'</td><td>'.$alln.'</td><td>'.$allm.'</td>';

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
<?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER  . ' ' . SYSTEM_VER_NOTE ?> // 作者: <a href="https://kenvix.com" target="_blank">Kenvix</a>  &amp; <a href="http://www.mokeyjay.com" target="_blank">mokeyjay</a> &amp;  <a href="http://fyy1999.lofter.com/" target="_blank">FYY</a>  &amp; <a href="http://www.stusgame.com/" target="_blank">StusGame</a>
