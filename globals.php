<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

if (isset($_COOKIE['wmzz_tc_user']) && isset($_COOKIE['wmzz_tc_pw'])) {
	$name = isset($_COOKIE['wmzz_tc_user']) ? strip_tags($_COOKIE['wmzz_tc_user']) : '';
	$pw = isset($_COOKIE['wmzz_tc_pw']) ? strip_tags($_COOKIE['wmzz_tc_pw']) : '';
	$osq = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."users` WHERE name = '{$name}' LIMIT 1");
	if($m->num_rows($osq) == 0) {
		setcookie("wmzz_tc_user",'', time() - 3600);
		header("Location: ".SYSTEM_URL."index.php?mod=login&error_msg=".urlencode('Cookies 所记录的账号信息不正确，请重新登录')."");die;
	}
	$p = $m->fetch_array($osq);
	if ($pw != $p['pw']) {
		setcookie("wmzz_tc_pw",'', time() - 3600);
		header("Location: ".SYSTEM_URL."index.php?mod=login&error_msg=".urlencode('Cookies 所记录的账号信息不正确，请重新登录')."");die;
	} else {
		define('LOGIN',true);
		define('ROLE', $p['role']);
		define('UID', $p['id']);
		define('NAME', $p['name']);
		define('EMAIL', $p['email']);
		define('BDUSS', $p['ck_bduss']);
		define('TABLE', $p['t']);
	}
}
if (SYSTEM_PAGE == 'admin:login') {
	$name = isset($_POST['user']) ? strip_tags($_POST['user']) : '';
	$pw = isset($_POST['pw']) ? strip_tags($_POST['pw']) : '';
	if (empty($name) || empty($pw)) {
		header("Location: ".SYSTEM_URL."index.php?mod=login&error_msg=".urlencode('请填写账户或密码'));die;
	}
	$osq = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."users` WHERE name = '{$name}' LIMIT 1");
	if($m->num_rows($osq) == 0) {
		header("Location: ".SYSTEM_URL."index.php?mod=login&error_msg=".urlencode('账户不存在 [ 提示：账户名不是昵称 ]'));die;
	}
	$p = $m->fetch_array($osq);
	if (md5(md5($pw)) != $p['pw']) {
		header("Location: ".SYSTEM_URL."index.php?mod=login&error_msg=".urlencode('密码错误'));die;
	} else {
		if (isset($_POST['ispersis']) && $_POST['ispersis'] == 1) {
			setcookie("wmzz_tc_user",$name, time()+65535*65535*65535);
			setcookie("wmzz_tc_pw",md5(md5($pw)), time()+65535*65535*65535);
			header("Location: ".SYSTEM_URL);
		} else {
			setcookie("wmzz_tc_user",$name);
			setcookie("wmzz_tc_pw",md5(md5($pw)));
			header("Location: ".SYSTEM_URL);
		}
	}
}
elseif (SYSTEM_PAGE == 'admin:reg') {
	$name = isset($_POST['user']) ? strip_tags($_POST['user']) : '';
	$mail = isset($_POST['mail']) ? strip_tags($_POST['mail']) : '';
	$pw = isset($_POST['pw']) ? strip_tags($_POST['pw']) : '';
	$yr = isset($_POST['yr']) ? strip_tags($_POST['yr']) : '';
	if (empty($name) || empty($mail) || empty($pw)) {
		msg('注册失败：请正确填写账户、密码或邮箱');
	}
	$x=$m->once_fetch_array("SELECT COUNT(*) AS total FROM `".DB_NAME."`.`".DB_PREFIX."users` WHERE name='{$name}'");
	if ($x['total'] > 0) {
		msg('注册失败：用户名已经存在');
	}
	if (!empty(option::get('yr_reg'))) {
		if (empty($yr)) {
			msg('注册失败：请输入邀请码');
		} else {
			if (option::get('yr_reg') != $yr) {
				msg('注册失败：邀请码错误');
			}
		}
	}
	$m->query('INSERT INTO `'.DB_NAME.'`.`'.DB_PREFIX.'users` (`id`, `name`, `pw`, `email`, `role`, `t`, `ck_bduss`) VALUES (NULL, \''.$name.'\', \''.md5(md5($pw)).'\', \''.$mail.'\', \'user\', \''.getfreetable().'\', NULL);');
	setcookie("wmzz_tc_user",$name);
	setcookie("wmzz_tc_pw",md5(md5($pw)));
	header("Location: ".SYSTEM_URL);
}
elseif (SYSTEM_PAGE == 'login') {
	template('login');
	die;
}
elseif (SYSTEM_PAGE == 'reg') {
	template('reg');
	die;
}
elseif (SYSTEM_PAGE == 'admin:logout') {
	setcookie("wmzz_tc_user",'', time() - 3600);
	setcookie("wmzz_tc_pw",'', time() - 3600);
	header("Location: ".SYSTEM_URL);
}
elseif (!defined('UID') && !defined('SYSTEM_DO_NOT_LOGIN')) {
	header("Location: ".SYSTEM_URL."index.php?mod=login");
}
?>