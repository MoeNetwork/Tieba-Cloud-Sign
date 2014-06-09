<?php
/**
 * StusGame Framework 部分函数库
 * @author 无名智者
 */
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
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
 * 加密密码
 */
function EncodePwd($pwd) {
	return eval('return '.option::get('pwdmode').';');
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
	if (true === @$zip->extractTo($path)) {
		$zip->close();
		return 0;
	} else {
		return 1;//文件权限问题
	}
}
/**
 * 清空缓冲区的内容
 */
function Clean() {
	ob_clean();
}

/**
 * 获取空闲的贴吧记录表
 *
 */
function getfreetable() {
	global $m;
	$x = $m->once_fetch_array("SELECT COUNT(*) AS fffff FROM  `".DB_NAME."`.`".DB_PREFIX."tieba`");
	$fbs = option::get('fb_tables');
	$fbset = option::get('fb');
	$f = unserialize($fbs);
	if ($x['fffff'] >= $fbset && !empty($f)) {
		$c = sizeof($f);
		foreach ($f as $key => $value) {
			$x = $m->once_fetch_array("SELECT COUNT(*) AS fffff FROM  `".DB_NAME."`.`".DB_PREFIX.$value."`");
			if ($x['fffff'] < $fbset) {
				break;
			}
		}
		return $value;
	} else {
		return 'tieba';
	}
}

/**
 * 清除用户的所有贴吧
 *
 * @param 用户ID
 */
function CleanUser($id) {
	global $m;
	$x=$m->once_fetch_array("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."users` WHERE  `id` = {$id} LIMIT 1");
	$m->query('DELETE FROM `'.DB_NAME.'`.`'.DB_PREFIX.$x['t'].'` WHERE `'.DB_PREFIX.$x['t'].'`.`uid` = '.$id);
}

/**
 * 删除用户
 * 为节省数据库，捆绑清除贴吧数据
 * 
 * @param 用户ID
 */
function DeleteUser($id) {
	global $m;
	CleanUser($id);
	$m->query('DELETE FROM `'.DB_NAME.'`.`'.DB_PREFIX.'users` WHERE `'.DB_PREFIX.'users`.`id` = '.$id);
}


/**
 * zip压缩
 * @param $orig_fname 将在zip的文件路径
 * @param $content 文件内容
 * @param $tempzip zip存储路径
 * @return bool
 */
function CreateZip($orig_fname, $content, $tempzip) {
	if (!class_exists('ZipArchive', FALSE)) {
		return false;
	}
	$zip = new ZipArchive();
	$res = $zip->open($tempzip, ZipArchive::CREATE);
	if ($res === TRUE) {
		$zip->addFromString($orig_fname, $content);
		$zip->close();
		return true;
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
 * fosckopen 改进版
 */

function XFSockOpen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE) {
	if (function_exists('fsockopen')) {
        $return = '';
        $matches = parse_url($url);
        $host = $matches['host'];
        $path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
        $port = !empty($matches['port']) ? $matches['port'] : 80;

        if($post) {
                $out = "POST $path HTTP/1.0\r\n";
                $out .= "Accept: */*\r\n";
                //$out .= "Referer: $boardurl\r\n";
                $out .= "Accept-Language: zh-cn\r\n";
                $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
                $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
                $out .= "Host: $host\r\n";
                $out .= 'Content-Length: '.strlen($post)."\r\n";
                $out .= "Connection: Close\r\n";
                $out .= "Cache-Control: no-cache\r\n";
                $out .= "Cookie: $cookie\r\n\r\n";
                $out .= $post;
        } else {
                $out = "GET $path HTTP/1.0\r\n";
                $out .= "Accept: */*\r\n";
                //$out .= "Referer: $boardurl\r\n";
                $out .= "Accept-Language: zh-cn\r\n";
                $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
                $out .= "Host: $host\r\n";
                $out .= "Connection: Close\r\n";
                $out .= "Cookie: $cookie\r\n\r\n";
        }
        $fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
        if(!$fp) {
                return '';//note $errstr : $errno \r\n
        } else {
                stream_set_blocking($fp, $block);
                stream_set_timeout($fp, $timeout);
                @fwrite($fp, $out);
                while (!feof($fp)) {
                        $status = stream_get_meta_data($fp);
                        if(!empty($status['timed_out'])) {
                                return '';
                        }
                        if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
                                break;
                        }
                }
                $stop = false;
                while(!feof($fp) && !$stop) {
                        $status = stream_get_meta_data($fp);
                        if(!empty($status['timed_out'])) {
                                return '';
                        }
                        $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                        $return .= $data;
                        if($limit) {
                                $limit -= strlen($data);
                                $stop = $limit <= 0;
                        }
                }
                @fclose($fp);
                return $return;
        }
    }
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
	$PluginHooks[$hook][] = $actionFunc;
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

/**
 * 将ROLE名称转换为中文名称
 *
 * @param ROLE
 */
function getrole($role) {
	if ($role == 'admin') {
		return '管理员';
	}
	elseif ($role == 'user') {
		return '用户';
	}
	elseif ($role == 'visitor') {
		return '访客';
	}
	else {
		return '未定义';
	}
}

/**
 * 页面重定向
 *
 * @param $url 地址
 */

function Redirect($url) {
	Clean();
	header("Location: ".$url);
	msg('<meta http-equiv="refresh" content="0; url='.htmlspecialchars($url).'" />请稍候......<br/><br/>如果您的浏览器没有自动跳转，请点击下面的链接',htmlspecialchars($url),false);
}

/**
 * 执行一个计划任务
 * 
 * @param 计划任务文件
 * @param 计划任务名称
 * @return 执行成功true，否则false
 */

function RunCron($file,$name) {
	return cron::run($file,$name);
}

/**
 * 使用反斜线引用字符串或数组
 * @param $s 需要转义的
 * @return 转义结果
 */

function adds($s) {
	if (is_array($s)) {
		$r = array();
		foreach ($s as $key => $value) {
			$k = addslashes($key);
			if (!is_array($value)) {
				$r[$k] = addslashes($value);
			} else {
				$r[$k] = $value;
			}
		}
		return $r;
	} else {
		return addslashes($s);
	}
}

/**
 * Framework 错误处理函数
 */

function sfc_error($errno, $errstr, $errfile, $errline) {
	if (SYSTEM_DEV == true) {
		switch ($errno) {
		    case E_USER_ERROR:          $errnoo = 'User Error'; break;
		    case E_USER_WARNING:        $errnoo = 'User Warning'; break;
		    case E_ERROR:               $errnoo = 'Error'; break;
	        case E_WARNING:             $errnoo = 'Warning'; break;
	        case E_PARSE:               $errnoo = 'Parse Error'; break;
			case E_USER_NOTICE:         $errnoo = 'User Notice';	    break;     
 			case E_CORE_ERROR:          $errnoo = 'Core Error'; break;
	        case E_CORE_WARNING:        $errnoo = 'Core Warning'; break;
	        case E_COMPILE_ERROR:       $errnoo = 'Compile Error'; break;
	        case E_COMPILE_WARNING:     $errnoo = 'Compile Warning'; break;
	        case E_STRICT:              $errnoo = 'Strict Warning'; break;
		    default:                    $errnoo = 'Unknown Error [ #'.$errno.' ]';  break;
   		}
		echo '<div class="alert alert-danger alert-dismissable"><strong>[ StusGame Framework ] '.$errnoo.':</strong> [ Line: '.$errline.' ]<br/>'.$errstr.'<br/>File: '.$errfile.'</div>';
	}
	doAction('error', $errno, $errstr, $errfile, $errline, $errnoo);
}

set_error_handler('sfc_error');
?>