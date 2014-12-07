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
 * 获取两段文本之间的文本
 *
 * @param 完整的文本
 * @param 左边文本
 * @param 右边文本
 * 返回“左边文本”与“右边文本”之间的文本
 */
function textMiddle($text, $left, $right) {
	$loc1 = stripos($text, $left);
	if (is_bool($loc1)) { return ""; }
	$loc1 += strlen($left);
	$loc2 = stripos($text, $right, $loc1);
	if (is_bool($loc2)) { return ""; }
	return substr($text, $loc1, $loc2 - $loc1);
}

/**
 * 获取Gravatar头像（或贴吧头像）
 * http://en.gravatar.com/site/implement/images/
 * @param $email
 * @param $s size
 * @param $d default avatar
 * @param $g
 */
function getGravatar($email, $s = 40, $d = 'mm', $g = 'g', $site = 'secure') {
	if(option::uget("face_img")) {
		if(option::uget("face_baiduid") != ""){
			$c = new wcurl('http://www.baidu.com/p/'.option::uget("face_baiduid"));
			$data = $c->get();
			$c->close();
			return stripslashes(textMiddle($data,'<img class=portrait-img src=\x22','\x22>'));
		} else {
			return 'http://tb.himg.baidu.com/sys/portrait/item/';
		}
	} else {
		$hash = md5($email);
		$avatar = "https://{$site}.gravatar.com/avatar/$hash?s=$s&d=$d&r=$g";
		return $avatar;
	}
}

/**
 * 解压zip
 * @param type $zipfile 要解压的文件
 * @param type $path 解压到该目录
 * @param type $type
 * @return int
 */
function UnZip($zipfile, $path) {
	if (!class_exists('ZipArchive', FALSE)) {
		return 3;//zip模块问题
	}
	$zip = new ZipArchive();
	if (@$zip->open($zipfile) !== TRUE) {
		return 2;//文件权限问题
	}
	if (true === @$zip->extractTo($path)) {
		$zip->close();
		return 0;
	} else {
		return 1;//文件权限问题
	}
}
/**
 * 清空缓冲区的内容
 * @note 已修复无法清除缓冲区的bug
 */
function Clean() {
	ob_clean();
}

/**
 * MySQL 随机取记录
 * 
 * @param $t 表
 * @param $c ID列，默认为id
 * @param $n 取多少个
 * @param $w 条件语句
 * @param $f bool 是否强制以多维数组形式返回，默认false
 * @return array 取1个直接返回结果数组(除非$f为true)，取>1个返回多维数组，用foreach取出
 */
function rand_row($t , $c = 'id' , $n = '1', $w = '' , $f = false) {
	global $m;
	if (!empty($w)) {
		$w = ' AND '.$w;
	}
	$sql = "SELECT * FROM `{$t}` AS r1 JOIN (SELECT (RAND() * (SELECT MAX(`{$c}`) FROM `{$t}`)) AS {$c}) AS r2 WHERE r1.{$c} >= r2.{$c} {$w} ORDER BY r1.{$c} ASC LIMIT 1;";
	if ($n == '1') {
		$r =  $m->once_fetch_array($sql);
		if (!empty($r)) {
			$r['id'] = intval($r['id']) + 1;
			if ($f === false) {
				return $r;
			} else {
				return array($r);
			}
		} else {
			return array();
		}
	} else {
		$r = array();
		for ($i=0; $i < $n; $i++) { 
			$r1 = $m->once_fetch_array($sql);
			if (!empty($r1)) {
				$r1['id'] = intval($r1['id']) + 1;
				$r[] = $r1;
			}
		}
		return $r;
	}
}

/**
 * 从数组中随机取一个值
 * @param array 数组
 */
function rand_array($a) {
	$r = array_rand($a,1);
	return $a[$r];
}

/**
 * 随机生成一个指定长度的正整数
 * @param int $l 长度
 */
function rand_int($l) {
	$int = null;
	for ($i=0; $i < $l; $i++) { 
		$int .= mt_rand(0,9);
	}
	return $int;
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
 * 批量复制
 * @param $source 源目录名  
 * @param $destination 目的目录名  
 * @return 成功返回TRUE，失败返回原因
 */
function CopyAll($source,$destination){   
    if(!is_dir($source)) {   
        return "Error:the {$source} is not a direction!";   
    }   
  
    if(!is_dir($destination)) {   
        mkdir($destination,0777);   
    }  

    $handle = dir($source);   
  
    while($entry=$handle->read()) {   
        if(($entry!==".")&&($entry!=="..")) {   
            if(is_dir($source."/".$entry)) {   
                CopyAll($source."/".$entry, $destination."/".$entry); 
            } else {
            	copy($source."/".$entry, $destination."/".$entry);
            }
		}
    }   
	return true;   
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
	global $i;
	$i['PluginHooks'][$hook][] = $actionFunc;
	return true;
}

/**
 * 执行挂在钩子上的函数,支持多参数 eg:doAction('post_comment', $author, $email, $url, $comment);
 *
 * @param string $hook
 */
function doAction($hook) {
	global $i;
	$args = array_slice(func_get_args(), 1);
	if (isset($i['PluginHooks'][$hook])) {
		foreach ($i['PluginHooks'][$hook] as $function) {
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
	$role = strtolower($role);
	if ($role == 'admin') {
		return '管理员';
	}
	elseif ($role == 'user') {
		return '用户';
	}
	elseif ($role == 'vip') {
		return 'VIP';
	}
	elseif ($role == 'visitor') {
		return '访客';
	}
	elseif ($role == 'banned') {
		return '禁止访问';
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
				$r[$k] = adds($value);
			}
		}
		return $r;
	} else {
		return addslashes($s);
	}
}

/**
 * 使用反斜线引用字符串或数组以便于SQL查询
 * 只引用'和\
 * @param $s 需要转义的
 * @return 转义结果
 */
function sqladds($s) {
	if (is_array($s)) {
		$r = array();
		foreach ($s as $key => $value) {
			$k = str_replace('\'','\\\'', str_replace('\\','\\\\',$value));

			if (!is_array($value)) {
				$r[$k] = str_replace('\'','\\\'', str_replace('\\','\\\\',$value));
			} else {
				$r[$k] = sqladds($value);
			}
		}
		return $r;
	} else {
		return str_replace('\'','\\\'', str_replace('\\','\\\\',$s));
	}
}


/**
 * 转为正数或者0
 * @param $s 需要转换的
 * @return 转换结果
 */

function topos($s) {
	return abs(intval($s));
}

/**
 * 执行一个通配符表达式匹配
 * [可当preg_match()的简化版本去理解]
 * @param string $exp 匹配表达式
 * @param string $str 在这个字符串内运行匹配
 * @param int $pat 规定匹配模式，0表示尽可能多匹配，1表示尽可能少匹配
 * @return array 匹配结果，$matches[0]将包含完整模式匹配到的文本， $matches[1] 将包含第一个捕获子组匹配到的文本，以此类推。
 */
function easy_match($exp, $str, $pat = 0) {
	$exp = str_ireplace('\\', '\\\\', $exp);
	$exp = str_ireplace('/', '\/', $exp);
	$exp = str_ireplace('?', '\?', $exp);
	$exp = str_ireplace('<', '\<', $exp);
	$exp = str_ireplace('>', '\>', $exp);
	$exp = str_ireplace('^', '\^', $exp);
	$exp = str_ireplace('$', '\$', $exp);
	$exp = str_ireplace('+', '\+', $exp);
	$exp = str_ireplace('(', '\(', $exp);
	$exp = str_ireplace(')', '\)', $exp);
	$exp = str_ireplace('[', '\[', $exp);
	$exp = str_ireplace(']', '\]', $exp);
	$exp = str_ireplace('|', '\|', $exp);
	$exp = str_ireplace('}', '\}', $exp);
	$exp = str_ireplace('{', '\{', $exp);
	if ($pat==0) {
		$z = '(.*)';
	} else {
		$z = '(.*?)';
	}
	$exp = str_ireplace('*', $z, $exp);
	$exp = '/' . $exp . '/';
	preg_match($exp, $str, $r);
	return $r;
}

/**
 * 执行一个全局通配符表达式匹配
 * [可当preg_match_all()的简化版本去理解]
 * @param string $exp 匹配表达式
 * @param string $str 在这个字符串内运行匹配
 * @param int $pat 规定匹配模式，0表示尽可能多匹配，1表示尽可能少匹配
 * @param int $flags 可以使用 PREG_PATTERN_ORDER 或 PREG_SET_ORDER 或 PREG_OFFSET_CAPTURE
 * @return array 匹配结果，数组排序通过flags指定。
 */
function easy_match_all($exp, $str, $pat = 0, $flags = PREG_PATTERN_ORDER) {
	$exp = str_ireplace('\\', '\\\\', $exp);
	$exp = str_ireplace('/', '\/', $exp);
	$exp = str_ireplace('?', '\?', $exp);
	$exp = str_ireplace('<', '\<', $exp);
	$exp = str_ireplace('>', '\>', $exp);
	$exp = str_ireplace('^', '\^', $exp);
	$exp = str_ireplace('$', '\$', $exp);
	$exp = str_ireplace('+', '\+', $exp);
	$exp = str_ireplace('(', '\(', $exp);
	$exp = str_ireplace(')', '\)', $exp);
	$exp = str_ireplace('[', '\[', $exp);
	$exp = str_ireplace(']', '\]', $exp);
	$exp = str_ireplace('|', '\|', $exp);
	$exp = str_ireplace('}', '\}', $exp);
	$exp = str_ireplace('{', '\{', $exp);
	if ($pat==0) {
		$z = '(.*)';
	} else {
		$z = '(.*?)';
	}
	$exp = str_ireplace('*', $z, $exp);
	$exp = '/' . $exp . '/';
	preg_match($exp, $str, $r, $flags);
	return $r;
}

/**
 * 已弃用，下列函数仅为了兼容旧插件
 */

function getCookie($pid) { misc::getCookie($pid); }

function DoSign_Mobile($uid,$kw,$id,$pid,$fid) { misc::DoSign_Mobile($uid,$kw,$id,$pid,$fid); }

function DoSign_Default($uid,$kw,$id,$pid,$fid) { misc::DoSign_Default($uid,$kw,$id,$pid,$fid); }

function DoSign_Client($uid,$kw,$id,$pid,$fid){ misc::DoSign_Client($uid,$kw,$id,$pid,$fid); }

function DoSign_All($uid,$kw,$id,$table,$sign_mode,$pid,$fid) { misc::DoSign_All($uid,$kw,$id,$table,$sign_mode,$pid,$fid); }

function DoSign($table,$sign_mode) { misc::DoSign($table,$sign_mode); }