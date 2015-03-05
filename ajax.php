<?php
require dirname(__FILE__).'/init.php';

switch (SYSTEM_PAGE) {

	case 'ajax:status':
		global $today,$m,$i;
		$count1 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `lastdo` = '".$today."' AND `uid` = ".UID));
		$count2 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `lastdo` != '".$today."' AND `uid` = ".UID));
		echo "<br/><b>签到状态：</b>已签到 {$count1[0]} 个贴吧，还有 {$count2[0]} 个贴吧等待签到";
		echo '<br/><b>您的签到数据表：</b>'.DB_PREFIX.TABLE;
		$c3 = $c4 = $c5 = $c6 = 0;
		if (ROLE == 'admin') {
			foreach ($i['table'] as $value) {
				$count3 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.$value."` WHERE `lastdo` = '".$today."' AND `no` != '1'"));
				$count4 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.$value."` WHERE `lastdo` != '".$today."' AND `no` != '1'"));
				$count5 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.$value."` WHERE `no` = '1' AND `status` = '0'"));
				$count6 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.$value."` WHERE `status` != '0' AND `no` != '1'"));
				$c3 = $c3 + $count3[0];
				$c4 = $c4 + $count4[0];
				$c5 = $c5 + $count5[0];
				$c6 = $c6 + $count6[0];
			}	
			echo "<br/><b>签到状态[总体]：</b>已签到 {$c3} 个贴吧，还有 {$c4} 个贴吧等待签到";
			echo "<br/><b>贴吧状态[总体]：</b>有 {$c5} 个贴吧签到出错，{$c6} 个贴吧已被设定为忽略";
			echo '<br/><b>用户注册/添加用户首选表：</b>'.DB_PREFIX.option::get('freetable');
		}
		break;


	case 'admin:update': 
		$c = new wcurl(SUPPORT_URL . 'callback.php');
		$json = json_decode($c->exec(),true);
		$c->close();
		if(count($json) != 0){
			if($json['version'] > SYSTEM_VER || $json['revision'] > SYSTEM_REV){
				echo '<form method="post" action="ajax.php?mod=admin:update:updnow">';
				echo  '<div class="bs-callout bs-callout-danger">
  <h4>有更新可用</h4>
  <br/>最新版本：V'.$json['version'].'.'.$json['revision'].'
  <span style="float:right">提交时间：'.$json['time'].'</span>
  <br/>更新描述：'.$json['message'].'
  <br/>上次更新描述：'.$json['lastMessage'].'
  <br/>文件将被临时下载到 /setup/update_cache 文件夹，更新前会自动备份文件以供回滚
</div>';
				if($json['revision'] == '0' || $json['version'] > SYSTEM_VER){
					echo '<input type="submit" class="btn btn-primary" value="更新到最新正式版"><br/><br/></form>';
				} else {
					echo '<div class="alert alert-danger" role="alert">开发版着重于尝鲜和更迭，但存在一定的不稳定性，更新请谨慎，后果自负哦。稳定版<a href="http://www.stus8.com/forum.php?mod=viewthread&tid=2141" target="_blank">点此下载</a></div>';
					echo '<input type="submit" class="btn btn-primary" value="更新到最新开发版"><br/><br/></form>';
				}
			} else {
				echo '<div class="alert alert-success">您当前正在使用最新版本的 '.SYSTEM_FN.'，无需更新</div>';
			}
		} else {
			echo '<div class="alert alert-info">无法连接到更新服务器，请<a href="http://www.stus8.com/forum.php?mod=viewthread&tid=2141">手动更新</a></div>';
		}
		break;

	case 'admin:update:updnow':
		if (!file_exists(SYSTEM_ROOT . '/setup/update_backup/')) {
			mkdir(SYSTEM_ROOT . '/setup/update_backup/', 0777, true);
		}
		if (!file_exists(UPDATE_CACHE)) {
			mkdir(UPDATE_CACHE, 0777, true);
		}

		//下载zip包
		switch (option::get('update_server')) {
			case '1':
				$c = new wcurl(UPDATE_SERVER_GITHUB);
				$floderName = UPDATE_FNAME_GITHUB;
				break;
			case '2':
				$c = new wcurl(UPDATE_SERVER_CODING);
				$floderName = UPDATE_FNAME_CODING;
				break;
			case '3':
				$c = new wcurl(UPDATE_SERVER_GITCAFE);
				$floderName = UPDATE_FNAME_GITCAFE;
				break;
			default:
				$c = new wcurl(UPDATE_SERVER_OSCGIT);
				$floderName = UPDATE_FNAME_OSCGIT;
				break;
		}
		$file = $c->exec();
		$c->close();
		$zipPath = UPDATE_CACHE.'update_'.time().'.zip';
		if(file_put_contents($zipPath, $file) === false){
			DeleteFile(UPDATE_CACHE);
			msg('错误 - 更新失败：<br/><br/>无法从更新服务器下载更新包');
		}

		//备份
		/*
        $file = SYSTEM_ROOT . '/setup/update_backup/'.date('Y-m-d H-i-s').'-'.getRandStr(7).'.zip';
        $z = new zip();
        $z->open($file,8);
        $z->backup();
        $z->close();
        */

		//解压缩
		$z = new zip();
		$z->open($zipPath);
		$z->extract(UPDATE_CACHE);
		$z->close();

		//检查更新文件
		$floderName = UPDATE_CACHE.$floderName;
		if(!is_dir($floderName)){
			DeleteFile(UPDATE_CACHE);
			msg('错误 - 更新失败：<br/><br/>无法解压缩更新包');
		}

		//删除配置文件
		if (file_exists($floderName.'/config.php')) {
			unlink($floderName.'/config.php');
		}
		if (file_exists($floderName.'/app.conf')) {
			unlink($floderName.'/app.conf');
		}
		if (file_exists($floderName.'/config.yaml')) {
			unlink($floderName.'/config.yaml');
		}
		
		//覆盖文件
		if(CopyAll($floderName,SYSTEM_ROOT) !== true){
			DeleteFile(UPDATE_CACHE);
			msg('错误 - 更新失败：<br/><br/>无法更新文件');
		}
		DeleteFile(UPDATE_CACHE);
		//获取最新的版本号
		$c = new wcurl(SUPPORT_URL . 'callback.php');
		$json = json_decode($c->exec(),true);
		$c->close();
		//修改版本号
		option::set('core_revision',$json['revision']);
		if($json['version'] > SYSTEM_VER){
			//每次主版本号变动必须有更新脚本，数据库中主版本号由更新脚本修改
			$updatefile = '<br/><br/>本次升级需要运行升级脚本。请点击运行： <a href="setup/update'.SYSTEM_VER.'to'.$json['version'].'.php">update'.SYSTEM_VER.'to'.$json['version'].'.php</a><br/>如果升级脚本不存在，可能是由于您本次更新跨越了多个版本，您需要依次运行每一个脚本。<br/>';
			msg('恭喜您！您已成功升级到 V'.$json['version'].'.'.$json['revision'].$updatefile,false);
		}
		msg('恭喜您！您已成功升级到 V'.$json['version'].'.'.$json['revision'], SYSTEM_URL);
		break;

	case 'admin:update:changeServer':
		if(isset($_GET['server'])){
			option::set('update_server',$_GET['server']);
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
		for ($e = 0; $e < 2; $e++) {
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