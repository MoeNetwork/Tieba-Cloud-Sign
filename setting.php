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
		if (isset($_GET['dis'])) {
			inactivePlugin($_GET['dis']);
			Redirect(SYSTEM_URL.'index.php?mod=admin:plugins&ok');
		}
		elseif (isset($_GET['act'])) {
			activePlugin($_GET['act']);
			Redirect(SYSTEM_URL.'index.php?mod=admin:plugins&ok');
		}
		elseif (isset($_GET['uninst'])) {
			uninstallPlugin($_GET['uninst']);
			Redirect(SYSTEM_URL.'index.php?mod=admin:plugins&ok');
		}
		Redirect(SYSTEM_URL.'index.php?mod=admin:plugins&ok');
		break;
	
	case 'admin:set':
		global $m;
		$sou = adds($_POST);
		@option::set('system_url',$sou['system_url']);
		@option::set('system_name',$sou['system_name']);
		@option::set('cron_limit',$sou['cron_limit']);
		@option::set('tb_max',$sou['tb_max']);
		@option::set('sign_mode', serialize($sou['sign_mode']));
		@option::set('footer',$sou['footer']);
		@option::set('enable_reg',$sou['enable_reg']);
		@option::set('protect_reg',$sou['protect_reg']);
		@option::set('yr_reg',$sou['yr_reg']);
		@option::set('icp',$sou['icp']);
		@option::set('protector',$sou['protector']);
		@option::set('trigger',$sou['trigger']);
		@option::set('mail_mode',$sou['mail_mode']);
		@option::set('mail_name',$sou['mail_name']);
		@option::set('mail_yourname',$sou['mail_yourname']);
		@option::set('mail_host',$sou['mail_host']);
		@option::set('mail_port',$sou['mail_port']);
		@option::set('mail_auth',$sou['mail_auth']);
		@option::set('mail_smtpname',$sou['mail_smtpname']);
		@option::set('mail_smtppw',$sou['mail_smtppw']);
		@option::set('dev',$sou['dev']);
		@option::set('bduss_num',$sou['bduss_num']);
		@option::set('fb',$sou['fb']);
		@option::set('cloud',$sou['cloud']);
		@option::set('enable_addtieba',$sou['enable_addtieba']);
		@option::set('dev',$sou['dev']);
		@option::set('pwdmode',$sou['pwdmode']);
		@option::set('retry_max',$sou['retry_max']);
		@option::set('cron_order',$sou['cron_order']);
		@option::set('sign_multith',$sou['sign_multith']);
		@option::set('cktime',$sou['cktime']);
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
		doAction('admin_set_save');
		Redirect(SYSTEM_URL.'index.php?mod=admin:set&ok');
		break;

	case 'admin:tools':
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

		case 'remtab':
			global $m;
			if (!empty($_POST['tab'])) {
				$m->query('DROP TABLE IF EXISTS `'.$_POST['tab'].'`');
			}
			break;

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

		default:
			msg('未定义操作');
			break;
		}
		Redirect(SYSTEM_URL.'index.php?mod=admin:tools&ok');
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
				foreach ($_POST['user'] as $value) {
					if ($_POST['crolev'] == 'user') {
						$role = 'user';
					} elseif ($_POST['crolev'] == 'admin') {
						$role = 'admin';
					} elseif ($_POST['crolev'] == 'vip') {
						$role = 'vip';
					} elseif ($_POST['crolev'] == 'banned') {
						$role = 'banned';
					} 

					$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX."users` SET `role` = '{$role}' WHERE `".DB_PREFIX."users`.`id` = {$value}");
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
				Redirect(SYSTEM_URL.'index.php?mod=admin:users&ok');
				break;

			default:
				msg('未定义操作');
				break;
			}
		Redirect(SYSTEM_URL.'index.php?mod=admin:users&ok');
		break;

	case 'admin:cron':
		if (isset($_GET['act'])) {
			$x = $m->once_fetch_array("SELECT *  FROM `".DB_NAME."`.`".DB_PREFIX."cron` WHERE `id` = '{$_GET['act']}'");
			cron::set($x['name'], $x['file'], '0', $x['status'], $x['freq'], $x['lastdo'], $x['log']);
		}
		elseif (isset($_GET['dis'])) {
			$x = $m->once_fetch_array("SELECT *  FROM `".DB_NAME."`.`".DB_PREFIX."cron` WHERE `id` = '{$_GET['dis']}'");
			cron::set($x['name'], $x['file'], '1', $x['status'], $x['freq'], $x['lastdo'], $x['log']);
		}
		elseif (isset($_GET['uninst'])) {
			$x = $m->once_fetch_array("SELECT *  FROM `".DB_NAME."`.`".DB_PREFIX."cron` WHERE `id` = '{$_GET['uninst']}'");
			cron::del($x['name']);
		}
		elseif (isset($_GET['add'])) {
			cron::set($_POST['name'], $_POST['file'], $_POST['no'], $_POST['status'], $_POST['lastdo'], $_POST['log']);
			Redirect(SYSTEM_URL.'index.php?mod=admin:cron&ok');
		}
		elseif (isset($_GET['run'])) {
			$return = cron::run($_GET['file'], $_GET['run']);
			$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX."cron` SET `lastdo` =  '".time()."',`log` = '{$return}' WHERE `".DB_PREFIX."cron`.`name` = '".$_GET['run']."'");
		}
		elseif (isset($_GET['order'])) {
			foreach ($_POST['ids'] as $key => $value) {
				$m->query("UPDATE `".DB_PREFIX."cron` SET  `orde` =  '{$_POST['order'][$key]}' WHERE  `".DB_PREFIX."cron`.`id` = ". $value);
			}
		}
		Redirect(SYSTEM_URL.'index.php?mod=admin:cron&ok');
		break;

	case 'baiduid':
		if (isset($_GET['delete'])) {
			CleanUser(UID);
			$m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."baiduid` WHERE `".DB_PREFIX."baiduid`.`uid` = ".UID);
		}
		elseif (isset($_GET['bduss'])) {
			if (option::get('bduss_num') == '-1' && ROLE != 'admin') msg('本站禁止绑定新账号');

			if (option::get('bduss_num') != '0' && ISVIP == false) {
				$count = $m->once_fetch_array("SELECT COUNT(*) AS `c` FROM `".DB_NAME."`.`".DB_PREFIX."baiduid` WHERE `".DB_PREFIX."baiduid`.`uid` = ".UID);
				if (($count['c'] + 1) > option::get('bduss_num')) msg('您当前绑定的账号数已达到管理员设置的上限<br/><br/>您当前已绑定 '.$count['c'].' 个账号，最多只能绑定 '.option::get('bduss_num').' 个账号'); 
			}
			$m->query("INSERT INTO `".DB_NAME."`.`".DB_PREFIX."baiduid` (`uid`,`bduss`) VALUES  (".UID.", '{$_GET['bduss']}' )");
		}
		elseif (isset($_GET['del'])) {
			$del = (int) $_GET['del'];
			$x=$m->once_fetch_array("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."users` WHERE  `id` = ".UID." LIMIT 1");
			$m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."baiduid` WHERE `".DB_PREFIX."baiduid`.`uid` = ".UID." AND `".DB_PREFIX."baiduid`.`id` = " . $del);	
			$m->query('DELETE FROM `'.DB_NAME.'`.`'.DB_PREFIX.$x['t'].'` WHERE `'.DB_PREFIX.$x['t'].'`.`uid` = '.UID.' AND `'.DB_PREFIX.$x['t'].'`.`pid` = '.$del);
		}
		doAction('baiduid_set');
		Redirect(SYSTEM_URL."?mod=baiduid");
		break;

	case 'showtb':
		if (isset($_GET['set'])) {
			$x=$m->fetch_array($m->query('SELECT * FROM  `'.DB_NAME.'`.`'.DB_PREFIX.TABLE.'` WHERE  `uid` = '.UID.' LIMIT 1'));
			$f=$x['tieba'];
			foreach ($_POST['no'] as $x) {
				preg_match('/(.*)\[(.*)\]/', $x, $v);
				$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX.TABLE."` SET `no` =  '{$v[1]}' WHERE  `".DB_PREFIX.TABLE."`.`id` = {$v[2]} ;");
			}
			Redirect(SYSTEM_URL.'index.php?mod=showtb&ok');
		}
		elseif (isset($_GET['ref'])) {
			$r = misc::scanTiebaByUser();
			Redirect(SYSTEM_URL.'index.php?mod=showtb');
		}
		elseif (isset($_GET['clean'])) {
			CleanUser(UID);
			Redirect(SYSTEM_URL.'index.php?mod=showtb');
		}
		elseif (isset($_POST['add'])) {
			if (option::get('enable_addtieba') == '1') {
				$v = addslashes(htmlspecialchars($_POST['add']));
				$pid = (int) strip_tags($_POST['pid']);
				$osq = $m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `uid` = ".UID." AND `tieba` = '{$v}';");
				if($m->num_rows($osq) == 0) {
					$m->query("INSERT INTO `".DB_NAME."`.`".DB_PREFIX.TABLE."` (`id`, `pid`, `uid`, `tieba`, `no`, `lastdo`) VALUES (NULL, {$pid} ,'".UID."', '{$v}', 0, 0);");
				}
			}
			Redirect(SYSTEM_URL.'index.php?mod=showtb&ok');
		}
		doAction('showtb_set');
		break;

		case 'set':
			doAction('set_save1');
			option::uset($_POST);
			doAction('set_save2');
			Redirect(SYSTEM_URL.'index.php?mod=set&ok');
			break;

	case 'testmail':
		$x = misc::mail(option::get('mail_name'), SYSTEM_FN.' V'.SYSTEM_VER.' - 邮件发送测试','本测试邮件还包含一个附件',array(SYSTEM_ROOT.'/README.md'));
		if($x == true) {
			Redirect(SYSTEM_URL.'index.php?mod=admin:set&mailtestok');
		} else {
			msg('邮件发送失败：'.$x);
		}
		break;
}

if (ROLE == 'admin' && $i['mode'][0] == 'plugin') {
	option::set('plugin_'.$i['mode'][1] , addslashes(serialize($_POST)));
	Redirect(SYSTEM_URL."index.php?mod=admin:setplug&plug={$i['mode'][1]}&ok");
}
?>