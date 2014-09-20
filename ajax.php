<?php
require dirname(__FILE__).'/init.php';

switch (SYSTEM_PAGE) {
    case 'ajax:status':
		global $today;
		global $m;
		$count1 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `lastdo` = '".$today."' AND `uid` = ".UID));
		$count1 = $count1[0];
		$count2 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `lastdo` != '".$today."' AND `uid` = ".UID));
		$count2 = $count2[0];
		echo "<b>签到状态：</b>已签到 {$count1} 个贴吧，还有 {$count2} 个贴吧等待签到";
		if (ROLE == 'admin') {
		$count1 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `lastdo` = '".$today."' AND `no` != '1'"));
		$count1 = $count1[0];
		$count2 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `lastdo` != '".$today."' AND `no` != '1'"));
		$count2 = $count2[0];
		$count5 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `no` = '1' AND `status` = '0'"));
		$count5 = $count5[0];
		$count6 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `status` != '0' AND `no` != '1'"));
		$count6 = $count6[0];
		$othertable = unserialize(option::get('fb_tables'));
		if (!empty($othertable)) {
			foreach ($othertable as $value) {
				$count3 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.$value."` WHERE `lastdo` = '".$today."' AND `no` != '1'"));
				$count4 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.$value."` WHERE `lastdo` != '".$today."' AND `no` != '1'"));
				$count1 = $count1 + $count3[0];
				$count2 = $count2 + $count4[0];
				$count7 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.$value."` WHERE `no` = '1' AND `status` = '0'"));
				$count8 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.$value."` WHERE `status` != '0' AND `no` != '1'"));
				$count5 = $count5 + $count7[0];
				$count6 = $count6 + $count8[0];
			}
		}


		echo "<br/><br/><b>签到状态[总体]：</b>已签到 {$count1} 个贴吧，还有 {$count2} 个贴吧等待签到";
		echo "<br/><br/><b>贴吧状态[总体]：</b>有 {$count5} 个贴吧签到出错，{$count6} 个贴吧已被设定为忽略";
		echo '<br/><br/><b>用户注册/添加用户首选表：</b>'.DB_PREFIX.option::get('freetable');
		}
		break;


	case 'admin:update': 
		$c=new wcurl(SUPPORT_URL . 'download.txt');
		$data=json_decode($c->exec());
		$c->close();
		if($data!=""){
			$t="";
			//是否有升级脚本
			if(isset($data->updatefile)){ echo "<input type=\"hidden\" name=\"updatefile\" value=\"{$data->updatefile}\">"; }
			//检测文件是否存在以及MD5是否相同
			foreach ($data->items->file as $file) {
				if(file_exists(SYSTEM_ROOT.$file->path)){
					$md5=md5(file_get_contents(SYSTEM_ROOT.$file->path));
					if($file->md5!=$md5){
						$t.="- {$file->path} <input type=\"hidden\" name=\"file[]\" value=\"{$file->path}\"><br/>";
					}
				} else {
					$t.="- {$file->path} <input type=\"hidden\" name=\"file[]\" value=\"{$file->path}\"><br/>";
				}
			}
			//检测文件夹是否缺失
			if($t!=""){
				foreach ($data->items->dir as $dir) {
					if(!is_dir(SYSTEM_ROOT.$dir)){
						$t.="<input type=\"hidden\" name=\"dir[]\" value=\"{$dir}\">";
					}
				}
			}
			echo $t;
		}
		break;

	case 'admin:update:updnow':
		if (!is_dir(SYSTEM_ROOT.'/setup/update_cache')) {
			mkdir(SYSTEM_ROOT.'/setup/update_cache');
		}
		if(isset($_POST['dir'])){//如果需要创建目录
			foreach ($_POST['dir'] as $dir) {
				mkdir(SYSTEM_ROOT.'/setup/update_cache'.$dir);
			}
		}
		foreach ($_POST['file'] as $file) {
			$c = new wcurl('http://git.oschina.net/kenvix/Tieba-Cloud-Sign/raw/master'.$file);
			file_put_contents(SYSTEM_ROOT.'/setup/update_cache'.$file, $c->exec());
			$c->close();
		}
		ReDirect('ajax.php?mod=admin:update:install&updfile=' . $_POST['updatefile']);
		break;

	case 'admin:update:install':
		CopyAll(SYSTEM_ROOT.'/setup/update_cache',SYSTEM_ROOT);
		DeleteFile(SYSTEM_ROOT.'/setup/update_cache');
		file_put_contents(SYSTEM_ROOT . '/setup/update.txt', wcurl::xget(SUPPORT_URL . 'download.txt'));
		if (!empty($_GET['updatefile'])) {
			ReDirect(SYSTEM_URL . $_GET['updatefile']);
		} else {
			msg('站点升级完毕', SYSTEM_URL);
		}
		break;

	case 'baiduid:getverify':
		$x = new wcurl('http://wappass.baidu.com/passport/',array('User-Agent: fxxxx phone'));
		$r = $x->post(array(
			'login_username'  => $_POST['bd_name'],
			'login_loginpass' => $_POST['bd_name']
			));
		preg_match('/\<img src=\"(.*)\" alt=\"wait...\" \/\>/',$r, $out);
		if (empty($out[1])) {
			echo '<b>无需验证码，请直接点击 [ 点击绑定 ] 继续</b>';
		} else {
			echo '<img onclick="addbdid_getcode();" src="'.$out[1].'"style="float:left;">&nbsp;&nbsp;&nbsp;请在下面输入左图中的字符<br>&nbsp;&nbsp;&nbsp;点击图片更换验证码';
			echo '<br/><br/><div class="input-group"><span class="input-group-addon">验证码</span>';
			echo '<input type="text" class="form-control" id="bd_v" name="bd_v" placeholder="请输入上图的字符" required></div><br/>';
		}
		preg_match('/\<input type=\"hidden\" id=\"vcodeStr\" name=\"vcodestr\" value=\"(.*)\"\/\>/', $r, $outt);
		echo '<input type="hidden" id="vcodeStr" name="vcodestr" value="'.$outt['1'].'"/>';
		break;

	case 'baiduid:bdid':
		//多次循环有助于解决验证码问题
		for ($e = 0; $e < 2; $i++) {
			$x = misc::loginBaidu( $_POST['bd_name'] , $_POST['bd_pw'] , $_POST['bd_v'] , $_POST['vcodestr'] );
			if (stristr($x, '您输入的验证码有误') || stristr($x, urlencode('您输入的验证码有误'))) {
				$error  = '您输入的验证码有误';
				if ($e < 2) {
			 	break;
				} else {
					continue;
				}
			} elseif (stristr($x, '您输入的密码有误') || stristr($x, urlencode('您输入的密码有误'))) {
				$error  = '您输入的密码或账号有误';
				break;
			} elseif (stristr($x, '请您输入验证码') || stristr($x, urlencode('请您输入验证码'))) {
			    $error  = '您没有输入验证码或发生系统错误';
			    break;
			} elseif (stristr($x, '请您输入验证码') || stristr($x, urlencode('请您输入验证码'))) {
				$error  = '请您输入密码';
				break;
			} else {
				preg_match('/Set-Cookie:(.*)BDUSS=(.*); expires=/', $x, $y);
				if (empty($y[2])) {
				    $error = '请检查用户名，密码，验证码是否正确';
				    break;
				} else {
					unset($error);
					break;
				}
			}
		}
		if (!empty($error)) {
			echo '{"error":"1","msg":"'.$error.'"}';
		} else {
			echo '{"error":"0","bduss":"'.$y[2].'"}';
		}
}
?>