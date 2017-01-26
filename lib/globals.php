<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
/**
 * 全局用户控制
 */
 
doAction('globals');

if (isset($_COOKIE['uid']) && isset($_COOKIE['pwd'])) {
    if(!empty($_COOKIE['con_pwd']) && !empty($_COOKIE['con_uid'])) {
        $con_uid = isset($_COOKIE['uid']) ? sqladds($_COOKIE['con_uid']) : '';
        $con_pw  = isset($_COOKIE['pwd']) ? sqladds($_COOKIE['con_pwd']) : '';
        $con_p   = $m->once_fetch_array("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."users` WHERE `id` = '{$con_uid}' LIMIT 1");
        if(empty($con_p['id']) || $con_pw != substr(sha1(EncodePwd($con_p['pw'])) , 4 , 32)) {
            setcookie("con_uid",'', time() - 3600);
            setcookie("con_pwd",'', time() - 3600);
        } else {
            if(SYSTEM_PAGE == 'usercontrolback') {
                setcookie("uid",$con_uid, time() + 999999);
                setcookie("pwd",$con_pw, time() + 999999);
                setcookie("con_uid",'', time() - 3600);
                setcookie("con_pwd",'', time() - 3600);
                redirect('index.php');
            } else {
                define('CON_UID', $con_p['id']);
                define('CON_NAME', $con_p['name']);
                define('CON_EMAIL', $con_p['email']);
                define('CON_TABLE', $con_p['t']);
            }
        }
    }
    $uid = isset($_COOKIE['uid']) ? sqladds($_COOKIE['uid']) : '';
    $pw = isset($_COOKIE['pwd']) ? sqladds($_COOKIE['pwd']) : '';
    $osq = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."users` WHERE `id` = '{$uid}' LIMIT 1");
    if($m->num_rows($osq) == 0) {
        setcookie("uid",'', time() - 3600);
        setcookie("pwd",'', time() - 3600);
        ReDirect("index.php?mod=login&error_msg=".urlencode('Cookies 所记录的账号信息不正确，请重新登录(#1)')."");die;
    }
	doAction('globals_1');
	$p = $m->fetch_array($osq);
	if ($pw != substr(sha1(EncodePwd($p['pw'])) , 4 , 32)) {
        setcookie("uid",'', time() - 3600);
        setcookie("pwd",'', time() - 3600);
		ReDirect("index.php?mod=login&error_msg=".urlencode('Cookies 所记录的账号信息不正确，请重新登录(#2)')."");die;
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
		if (ROLE == 'admin') {
			define('ISADMIN', true);
		} else {
			define('ISADMIN',false);
		}
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
    // 检查验证码
    if(option::get('captcha')){
        session_start();
        if(empty($_POST['captcha'])){
            redirect('index.php?mod=login&error_msg='.urlencode('请填写验证码'));die;
        } else {
            if(strtolower($_POST['captcha']) != strtolower($_SESSION['captcha'])){
                redirect('index.php?mod=login&error_msg='.urlencode('验证码错误'));die;
            } else {
                session_destroy();
            }
        }
    }
    // 正常登录流程
	$name = isset($_POST['user']) ? sqladds($_POST['user']) : '';
	$pw = isset($_POST['pw']) ? $_POST['pw'] : '';
	if (empty($name) || empty($pw)) {
		ReDirect("index.php?mod=login&error_msg=".urlencode('请填写账户或密码'));die;
	}
	$osq = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."users` WHERE `name` = '{$name}' OR `email` = '{$name}' LIMIT 1");
    if($m->num_rows($osq) == 0) {
        ReDirect("index.php?mod=login&error_msg=".urlencode('账户不存在 [ 提示：账户不是昵称，账户可为用户名或者邮箱地址 ]'));die;
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
			setcookie("uid",$p['id'], time() + $cktime);
			setcookie("pwd",substr(sha1(EncodePwd(EncodePwd($pw))) , 4 , 32), time() + $cktime);
			ReDirect('index.php');
		} else {
			setcookie("uid",$p['id']);
			setcookie("pwd",substr(sha1(EncodePwd(EncodePwd($pw))) , 4 , 32));
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
    // 检查验证码
    if(option::get('captcha')){
        session_start();
        if(empty($_POST['captcha'])){
            redirect('index.php?mod=reg&error_msg='.urlencode('请填写验证码'));die;
        } else {
            if(strtolower($_POST['captcha']) != strtolower($_SESSION['captcha'])){
                redirect('index.php?mod=reg&error_msg='.urlencode('验证码错误'));die;
            } else {
                session_destroy();
            }
        }
    }
    // 正常注册流程
	$name = isset($_POST['user']) ? sqladds($_POST['user']) : '';
    // 用户名不得含有@符号，避免登录时与邮箱混淆
    if(stripos($name, '@') !== FALSE) msg('注册失败：用户名不得含有@符号');
    $mail = isset($_POST['mail']) ? sqladds($_POST['mail']) : '';
	$pw = isset($_POST['pw']) ? sqladds($_POST['pw']) : '';
	$yr = isset($_POST['yr']) ? sqladds($_POST['yr']) : '';
	if (empty($name) || empty($mail) || empty($pw)) {
		msg('注册失败：请正确填写用户名、密码或邮箱');
	}
    if ($_POST['pw'] != $_POST['rpw']) {
        msg('注册失败：两次输入的密码不一致，请重新输入');
    }
	$x=$m->once_fetch_array("SELECT COUNT(*) AS total FROM `".DB_NAME."`.`".DB_PREFIX."users` WHERE `name` = '{$name}' OR `email` = '{$mail}' LIMIT 1");
	$y=$m->once_fetch_array("SELECT COUNT(*) AS total FROM `".DB_NAME."`.`".DB_PREFIX."users`");
	if ($x['total'] > 0) {
		msg('注册失败：用户名或邮箱已经被注册');
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
	doAction('admin_reg_3');
	ReDirect('index.php?mod=login&msg=' . urlencode('成功注册，请输入账号信息登录本站 [ 账号为用户名或邮箱地址 ]'));
}
elseif (SYSTEM_PAGE == 'captcha') {
    // 防止恶意用户通过get参数临时修改验证码等级使其更易于被识别
    // 只有管理员才能通过get参数临时修改验证码等级（用于设置页预览）
    define('SYSTEM_NO_ERROR',true);
    if(defined('ROLE') && ROLE == 'admin'){
        $level = isset($_GET['level']) ? $_GET['level'] : option::get('captcha'); // 验证码等级。0关闭，1简单，2中等，3困难，4反人类
    } else {
        $level = option::get('captcha');
    }
    if($level){
        // 不同的验证码等级不同的配置
        $data = array(
            1 => array('line' => 5, 'star' => 30, 'fontsize' => 25),
            2 => array('line' => 10, 'star' => 60, 'fontsize' => 20),
            3 => array('line' => 20, 'star' => 100, 'fontsize' => 17),
            4 => array('line' => 40, 'star' => 250, 'fontsize' => 15),
        );
        require_once SYSTEM_ROOT.'/lib/class.captcha.php';
        $c = new Captcha($data[$level]);
        $c->create();
        session_start();
        $_SESSION['captcha'] = $c->getCode();
    } else {
		require_once SYSTEM_ROOT.'/lib/class.captcha.php';
        $c = new Captcha();
		$c->createNoting();
	}
    exit;
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
	csrf();
	doAction('logout');
	setcookie("uid",'', time() - 3600);
	setcookie("toolpw",'', time() - 3600);
	setcookie("pwd",'', time() - 3600);
	ReDirect('index.php?mod=login');
}
elseif (!defined('UID') && !defined('SYSTEM_DO_NOT_LOGIN')) {
	define('ROLE', 'visitor');
	$i['user']['role'] = 'visitor';
	ReDirect('index.php?mod=login');
}