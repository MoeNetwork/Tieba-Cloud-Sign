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
		<span id="avatar" style="float:right;"><img src="<?php echo getGravatar() ?>" alt="您的头像" title="您的头像" class="img-rounded" height='80px' width='80px' onerror="$('#avatar').html('无法加载头像');"></span>
		<?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?>.<?php echo SYSTEM_REV ?> <?php echo SYSTEM_VER_NOTE ?>
		<br/>
		点击上方导航栏的 功能菜单 可以列出所有功能
		<br/>
		此程序作者为  <a href="http://zhizhe8.net" target="_blank">Kenvix</a> &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a> &amp; <a href="http://fyy.l19l.com/" target="_blank">FYY</a>
		<br/>本站 [ <?php echo SYSTEM_NAME ?> ] 保留所有权利
	<?php doAction('index_p_1'); ?>
	</div>
</div>

<?php if(defined('CON_NAME')) {?>
<div class="alert alert-danger" role="alert">
	<b><?php echo CON_NAME; ?></b>，你现在正在控制用户 <b><?php echo NAME; ?></b>
	<br/>点击<a href="index.php?mod=usercontrolback">右上角的 返回 按钮或此处</a>即可返回你的账户
</div>
<?php } ?>

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
			echo '<span id="baiduid_used">' . count($i['user']['bduss']).'</span> 个 / ';
			if ($i['opt']['bduss_num'] != '0' && $i['opt']['bduss_num'] != '-1' && ISVIP == false) { 
				echo '<span id="baiduid_limit">'.$i['opt']['bduss_num'].'</span> 个'; 
			} elseif ($i['opt']['bduss_num'] == '-1') {
				echo '<span id="baiduid_limit">禁止绑定</span>'; 
			} else { 
				echo '<span id="baiduid_limit">无限</span>'; 
			} // ('.getrole(ROLE).')
			echo '<div class="progress hidden-xs" style="float:right;width:45%">
  <div class="progress-bar" role="progressbar" aria-valuenow="0" id="baiduid_prog" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
  </div>
</div>';
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
				echo '<span id="tb_used">' . $i['user']['tbnum'].'</span> 个 / ';
				if ($i['opt']['tb_max'] != '0' && $i['opt']['tb_max'] != '-1' && ISVIP == false) { 
					echo '<span id="tb_limit">' . $i['opt']['tb_max'] . '</span> 个'; 
				} elseif ($i['opt']['tb_max'] == '-1') {
					echo '<span id="tb_limit">禁止刷新</span>'; 
				} else { 
					echo '<span id="tb_limit">无限</span>'; 
				} 
				echo '<div class="progress hidden-xs" style="float:right;width:45%">
  <div class="progress-bar" role="progressbar" aria-valuenow="0" id="tb_prog" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
  </div>
</div>';
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
			echo '<li class="list-group-item"><b>计划任务上次执行日期：</b>'.(empty($i['opt']['cron_last_do_time']) ? '<font color="red">从未运行</font>' : $i['opt']['cron_last_do_time']);
			if (time() - strtotime($i['opt']['cron_last_do_time']) > 86400) { //如果是'从未执行'，结果就为time()
				echo '<br/><font color="red"><span class="glyphicon glyphicon-warning-sign"></span> <b>警告：</b></font>计划任务今天尚未运行，是否已设置 <b>do.php</b> 到您的主机的计划任务？';
			}
			echo '</li>';
			if (!file_exists(SYSTEM_ROOT . '/setup/install.lock')) {
				echo '<li class="list-group-item"><font color="red"><span class="glyphicon glyphicon-warning-sign"></span> <b>安全性警告：</b></font>未找到 <b>/setup/install.lock</b> 文件，站点将有被恶意重装的风险，请务必建立一个空的 install.lock 文件，<a href="setting.php?mod=admin:create_lock">点此建立</a>';
			}
			doAction('index_p_3');
		
            if(defined('SYSTEM_KEY')) {
                echo '<li class="list-group-item">商业授权密钥：' . SYSTEM_KEY . '</li>';
            }
		?>
		</li>
	</ul>
</div>

<div class="panel panel-danger">
	<div class="panel-heading">
		<h3 class="panel-title">服务器信息</h3>
	</div>
	<ul class="list-group" id="server">
	读取中...
	</ul>
</div>
<?php
}

//由于历史原因，挂载点有2个
doAction('index_3');
doAction('index_2');
echo '<br/>'.SYSTEM_FN ?> V<?php echo SYSTEM_VER  . ' ' . SYSTEM_VER_NOTE ?> // 作者: <a href="http://zhizhe8.net" target="_blank">Kenvix</a> &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a> &amp; <a href="http://fyy.l19l.com/" target="_blank">FYY</a>

<script type="text/javascript">
	$.ajax({ 
	  async:true, 
	  url: 'ajax.php?mod=admin:server', 
	  type: "GET", 
	  data : {},
	  dataType: 'HTML', 
	  timeout: 90000, 
	  success: function(data){
	  	$("#server").html(data);
	  },
	  error: function(error){
	  	$("#server").html("服务器信息读取失败。");
	  }
	});
	<?php if ($i['opt']['bduss_num'] != '0' && $i['opt']['bduss_num'] != '-1' && ISVIP == false) { ?>
	var baiduid = Math.round($("#baiduid_used").html() / $("#baiduid_limit").html() * 100);
	$("#baiduid_prog").html(baiduid + '%');
	$("#baiduid_prog").css("width",baiduid + '%');
	$("#baiduid_prog").attr("aria-valuenow",baiduid);
	<?php } ?>
	<?php if ($i['opt']['tb_max'] != '0' && $i['opt']['tb_max'] != '-1' && ISVIP == false) { ?>
	var tb = Math.round($("#tb_used").html() / $("#tb_limit").html() * 100);
	$("#tb_prog").html(tb + '%');
	$("#tb_prog").css("width",tb + '%');
	$("#tb_prog").attr("aria-valuenow",tb);
	<?php } ?>
</script>