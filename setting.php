<?php
require dirname(__FILE__).'/init.php';

if (ROLE != 'user' && ROLE != 'admin') {
	msg('权限不足');
}

if (ROLE != 'admin' && stristr(strip_tags($_GET['mod']), 'admin:')) {
	msg('权限不足');
}

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
		@option::set('fb',$sou['fb']);
		@option::set('cloud',$sou['cloud']);
		@option::set('enable_addtieba',$sou['enable_addtieba']);
		@option::set('dev',$sou['dev']);
		@option::set('pwdmode',$sou['pwdmode']);
		@option::set('retry_max',$sou['retry_max']);
		@option::set('cron_order',$sou['cron_order']);
		if (empty($sou['fb_tables'])) {
			@option::set('fb_tables',NULL);
		} else {
			$fb_tables = explode("\n",$sou['fb_tables']);
			$fb_tab = array();
			$n= 0;
			foreach ($fb_tables as $value) {
				$n++;
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
			$rs=$m->query("SHOW TABLES FROM ".DB_NAME);
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
					$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX."users` SET  `ck_bduss` =  '' WHERE  `".DB_PREFIX."users`.`id` = ".$value);
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
				$m->query('INSERT INTO `'.DB_NAME.'`.`'.DB_PREFIX.'users` (`id`, `name`, `pw`, `email`, `role`, `t`, `ck_bduss`) VALUES (NULL, \''.$name.'\', \''.EncodePwd($pw).'\', \''.$mail.'\', \''.$role.'\', \''.getfreetable().'\', NULL);');
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
			$m->query("UPDATE  `".DB_NAME."`.`".DB_PREFIX."users` SET  `ck_bduss` =  NULL WHERE  `".DB_PREFIX."users`.`id` =".UID.";");
		}
		elseif (isset($_GET['bduss'])) {
			$m->query("UPDATE  `".DB_NAME."`.`".DB_PREFIX."users` SET  `ck_bduss` =  '".strip_tags($_GET['bduss'])."' WHERE  `".DB_PREFIX."users`.`id` =".UID.";");
			Clean();
			Redirect(SYSTEM_URL."?mod=baiduid");
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
			  set_time_limit(0);
			  $n      = 0;
			  $n2     = 0;
			  $n3     = 1;
			  $addnum = 0; 
			  $list   = array();
			  $o      = option::get('tb_max');
			  while(true) {
			  	  $url = 'http://tieba.baidu.com/f/like/mylike?&pn='.$n3;
			  	  $n3++;
			  	  $addnum = 0;
			  	  $c      = new wcurl($url, array('User-Agent: Phone',"X-FORWARDED-FOR:183.185.2.".mt_rand(1,255))); 
				  $c->addcookie("BDUSS=".BDUSS);
				  $ch = $c->exec();
				  $c->close();
				  dir($ch);
				  preg_match_all('/\<td\>(.*?)\<a href=\"\/f\?kw=(.*?)\" title=\"(.*?)\">(.*?)\<\/a\>\<\/td\>/', $ch, $list);
				  foreach ($list[3] as $v) {
				  	$v = mb_convert_encoding($v, "UTF-8", "GBK");
				  	$osq = $m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `uid` = ".UID." AND `tieba` = '{$v}';");
					if($m->num_rows($osq) == 0) {
						$n++;
						if (!empty($o) && ROLE != 'admin' && $n > $o) {
							msg('当前贴吧数量超出系统限定，无法将贴吧记录到数据库');
						}
						$m->query("INSERT INTO `".DB_NAME."`.`".DB_PREFIX.TABLE."` (`id`, `uid`, `tieba`, `no`, `lastdo`) VALUES (NULL, '".UID."', '{$v}', 0, 0);");
					}
					$addnum++;
				  }
				  if (!isset($list[3][0])) {
				  	break;
				  }
				  elseif($o != 0 && $n2 >= $o && ROLE != 'admin') {
				  	break;
				  }
				  $n2 = $n2 + $addnum;
			  }
			  Redirect(SYSTEM_URL.'index.php?mod=showtb');
		}
		elseif (isset($_GET['clean'])) {
			CleanUser(UID);
			Redirect(SYSTEM_URL.'index.php?mod=showtb');
		}
		elseif (isset($_POST['add'])) {
			if (option::get('enable_addtieba') == '1') {
				$v = strip_tags($_POST['add']);
				$osq = $m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `uid` = ".UID." AND `tieba` = '{$v}';");
				if($m->num_rows($osq) == 0) {
					$m->query("INSERT INTO `".DB_NAME."`.`".DB_PREFIX.TABLE."` (`id`, `uid`, `tieba`, `no`, `lastdo`) VALUES (NULL, '".UID."', '{$v}', 0, 0);");
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

if (ROLE == 'admin' && stristr(strip_tags($_GET['mod']), 'plugin:')) {
	$plug = trim(strip_tags($_GET['mod']),'plugin:');
	option::set('plugin_'.$plug, serialize($_POST));
	Redirect(SYSTEM_URL."index.php?mod=admin:setplug&plug={$plug}&ok");
}
?>