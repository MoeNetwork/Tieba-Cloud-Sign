<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }
global $i,$m;
$today = date('Y-m-d');
doAction('index_1');
?>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">程序信息</h3>
	</div>
	<div class="panel-body">
		<span id="avatar" style="float:right;"><img src="<?php echo getGravatar() ?>" alt="您的头像" title="您的头像" class="img-rounded" height='100px' weight='100px' onerror="$('#avatar').html('无法加载头像');"></span>
		点击上方导航栏的 功能菜单 可以列出所有功能
		<br/><br/>
		此程序作者为  <a href="http://zhizhe8.net" target="_blank">无名智者</a> @ <a href="http://www.stus8.com/" target="_blank">StusGame GROUP</a> &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a>
		<br/><br/>本站 [ <?php echo SYSTEM_NAME ?> ] 保留所有权利
	<?php doAction('index_p_1'); ?>
	</div>
</div>

<div class="panel panel-<?php if (empty($i['user']['tbnum'])) { echo 'warning'; } else { echo 'success'; } ?>">
	<div class="panel-heading">
		<h3 class="panel-title">用户信息</h3>
	</div>
	<ul class="list-group">
		<?php
			if(!empty($i['opt']['ann'])){
				echo '<li class="list-group-item"><span class="glyphicon glyphicon-bullhorn"></span> <b>公告：</b>';
				echo $i['opt']['ann'].'</li>';
			}
		?>
		<li class="list-group-item">
			<span class="glyphicon glyphicon-user"></span>
			<b>用户组：</b>
			<?php
				if(ISVIP) { echo '您是尊贵的 '.getrole(ROLE).'，享有无限绑定数和贴吧数等特权。'; } //<font color="orange"></font>
				else { echo getrole(ROLE); }
			?>
		</li>
		<li class="list-group-item">
		<?php if(empty($i['user']['bduss'])){ ?>
			<span class="glyphicon glyphicon-info-sign"></span>
			 您还没有绑定任何百度账号，无法使用云签到功能，<a href="index.php?mod=baiduid">前往绑定</a>
		<?php } else { ?>
			<span class="glyphicon glyphicon-link"></span> <b>百度账号数：</b>
		<?php
			echo count($i['user']['bduss']).'条 / ';
			if ($i['opt']['bduss_num'] != '0' && ISVIP == false) { echo $i['opt']['bduss_num'].' 条'; }
			else { echo '无限'; } // ('.getrole(ROLE).')
		}
		?>
		</li>
		<li class="list-group-item">
		<?php
			if(empty($i['user']['tbnum'])){ ?>
				<span class="glyphicon glyphicon-info-sign"></span>
				 您还没有绑定刷新贴吧列表，无法自动签到，<a href="index.php?mod=showtb">前往刷新</a>
			<?php } else { ?>
				<span class="glyphicon glyphicon-check"></span> <b>贴吧个数：</b>
			<?php
				echo $i['user']['tbnum'].'个 / ';
				if ($i['opt']['tb_max'] != '0' && ISVIP == false) { echo $i['opt']['tb_max'].' 个'; }
				else { echo '无限'; } 
			}
		?>
		</li>
		<?php doAction('index_p_2'); ?>
		<li class="list-group-item">
			<span class="glyphicon glyphicon-stats"></span>
			<b>签到状态统计信息：</b>
			<span id="stat" onclick="view_status(this);"><a href='javascript:void(0)'>点击查看</a></span>
		</li>
	</ul>
</div>

<?php 
if (ROLE == 'admin') {
?>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">管理面板</h3>
	</div>
	<ul class="list-group">
		<?php
			echo '<li class="list-group-item"><b>计划任务上次执行日期：</b>'.$i['opt']['cron_last_do_time'];
			if (time() - strtotime($i['opt']['cron_last_do_time']) > 86400) { //如果是'从未执行'，结果就为time()
				echo '<br/><font color="red"><span class="glyphicon glyphicon-warning-sign"></span> <b>警告：</b></font>计划任务今天尚未运行，是否已设置 <b>do.php</b> 到您的主机的计划任务？';
			}
			echo '</li>';
			if (!file_exists(SYSTEM_ROOT . '/setup/install.lock')) {
				echo '<li class="list-group-item"><font color="red"><span class="glyphicon glyphicon-warning-sign"></span> <b>安全性警告：</b></font>未找到 <b>/setup/install.lock</b> 文件，站点将有被恶意重装的风险，请务必建立一个空的 install.lock 文件，<a href="setting.php?mod=admin:create_lock">点此建立</a>';
			}
			doAction('index_p_3');
			echo '<li class="list-group-item"><p class="info">请填写您的邮件地址，订阅 StusGame 云签到官方订阅，以便于及时接收关于云签到程序的更新与重要通知：</p><div class="mailInput"><form action="https://list.qq.com/cgi-bin/qf_compose_send" target="_blank" method="post"><input type="hidden" name="t" value="qf_booked_feedback"><input type="hidden" name="id" value="f752182ed774de32ef9ee39fbb5e44e38261368b16e7ea44"><div class="input-group">
				<input type="hidden" name="mod" value="baiduid">
				<span class="input-group-addon">输入邮箱地址</span>
				<input type="text" class="form-control" name="to">
				<span class="input-group-btn"><input type="submit" class="btn btn-primary" value="点击订阅"></span></div></form></div></li>';
		?>
		</li>
	</ul>
</div>
<?php
}

//由于历史原因，挂载点有2个
doAction('index_3');
doAction('index_2');
echo '<br/>'.SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> // 作者: <a href="http://zhizhe8.net" target="_blank">无名智者</a> &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a>