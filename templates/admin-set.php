<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足！'); }
global $m;

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">设置保存成功</div>';
}
elseif(isset($_GET['mailtestok'])) {
	echo '<div class="alert alert-success">一封包含附件 ( README.md ) 的邮件已经发送到您的邮箱 '.option::get('mail_name').'，请查收</div>';
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
	echo '<tr><td>'.$name.'</td><td><input type="'.$type.'" name="'.$x.'" value="'.htmlspecialchars($value).'" '.$other.'>'.$text.'</td>';
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
		addset('签到失败重试次数<br/>0为无限，-1为不重试','number','retry_max','min="-1" step="1" class="form-control"');
		?>
		<tr><td>注册杂项设置</td><td>
		<input type="checkbox" name="enable_reg" value="1" <?php if(option::get('enable_reg') == 1) { echo 'checked'; } ?>> 允许用户注册<br/>
		<input type="checkbox" name="protect_reg" value="1" <?php if(option::get('protect_reg') == 1) { echo 'checked'; } ?>> 反恶意注册
		</td>
		<?php addset('邀请码设置<br/>留空表示无需邀请码','text','yr_reg',' class="form-control"'); ?>
		</td>
		</tr>
		<?php /*
		<tr><td>签到模式<br/>设置多个可以在某一签到失败时由其他模式代替签到</td><td>
		<?php $sign_mode = unserialize(option::get('sign_mode')); ?>
		<input type="checkbox" name="sign_mode[]" value="0" <?php if (isset($sign_mode[0])) { echo 'selected'; } ?> > 移动端普通签到<br/>
		<input type="checkbox" name="sign_mode[]" value="1" <?php if (isset($sign_mode[1])) { echo 'selected'; } ?> > 模拟客户端签到
		</td>
		*/ ?>
		</td>
		</tr>
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
		<tr><td>邮件综合设置
		<br/><br/><br/><input type="button" class="btn btn-default" onclick="location = '<?php echo SYSTEM_URL; ?>setting.php?mod=testmail'" value="测试邮件发送">
		<br/><br/>测试前请先保存设置
		</td><td>
		<div class="input-group">
			  <span class="input-group-addon">邮件发送模式</span>
			  <select name="mail_mode" class="form-control"  onchange="if(this.value == 'SMTP') { $('#smtp_set').show(); } else { $('#smtp_set').hide(); }">
			  	<option value="MAIL" <?php if(option::get('mail_mode') == 'MAIL') { echo 'selected'; } ?>>PHP Mail 函数</option>
			  	<option value="SMTP" <?php if(option::get('mail_mode') == 'SMTP') { echo 'selected'; } ?>>SMTP [ 支持验证 ]</option>
			  </select>
			</div><br/>


			<div class="input-group">
			  <span class="input-group-addon">发件人邮箱</span>
			  <input type="email" name="mail_name" class="form-control"  value="<?php echo option::get('mail_name') ?>">
			</div><br/>

			<div class="input-group">
				<span class="input-group-addon">发件人名称</span>
				<input type="text" name="mail_yourname" class="form-control" value="<?php echo option::get('mail_yourname') ?>" >
			</div><br/>

			<div id="smtp_set" <?php if(option::get('mail_mode') != 'SMTP') { echo 'style="display:none;"'; } ?>>
				<div class="input-group">
					<span class="input-group-addon">SMTP服务器地址</span>
				    <input type="text" name="mail_host" class="form-control"  value="<?php echo option::get('mail_host') ?>">
				</div><br/>
				<div class="input-group">
					<span class="input-group-addon">SMTP服务器端口</span>
					<input type="number" name="mail_port" class="form-control"  value="<?php echo option::get('mail_port') ?>">
				</div><br/>

				<div class="input-group">
				  <span class="input-group-addon">需要身份验证</span>
				  <select name="mail_auth" class="form-control"  onchange="if(this.value == '1') { $('#smtp_set_auth').show(); } else { $('#smtp_set_auth').hide(); }">
				  	<option value="1" <?php if(option::get('mail_auth') == '1') { echo 'selected'; } ?>>是</option>
				  	<option value="0" <?php if(option::get('mail_auth') == '0') { echo 'selected'; } ?>>否</option>
				  </select>
				</div><br/>

				<div id="smtp_set_auth" <?php if(option::get('mail_auth') == '0') { echo 'style="display:none;"'; } ?>>
					<div class="input-group">
					  <span class="input-group-addon">SMTP用户名</span>
					  <input type="text" name="mail_smtpname" class="form-control" value="<?php echo option::get('mail_smtpname') ?>">
					</div><br/>

					<div class="input-group">
					  <span class="input-group-addon">SMTP密码</span>
					  <input type="password" name="mail_smtppw" class="form-control" value="<?php echo option::get('mail_smtppw') ?>">
					</div><br/>
				</div>
			</div>

		</td>
		</td>
		<?php
		addset('ICP 备案信息<br/>没有请留空','text','icp',' class="form-control"');
		addset('依靠访客触发任务','checkbox','trigger',null,' 建议仅在不支持计划任务并拒绝加入云平台时使用');
		addset('启用安全保护模块','checkbox','protector',null,' 建议开启');
		addset('允许手动添加贴吧','checkbox','enable_addtieba',null,' 开启后用户可以手动添加任何贴吧，添加贴吧时会忽略贴吧数量上限');
		addset('密码保存模式<br/>支持PHP，无需加 &lt?php 或 ?&gt 使用 $pwd 表示变量密码，填写\'或者"需要加上转义符\\，只能写一条语句，后面不需要带 ;','text','pwdmode',' class="form-control"','<br/>结果：'.highlight_string('<?php '.option::get('pwdmode').'; ?>',true));
		doAction('admin_set');
		addset('加入云平台','checkbox','cloud',null,' 建议开启，选择关闭将不连接云平台获取BDUSS并且不提供云触发器');
		addset('开发者模式','checkbox','dev',null,' 生产环境建议关闭');
		?>
	</tbody>
</table><input type="submit" class="btn btn-primary" value="提交更改">
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>