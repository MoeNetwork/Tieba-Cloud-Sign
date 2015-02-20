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
		$c=new wcurl(SUPPORT_URL . 'check.php?ver=' . SYSTEM_VER);
		$data=json_decode($c->exec());
		$c->close();
		$d = '';
		if($data!=""){
			$t="";
			//预先提供文件夹列表
			foreach ($data->items->dir as $dir) {
				$d .= '<input type="hidden" name="dir[]" value="'.$dir.'">';
			}
			//是否有升级脚本
			if(isset($data->updatefile)){ echo "<input type=\"hidden\" name=\"updatefile\" value=\"{$data->updatefile}\">"; }
			//检测文件是否存在以及MD5是否相同
			foreach ($data->items->file as $file) {
				if(file_exists(SYSTEM_ROOT.$file->path)){
					$md5=md5(file_get_contents(SYSTEM_ROOT.$file->path));
					if($file->md5!=$md5){
						$t.="<input type=\"checkbox\" name=\"file[]\" value=\"{$file->path}\" checked> {$file->path} <br/>";
					}
				} else {
					$t.="<input type=\"checkbox\" name=\"file[]\" value=\"{$file->path}\" checked> {$file->path} <br/>";
				}
			}
			if (!empty($t)) {
				echo '<form method="post" action="ajax.php?mod=admin:update:updnow">';
				echo  '<div class="bs-callout bs-callout-danger">
  <h4>有更新可用</h4>
  <br/>最新版本：V'.$data->version.'
  <span style="float:right">提交时间：'.$data->date.'</span>
  <br/>更新描述：'.$data->msg.'
  <br/>上次更新描述：'.$data->lastmsg.'
  <br/>文件将被临时下载到 /setup/update_cache 文件夹，更新前会自动备份文件以供回滚
</div>';
				echo '<div class="alert alert-warning"><form action="ajax.php?mod=admin:update:updnow" method="post"><b>以下文件可以更新</b>:<br/>';
				echo '<input type="hidden" name="server" value="'.intval($_GET['server']).'">';
				echo $d.$t;
				echo '</div><input type="submit" class="btn btn-primary" value="更新上述文件到最新正式版本"><br/><br/></form>';
			} else {
				echo '<div class="alert alert-success">您当前正在使用最新版本的 '.SYSTEM_FN.'，无需更新</div>';
			}
		} else {
			echo '<div class="alert alert-info">无法连接到更新服务器，请前往<a href="https://git.oschina.net/kenvix/Tieba-Cloud-Sign">OSCGit</a>自行更新</div>';
		}
		break;

	case 'admin:update:updnow':
		$backup = SYSTEM_ROOT.'/setup/update_backup/' . time() . '-' . getRandStr(7);

		switch ($_POST['server']) {
			case '2':
				$server = 'https://raw.githubusercontent.com/kenvix/Tieba-Cloud-Sign/master';
				break;

			case '3':
				$server = 'https://coding.net/u/kenvix/p/Tieba-Cloud-Sign/git/raw/master';
				break;

			case '4':
				$server = 'http://gitcafe.com/kenvix/Tieba-Cloud-Sign/raw/master';
				break;
			
			default:
				$server = 'https://git.oschina.net/kenvix/Tieba-Cloud-Sign/raw/master';
				break;
		}

		mkdir(SYSTEM_ROOT . '/update_cache',0777,true);

		if(isset($_POST['dir'])){ //如果需要创建目录
			foreach ($_POST['dir'] as $dir) {
				mkdir(SYSTEM_ROOT.'/setup/update_cache'.$dir , 0777 , true);
				mkdir($backup.$dir , 0777 , true);
			}
		}

		mkdir($backup , 0777 , true); //创建更新备份
		file_put_contents($backup . '/__backup.ini', '[info]'."\r\n".'
name='.SYSTEM_NAME."\r\n".'
ver='.SYSTEM_VER."\r\n".'
time='.date('Y-m-d H:m:s') ."\r\n");

		foreach ($_POST['file'] as $file) {
			$c     = new wcurl($server.$file);
			$data  = $c->exec();
			$c->close();
			if (empty($data)) {
				DeleteFile(SYSTEM_ROOT.'/setup/update_cache');
				msg('错误：更新失败：<br/><br/>与更新服务器的连接中断：无法下载数据' . $server.$file);
			}
			file_put_contents(SYSTEM_ROOT.'/setup/update_cache'.$file, $data);
			copy(SYSTEM_ROOT . $file , $backup . $file);
		}
		ReDirect('ajax.php?mod=admin:update:install&updfile=' . $_POST['updatefile']);
		break;

	case 'admin:update:install':
		CopyAll(SYSTEM_ROOT.'/setup/update_cache',SYSTEM_ROOT);
		DeleteFile(SYSTEM_ROOT.'/setup/update_cache');
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
		break;

	default:
		msg('未定义操作');
		break;
}
?>