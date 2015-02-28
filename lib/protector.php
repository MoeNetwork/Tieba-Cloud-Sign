<?php
!defined('SYSTEM_ROOT') && exit('fuck♂you');
//防护脚本版本号
define("wmzz_prot_VERSION", '0.1.1.9');
//防护脚本MD5值
define("wmzz_prot_MD5", md5(@file_get_contents(__FILE__)));
//get拦截规则
$getfilter = "<.*=(&\\#\\d+?;)+?>|<.*data=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\\(\d+?|sleep\s*?\\([\d\.]+?\\)|load_file\s*?\\()|<[a-z]+?\\b[^>]*?\\bon([a-z]{4,})\s*?=|^\\+\\/v(8|9)|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
//post拦截规则
$postfilter = "<.*=(&\\#\\d+?;)+?>|<.*data=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\\(\d+?|sleep\s*?\\([\d\.]+?\\)|load_file\s*?\\()|<[^>]*?\\b(onerror|onmousemove|onload|onclick|onmouseover)\\b|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
//cookie拦截规则
$cookiefilter = "benchmark\s*?\\(\d+?|sleep\s*?\\([\d\.]+?\\)|load_file\s*?\\(|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
//referer获取
$wmzz_prot_referer = empty($_SERVER['HTTP_REFERER']) ? array() : array('HTTP_REFERER'=>$_SERVER['HTTP_REFERER']);

class wmzz_prot_http {

  var $method;
  var $post;
  var $header;
  var $ContentType;

  function __construct() {
    $this->method = '';
    $this->cookie = '';
    $this->post = '';
    $this->header = '';
    $this->errno = 0;
    $this->errstr = '';
  }

  function post($url, $data = array(), $referer = '', $limit = 0, $timeout = 30, $block = TRUE) {
    $this->method = 'POST';
    $this->ContentType = "Content-Type: application/x-www-form-urlencoded\r\n";
    if($data) {
      $post = '';
      foreach($data as $k=>$v) {
        $post .= $k.'='.rawurlencode($v).'&';
      }
      $this->post .= substr($post, 0, -1);
    }
    return $this->request($url, $referer, $limit, $timeout, $block);
  }

  function request($url, $referer = '', $limit = 0, $timeout = 30, $block = TRUE) {
    $matches = parse_url($url);
    $host = $matches['host'];
    $path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
    $port = $matches['port'] ? $matches['port'] : 80;
    if($referer == '') $referer = URL;
    $out = "$this->method $path HTTP/1.1\r\n";
    $out .= "Accept: */*\r\n";
    $out .= "Referer: $referer\r\n";
    $out .= "Accept-Language: zh-cn\r\n";
    $out .= "User-Agent: ".$_SERVER['HTTP_USER_AGENT']."\r\n";
    $out .= "Host: $host\r\n";
    if($this->method == 'POST') {
      $out .= $this->ContentType;
      $out .= "Content-Length: ".strlen($this->post)."\r\n";
      $out .= "Cache-Control: no-cache\r\n";
      $out .= "Connection: Close\r\n\r\n";
      $out .= $this->post;
    } else {
      $out .= "Connection: Close\r\n\r\n";
    }
    if($timeout > ini_get('max_execution_time')) @set_time_limit($timeout);
    $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
    $this->post = '';
    if(!$fp) {
      return false;
    } else {
      stream_set_blocking($fp, $block);
      stream_set_timeout($fp, $timeout);
      fwrite($fp, $out);
      $this->data = '';
      $status = stream_get_meta_data($fp);
      if(!$status['timed_out']) {
        $maxsize = min($limit, 1024000);
        if($maxsize == 0) $maxsize = 1024000;
        $start = false;
        while(!feof($fp)) {
          if($start) {
            $line = fread($fp, $maxsize);
            if(strlen($this->data) > $maxsize) break;
            $this->data .= $line;
          } else {
            $line = fgets($fp);
            $this->header .= $line;
            if($line == "\r\n" || $line == "\n") $start = true;
          }
        }
      }
      fclose($fp);
      return "200";
    }
  }

}

/**
 *   关闭用户错误提示
 */
function wmzz_prot_error() {
  if (ini_get('display_errors')) {
    ini_set('display_errors', '0');
  }
}
function wmzz_prot_cheack() { return true; }
function wmzz_prot_updateck($ve) { return true; }
/** 
 * 统计攻击次数
 */
function wmzz_prot_slog() { 
}
/**
 *  参数拆分
 */
function wmzz_prot_arr_foreach($arr) {
  static $str;
  if (!is_array($arr)) {
    return $arr;
  }
  foreach ($arr as $key => $val ) {

    if (is_array($val)) {

      wmzz_prot_arr_foreach($val);
    } else {

      $str[] = $val;
    }
  }
  return implode($str);
}

/**
 *  防护提示页
 */
function wmzz_prot_pape(){
ob_clean(); flush();
?>
<!DOCTYPE html>
<html>
<head>			
<title>安全警告</title>
<meta charset="utf-8" />
<style type="text/css">
body {
  background-color:#F7F7F7;
  font-family: Arial;
  font-size: 12px;
  line-height:150%;
}
.main {
  background-color:#FFFFFF;
  font-size: 12px;
  color: #666666;
  width:650px;
  margin:60px auto 0px;
  border-radius: 10px;
  padding:30px 10px;
  list-style:none;
  border:#DFDFDF 1px solid;
}
.main p {
  line-height: 18px;
  margin: 5px 20px;
}
</style><div class="main">
<h2>警告信息</h2>
<?php echo SYSTEM_FN; ?> 安全系统检测到您可能试图执行危险代码，已被系统拦截。
<br/><br/>有关更多信息，请联系 <?php echo SYSTEM_FN; ?> 的站点管理员<br/><br/>
<input type="button" style="width:30%" value="<< 返回上一页" onclick="history.back(-1);">
<br/><br/><small>Powered By <?php echo SYSTEM_FN; ?></a>&nbsp;And&nbsp;<a href="http://zhizhe8.net" target="_blank">站点安全保护</a></small>
</div>
<?php
}

/**
 *  攻击检查拦截
 */
function wmzz_prot_StopAttack($StrFiltKey,$StrFiltValue,$ArrFiltReq,$method) {
  $StrFiltValue=wmzz_prot_arr_foreach($StrFiltValue);
  if (preg_match("/".$ArrFiltReq."/is",$StrFiltValue)==1){
    wmzz_prot_slog();
    exit(wmzz_prot_pape());
  }
  if (preg_match("/".$ArrFiltReq."/is",$StrFiltKey)==1){
    wmzz_prot_slog();
    exit(wmzz_prot_pape());
  }

}

/**
 *  curl方式提交
 */
function wmzz_prot_curl($url , $postdata = array()) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
  $response = curl_exec($ch);
  $httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
  curl_close($ch);
  return array('httpcode'=>$httpcode,'response'=>$response);
}

if (option::get('protector') == 1 && (!defined('ROLE') || ROLE != 'admin')) {
    foreach($_GET as $key=>$value) {
      wmzz_prot_StopAttack($key,$value,$getfilter,"GET");
    }
    foreach($_POST as $key=>$value) {
      wmzz_prot_StopAttack($key,$value,$postfilter,"POST");
    }
    foreach($_COOKIE as $key=>$value) {
      wmzz_prot_StopAttack($key,$value,$cookiefilter,"COOKIE");
    }
    foreach($wmzz_prot_referer as $key=>$value) {
      wmzz_prot_StopAttack($key,$value,$postfilter,"REFERRER");
    }
}

?>