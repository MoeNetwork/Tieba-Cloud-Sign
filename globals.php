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
		} else {
			setcookie("wmzz_tc_user",$name);
			setcookie("wmzz_tc_pw",md5(md5($pw)));
			@define('LOGIN',true);
			@define('ROLE', $p['role']);
			@define('UID', $p['id']);
			@define('NAME', $p['name']);
			@define('EMAIL', $p['email']);
			@define('BDUSS', $p['ck_bduss']);
		}
	}
}
elseif (SYSTEM_PAGE == 'login') {
	template('login');
	die;
}
elseif (!defined('UID') && !defined('SYSTEM_DO_NOT_LOGIN')) {
	header("Location: ".SYSTEM_URL."index.php?mod=login");
}
?>