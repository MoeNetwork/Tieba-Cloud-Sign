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
 * (弃用)生成一个随机的字符串
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
	for ($e = 0; $e < $length; $e++) {
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
 * 获取一个bduss对应的百度用户名
 *
 * @param bduss
 * 返回百度用户名，失败返回空
 */
function getBaiduId($bduss){
	global $m;
	$header[] = 'Content-Type:application/x-www-form-urlencoded; charset=UTF-8';
	$header[] = 'Cookie: BDUSS='.$bduss;
	$c = new wcurl('http://wapp.baidu.com/',$header);
	$data = $c->get();
	$c->close();
	return urldecode(textMiddle($data,'i?un=','">'));
}

/**
 * 获取Gravatar头像（或贴吧头像）
 * http://en.gravatar.com/site/implement/images/
 * @param $email
 * @param $s size
 * @param $d default avatar
 * @param $g
 */
function getGravatar($s = 140, $d = 'mm', $g = 'g', $site = 'secure') {
	if(option::uget('face_img') == 1) {
		if(option::uget('face_url') != ''){
			return option::uget('face_url');
		} else {
			return 'http://tb.himg.baidu.com/sys/portrait/item/';
		}
	} else {
		$hash = md5(EMAIL);
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
 * [已搬走]MySQL 随机取记录
 * 请查看S::rand()
 * @param $t 表
 * @param $c ID列，默认为id
 * @param $n 取多少个
 * @param $w 条件语句
 * @param $f bool 是否强制以多维数组形式返回，默认false
 * @param $p string 随机数据前缀，如果产生冲突，请修改本项
 * @return array 取1个直接返回结果数组(除非$f为true)，取>1个返回多维数组，用foreach取出
 */
function rand_row($t , $c = 'id' , $n = '1', $w = '' , $f = false , $p = 'tempval_') {
	global $m;
	return $m->rand($t , $c , $n, $w, $f, $p);
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
	for ($e=0; $e < $l; $e++) { 
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
	if (!empty($fbset) && $x['fffff'] >= $fbset && !empty($f)) {
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
	if (!file_exists($file)) 
		return false;
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
        return '错误：'.$source.'并不是一个目录';
    }
  
    if(!is_dir($destination)) {
        mkdir($destination,0777,true);
    }

    $handle = dir($source);
  
    while($entry=$handle->read()) {
        if(($entry!=='.')&&($entry!=='..')) {
            if(is_dir($source.'/'.$entry)) {
                CopyAll($source.'/'.$entry, $destination.'/'.$entry);
            } else {
            	copy($source.'/'.$entry, $destination.'/'.$entry);
            }
		}
    }
	return true;
}

/**
 * 备份指定表的数据结构和所有数据
 *
 * @param string $table 数据库表名
 * @return string
 */
function dataBak($table) {
	global $m;
	$sql = "DROP TABLE IF EXISTS `$table`;\n";
	$createtable = $m->query("SHOW CREATE TABLE $table");
	$create = $m->fetch_row($createtable);
	$sql .= $create[1].";\n\n";

	$rows = $m->query("SELECT * FROM $table");
	$numfields = $m->num_fields($rows);
	$numrows = $m->num_rows($rows);
	while ($row = $m->fetch_row($rows)) {
		$comma = '';
		$sql .= "INSERT INTO `$table` VALUES(";
		for ($e = 0; $e < $numfields; $e++) {
			$sql .= $comma."'" . $m->escape_string($row[$e]) . "'";
			$comma = ',';
		}
		$sql .= ");\n";
	}
	$sql .= "\n";
	return $sql;
}

/**
 * 执行一个网络请求而不等待返回结果
 * @param string $url URL
 * @param string $post post数据包，留空为get
 * @param string $cookie cookies
 * @return bool fsockopen是否成功
 */
function sendRequest($url , $post = '' , $cookie = '') {
	if (function_exists('fsockopen')) {
		$matches = parse_url($url);
        $host = $matches['host'];
        if (substr($url, 0, 8) == 'https://') {
        	$host = 'ssl://' . $host;
        }
        $path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
        $port = !empty($matches['port']) ? $matches['port'] : 80;
        if(!empty($post)) {
                $out = "POST $path HTTP/1.1\r\n";
                $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
                $out .= "Host: $host\r\n";
                $out .= "Connection: Close\r\n\r\n";
                $out .= $post;
        } else {
                $out = "GET $path HTTP/1.1\r\n";
                $out .= "Host: $host\r\n";
                $out .= "Connection: Close\r\n\r\n";
        }
        $fp = fsockopen($host, $port);
		if (!$fp) {
			return false;
		} else {
			stream_set_blocking($fp , 0);
			stream_set_timeout($fp , 0);
			fwrite($fp, $out);
			fclose($fp);
			return true;
		}
	} else {
		$x = new wcurl($url);
		$x->set(CURLOPT_CONNECTTIMEOUT , 1);
		$x->set(CURLOPT_TIMEOUT , 1);
		$x->addcookie($cookie);
		if (empty($post)) {
			$x->post($post);
		} else {
			$x->exec();
		}
		return true;
	}
}

/**
 * fosckopen 改进版
 */ 

function XFSockOpen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = false) {
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
        $fp = fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
        if(!$fp) {
                return false;//note $errstr : $errno \r\n
        } else {
                stream_set_blocking($fp, $block);
                stream_set_timeout($fp, $timeout);
                fwrite($fp, $out);
                while (!feof($fp)) {
                        $status = stream_get_meta_data($fp);
                        if(!empty($status['timed_out'])) {
                                return false;
                        }
                        if(($header = fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
                                break;
                        }
                }
                $stop = false;
                while(!feof($fp) && !$stop) {
                        $status = stream_get_meta_data($fp);
                        if(!empty($status['timed_out'])) {
                                return false;
                        }
                        $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                        $return .= $data;
                        if($limit) {
                                $limit -= strlen($data);
                                $stop = $limit <= 0;
                        }
                }
                fclose($fp);
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
 * 打印可读的变量信息
 * @param mixed $var
 * @param bool $mode false = var_dump() | true = print_r()
 */
function dump($var , $mode = false) {
	echo '<pre>';
	if (!$mode) {
		var_dump($var);
	} else {
		print_r($var);
	}
	echo '</pre>';
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
	msg('<meta http-equiv="refresh" content="0; url='.htmlspecialchars($url).'" />请稍候......<br/><br/>如果您的浏览器没有自动跳转，请点击下面的链接',htmlspecialchars($url));
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
 * 根据文件名称获取扩展名
 * @param string $name 文件名
 * @return string 扩展名（不带.）
 */
function get_extname($name) {
	return pathinfo($name, PATHINFO_EXTENSION);
}

/**
 * 根据扩展名获取文件的MIME
 * @param string $ext 扩展名
 * @return string MIME
 */
function get_mime($ext) {
	static $mime_types = array(
        'apk'     => 'application/vnd.android.package-archive',
        '3gp'     => 'video/3gpp', 
        'ai'      => 'application/postscript', 
        'aif'     => 'audio/x-aiff', 
        'aifc'    => 'audio/x-aiff', 
        'aiff'    => 'audio/x-aiff', 
        'asc'     => 'text/plain', 
        'atom'    => 'application/atom+xml', 
        'au'      => 'audio/basic', 
        'avi'     => 'video/x-msvideo', 
        'bcpio'   => 'application/x-bcpio', 
        'bin'     => 'application/octet-stream', 
        'bmp'     => 'image/bmp', 
        'cdf'     => 'application/x-netcdf', 
        'cgm'     => 'image/cgm', 
        'class'   => 'application/octet-stream', 
        'cpio'    => 'application/x-cpio', 
        'cpt'     => 'application/mac-compactpro', 
        'csh'     => 'application/x-csh', 
        'css'     => 'text/css', 
        'dcr'     => 'application/x-director', 
        'dif'     => 'video/x-dv', 
        'dir'     => 'application/x-director', 
        'djv'     => 'image/vnd.djvu', 
        'djvu'    => 'image/vnd.djvu', 
        'dll'     => 'application/octet-stream', 
        'dmg'     => 'application/octet-stream', 
        'dms'     => 'application/octet-stream', 
        'doc'     => 'application/msword', 
        'dtd'     => 'application/xml-dtd', 
        'dv'      => 'video/x-dv', 
        'dvi'     => 'application/x-dvi', 
        'dxr'     => 'application/x-director', 
        'eps'     => 'application/postscript', 
        'etx'     => 'text/x-setext', 
        'exe'     => 'application/octet-stream', 
        'ez'      => 'application/andrew-inset', 
        'flv'     => 'video/x-flv', 
        'gif'     => 'image/gif', 
        'gram'    => 'application/srgs', 
        'grxml'   => 'application/srgs+xml', 
        'gtar'    => 'application/x-gtar', 
        'gz'      => 'application/x-gzip', 
        'hdf'     => 'application/x-hdf', 
        'hqx'     => 'application/mac-binhex40', 
        'htm'     => 'text/html', 
        'html'    => 'text/html', 
        'ice'     => 'x-conference/x-cooltalk', 
        'ico'     => 'image/x-icon', 
        'ics'     => 'text/calendar', 
        'ief'     => 'image/ief', 
        'ifb'     => 'text/calendar', 
        'iges'    => 'model/iges', 
        'igs'     => 'model/iges', 
        'jnlp'    => 'application/x-java-jnlp-file', 
        'jp2'     => 'image/jp2', 
        'jpe'     => 'image/jpeg', 
        'jpeg'    => 'image/jpeg', 
        'jpg'     => 'image/jpeg', 
        'js'      => 'application/x-javascript', 
        'kar'     => 'audio/midi', 
        'latex'   => 'application/x-latex', 
        'lha'     => 'application/octet-stream', 
        'lzh'     => 'application/octet-stream', 
        'm3u'     => 'audio/x-mpegurl', 
        'm4a'     => 'audio/mp4a-latm', 
        'm4p'     => 'audio/mp4a-latm', 
        'm4u'     => 'video/vnd.mpegurl', 
        'm4v'     => 'video/x-m4v', 
        'mac'     => 'image/x-macpaint', 
        'man'     => 'application/x-troff-man', 
        'mathml'  => 'application/mathml+xml', 
        'me'      => 'application/x-troff-me', 
        'mesh'    => 'model/mesh', 
        'mid'     => 'audio/midi', 
        'midi'    => 'audio/midi', 
        'mif'     => 'application/vnd.mif', 
        'mov'     => 'video/quicktime', 
        'movie'   => 'video/x-sgi-movie', 
        'mp2'     => 'audio/mpeg', 
        'mp3'     => 'audio/mpeg', 
        'mp4'     => 'video/mp4', 
        'mpe'     => 'video/mpeg', 
        'mpeg'    => 'video/mpeg', 
        'mpg'     => 'video/mpeg', 
        'mpga'    => 'audio/mpeg', 
        'ms'      => 'application/x-troff-ms', 
        'msh'     => 'model/mesh', 
        'mxu'     => 'video/vnd.mpegurl', 
        'nc'      => 'application/x-netcdf', 
        'oda'     => 'application/oda', 
        'ogg'     => 'application/ogg', 
        'ogv'     => 'video/ogv', 
        'pbm'     => 'image/x-portable-bitmap', 
        'pct'     => 'image/pict', 
        'pdb'     => 'chemical/x-pdb', 
        'pdf'     => 'application/pdf', 
        'pgm'     => 'image/x-portable-graymap', 
        'pgn'     => 'application/x-chess-pgn', 
        'pic'     => 'image/pict', 
        'pict'    => 'image/pict', 
        'png'     => 'image/png', 
        'pnm'     => 'image/x-portable-anymap', 
        'pnt'     => 'image/x-macpaint', 
        'pntg'    => 'image/x-macpaint', 
        'ppm'     => 'image/x-portable-pixmap', 
        'ppt'     => 'application/vnd.ms-powerpoint', 
        'ps'      => 'application/postscript', 
        'qt'      => 'video/quicktime', 
        'qti'     => 'image/x-quicktime', 
        'qtif'    => 'image/x-quicktime', 
        'ra'      => 'audio/x-pn-realaudio', 
        'ram'     => 'audio/x-pn-realaudio', 
        'ras'     => 'image/x-cmu-raster', 
        'rdf'     => 'application/rdf+xml', 
        'rgb'     => 'image/x-rgb', 
        'rm'      => 'application/vnd.rn-realmedia', 
        'roff'    => 'application/x-troff', 
        'rtf'     => 'text/rtf', 
        'rtx'     => 'text/richtext', 
        'sgm'     => 'text/sgml', 
        'sgml'    => 'text/sgml', 
        'sh'      => 'application/x-sh', 
        'shar'    => 'application/x-shar', 
        'silo'    => 'model/mesh', 
        'sit'     => 'application/x-stuffit', 
        'skd'     => 'application/x-koan', 
        'skm'     => 'application/x-koan', 
        'skp'     => 'application/x-koan', 
        'skt'     => 'application/x-koan', 
        'smi'     => 'application/smil', 
        'smil'    => 'application/smil', 
        'snd'     => 'audio/basic', 
        'so'      => 'application/octet-stream', 
        'spl'     => 'application/x-futuresplash', 
        'src'     => 'application/x-wais-source', 
        'sv4cpio' => 'application/x-sv4cpio', 
        'sv4crc'  => 'application/x-sv4crc', 
        'svg'     => 'image/svg+xml', 
        'swf'     => 'application/x-shockwave-flash', 
        't'       => 'application/x-troff', 
        'tar'     => 'application/x-tar', 
        'tcl'     => 'application/x-tcl', 
        'tex'     => 'application/x-tex', 
        'texi'    => 'application/x-texinfo', 
        'texinfo' => 'application/x-texinfo', 
        'tif'     => 'image/tiff', 
        'tiff'    => 'image/tiff', 
        'tr'      => 'application/x-troff', 
        'tsv'     => 'text/tab-separated-values', 
        'txt'     => 'text/plain', 
        'ustar'   => 'application/x-ustar', 
        'vcd'     => 'application/x-cdlink', 
        'vrml'    => 'model/vrml', 
        'vxml'    => 'application/voicexml+xml', 
        'wav'     => 'audio/x-wav', 
        'wbmp'    => 'image/vnd.wap.wbmp', 
        'wbxml'   => 'application/vnd.wap.wbxml', 
        'webm'    => 'video/webm', 
        'wml'     => 'text/vnd.wap.wml', 
        'wmlc'    => 'application/vnd.wap.wmlc', 
        'wmls'    => 'text/vnd.wap.wmlscript', 
        'wmlsc'   => 'application/vnd.wap.wmlscriptc', 
        'wmv'     => 'video/x-ms-wmv', 
        'wrl'     => 'model/vrml', 
        'xbm'     => 'image/x-xbitmap', 
        'xht'     => 'application/xhtml+xml', 
        'xhtml'   => 'application/xhtml+xml', 
        'xls'     => 'application/vnd.ms-excel', 
        'xml'     => 'application/xml', 
        'xpm'     => 'image/x-xpixmap', 
        'xsl'     => 'application/xml', 
        'xslt'    => 'application/xslt+xml', 
        'xul'     => 'application/vnd.mozilla.xul+xml', 
        'xwd'     => 'image/x-xwindowdump', 
        'xyz'     => 'chemical/x-xyz', 
        'zip'     => 'application/zip' 
    );
    return isset($mime_types[$ext]) ? $mime_types[$ext] : 'application/octet-stream';
}