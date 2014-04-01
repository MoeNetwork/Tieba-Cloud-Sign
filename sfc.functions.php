<?php
/**
 * StusGame Framework 部分函数库
 * @author 无名智者
 */

/**
 * 获取用户ip地址
 */
function getIp() {
	$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
	if (!ip2long($ip)) {
		$ip = '';
	}
	return $ip;
}

/**
 * 验证email地址格式
 */
function checkMail($email) {
	if (preg_match("/^[\w\.\-]+@\w+([\.\-]\w+)*\.\w+$/", $email) && strlen($email) <= 60) {
		return true;
	} else {
		return false;
	}
}

/**
 * 生成一个随机的字符串
 *
 * @param int $length
 * @param boolean $special_chars
 * @return string
 */
function getRandStr($length = 12, $special_chars = false) {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	if ($special_chars) {
		$chars .= '!@#$%^&*()';
	}
	$randStr = '';
	for ($i = 0; $i < $length; $i++) {
		$randStr .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	}
	return $randStr;
}

/**
 * 获取Gravatar头像
 * http://en.gravatar.com/site/implement/images/
 * @param $email
 * @param $s size
 * @param $d default avatar
 * @param $g
 */
function getGravatar($email, $s = 40, $d = 'mm', $g = 'g', $site = '2') {
	$hash = md5($email);
	$avatar = "http://{$site}.gravatar.com/avatar/$hash?s=$s&d=$d&r=$g";
	return $avatar;
}

/**
 * 解压zip
 * @param type $zipfile 要解压的文件
 * @param type $path 解压到该目录
 * @param type $type
 * @return int
 */
function UnZip($zipfile, $path, $type = 'tpl') {
	if (!class_exists('ZipArchive', FALSE)) {
		return 3;//zip模块问题
	}
	$zip = new ZipArchive();
	if (@$zip->open($zipfile) !== TRUE) {
		return 2;//文件权限问题
	}
	$r = explode('/', $zip->getNameIndex(0), 2);
	$dir = isset($r[0]) ? $r[0] . '/' : '';
	switch ($type) {
		case 'tpl':
			$re = $zip->getFromName($dir . 'header.php');
			if (false === $re)
				return -2;
			break;
		case 'plugin':
			$plugin_name = substr($dir, 0, -1);
			$re = $zip->getFromName($dir . $plugin_name . '.php');
			if (false === $re)
				return -1;
			break;
		case 'backup':
			$sql_name = substr($dir, 0, -1);
			if (getFileSuffix($sql_name) != 'sql')
				return -3;
			break;
		case 'update':
			break;
	}
	if (true === @$zip->extractTo($path)) {
		$zip->close();
		return 0;
	} else {
		return 1;//文件权限问题
	}
}

/**
 * zip压缩
 */
function CreateZip($orig_fname, $content) {
	if (!class_exists('ZipArchive', FALSE)) {
		return false;
	}
	$zip = new ZipArchive();
	$tempzip = SYSTEM_ROOT . '/cache/zip_temp_'.time().mt_rand().'.zip';
	$res = $zip->open($tempzip, ZipArchive::CREATE);
	if ($res === TRUE) {
		$zip->addFromString($orig_fname, $content);
		$zip->close();
		$zip_content = file_get_contents($tempzip);
		unlink($tempzip);
		return $zip_content;
	} else {
		return false;
	}
}

/**
 * 删除文件或目录
 */
function DeleteFile($file) {
	if (empty($file))
		return false;
	if (@is_file($file))
		return @unlink($file);
	$ret = true;
	if ($handle = @opendir($file)) {
		while ($filename = @readdir($handle)) {
			if ($filename == '.' || $filename == '..')
				continue;
			if (!DeleteFile($file . '/' . $filename))
				$ret = false;
		}
	} else {
		$ret = false;
	}
	@closedir($handle);
	if (file_exists($file) && !rmdir($file)) {
		$ret = false;
	}
	return $ret;
}

/**
 * 该函数在插件中调用,挂载插件函数到预留的钩子上
 * WARNING:所有addAction应该放到doAction前面，否则将不能执行！
 *
 * @param string $hook
 * @param string $actionFunc
 * @return boolearn
 */
function addAction($hook, $actionFunc) {
	global $PluginHooks;
	if (!@in_array($actionFunc, $PluginHooks[$hook])) {
		$PluginHooks[$hook][] = $actionFunc;
	}
	return true;
}

/**
 * 执行挂在钩子上的函数,支持多参数 eg:doAction('post_comment', $author, $email, $url, $comment);
 *
 * @param string $hook
 */
function doAction($hook) {
	global $PluginHooks;
	$args = array_slice(func_get_args(), 1);
	if (isset($PluginHooks[$hook])) {
		foreach ($PluginHooks[$hook] as $function) {
			$string = call_user_func_array($function, $args);
		}
	}
}
?>