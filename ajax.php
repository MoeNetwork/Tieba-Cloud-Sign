<?php
require dirname(__FILE__).'/init.php';

switch (SYSTEM_PAGE) {

	case 'ajax:status':
		global $m,$i;
		$today = date('d');
		$count = array(
			'userSigned'  => 0,
			'userWaiting' => 0,
			'userError'   => 0,
			'allSigned'   => 0,
			'allWaiting'  => 0,
			'allNo'       => 0,
			'allError'    => 0
		);
		$signUser = $m->query("SELECT `latest`,`status` FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `uid` = ".UID." AND `no` = '0'");
		while($countUser = $m->fetch_array($signUser)) {
			if($countUser['latest'] == $today) {
				if($countUser['status'] != '0') {
					$count['userError']++;
				} else {
					$count['userSigned']++;
				}
			} else {
				$count['userWaiting']++;
			}
		}
		echo "<br/><b>签到状态：</b>已签到 {$count['userSigned']} 个贴吧，{$count['userError']} 个出错， {$count['userWaiting']} 个贴吧等待签到";
		echo '<br/><b>您的签到数据表：</b>'.DB_PREFIX.TABLE;

		if (ROLE == 'admin') {
			foreach ($i['table'] as $value) {
				$signTab = $m->query("SELECT `latest`,`status`,`no` FROM `".DB_NAME."`.`".DB_PREFIX.$value."`");
				while($countTab = $m->fetch_array($signTab)) {
					if($countTab['no'] != '0') {
						$count['allNo']++;
					} elseif($countTab['latest'] == $today) {
						if($countTab['status'] != '0') {
							$count['allError']++;
						} else {
							$count['allSigned']++;
						}
					} else {
						$count['allWaiting']++;
					}
				}
			}	
			echo "<br/><b>签到状态[总体]：</b>已签到 {$count['allSigned']} 个贴吧，还有 {$count['allWaiting']} 个贴吧等待签到";
			echo "<br/><b>贴吧状态[总体]：</b>有 {$count['allError']} 个贴吧签到出错，{$count['allNo']} 个贴吧已被设定为忽略";
			echo '<br/><b>用户注册/添加用户首选表：</b>'.DB_PREFIX.option::get('freetable');
		}
		break;

	case 'admin:server':
		?>
		<li class="list-group-item">
			<b>PHP 版本：</b><?php echo phpversion() ?>
			<?php if(ini_get('safe_mode')) { echo '线程安全'; } else { echo '非线程安全'; } ?>
		</li>
		<?php if(version_compare('5.3', phpversion()) === 1) { echo '<li class="list-group-item"><b>PHP 版本警告：</b><font color="red">PHP 版本太低</font>，未来云签到可能不再支持当前版本 <a href="http://php.net/manual/zh/appendices.php" target="_blank">查看如何升级</a></li>'; }?>
		<?php if(get_magic_quotes_gpc()) { echo '<li class="list-group-item"><b>性能警告：</b><font color="red">魔术引号被激活</font>，云签到正以低效率模式运行 <a href="http://php.net/manual/zh/security.magicquotes.whynot.php" target="_blank">为什么不用魔术引号</a> <a href="http://php.net/manual/zh/security.magicquotes.disabling.php" target="
		_blank">如何关闭魔术引号</a></li>'; }?>
		<li class="list-group-item">
			<b>MySQL 版本：</b><?php echo $m->getMysqlVersion() ?>
		</li>
		<?php if(!empty($_SERVER['SERVER_ADDR'])) { ?>
		<li class="list-group-item">
			<b>服务器地址：</b><?php echo $_SERVER['SERVER_ADDR'] ?>
		</li>
		<?php } ?>
		<li class="list-group-item">
			<b>服务器软件：</b><?php echo $_SERVER['SERVER_SOFTWARE'] ?>
		</li>
		<li class="list-group-item">
			<b>服务器系统：</b><?php echo php_uname('a') ?>
		</li>
		<li class="list-group-item">
			<b>程序最大运行时间：</b><?php echo ini_get('max_execution_time') ?>s
		</li>
		<li class="list-group-item">
			<b>POST许可：</b><?php echo ini_get('post_max_size'); ?>
		</li>
		<li class="list-group-item">
			<b>文件上传许可：</b><?php echo ini_get('upload_max_filesize'); ?>
		</li>
		<?php
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
				if(isset($data->with_script)) {
					echo '<div class="alert alert-danger" role="alert">该版本涉及到数据库更改，无法自动更新，请前往论坛了解详情</div>';
				} else {
					echo '<div class="alert alert-warning"><form action="ajax.php?mod=admin:update:updnow" method="post"><b>以下文件可以更新</b>:<br/>';
					echo '<input type="hidden" name="server" value="'.intval($_GET['server']).'">';
					echo $d.$t;
					echo '</div><input type="submit" class="btn btn-primary" value="更新上述文件到最新正式版本"><br/><br/></form>';
				}
			} else {
				echo '<div class="alert alert-success">您当前正在使用最新版本的 '.SYSTEM_FN.'，无需更新</div>';
			}
		} else {
			echo '<div class="alert alert-info">无法连接到更新服务器，请前往<a href="https://git.oschina.net/kenvix/Tieba-Cloud-Sign">OSCGit</a>自行更新</div>';
		}
		break;

	case 'admin:update:updnow':
		$backup = SYSTEM_ROOT.'/setup/update_backup/' . time() . '-' . getRandStr(7);

		switch (option::get('update_server')) {
			case '2':
				$server = UPDATE_FNAME_GITHUB;
				break;

			case '3':
				$server = UPDATE_FNAME_CODING;
				break;

			case '4':
				$server = UPDATE_FNAME_GITCAFE;
				break;

			default:
				$server = UPDATE_FNAME_OSCGIT;
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

	/*
	case 'admin:update': 
		$c    = new wcurl(SUPPORT_URL . 'get.php?ver=' . SYSTEM_VER);
		$data = json_decode($c->exec());
		$c->close();
		$d    = '';
		if(!empty($data)){
			$t = '';
			//预先提供文件夹列表
			foreach ($data->items->dir as $dir) {
				$d .= '<input type="hidden" name="dir[]" value="'.$dir.'">';
			}

			//检测文件是否存在以及MD5是否相同
			foreach ($data->items->file as $file) {
				if(file_exists(SYSTEM_ROOT.'/'.$file->path)){
					$md5 = md5_file(SYSTEM_ROOT.'/'.$file->path);
					if($file->md5 != $md5){
						$t.="<input type=\"checkbox\" name=\"file[]\" value=\"{$file->path}\" checked> <span class=\"glyphicon glyphicon-pencil\" title=\"修改\"></span>  {$file->path} <br/>";
					}
				} else {
					$t.="<input type=\"checkbox\" name=\"file[]\" value=\"{$file->path}\" checked><span class=\"glyphicon glyphicon-plus\" title=\"新增\"></span> {$file->path} <br/>";
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
				echo '<div class="alert alert-warning"><form action="ajax.php?mod=admin:update:updnow" method="post"><b>以下文件可以更新</b>: [ 不选表示不更新 ]<br/>';
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
        $file = SYSTEM_ROOT . '/setup/update_backup/'.date('Y-m-d H-i-s').'-'.getRandStr(7).'.zip';
        $z = new zip();
        $z->open($file,8);
        $z->backup();
        $z->close();
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
		*/

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
				    $error = '请检查用户名，密码，验证码是否正确<br/><br/>也有可能是百度强制开启了异地登陆保护导致的，请尝试手动绑定';
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

	case 'admin:c_update:check':
		global $i;
		$c = new wcurl(SUPPORT_URL.'getplug.php?m=ver&pname='.$_GET['plug']);
		$cloud = json_decode($c->exec(),true);
		$c->close();
		if(empty($cloud['version'])){
			die('未找到该产品信息');
		}
		echo '最新版本：'.$cloud['version'];
		if($cloud['version'] > $i['plugins']['desc'][$_GET['plug']]['plugin']['version']){
			echo '//'.$i['plugins']['desc'][$_GET['plug']]['plugin']['name'].' - 发现新版本//最新版本：'.$cloud['version'].'<br/>版本描述：'.$cloud['whatsnew'].'<br/>'.'本次更新需要支付 '.$cloud['updateXB'].' XB。<a href="setting.php?mod=admin:cloud&upd='.$_GET['plug'].'">立即更新</a>';
		}
		break;

	default:
		msg('未定义操作');
		break;
}
?>