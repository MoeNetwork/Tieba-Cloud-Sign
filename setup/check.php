<?php
if (!defined('DO_NOT_LOAD_UI')) {
    define('SYSTEM_FN','百度贴吧云签到');
	define('SYSTEM_VER','1.0');
	define('SYSTEM_ROOT',dirname(__FILE__).'/..');
	define('SYSTEM_PAGE',isset($_REQUEST['mod']) ? strip_tags($_REQUEST['mod']) : 'default');
	header("content-type:text/html; charset=utf-8");
	echo '<!DOCTYPE html><html><head>';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	echo '<title>功能检查 - '.SYSTEM_FN.'</title><meta name="generator" content="God.Kenvix\'s Blog (http://zhizhe8.net) and StusGame GROUP (http://www.stus8.com)" /></head><body>';
	echo '<script src="../source/js/jquery.min.js"></script>';
	echo '<link rel="stylesheet" href="../source/css/bootstrap.min.css">';
	echo '<script src="../source/js/bootstrap.min.js"></script>';
	echo '<style type="text/css">body { font-family:"微软雅黑","Microsoft YaHei";background: #eee; }</style>';
	?>
		<div class="navbar navbar-default" role="navigation">
	  <!-- Brand and toggle get grouped for better mobile display -->
	  <div class="navbar-header">
	    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
	      <span class="sr-only">贴吧云签到</span>
	      <span class="icon-bar"></span>
	      <span class="icon-bar"></span>
	      <span class="icon-bar"></span>
	    </button>
	    <a class="navbar-brand" href="check.php">贴吧云签到主机功能检查</a>
	  </div>
	  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	    <ul class="nav navbar-nav">
	          <li><a href="http://www.stus8.com" target="_blank">StusGame GROUP</a></li>
	          <li><a href="http://zhizhe8.net" target="_blank">Kenvix个人博客</a></li>
	    </ul>
	  </div><!-- /.navbar-collapse -->
	</div>
	<div style="width:90%;margin: 0 auto;overflow: hidden;position: relative;">
	<?php
}

function checkfunc($f,$m = false) {
	if (function_exists($f)) {
		return '<font color="green">可用</font>';
	} else {
		if ($m == false) {
			return '<font color="black">不支持</font>';
		} else {
			return '<font color="red">不支持</font>';
		}
	}
}

function checkclass($f,$m = false) {
	if (class_exists($f)) {
		return '<font color="green">可用</font>';
	} else {
		if ($m == false) {
			return '<font color="black">不支持</font>';
		} else {
			return '<font color="red">不支持</font>';
		}
	}
}

?>
<h3>环境检查</h3>
<table class="table table-striped">
	<thead>
		<tr>
			<th style="width:20%">功能 / 举例</th>
			<th style="width:15%">需求</th>
			<th style="width:15%">当前</th>
			<th style="width:50%">用途</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>cURL: curl_exec()</td>
			<td>必须</td>
			<td><?php echo checkfunc('curl_exec',true); ?></td>
			<td>抓取网页</td>
		</tr>
		<tr>
			<td>JSON: json_decode()</td>
			<td>必须</td>
			<td><?php echo checkfunc('json_decode',true); ?></td>
			<td>解析和编码 JSON</td>
		</tr>
		<tr>
			<td>file_get_contents()</td>
			<td>必须</td>
			<td><?php echo checkfunc('file_get_contents',true); ?></td>
			<td>读取文件</td>
		</tr>
		<tr>
			<td>Socket: fsockopen()</td>
			<td>推荐</td>
			<td><?php echo checkfunc('fsockopen'); ?></td>
			<td>Socket，例如模拟多线程签到</td>
		</tr>
		<tr>
			<td>ZipArchive</td>
			<td>推荐</td>
			<td><?php echo checkclass('ZipArchive'); ?></td>
			<td>Zip 解包和压缩</td>
		</tr>
		<tr>
			<td>写入权限</td>
			<td>推荐</td>
			<td><?php if (is_writable(SYSTEM_ROOT)) { echo '<font color="green">可用</font>'; } else { echo '<font color="black">不支持</font>'; } ?></td>
			<td>写入文件(1/2)</td>
		</tr>
		<tr>
			<td>file_put_contents()</td>
			<td>推荐</td>
			<td><?php echo checkfunc('file_put_contents'); ?></td>
			<td>写入文件(2/2)</td>
		</tr>
		<tr>
			<td>MySQL: mysql_connect()</td>
			<td>必须</td>
			<td><?php echo checkfunc('mysql_connect'); ?></td>
			<td>数据库操作，若支持 MySQLi 可忽略本项</td>
		</tr>
		<tr>
			<td>MySQLi: mysqli</td>
			<td>推荐</td>
			<td><?php echo checkclass('mysqli'); ?></td>
			<td>数据库操作，若支持本项可忽略不支持 MySQL 函数</td>
		</tr>
		<tr>
			<td>xml_parser_create()</td>
			<td>推荐</td>
			<td><?php echo checkfunc('xml_parser_create'); ?></td>
			<td>XML解析</td>
		</tr>
		<tr>
			<td>SimpleXML: simplexml_load_file()</td>
			<td>推荐</td>
			<td><?php echo checkfunc('simplexml_load_file'); ?></td>
			<td>XML 解析</td>
		</tr>
		<tr>
			<td>MbString: mb_convert_encoding()</td>
			<td>必须</td>
			<td><?php echo checkfunc('mb_convert_encoding'); ?></td>
			<td>各种字符串操作</td>
		</tr>
		<tr>
			<td>PHP 5+</td>
			<td>必须</td>
			<td><?php echo phpversion(); ?></td>
			<td>核心，未来云签到可能不支持PHP 5.3以下版本</td>
		</tr>
		<tr>
			<td>Zend Guard Loader</td>
			<td>可选</td>
			<td>未知</td>
			<td>安装 Zend 加密的插件，程序本身没有加密</td>
		</tr>
	</tbody>
</table>
<h3>功能检查</h3>
<table class="table table-striped">
	<thead>
		<tr>
			<th style="width:20%">功能 / 举例</th>
			<th style="width:15%">需求</th>
			<th style="width:15%">当前</th>
			<th style="width:50%">用途</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>连接百度服务器</td>
			<td>必须</td>
			<td>
				<?php
					if(function_exists('curl_exec')){
						require_once SYSTEM_ROOT.'/lib/class.wcurl.php';
						$x = new wcurl('http://wappass.baidu.com/passport/',array('User-Agent: Phone'.mt_rand()));
						$result = $x->exec();
						$result = strpos($result,'登录百度帐号');
						if(!empty($result)){
							echo '<font color="green">可用</font>';
						} else {
							echo '<font color="red">无法连接到百度服务器。请询问您的主机商。</font>';
						}
					} else {
						echo '<font color="red">请先联系您的主机商开启cURL功能。</font>';
					}
				?>
			</td>
			<td>执行签到等</td>
		</tr>
</tbody>
</table>