<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足！'); }
global $m,$i;

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">设置保存成功</div>';
}
elseif(isset($_GET['mailtestok'])) {
	echo '<div class="alert alert-success">一封邮件已经发送到您的邮箱 '.$i['user']['email'].'，请查收</div>';
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
if (isset($i['mode'][2]) && $i['mode'][2] == 'sign') {
?>
<ul class="nav nav-tabs" role="tablist">
  <li><a href="index.php?mod=admin:set">全局设置</a></li>
  <li class="active"><a href="index.php?mod=admin:set:sign">签到设置</a></li>
</ul>
<form action="setting.php?mod=admin:set&type=sign" method="post">
<div class="table-responsive">
<table class="table table-hover">
	<thead>
		<tr>
			<th style="width:40%">参数</th>
			<th>值</th>
		</tr>
	</thead>
	<tbody>
	<?php
		addset('单表单次签到执行数量<br/>0为一次性全部签到。此功能非常重要，设置为0会导致每次都扫描贴吧表，效率极低，请按需修改','number','cron_limit','min="0" step="1" class="form-control"','注意这是控制单个表的，当你有N个表时，单次签到数量为 N × 分表数');
		addset('最大关注贴吧数量<br/>0为不限,对管理员无效','number','tb_max','min="0" step="1" class="form-control"');
		addset('签到失败重试次数<br/>0为无限，-1为不重试','number','retry_max','min="-1" step="1" class="form-control"');
		addset('签到开始时间<br/>24小时制。例如设为-1，则从0点开始签到','number','sign_hour','min="-1" step="1" max="24" class="form-control"');
		addset('签到间隔时间<br/>单位为毫秒，0为不暂停','number','sign_sleep','min="0" step="1" class="form-control"','适量的间隔时间可以防止签到过快而失败的问题，但会导致签到效率降低');
		addset('允许手动添加贴吧','checkbox','enable_addtieba',null,' 开启后用户可以手动添加任何贴吧，添加贴吧时会忽略贴吧数量上限');
		addset('在绑定百度账号时记录用户名','checkbox','baidu_name');
	?>
	<tr><td>签到模式设置<br/>选择多个将在某个模式失败后使用下一种<br/>启用的签到模式越多，消耗的流量和时间越多</td><td><?php $sign_mode = unserialize(option::get('sign_mode')) ?>
		<input type="checkbox" name="sign_mode[]" value="1" <?php if(!empty($sign_mode) && in_array('1',$sign_mode)) { echo 'checked'; } ?>> 模拟手机客户端签到<br/>
		<input type="checkbox" name="sign_mode[]" value="3" <?php if(!empty($sign_mode) && in_array('3',$sign_mode)) { echo 'checked'; } ?>> 手机网页签到<br/>
		<input type="checkbox" name="sign_mode[]" value="2" <?php if(!empty($sign_mode) && in_array('2',$sign_mode)) { echo 'checked'; } ?>> 网页签到
		</td>
	</tr>
	<tr><td>贴吧数据分表<br/><br/>全部留空为不分表<br/>每行一个表名，无需填写表前缀<br/>错误的设置将导致签到程序不能正常工作<br/>当某一表存储的贴吧记录数目明显超过设定值时才能生效<br/>单个用户将终生使用某一表，所以请设置小点<br/>当所有的表的记录都超过设定值时，新的贴吧将往最后一个表写</td><td>
		<div class="input-group">
			  <span class="input-group-addon">记录超过此行数时分表</span>
			  <input type="number" min="0" step="1" class="form-control" name="fb" value="<?php echo option::get('fb') ?>">
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
	</tr>
	</tbody>
</table>
</div>
<input type="submit" class="btn btn-primary" value="提交更改">
</form>
<?php
} else {
?>
<ul class="nav nav-tabs" role="tablist">
  <li class="active"><a href="index.php?mod=admin:set">全局设置</a></li>
  <li><a href="index.php?mod=admin:set:sign">签到设置</a></li>
</ul>
<form action="setting.php?mod=admin:set&type=system" method="post">
<div class="table-responsive">
<table class="table table-hover">
	<thead>
		<tr>
			<th style="width:40%">参数</th>
			<th>值</th>
		</tr>
	</thead>
	<tbody>
		<?php
		addset('站点地址<br/>后面必须带上 /','text','system_url',' class="form-control"');
		addset('站点名称<br/>支持 HTML','text','system_name',' class="form-control"');
		?>
		<tr><td>自定义底部信息<br/><br/>支持 HTML</td><td>
		<textarea name="footer" class="form-control" style="height:200px"><?php echo htmlspecialchars(option::get('footer')) ?></textarea>
		</td>
		</tr>
		<tr><td>主页公告信息<br/>较长公告建议以&lt;br/&gt;开头<br/>支持 HTML</td><td>
		<textarea name="ann" class="form-control" style="height:200px"><?php echo htmlspecialchars(option::get('ann')) ?></textarea>
		</td>
		</tr>
		<?php
		addset('最大允许用户绑定账号数<br/>0为无限，-1为禁止绑定，对管理员无效','number','bduss_num','min="-1" step="1" class="form-control"');
		addset('计划任务线程数<br/>0单线程，此为模拟多线程','number','sign_multith','min="0" step="1" class="form-control"');
		addset('计划任务同时运行<br/>主机需支持fsockopen','checkbox','cron_asyn','',' 当 do.php 被运行时，所有计划任务同时运行，有效提高计划任务效率，在高配机器上会加速任务，低配机器上可能会导致减速');
		addset('计划任务密码<br/>留空为无密码，不能包含空格等特殊字符','text','cron_pw',' class="form-control"','启用后需要通过访问 <b>do.php?pw=密码</b> 才能执行计划任务，POST/GET 均可');
		?>
		<tr><td>注册杂项设置</td><td>
		<input type="checkbox" name="enable_reg" value="1" <?php if(option::get('enable_reg') == 1) { echo 'checked'; } ?>> 允许用户注册<br/>
		<input type="checkbox" name="protect_reg" value="1" <?php if(option::get('protect_reg') == 1) { echo 'checked'; } ?>> 反恶意注册
		</td>
		</tr>
		<?php addset('邀请码设置<br/>留空表示无需邀请码','text','yr_reg',' class="form-control"'); ?>
		<tr><td>邮件综合设置
		<br/><br/><input type="button" class="btn btn-default" onclick="location = '<?php echo SYSTEM_URL; ?>setting.php?mod=testmail'" value="测试邮件发送">
		<br/><br/>测试前请先保存设置
		<br/><br/>无加密的SMTP服务器端口号通常为 25
		<br/>SSL加密的SMTP服务器端口号通常为 465
		</td><td>
		<div class="input-group">
			  <span class="input-group-addon">邮件发送模式</span>
			  <select name="mail_mode" class="form-control"  onchange="if(this.value == 'SMTP') { $('#smtp_set').show(); } else { $('#smtp_set').hide(); }">
			  	<option value="MAIL" <?php if(option::get('mail_mode') == 'MAIL') { echo 'selected'; } ?>>PHP Mail 函数 [ 不支持附件 ]</option>
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
					<span class="input-group-addon">SSL 加密</span>
					<select name="mail_ssl" class="form-control">
				  	<option value="0" <?php if(option::get('mail_ssl') == '0') { echo 'selected'; } ?>>否</option>
				  	<option value="1" <?php if(option::get('mail_ssl') == '1') { echo 'selected'; } ?>>是</option>
				  </select>
				</div>
				<br/><div class="input-group">
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
		</tr>
		<?php
		addset('ICP 备案信息<br/>没有请留空','text','icp',' class="form-control"');
		addset('依靠访客触发任务','checkbox','trigger',null,' 建议在不支持计划任务并拒绝加入云平台时使用，开启计划任务密码后无效');
		addset('启用安全保护模块','checkbox','protector',null,' 建议开启');
		addset('记住密码有效期<br/>单位为秒，过大会导致浏览器无法记录','number','cktime',' class="form-control" step="1" min="1"');
		doAction('admin_set');
		addset('环境为引擎','checkbox','isapp',null,' 如果您的主机不支持写入或者为应用引擎，请选择此项');
		addset('开发者模式','checkbox','dev',null,' 生产环境建议关闭');
		?>
	</tbody>
</table>
</div>
<input type="submit" class="btn btn-primary" value="提交更改">
</form>
<?php } ?>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> // 作者: <a href="http://zhizhe8.net" target="_blank">无名智者</a> &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a>
