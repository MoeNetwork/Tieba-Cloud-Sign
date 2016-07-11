<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足！'); }
global $m,$i;

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">设置保存成功</div>';
} elseif(isset($_GET['mailtestok'])) {
	echo '<div class="alert alert-success">一封邮件已经发送到您的邮箱 '.$i['user']['email'].'，请查收</div>';
} elseif(isset($_GET['bbstestok'])) {
	echo '<div class="alert alert-success">成功登陆到产品中心</div>';
}

if (isset($i['mode'][2]) && $i['mode'][2] == 'sign') {
	/*准备*/
	?>
	<ul class="nav nav-tabs" role="tablist">
	  <li><a href="index.php?mod=admin:set">全局设置</a></li>
	  <li class="active"><a href="index.php?mod=admin:set:sign">签到设置</a></li>
	</ul>
	<script>
		function viewSignScanModeHelp() {
			str  = '该设置影响签到效率，不当的设置可能会导致效率降低并漏签<br/><br/>';
			str += '<b>1.永不随机，按顺序抽取</b><br/>该模式是数据库性能最高的模式，不会有贴吧漏签，采取"先来后到"的签到原则。但是如果启用了多线程功能，这会导致一个贴吧被重复签到多次，导致整体签到效率降低<br/><br/>';
			str += '<b>2.随机，使用 JOIN()</b><br/>该模式的数据库性能仅次于永不随机模式，随机抽取没有签到的贴吧并为其签到。多线程模式下可以较好地分配签到任务到每个线程，但是可能会有极少数贴吧漏签；此外，如果频繁清空和刷入新贴吧，也会增加漏签率<br/><br/>';
			str += '<b>3.随机，使用 ORDER BY RAND()</b><br/>能够很好地分配签到任务到每个线程，随机抽取没有签到的贴吧并为其签到，不会有贴吧漏签。但是，一个表的贴吧数量越多，该模式的数据库性能就会越差，如果你能很好地使用分表功能，建议使用本选项';
			str += '<br/><br/><b><font color="red">注意：</font></b>此处所指的 "漏签"，是指因为没有被扫描到而引发的未签到，而不是签到失败';
			alert(str);
		}
	</script>
	<?php
	/*开始*/
	$set2['title'] = '签到设置';
	$set2['name'] = 'signset';
	$set2['url'] = 'setting.php?mod=admin:set&type=sign';
	$set2['method'] = '1';

	$content2['cron_limit'] = array('td1'=>'<b>单表单次签到执行数量</b><br/>0为一次性全部签到。此功能非常重要，设置为0会导致每次都扫描贴吧表，效率极低，请按需修改','type'=>'number','text'=>'注意这是控制单个表的，当你有N个表时，单次签到数量为 N × 分表数','extra'=>'min="0" step="1"');
	$content2['bduss_num'] = array('td1'=>'<b>最大允许用户绑定账号数</b><br/>0为无限，-1为禁止绑定，对管理员无效','type'=>'number','text'=>'','extra'=>'min="-1" step="1"');
	$content2['tb_max'] = array('td1'=>'<b>最大关注贴吧数量</b><br/>0为不限,对管理员无效','type'=>'number','text'=>'','extra'=>'min="0" step="1"');
	$bsphtml = '<tr><td><b>禁止重复添加同一百度账号</b><br/>禁止添加用户名一样的百度账号<br/>对管理员无效</td><td>
	            <label><input type="radio" name="same_pid" value="0" '.(option::get('same_pid') == '0' ? 'checked' : '').'> 不禁止(可以重复添加)</label><br/>
	            <label><input type="radio" name="same_pid" value="1" '.(option::get('same_pid') == '1' ? 'checked' : '').'> 仅禁止同一云签到账号重复添加</label><br/>
	            <label><input type="radio" name="same_pid" value="2" '.(option::get('same_pid') == '2' ? 'checked' : '').'> 全局禁止(一旦有用户添加则其他用户不能添加)</label>
	        </td>
	    </tr>';
	$content2['same_pid'] = array('html'=>$bsphtml,'type'=>'else');
	$content2['retry_max'] = array('td1'=>'<b>签到失败重试次数</b><br/>0为无限，-1为不重试','type'=>'number','text'=>'','extra'=>'min="-1" step="1"');
	$content2['sign_hour'] = array('td1'=>'<b>签到开始时间</b><br/>24小时制。例如设为-1，则从0点开始签到','type'=>'number','text'=>'','extra'=>'min="-1" step="1" max="24"');
	$content2['sign_sleep'] = array('td1'=>'<b>签到间隔时间</b><br/>单位为毫秒，0为不暂停','type'=>'number','text'=>'适量的间隔时间可以防止签到过快而失败的问题，但会导致签到效率降低','extra'=>'min="0" step="1"');
	$content2['enable_addtieba'] = array('td1'=>'<b>允许手动添加贴吧</b>','type'=>'checkbox','text'=>'开启后用户可以手动添加贴吧，添加时必须≤最大关注贴吧数量','extra'=>'');
	$sign_mode = unserialize(option::get('sign_mode'));
	$smhtml = '<tr><td><b>签到模式设置</b><br/>选择多个将在某个模式失败后使用下一种<br/>启用的签到模式越多，消耗的流量和时间越多</td><td>
	            <label><input type="checkbox" name="sign_mode[]" value="1" '.(in_array('1',$sign_mode) ? 'checked' : '').'> 模拟手机客户端签到</label><br/>
	            <label><input type="checkbox" name="sign_mode[]" value="3" '.(in_array('2',$sign_mode) ? 'checked' : '').'> 手机网页签到</label><br/>
	            <label><input type="checkbox" name="sign_mode[]" value="2" '.(in_array('3',$sign_mode) ? 'checked' : '').'> 网页签到</label>
	        </td>
	    </tr>';
	$content2['sign_mode'] = array('html'=>$smhtml,'type'=>'else');
	$content2['sign_scan'] = array('td1'=>'<b>贴吧数据表搜寻方法</b><br/><br/><input type="button" class="btn btn-default" onclick="viewSignScanModeHelp();" value="查看帮助">','type'=>'select','text'=>'<br/>该设置影响签到效率，不当的设置可能会导致效率降低并漏签');
	$content2['sign_scan']['select'] = array('0'=>'永不随机，按顺序抽取','1'=>'随机，使用 JOIN','2'=>'随机，使用 ORDER BY RAND()');
	$ft1 = option::get('fb');
	if (!empty($i['tabpart'])) {
		$temp = '';
		foreach ($i['tabpart'] as $value) {
			$temp .= $value."\n";
		}
		$ft2 = trim($temp,"\n");
		unset($value);
	} else {
		$ft2 = '';
	}
	$fthtml = '<tr><td><b>贴吧数据分表</b><br/><br/>全部留空为不分表<br/>每行一个表名，无需填写表前缀<br/>错误的设置将导致签到程序不能正常工作<br/>当某一表存储的贴吧记录数目明显超过设定值时才能生效<br/>单个用户将终生使用某一表，所以请设置小点<br/>当所有的表的记录都超过设定值时，新的贴吧将往最后一个表写</td><td>
		<div class="input-group">
			  <span class="input-group-addon">记录超过此行数时分表</span>
			  <input type="number" min="0" step="1" class="form-control" name="fb" value="'.$ft1.'">
		</div><br/>
		<textarea class="form-control" style="height:150px" name="fb_tables">'.$ft2.'</textarea>
		</td>
	</tr>';
	$content2['fb_tables'] = array('html'=>$fthtml,'type'=>'else');
	echo former::create($set2,$content2);
} else {
	/*准备*/
	?>
	<ul class="nav nav-tabs" role="tablist">
	  <li class="active"><a href="index.php?mod=admin:set">全局设置</a></li>
	  <li><a href="index.php?mod=admin:set:sign">签到设置</a></li>
	</ul>
	<?php
	/*开始*/
	$set1['title'] = '全局设置';
	$set1['name'] = 'systemset';
	$set1['url'] = 'setting.php?mod=admin:set&type=system';
	$set1['method'] = '1';

	$content1['system_url'] = array('td1'=>'<b>站点地址</b><br/>后面必须带上 /','type'=>'text','text'=>'','extra'=>'');
	$content1['system_name'] = array('td1'=>'<b>站点名称</b><br/>支持 HTML','type'=>'text','text'=>'','extra'=>'');
	$content1['system_keywords'] = array('td1'=>'<b>关键字</b>(Keywords)<br/>SEO功能，以半角逗号(,)为分隔符','type'=>'text','text'=>'','extra'=>'');
	$content1['system_description'] = array('td1'=>'<b>描述</b>(Description)<br/>SEO功能，以半角逗号(,)为分隔符','type'=>'text','text'=>'','extra'=>'');
	$footer = htmlspecialchars(option::get('footer'));
	$footerhtml = '<tr><td><b>自定义底部信息</b><br/><br/>支持 HTML</td><td>
		<textarea name="footer" class="form-control" style="height:200px">'.$footer.'</textarea>
		</td></tr>';
	$content1['footer'] = array('html'=>$footerhtml,'type'=>'else');
	$ann = htmlspecialchars(option::get('ann'));
	$annhtml = '<tr><td><b>主页公告信息</b><br/><br/>较长公告建议以&lt;br/&gt;开头<br/>支持 HTML</td><td>
		<textarea name="ann" class="form-control" style="height:200px">'.$ann.'</textarea>
		</td></tr>';
	$content1['ann'] = array('html'=>$annhtml,'type'=>'else');
	$content1['sign_multith'] = array('td1'=>'<b>计划任务线程数</b><br/>0单线程，此为模拟多线程','type'=>'number','text'=>'','extra'=>'min="0" step="1"');
	$content1['cron_asyn'] = array('td1'=>'<b>计划任务同时运行</b><br/>主机需支持fsockopen','type'=>'checkbox','text'=>'当 do.php 被运行时，所有计划任务同时运行，有效提高计划任务效率，在高配机器上会加速任务，低配机器上可能会导致减速','extra'=>'');
	$content1['cron_pw'] = array('td1'=>'<b>计划任务密码</b><br/>留空为无密码，不能包含空格等特殊字符<br/><a href="javascript:;" onclick="alert(\'你需要通过访问 <b>do.php?pw=密码</b> 执行计划任务<br/>例如：'.SYSTEM_URL.'do.php?pw=yourpassword<br/><br/>若您要通过命令行执行计划任务，请加上参数 <b>--pw=密码</b><br/>例如：php do.php --pw=yourpassword<br/>命令行模式注意：你需要指明do.php的绝对路径，或者将do.php加入PATH\')">帮助：启用密码功能后如何执行计划任务？</a>','type'=>'text','text'=>'','extra'=>'');
	$reg1 = option::get('enable_reg') == 1 ? ' checked' : '' ;
	$reg2 = option::get('protect_reg') == 1 ? ' checked' : '' ;
	$reg3 = option::get('yr_reg');
	$reg4 = option::get('stop_reg');
	$reghtml = '<tr><td><b>注册相关设置</b><br/><br/>邀请码框留空表示无需邀请码<br/><br/>停止注册提示框输入指定提示内容</td><td>
		<div class="input-group">
			&nbsp;&nbsp;<input type="checkbox" name="enable_reg" value="1"'.$reg1.'> 允许用户注册&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="checkbox" name="protect_reg" value="1"'.$reg2.'> 反恶意注册
		</div><br/>
		<div class="input-group">
			<span class="input-group-addon">邀请码设置</span><input type="text" name="yr_reg" id="yr_reg" value="'.$reg3.'" class="form-control">
		</div><br/>
		<div class="input-group">
			<span class="input-group-addon">停止注册提示</span><input type="text" name="stop_reg" id="stop_reg" value="'.$reg4.'" class="form-control">
		</div>
		</td></tr>';
	$content1['reg'] = array('html'=>$reghtml,'type'=>'else');
	$content1['icp'] = array('td1'=>'<b>ICP 备案信息</b><br/>没有请留空','type'=>'text','text'=>'','extra'=>'');
	$content1['trigger'] = array('td1'=>'<b>依靠访客触发任务</b>','type'=>'checkbox','text'=>'建议在不支持计划任务并拒绝加入云平台时使用，开启计划任务密码后无效','extra'=>'');
	$content1['cktime'] = array('td1'=>'<b>Cookie有效期</b><br/>单位为秒，过大会导致浏览器无法记录','type'=>'number','text'=>'','extra'=>'step="1" min="1"');
	$content1['csrf'] = array('td1'=>'<b>停用CSRF防御</b>','type'=>'checkbox','text'=>'贴吧云签到可以防御CSRF攻击，开启该选项会导致站点处于危险状态','extra'=>'');
	$content1['isapp'] = array('td1'=>'<b>环境为引擎</b>','type'=>'checkbox','text'=>'如果您的主机不支持写入或者为应用引擎，请选择此项','extra'=>'');
	$content1['dev'] = array('td1'=>'<b>开发者模式</b>','type'=>'checkbox','text'=>'生产环境下请勿开启','extra'=>'');
		/*警告：超长内容*/
	//内容较长时用缓冲区更方便
	ob_start();
	?>
		<tr><td><b>邮件综合设置</b>
		<br/><br/><input type="button" class="btn btn-default" onclick="location = '<?php echo SYSTEM_URL; ?>setting.php?mod=admin:testmail'" value="测试邮件发送">
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
					 	 <input type="text" onclick="$(this).attr('value','').attr('type','password').attr('name','mail_smtppw').removeAttr('readonly');" class="form-control" readonly value="保持原密码 ( 点击可以修改 )">
					</div><br/>
				</div>
			</div>
		</td>
		</tr>
	<?php
	$mailhtml = ob_get_clean();
	$content1['mail'] = array('html'=>$mailhtml,'type'=>'else');
	/*end 超长内容*/
	echo former::create($set1,$content1);
}
?>

<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER  . ' ' . SYSTEM_VER_NOTE ?> // 作者: <a href="http://zhizhe8.net" target="_blank">Kenvix</a>  &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a> &amp;  <a href="http://fyy.l19l.com/" target="_blank">FYY</a> 
