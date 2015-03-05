<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
/**
 * 全局用户控制
 */
loadplugins();
if (isset($_COOKIE['wmzz_tc_user']) && isset($_COOKIE['wmzz_tc_pw'])) {
    $name = isset($_COOKIE['wmzz_tc_user']) ? addslashes(strip_tags($_COOKIE['wmzz_tc_user'])) : '';
    $pw = isset($_COOKIE['wmzz_tc_pw']) ? addslashes(strip_tags($_COOKIE['wmzz_tc_pw'])) : '';
	$osq = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."users` WHERE name = '{$name}' LIMIT 1");
	if($m->num_rows($osq) == 0) {
		$osq = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."users` WHERE email = '{$name}' LIMIT 1");
		if($m->num_rows($osq) == 0) {
			ReDirect("index.php?mod=login&error_msg=".urlencode('Cookies 所记录的账号信息不正确，请重新登录')."");die;
		}
	}
	doAction('globals_1');
	$p = $m->fetch_array($osq);
	if ($pw != $p['pw']) {
		setcookie("wmzz_tc_pw",'', time() - 3600);
		ReDirect("index.php?mod=login&error_msg=".urlencode('Cookies 所记录的账号信息不正确，请重新登录')."");die;
	} else {
		define('LOGIN',true);
		define('ROLE', $p['role']);
		define('UID', $p['id']);
		define('NAME', $p['name']);
		define('EMAIL', $p['email']);
		define('TABLE', $p['t']);
		if (!defined('SYSTEM_ONLY_CHECK_LOGIN')) {
			$i['user']['login'] = true;
			$i['user']['role'] = $p['role'];
			$i['user']['uid'] = $p['id'];
			$i['user']['name'] = $p['name'];
			$i['user']['email'] = $p['email'];
			$i['user']['pw'] = $p['pw'];
			$i['user']['bduss'] = array();
			$i['user']['table'] = $p['t'];
			$i['user']['tbnum'] = $m->fetch_row($m->query('SELECT COUNT(*) FROM  `'.DB_NAME.'`.`'.DB_PREFIX.TABLE.'` WHERE `uid` = '.UID));
			$i['user']['tbnum'] = $i['user']['tbnum'][0];
			$bds = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."baiduid` WHERE uid = ".UID);
			while ($bd = $m->fetch_array($bds)) {
				$bdspid = $bd['id'];
				$i['user']['bduss'][$bdspid] = $bd['bduss'];
				$i['user']['baidu'][$bdspid] = $bd['name'] ;
			}
			$optss = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."users_options` WHERE uid = ".UID);
			$GLOBALS = $i['user'];
			while ($opts = $m->fetch_array($optss)) {
				$name = $opts['name'];
				$i['user']['opt'][$name] = $opts['value'];
			}
		}
		//是否为VIP，管理员和VIP组的用户都为VIP
		if (ROLE == 'admin' || ROLE == 'vip') {
			$i['user']['isvip'] = true;
			define('ISVIP', true);
		} else {
			$i['user']['isvip'] = false;
			define('ISVIP', false);
		}
		if (ROLE == 'banned') {
			msg('你已被禁止访问，请联系管理员解封');
		}
	}
	doAction('globals_2');
}
doAction('globals_3');
if (SYSTEM_PAGE == 'admin:login') {
	if (defined('ROLE')) {
		ReDirect('index.php');
	}
	define('ROLE', 'visitor');
	$i['user']['role'] = 'visitor';
	doAction('admin_login_1');
	$name = isset($_POST['user']) ? addslashes(strip_tags($_POST['user'])) : '';
	$pw = isset($_POST['pw']) ? addslashes(strip_tags($_POST['pw'])) : '';
	if (empty($name) || empty($pw)) {
		ReDirect("index.php?mod=login&error_msg=".urlencode('请填写账户或密码'));die;
	}
	$osq = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."users` WHERE name = '{$name}' LIMIT 1");
	if($m->num_rows($osq) == 0) {
		$osq = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."users` WHERE email = '{$name}' LIMIT 1");
		if($m->num_rows($osq) == 0) {
			ReDirect("index.php?mod=login&error_msg=".urlencode('账户不存在 [ 提示：账户不是昵称，账户可为用户名或者邮箱地址 ]'));die;
		}
	}
	$p = $m->fetch_array($osq);
	if (EncodePwd($pw) != $p['pw']) {
		ReDirect("index.php?mod=login&error_msg=".urlencode('密码错误'));die;
	} else {
		doAction('admin_login_3');
		if (isset($_POST['ispersis']) && $_POST['ispersis'] == 1) {
			$cktime = (int) option::get('cktime');
			if (empty($cktime)) {
				option::set('cktime','999999');
				$cktime = 999999;
			}
			setcookie("wmzz_tc_user",$name, time() + $cktime);
			setcookie("wmzz_tc_pw",EncodePwd($pw), time() + $cktime);
			ReDirect('index.php');
		} else {
			setcookie("wmzz_tc_user",$name);
			setcookie("wmzz_tc_pw",EncodePwd($pw));
			ReDirect('index.php');
		}
	}
	doAction('admin_login_2');
}
elseif (SYSTEM_PAGE == 'admin:reg') {
	if (defined('ROLE')) {
		ReDirect('index.php');
	}
	define('ROLE', 'visitor');
	doAction('admin_reg_1');
	if (option::get('enable_reg') != '1') {
		msg('注册失败：该站点已关闭注册');
	}
	$name = isset($_POST['user']) ? addslashes(strip_tags($_POST['user'])) : '';
	$mail = isset($_POST['mail']) ? addslashes(strip_tags($_POST['mail'])) : '';
	$pw = isset($_POST['pw']) ? addslashes(strip_tags($_POST['pw'])) : '';
	$yr = isset($_POST['yr']) ? addslashes(strip_tags($_POST['yr'])) : '';
	if (empty($name) || empty($mail) || empty($pw)) {
		msg('注册失败：请正确填写账户、密码或邮箱');
	}
	$x=$m->once_fetch_array("SELECT COUNT(*) AS total FROM `".DB_NAME."`.`".DB_PREFIX."users` WHERE name='{$name}'");
	$z=$m->once_fetch_array("SELECT COUNT(*) AS total FROM `".DB_NAME."`.`".DB_PREFIX."users` WHERE email='{$mail}'");
	$y=$m->once_fetch_array("SELECT COUNT(*) AS total FROM `".DB_NAME."`.`".DB_PREFIX."users`");
	if ($x['total'] > 0) {
		msg('注册失败：用户名已经存在');
	}
	if ($z['total'] > 0) {
		msg('注册失败：邮箱已经存在');
	}
	if (!checkMail($mail)) {
		msg('注册失败：邮箱格式不正确');
	}
	$yr_reg = option::get('yr_reg');
	if (!empty($yr_reg)) {
		if (empty($yr)) {
			msg('注册失败：请输入邀请码');
		} else {
			if ($yr_reg != $yr) {
				msg('注册失败：邀请码错误');
			}
		}
	}
	if ($y['total'] <= 0) {
		$role = 'admin';
	} else {
		$role = 'user';
	}
	doAction('admin_reg_2');
	$m->query('INSERT INTO `'.DB_NAME.'`.`'.DB_PREFIX.'users` (`id`, `name`, `pw`, `email`, `role`, `t`) VALUES (NULL, \''.$name.'\', \''.EncodePwd($pw).'\', \''.$mail.'\', \''.$role.'\', \''.getfreetable().'\');');
	setcookie("wmzz_tc_user",$name);
	setcookie("wmzz_tc_pw",EncodePwd($pw));
	doAction('admin_reg_3');
	ReDirect('index.php');
}

elseif (SYSTEM_PAGE == 'login') { 
	if (defined('ROLE')) {
		ReDirect('index.php');
	}
	define('ROLE', 'visitor');
	$i['user']['role'] = 'visitor';
	template('login');
	doAction('login_page_4');
	die;
}
elseif (SYSTEM_PAGE == 'reg') {
	if (defined('ROLE')) {
		ReDirect('index.php');
	}
	define('ROLE', 'visitor');
	$i['user']['role'] = 'visitor';
	template('reg');
	doAction('reg_page_4');
	die;
}
elseif (isset($_GET['pub_plugin'])) {
	define('ROLE', 'visitor');
	define('SYSTEM_READY_LOAD_PUBPLUGIN', true);
}
elseif (SYSTEM_PAGE == 'admin:logout') {
	doAction('logout');
	setcookie("wmzz_tc_user",'', time() - 3600);
	setcookie("wmzz_tc_pw",'', time() - 3600);
	ReDirect('index.php?mod=login');
}
elseif (!defined('UID') && !defined('SYSTEM_DO_NOT_LOGIN')) {
	define('ROLE', 'visitor');
	$i['user']['role'] = 'visitor';
	ReDirect('index.php?mod=login');
}
