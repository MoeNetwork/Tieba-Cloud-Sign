<?php
if (!defined('DO_NOT_LOAD_UI')) {
    define('SYSTEM_FN','百度贴吧云签到');
	define('SYSTEM_VER','1.0');
	define('SYSTEM_ROOT',dirname(__FILE__).'/..');
	define('SYSTEM_PAGE',isset($_REQUEST['mod']) ? strip_tags($_REQUEST['mod']) : 'default');
	header("content-type:text/html; charset=utf-8");
	echo '<!DOCTYPE html><html><head>';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	echo '<title>功能检查 - '.SYSTEM_FN.'</title><meta name="generator" content="God.Kenvix\'s Blog (https://kenvix.com) and StusGame (http://www.stusgame.com)" /></head><body>';
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
	          <li><a href="http://www.stusgame.com" target="_blank">StusGame</a></li>
	          <li><a href="https://kenvix.com" target="_blank">Kenvix个人博客</a></li>
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
			<th style="width:20%">功能</th>
			<th style="width:15%">需求</th>
			<th style="width:15%">当前</th>
			<th style="width:50%">用途</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><a href="http://php.net/" target="_blank">PHP 5+</a></td>
			<td>必须</td>
			<td><?php echo phpversion(); ?></td>
			<td>核心，未来云签到可能不支持 PHP 5.3 以下版本</td>
		</tr>
		<tr>
			<td><a href="http://php.net/manual/zh/book.curl.php" target="_blank">Client URL</a></td>
			<td>必须</td>
			<td><?php echo checkfunc('curl_exec',true); ?></td>
			<td>抓取网页，用于执行签到等</td>
		</tr>
		<tr>
			<td><a href="http://php.net/manual/zh/book.json.php" target="_blank">JSON</a></td>
			<td>必须</td>
			<td><?php echo checkfunc('json_decode',true); ?></td>
			<td>解析和编码 JSON，用于执行签到等</td>
		</tr>
		<tr>
			<td><a href="http://php.net/manual/zh/function.file-get-contents.php" target="_blank">file_get_contents()</a></td>
			<td>必须</td>
			<td><?php echo checkfunc('file_get_contents',true); ?></td>
			<td>读取文件，用于执行签到等</td>
		</tr>
		<tr>
			<td><a href="http://php.net/manual/zh/book.mbstring.php" target="_blank">MbString</a></td>
			<td>必须</td>
			<td><?php echo checkfunc('mb_ereg'); ?></td>
			<td>各种字符串操作，用于读取贴吧等</td>
		</tr>
		<tr>
			<td><a href="http://php.net/manual/zh/book.mysql.php" target="_blank">MySQL</a></td>
			<td>必须</td>
			<td><?php echo checkfunc('mysql_connect'); ?></td>
			<td>数据库操作，若支持 MySQLi 可忽略本项</td>
		</tr>
		<tr>
			<td><a href="http://php.net/manual/zh/class.mysqli.php" target="_blank">MySQLi</a></td>
			<td>推荐</td>
			<td><?php echo checkclass('mysqli'); ?></td>
			<td>数据库操作，若支持本项可忽略不支持 MySQL 函数</td>
		</tr>
		<tr>
			<td><a href="http://www.php.net/manual/zh/function.file-put-contents.php" target="_blank">写入文件</a></td>
			<td>推荐</td>
			<td><?php if (is_writable(SYSTEM_ROOT) && function_exists('file_put_contents')) { echo '<font color="green">可用</font>'; } else { echo '<font color="black">不支持</font>'; } ?></td>
			<td>本地文件写入，用于在线更新和上传等</td>
		</tr>
		<tr>
			<td><a href="http://php.net/manual/zh/class.ziparchive.php" target="_blank">ZipArchive</a></td>
			<td>推荐</td>
			<td><?php echo checkclass('ZipArchive'); ?></td>
			<td>Zip 解包和压缩，用于在线更新和上传等</td>
		</tr>
		<tr>
			<td><a href="http://php.net/manual/zh/book.image.php" target="_blank">GD</a></td>
			<td>推荐</td>
			<td><?php echo checkfunc('imagecreatetruecolor'); ?></td>
			<td>图像处理，用于生成验证码</td>
		</tr>
		<tr>
			<td><a href="http://www.php.net/manual/zh/function.fsockopen.php" target="_blank">Socket: fsockopen()</a></td>
			<td>推荐</td>
			<td><?php echo checkfunc('fsockopen'); ?></td>
			<td>Socket，用于模拟多线程签到</td>
		</tr>
		<tr>
			<td><a href="http://www.zend.com/" target="_blank">Zend Guard Loader</a></td>
			<td>可选</td>
			<td>未知</td>
			<td>安装 Zend 加密的插件，程序本身没有加密</td>
		</tr>
	</tbody>
</table>