<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
global $m;

if (isset($_GET['setting'])) {
	option::set('system_url',$_POST['system_url']);
	option::set('cron_limit',$_POST['cron_limit']);
	option::set('tb_max',$_POST['tb_max']);
	option::set('footer',$_POST['footer']);
	option::set('enable_reg',$_POST['enable_reg']);
	option::set('protect_reg',$_POST['protect_reg']);
	option::set('yr_reg',$_POST['yr_reg']);
	option::set('icp',$_POST['icp']);
	header("Location: ".SYSTEM_URL.'index.php?mod=admin:set&ok');
}
elseif (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">设置保存成功</div>';
}

function addset($name,$type,$x,$other = '') {
	echo '<tr><td>'.$name.'</td><td><input type="'.$type.'" name="'.$x.'" class="form-control" value="'.option::get($x).'" '.$other.'></td>';
}

?><form action="index.php?mod=admin:set&setting" method="post">
<table class="table table-striped">
	<thead>
		<tr>
			<th style="width:35%">参数</th>
			<th style="width:65%">值</th>
		</tr>
	</thead>
	<tbody>
		<?php
		addset('站点地址<br/>后面必须带上 /','text','system_url');
		addset('单次签到执行数量<br/>0为一次性全部签到','number','cron_limit','min="0" step="1"');
		addset('最大关注贴吧数量<br/>0为不限,对管理员无效','number','tb_max','min="0" step="1"');
		addset('自定义底部信息<br/>支持 HTML','text','footer');
		?>
		<tr><td>注册杂项设置</td><td>
		<input type="checkbox" name="enable_reg" value="1" <?php if(option::get('enable_reg') == 1) { echo 'checked'; } ?>> 允许用户注册<br/>
		<input type="checkbox" name="protect_reg" value="1" <?php if(option::get('protect_reg') == 1) { echo 'checked'; } ?>> 反恶意注册
		</td>
		<tr><td>邀请码设置<br/><br/>留空表示无需邀请码<br/>允许用户注册时生效<br/>每行一个邀请码</td><td>
		<textarea name="yr_reg" class="form-control" style="height:130px"><?php echo option::get('yr_reg') ?></textarea>
		</td>
		<?php
		addset('ICP 备案信息<br/>没有请留空','text','icp');
		?>
	</tbody>
</table><input type="submit" class="btn btn-primary" value="提交更改">
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>