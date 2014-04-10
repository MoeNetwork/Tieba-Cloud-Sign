<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足！'); }
global $m;

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">设置保存成功</div>';
}

function addset($name,$type,$x,$other = '',$text = '') {
	if ($type == 'checkbox') {
		if (option::get($x) == 1) {
			$other .= ' checked="checked"';
		}
		$value = '1';
	} else {
		$value = option::get($x);
	}
	echo '<tr><td>'.$name.'</td><td><input type="'.$type.'" name="'.$x.'" value="'.$value.'" '.$other.'>'.$text.'</td>';
}
?><form action="setting.php?mod=admin:set" method="post">
<table class="table table-striped">
	<thead>
		<tr>
			<th style="width:35%">参数</th>
			<th style="width:65%">值</th>
		</tr>
	</thead>
	<tbody>
		<?php
		addset('站点地址<br/>后面必须带上 /','text','system_url',' class="form-control"');
		addset('单表单次签到执行数量<br/>0为一次性全部签到','number','cron_limit','min="0" step="1" class="form-control"','注意这是控制单个表的，当你有N个表时，单次签到数量为 N × 分表数');
		addset('最大关注贴吧数量<br/>0为不限,对管理员无效','number','tb_max','min="0" step="1" class="form-control"');
		addset('自定义底部信息<br/>支持 HTML','text','footer',' class="form-control"');
		?>
		<tr><td>注册杂项设置</td><td>
		<input type="checkbox" name="enable_reg" value="1" <?php if(option::get('enable_reg') == 1) { echo 'checked'; } ?>> 允许用户注册<br/>
		<input type="checkbox" name="protect_reg" value="1" <?php if(option::get('protect_reg') == 1) { echo 'checked'; } ?>> 反恶意注册
		</td>
		<?php addset('邀请码设置<br/>留空表示无需邀请码','text','yr_reg',' class="form-control"'); ?>
		</td>
		<tr><td>贴吧数据分表<br/><br/>全部留空为不分表<br/>每行一个表名，无需填写表前缀<br/>错误的设置将导致签到程序不能正常工作<br/>当某一表存储的贴吧记录数目明显超过设定值时才能生效<br/>单个用户将终生使用某一表，所有请设置小点<br/>当所有的表的记录都超过设定值时，新的贴吧将往最后一个表写</td><td>
		<div class="input-group">
			  <span class="input-group-addon">记录超过此行数时分表</span>
			  <input type="number" min="2" step="1" class="form-control" name="fb" value="<?php echo option::get('fb') ?>">
		</div><br/>
		<textarea class="form-control" style="height:150px" name="fb_tables"><?php
		if (is_array(unserialize(option::get('fb_tables')))) {
			$temp = '';
			foreach (unserialize(option::get('fb_tables')) as $value) {
				$temp .= $value."\n";
			}
			echo trim($temp,"\n");
			unset($value);
		}
		?></textarea>
		</td>
		<?php
		addset('ICP 备案信息<br/>没有请留空','text','icp',' class="form-control"');
		addset('依靠访客触发任务','checkbox','trigger',null,' 建议仅在不支持计划任务并拒绝加入云平台时使用');
		addset('启用安全保护模块','checkbox','protector',null,' 建议开启');
		doAction('admin_set');
		addset('加入云平台','checkbox','cloud',null,' 建议开启，选择关闭将不连接云平台获取BDUSS并且不提供云触发器');
		addset('开发者模式','checkbox','dev',null,' 生产环境建议关闭');
		?>
	</tbody>
</table><input type="submit" class="btn btn-primary" value="提交更改">
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>