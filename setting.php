<?php
/**
 * 设置保存页面
 */

require dirname(__FILE__).'/init.php';

if (ROLE != 'user' && ROLE != 'admin' && ROLE != 'vip') {
    msg('权限不足');
}

if (ROLE != 'admin' && stristr(strip_tags($_GET['mod']), 'admin:')) {
	msg('权限不足');
}

global $i;
global $m;

switch (SYSTEM_PAGE) {

	case 'admin:plugins':
		doAction('plugin_setting_1');
		if (isset($_GET['dis'])) {
			inactivePlugin($_GET['dis']);
		}
		elseif (isset($_GET['act'])) {
			activePlugin($_GET['act']);
		}
		elseif (isset($_GET['upd'])) {
			if(updatePlugin($_GET['upd']) == false){
				Redirect('index.php?mod=admin:plugins&error_msg='.urlencode("插件更新失败"));
			}
		}
		elseif (isset($_GET['uninst'])) {
			uninstallPlugin($_GET['uninst']);
		}
		elseif (isset($_GET['install'])) {
			if(!empty($_REQUEST['ver'])){
				msg ('该插件仅适用于 V'.$_REQUEST['ver'].' 及以上的版本，您的云签到版本低于插件所需最低版本，是否强制安装（强制安装可能造成云签到损坏）<br/><br/><a href="setting.php?mod=admin:plugins&install='.$_GET['install'].'">强制安装</a>　　<a href="setting.php?mod=admin:plugins">取消安装</a><br/>',false,true);
			}
			installPlugin($_GET['install']);
		}
		elseif (isset($_GET['xorder'])){
			global $m;
			foreach($_POST as $id=>$order){
				$m->query('Update `'.DB_NAME.'`.`'.DB_PREFIX."plugins` Set `order`={$order} Where `name`='{$id}'");
			}
		}
		doAction('plugin_setting_2');
		Redirect('index.php?mod=admin:plugins&ok');
		break;

	case 'admin:cloud':
		doAction('plugin_update_1');
		global $i;
		$plug = $i['plugins']['desc'][$_GET['upd']];

		if (!file_exists(UPDATE_CACHE)) {
			mkdir(UPDATE_CACHE, 0777, true);
		}

		$up_url = SUPPORT_URL.'getplug.php?m=up&pname='.$_GET['upd'].'&user='.option::get('bbs_us').'&pw='.option::get('bbs_pw');
		$c = new wcurl($up_url);
		$file = $c->exec();
		$c->close();

		if($file == 'WRONG'){
			msg('错误 - 更新失败：<br/><br/>产品中心拒绝了下载<br/>请检查全局设置中的账号是否正确以及是否购买过此插件');
		}
		
		$zipPath = UPDATE_CACHE.'update_plug_'.time().'.zip';
		if(file_put_contents($zipPath, $file) === false){
			DeleteFile(UPDATE_CACHE);
			msg('错误 - 更新失败：<br/><br/>无法下载更新包');
		}

		//解压缩
		$z = new zip();
		$z->open($zipPath);
		$z->extract(UPDATE_CACHE);
		$z->close();

		//检查更新文件
		$floderName = UPDATE_CACHE.$_GET['upd'];
		if(!is_dir($floderName)){
			DeleteFile(UPDATE_CACHE);
			msg('错误 - 更新失败：<br/><br/>无法解压缩更新包');
		}
		
		//覆盖文件
		if(CopyAll($floderName,SYSTEM_ROOT.'/plugins/'.$_GET['upd']) !== true){
			DeleteFile(UPDATE_CACHE);
			msg('错误 - 更新失败：<br/><br/>无法更新文件');
		}
		DeleteFile(UPDATE_CACHE);

		doAction('plugin_update_2');
		msg('（1/2）已成功下载最新版本的 '.$plug['plugin']['name'].' 插件。请单击下一步，以完成更新<br/><br/><a href="setting.php?mod=admin:plugins&upd='.$_GET['upd'].'">>> 下一步</a>',false);
		break;
	
	case 'admin:set':
		global $m;
		$sou = $_POST;
		if ($_GET['type'] == 'sign') {
			@option::set('cron_limit',$sou['cron_limit']);
			@option::set('tb_max',$sou['tb_max']);
			@option::set('bduss_num',$sou['bduss_num']);
			@option::set('sign_mode', serialize($sou['sign_mode']));
			@option::set('enable_addtieba',$sou['enable_addtieba']);
			@option::set('retry_max',$sou['retry_max']);
			@option::set('sign_hour',$sou['sign_hour']);
			@option::set('fb',$sou['fb']);
            @option::set('sign_sleep',$sou['sign_sleep']);
            @option::set('sign_scan',$sou['sign_scan']);
			if (empty($sou['fb_tables'])) {
				@option::set('fb_tables',NULL);
			} else {
				$fb_tables = explode("\n",$sou['fb_tables']);
				$fb_tab = array();
				$n= 0;
				foreach ($fb_tables as $value) {
					$n++;
					$value = strtolower($value);
					$sql = str_ireplace('{VAR-DB}', DB_NAME, str_ireplace('{VAR-TABLE}', trim(DB_PREFIX.$value), file_get_contents(SYSTEM_ROOT.'/setup/template.table.sql')));
					$m->query($sql);
					$fb_tab[$n] .= trim($value);
				}
				@option::set('fb_tables', serialize($fb_tab));
			}
		} else {
			@option::set('system_url',$sou['system_url']);
			@option::set('system_name',$sou['system_name']);
			@option::set('system_keywords',$sou['system_keywords']);
			@option::set('system_description',$sou['system_description']);
			@option::set('footer',$sou['footer']);
			@option::set('ann',$sou['ann']);
			@option::set('enable_reg',$sou['enable_reg']);
			@option::set('protect_reg',$sou['protect_reg']);
			@option::set('yr_reg',$sou['yr_reg']);
			@option::set('stop_reg',$sou['stop_reg']);
			@option::set('icp',$sou['icp']);
			@option::set('trigger',$sou['trigger']);
			@option::set('bbs_us',$sou['bbs_us']);
			@option::set('bbs_pw',$sou['bbs_pw']);
			@option::set('mail_mode',$sou['mail_mode']);
			@option::set('mail_name',$sou['mail_name']);
			@option::set('mail_yourname',$sou['mail_yourname']);
			@option::set('mail_host',$sou['mail_host']);
			@option::set('mail_port',$sou['mail_port']);
			@option::set('mail_auth',$sou['mail_auth']);
			@option::set('mail_ssl',$sou['mail_ssl']);
			@option::set('mail_smtpname',$sou['mail_smtpname']);
			if (isset($sou['mail_smtppw'])) {
				@option::set('mail_smtppw',$sou['mail_smtppw']);
			}
			@option::set('dev',$sou['dev']);
			@option::set('dev',$sou['dev']);
			@option::set('cron_pw',$sou['cron_pw']);
			@option::set('cron_asyn',$sou['cron_asyn']);
			@option::set('sign_multith',$sou['sign_multith']);
			@option::set('cktime',$sou['cktime']);
			@option::set('isapp',$sou['isapp']);
		}
		doAction('admin_set_save');
		Redirect('index.php?mod=admin:set:'. $_GET['type'].'&ok');
		break;

	case 'admin:tools':
		/*
		$toolpw = option::get('toolpw');
		if(!empty($_POST['toolpw'])){
			$cookies = md5(md5(md5($_POST['toolpw'])));
			if(empty($toolpw)){
				option::add('toolpw',$cookies);
				setcookie('toolpw',$cookies);
				Redirect('index.php?mod=admin:tools&ok');
			} else {
				setcookie('toolpw',$cookies);
				Redirect('index.php?mod=admin:tools');
			}
		}	
		if($_COOKIE['toolpw'] != $toolpw || empty($toolpw)){
			Redirect('index.php?mod=admin:tools');
		}
		*/
		switch (strip_tags($_GET['setting'])) {
			
		case 'optim':
			global $m;
			$rs=$m->query("SHOW TABLES FROM `".DB_NAME.'`');
			while ($row = $m->fetch_row($rs)) {
				$m->query('OPTIMIZE TABLE  `'.DB_NAME.'`.`'.$row[0].'`');
			}
			break;
		
		case 'fixdoing':
			option::set('cron_isdoing',0);
			break;

		case 'reftable':
			option::set('freetable',getfreetable());
			break;

		case 'cron_sign_again':
			option::set('cron_sign_again','');
			break;

		case 'runsql':
			global $m;
			if (!empty($_POST['sql'])) {
				$sql = str_ireplace('{VAR-DBNAME}', DB_NAME, str_ireplace('{VAR-PREFIX}', DB_PREFIX, $_POST['sql']));
				$m->xquery($sql);
			}
			break;

		case 'backup':
			global $m;
			$list  = !empty($_POST['tab']) ? array_map('addslashes', $_POST['tab']) : msg('请至少选择一个需要导出的表');
			$dump  = '#Warning: Do not change the comments!!!'  . "\n";
			$dump .= '#Tieba-Cloud-Sign Database Backup' . "\n";
			$dump .= '#Version:' . SYSTEM_VER . "\n";
			$dump .= '#Date:' . date('Y-m-d H:m:s') . "\n";
			$dump .= '############## Start ##############' . "\n";
			foreach ($list as $table) {
				$dump .= dataBak($table);
			}
			$dump .= "\n" . '############## End ##############';
			$file  = 'cloud_sign_' . date('Y-m-d_H-m-s') . '.sql';
			if (!empty($_POST['zip'])) {
				if (!is_dir(SYSTEM_ROOT.'/source/cache')) {
					mkdir(SYSTEM_ROOT.'/source/cache' , 0777 , true);
				}
				if(CreateZip($file , $dump , SYSTEM_ROOT.'/source/cache/' . $file . '.zip') === true) {
					header('Content-Type: application/zip');
					header('Content-Disposition: attachment; filename=' . $file . '.zip');
					echo file_get_contents(SYSTEM_ROOT.'/source/cache/' . $file . '.zip');
					unlink(SYSTEM_ROOT.'/source/cache/' . $file . '.zip');
					die;
				}
			}
			header('Content-Type: text/x-sql');
			header('Content-Disposition: attachment; filename=' . $file);
			echo $dump;
			die;
			break;

		case 'remtab':
			global $m;
			if (!empty($_POST['tab'])) {
				$m->query('DROP TABLE IF EXISTS `'.$_POST['tab'].'`');
			}
			break;

		case 'truntab':
			if (!empty($_POST['tab'])) {
				$m->query('TRUNCATE TABLE `'.$_POST['tab'].'`');
			}
			break;

		/*
		case 'updatefid':
			global $m;
			global $i;
			$fbs = $i['tabpart'];
				if (!isset($_GET['ok'])) {
				$step = $_GET['step'] + 30;
				$next = isset($_GET['in']) ? strip_tags($_GET['in']) : 'tieba';
				$c  = $m->once_fetch_array("SELECT COUNT(*) AS `x` FROM `".DB_PREFIX.$next."`");
				$c2 = $m->once_fetch_array("SELECT COUNT(*) AS `x` FROM `".DB_PREFIX.$next."` WHERE `fid` = '0'");
				if ($c2['x'] >= 1) {
					$x  = $m->query("SELECT * FROM `".DB_PREFIX.$next."` WHERE `fid` = '0' LIMIT 30");
					while ($r = $m->fetch_array($x)) {
						$fid = misc::getFid($r['tieba']);
						$m->query("UPDATE `".DB_PREFIX.$next."` SET  `fid` =  '{$fid}' WHERE `".DB_PREFIX.$next."`.`id` = {$r['id']};");
					}
				} else {
					$step = 0;
					if ($next == 'tieba') {
						$next = $fbs[1];
					} else {
						foreach ($fbs as $ke => $va) {
							if ($va == $next) {
								$newkey = $ke + 1;
								$next = $ke[$newkey];
								break;
							}
						}
					}
				}

				if (empty($next)) {
					msg('<meta http-equiv="refresh" content="0; url='.SYSTEM_URL.'index.php?mod=admin:tools&ok" />即将完成更新......程序将自动继续<br/><br/><a href="'.SYSTEM_URL.'index.php?mod=admin:tools&ok">如果你的浏览器没有自动跳转，请点击这里</a>',false);
				}

				msg('<meta http-equiv="refresh" content="0; url=setting.php?mod=updatefid&step='.$step.'&in='.$next.'" />已经更新表：'.DB_PREFIX.$next.' / 即将更新表：'.DB_PREFIX.$next.'<br/><br/>当前进度：'.$step.' / '.$c['x'].' <progress style="width:60%" value="'.$step.'" max="'.$c['x'].'"></progress><br/><br/>切勿关闭浏览器，程序将自动继续<br/><br/><a href="setting.php?mod=updatefid&step='.$step.'&in='.$next.'">如果你的浏览器没有自动跳转，请点击这里</a>',false);
				}
			break;
			*/

		case 'install_plugin':
			doAction('plugin_install_1');
			if (!class_exists('ZipArchive')) {
				msg('插件安装失败：你的主机不支持 ZipArchive 类，请返回');
			}
			if (!is_writable(SYSTEM_ROOT . '/plugins')) {
				msg('插件安装失败：你的主机不支持文件写入，请手动安装插件');
			}
			if (!isset($_FILES['plugin'])) {
				msg('若要安装插件，请上传插件包');
			}
			$file = $_FILES['plugin'];
			if (!empty($file['error'])) {
				msg('插件安装失败：在上传文件时发生错误：代码：' . $file['error']);
			}
			if (empty($file['size'])) {
				msg('插件安装失败：插件包大小无效 ( 0 Byte )，请确认压缩包是否已损坏');
			}
			$z	= new ZipArchive();
			if(!$z->open($file['tmp_name'])) {
				msg('插件安装失败：无法打开压缩包');
			}
			$rdc = explode('/', $z->getNameIndex(0), 2);
			$rd  = $rdc[0];
			if($z->getFromName($rd . '/' . $rd . '.php') === false) {
				msg('插件安装失败：插件包不合法，请确认此插件为'.SYSTEM_FN.'插件');
			}
			if(!$z->extractTo(SYSTEM_ROOT . '/plugins')) {
				msg('插件安装失败：解压缩失败');
			}
			doAction('plugin_install_2');
			msg('插件安装成功');
			break;

		default:
			msg('未定义操作');
			break;
		}
		doAction('admin_tools_doing');
		Redirect('index.php?mod=admin:tools&ok');
		break;

	case 'admin:users':
		switch (strip_tags($_POST['do'])) {
			case 'cookie':
				foreach ($_POST['user'] as $value) {
					$m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."baiduid` WHERE  `".DB_PREFIX."baiduid`.`uid` = ".$value);
				}
				doAction('admin_users_cookie');
				break;
			
			case 'clean':
				foreach ($_POST['user'] as $value) {
					CleanUser($value);
				}
				doAction('admin_users_clean');
				break;

			case 'delete':
				foreach ($_POST['user'] as $value) {
					DeleteUser($value);
				}
				doAction('admin_users_delete');
				break;

			case 'crole':
				if($value=='1'){
				msg("操作失败：权限不足，您无权修改站点创始人的权限。");
				} else {
					foreach ($_POST['user'] as $value) {
						if ($_POST['crolev'] == 'user') {
							$role = 'user';
						} 
						elseif ($_POST['crolev'] == 'admin') {
							$role = 'admin';
						} 
						elseif ($_POST['crolev'] == 'vip') {
							$role = 'vip';
						} 
						elseif ($_POST['crolev'] == 'banned') {
							$role = 'banned';
						}
					$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX."users` SET `role` = '{$role}' WHERE `".DB_PREFIX."users`.`id` = {$value}");
					}
				}
				doAction('admin_users_crole');
				break;

			case 'cset':
				foreach ($_POST['user'] as $value) {
					option::udel($value);
				}
				doAction('admin_users_cset');
				break;

			case 'add':
				$name = isset($_POST['name']) ? strip_tags($_POST['name']) : '';
				$mail = isset($_POST['mail']) ? strip_tags($_POST['mail']) : '';
				$pw   = isset($_POST['pwd']) ? strip_tags($_POST['pwd']) : '';
				$role = isset($_POST['role']) ? strip_tags($_POST['role']) : 'user';

				if (empty($name) || empty($mail) || empty($pw)) {
					msg('添加用户失败：请正确填写账户、密码或邮箱');
				}
				$x=$m->once_fetch_array("SELECT COUNT(*) AS total FROM `".DB_NAME."`.`".DB_PREFIX."users` WHERE name='{$name}'");
				if ($x['total'] > 0) {
					msg('添加用户失败：用户名已经存在');
				}
				$m->query('INSERT INTO `'.DB_NAME.'`.`'.DB_PREFIX.'users` (`id`, `name`, `pw`, `email`, `role`, `t`) VALUES (NULL, \''.$name.'\', \''.EncodePwd($pw).'\', \''.$mail.'\', \''.$role.'\', \''.getfreetable().'\');');
				doAction('admin_users_add');
				Redirect('index.php?mod=admin:users&ok');
				break;

			default:
				msg('未定义操作');
				break;
			}
		Redirect('index.php?mod=admin:users&ok');
		break;

	case 'admin:cron':
		doAction('cron_setting_1');
		if (!empty($_GET['act'])) {
			cron::aset($_GET['act'] , array('no' => 0));
		}
		elseif (!empty($_GET['dis'])) {
			cron::aset($_GET['dis'] , array('no' => 1));
		}
		elseif (isset($_GET['uninst'])) {
			cron::del($_GET['uninst']);
		}
		elseif (isset($_GET['add'])) {
			if(stripos($_POST['file'],'do.php') !== false){
				msg('<h4>请不要将do.php加入到云签的计划任务中来</h4>若需签到，请用云监控监控<br/>'.SYSTEM_URL.'do.php<br/>即可实现计划任务(cron)的效果<br/><br/>推荐云监控:<a href="http://www.aliyun.com/product/jiankong/" target="_blank">阿里云监控</a> 或 <a href="http://jk.cloud.360.cn/" target="_blank">360网站服务监控</a> 或 <a href="http://ce.baidu.com/" target="_blank">百度云观测</a><br/>如果你的服务器在国外且国内访问较慢，则推荐使用:<a href="http://www.mywebcron.com/" target="_blank">Free Web Cron Service </a>',SYSTEM_URL.'index.php?mod=admin:cron');
			} else {
				cron::set($_POST['name'], $_POST['file'], $_POST['no'], $_POST['status'], $_POST['freq'] ,$_POST['lastdo'], $_POST['log']);
			}
			
		}
		elseif (isset($_GET['run'])) {
			$return = cron::run($_GET['file'], $_GET['run']);
			cron::aset($_GET['run'] , array('lastdo' => time() , 'log' => $return));
		}
		elseif (isset($_GET['xorder'])) {
			foreach ($_POST['order'] as $key => $value) {
				cron::aset($key , array('orde' => $value));
			}
		}
		doAction('cron_setting_2');
		Redirect('index.php?mod=admin:cron&ok');
		break;

	case 'admin:update:back':
		if (isset($_GET['del'])) {
			if (file_exists(SYSTEM_ROOT . '/setup/update_backup/' . $_GET['del'])) {
				DeleteFile(SYSTEM_ROOT . '/setup/update_backup/' . $_GET['del']);
			}
			Redirect('index.php?mod=admin:update:back&ok');
		}

		if (isset($_GET['dir'])) {
			if (file_exists(SYSTEM_ROOT . '/setup/update_backup/' . $_GET['dir'] . '/__backup.ini')) {
				if(CopyAll(SYSTEM_ROOT . '/setup/update_backup/' . $_GET['dir'] , SYSTEM_ROOT) !== true) {
					msg('版本回滚失败');
				}
				unlink(SYSTEM_ROOT . '/__backup.ini');
				msg('版本回滚成功','index.php');
			} else {
				msg('版本回滚失败：该备份不存在或不正确');
			}
		}
		break;

	case 'admin:create_lock':
		if (!file_put_contents(SYSTEM_ROOT . '/setup/install.lock', '1')) {
			$msg = '未能放置 install.lock，请手动完成。<br/><br/>';
		} else {
			$msg = '系统成功放置 install.lock，但是如果您的环境为引擎，您必须手工放置<br/><br/>';
		}
		$msg .= '若要手工放置，请在云签到的 setup 目录下建立一个 install.lock 文件';
		msg($msg);
		break;

	case 'baiduid':
		if (isset($_GET['delete'])) {
			doAction('baiduid_set_1');
			CleanUser(UID);
			$m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."baiduid` WHERE `".DB_PREFIX."baiduid`.`uid` = ".UID);
		}
		elseif (!empty($_GET['bduss'])) {
			if (option::get('bduss_num') == '-1' && ROLE != 'admin') msg('本站禁止绑定新账号');

			if (option::get('bduss_num') != '0' && ISVIP == false) {
				$count = $m->once_fetch_array("SELECT COUNT(*) AS `c` FROM `".DB_NAME."`.`".DB_PREFIX."baiduid` WHERE `".DB_PREFIX."baiduid`.`uid` = ".UID);
				if (($count['c'] + 1) > option::get('bduss_num')) msg('您当前绑定的账号数已达到管理员设置的上限<br/><br/>您当前已绑定 '.$count['c'].' 个账号，最多只能绑定 '.option::get('bduss_num').' 个账号'); 
			}
			// 去除双引号和bduss
			$bduss = str_replace('"', '', $_GET['bduss']);
			$bduss = str_ireplace('BDUSS=', '', $bduss);
			$bduss = sqladds($bduss);
			$baidu_name = sqladds(getBaiduId($bduss));
			if (empty($baidu_name)) {
				msg('您的 BDUSS Cookie 信息有误，请核验后重新绑定');
			}
			doAction('baiduid_set_2');
			$m->query("INSERT INTO `".DB_NAME."`.`".DB_PREFIX."baiduid` (`uid`,`bduss`,`name`) VALUES  (".UID.", '{$bduss}', '{$baidu_name}')");
		}
		elseif (!empty($_GET['del'])) {
			$del = (int) $_GET['del'];
			doAction('baiduid_set_3');
			$x=$m->once_fetch_array("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."users` WHERE  `id` = ".UID." LIMIT 1");
			$m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."baiduid` WHERE `".DB_PREFIX."baiduid`.`uid` = ".UID." AND `".DB_PREFIX."baiduid`.`id` = " . $del);	
			$m->query('DELETE FROM `'.DB_NAME.'`.`'.DB_PREFIX.$x['t'].'` WHERE `'.DB_PREFIX.$x['t'].'`.`uid` = '.UID.' AND `'.DB_PREFIX.$x['t'].'`.`pid` = '.$del);
		}
		elseif (!empty($_GET['reget'])){
			$reget = (int) $_GET['reget'];
			$x=$m->once_fetch_array("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."baiduid` WHERE `uid` = ".UID." AND `id` = ".$reget." LIMIT 1");
			if(!empty($x)){
				$baidu_name = sqladds(getBaiduId($x['bduss']));
				if(empty($baidu_name)){
					$baidu_name = '已失效';
				}
				$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX."baiduid` SET `name` = '$baidu_name' WHERE `id` = '$reget'");
			}
		}
		doAction('baiduid_set');
		Redirect("index.php?mod=baiduid");
		break;

	case 'showtb':
		if (isset($_GET['set'])) {
			$x=$m->fetch_array($m->query('SELECT * FROM  `'.DB_NAME.'`.`'.DB_PREFIX.TABLE.'` WHERE  `uid` = '.UID.' LIMIT 1'));
			$f=$x['tieba'];
			foreach ($_POST['no'] as $k => $x) {
				$id = intval($k);
				if ($x == '0') {
					$xv = '0';
				} else {
					$xv = '1';
				}
				$m->query("UPDATE `".DB_PREFIX.TABLE."` SET `no` =  '{$xv}' WHERE  `id` = '{$id}' AND `uid` = '".UID."' ;");
			}
			Redirect('index.php?mod=showtb&ok');
		}
		elseif (isset($_GET['ref'])) {
			$r = misc::scanTiebaByUser();
			Redirect('index.php?mod=showtb');
		}
		elseif (isset($_GET['clean'])) {
			CleanUser(UID);
			Redirect('index.php?mod=showtb');
		}
		elseif (isset($_GET['del'])) {
			$id = (int) sqladds($_REQUEST['id']);
			$m->query('DELETE FROM  `'.DB_NAME.'`.`'.DB_PREFIX.TABLE.'` WHERE `id` ='.$id);
			Redirect('index.php?mod=showtb&ok');
			}
		elseif (isset($_GET['reset'])) {
			$max = $m->fetch_array($m->query("select max(id) as id from `".DB_NAME."`.`".DB_PREFIX.TABLE."` where `uid`=".UID));
			$min = $m->fetch_array($m->query("select min(id) as id from `".DB_NAME."`.`".DB_PREFIX.TABLE."` where `uid`=".UID));
			$max = $max['id'];
			$min = $min['id'];
			while($min < $max) {
				$res = $m->fetch_array($m->query('SELECT * FROM `'.DB_NAME.'`.`'.DB_PREFIX.TABLE.'` WHERE `id` ='.$min.' Limit 1')); 
				if($res['status'] != 0){
					$m->query('UPDATE `'.DB_NAME.'`.`'.DB_PREFIX.TABLE.'` SET `latest` = 0,`status` = 0,`last_error` = NULL WHERE `id` ='.$min);
					}
				$min = $min + 1;
				}
			Redirect('index.php?mod=showtb&ok');
			}
		elseif (isset($_POST['add'])) {
			if (option::get('enable_addtieba') == '1') {
				$v = addslashes(htmlspecialchars($_POST['add']));
				$pid = (int) strip_tags($_POST['pid']);
				$osq = $m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `uid` = ".UID." AND `tieba` = '{$v}';");
				if($m->num_rows($osq) == 0) {
					$table = $m->fetch_array($m->query('select * from `'.DB_NAME.'`.`'.DB_PREFIX.'users` where `id` = '.UID));
					$tb_max = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.$table['t']."` where `uid` = ".UID));	
					if(ROLE == 'admin' || ROLE == 'vip'){
						$m->query("INSERT INTO `".DB_NAME."`.`".DB_PREFIX.TABLE."` (`id`, `pid`, `uid`, `tieba`, `no`, `latest`) VALUES (NULL, {$pid} ,'".UID."', '{$v}', 0, 0);");
					} else {
						if($tb_max[0] < option::get('tb_max')){
							$m->query("INSERT INTO `".DB_NAME."`.`".DB_PREFIX.TABLE."` (`id`, `pid`, `uid`, `tieba`, `no`, `latest`) VALUES (NULL, {$pid} ,'".UID."', '{$v}', 0, 0);");
						} else {
							msg('错误：您的贴吧数量超过限制，无法刷新！');
						}
					}																				
				}
			}
			Redirect('index.php?mod=showtb&ok');
		}
		doAction('showtb_set');
		break;

		case 'set':
			// 获取头像的url
			if($i['post']['face_img'] == 1 && $i['post']['face_baiduid'] != ''){
				$c = new wcurl('http://www.baidu.com/p/'.option::uget("face_baiduid"));
				$data = $c->get();
				$c->close();
				$i['post']['face_url'] = stripslashes(textMiddle($data,'<img class=portrait-img src=\x22','\x22>'));
			}
			/*
			受信任的设置项，如果插件要使用系统的API去储存设置，必须通过set_save1或set_save2挂载点挂载设置名
			具体挂载方法为：
			global $PostArray;
			$PostArray[] = '设置名';
			为了兼容旧版本，可以global以后检查一下是不是空变量，为空则为旧版本
			*/
			$PostArray = array(
				'face_img',
				'face_baiduid',
				'face_url'
			);
			doAction('set_save1');
			//更改邮箱
			if($_POST['mail'] != $i['user']['email'] && !empty($_POST['mail'])){
				if (checkMail($_POST['mail'])) {
					$mail = sqladds($_POST['mail']);
					$z=$m->once_fetch_array("SELECT COUNT(*) AS total FROM `".DB_NAME."`.`".DB_PREFIX."users` WHERE email='{$mail}'");
					if ($z['total'] > 0) {
						msg('修改失败：邮箱已经存在');
					}
					$m->query("UPDATE `".DB_PREFIX."users` SET `email` = '{$mail}' WHERE `id` = '".UID."';");
				} else {
					msg('邮箱格式有误，请检查');
				}
			}
			$set = array();
			foreach ($PostArray as $value) {
				if (!isset($i['post'][$value])) {
					$i['post'][$value] = '';
				}
				@option::uset($value , $i['post'][$value]);
			}
			doAction('set_save2');
			Redirect('index.php?mod=set&ok');
			break;

	case 'admin:testmail':
		global $i;
		$x = misc::mail($i['user']['email'], SYSTEM_FN.' V'.SYSTEM_VER.' - 邮件发送测试','这是一封关于 ' . SYSTEM_FN . ' 的测试邮件，如果你收到了此邮件，表示邮件系统可以正常工作<br/><br/>站点地址：' . SYSTEM_URL , array('测试附件.txt' => '这是一个测试附件'));
		if($x === true) {
			Redirect('index.php?mod=admin:set&mailtestok');
		} else {
			msg('邮件发送失败，发件日志：<br/>'.$x);
		}
		break;

	case 'admin:testbbs':
		global $i;
		$ch_url = SUPPORT_URL.'getplug.php?m=check&user='.option::get('bbs_us').'&pw='.option::get('bbs_pw');
		$c = new wcurl($ch_url);
		$x = $c->exec();
		$c->close();
		if($x == 'RIGHT') {
			Redirect('index.php?mod=admin:set&bbstestok');
		} else {
			if(empty($x)){
				$x = '错误 - 与产品中心连接失败';
			}
			msg('错误 - '.$x);
		}
		break;
}

if (ROLE == 'admin' && $i['mode'][0] == 'plugin') {
	option::pset($i['mode'][1] , $_POST);
	Redirect("index.php?mod=admin:setplug&plug={$i['mode'][1]}&ok");
} elseif (ROLE == 'admin' && $i['mode'][0] == 'setplugin') {
	settingPlugin($i['mode'][1]);
}