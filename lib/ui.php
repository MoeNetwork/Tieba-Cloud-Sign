<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
function loadhead() {
	doAction('top');
	echo '<!DOCTYPE html><html><head>';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	echo '<meta http-equiv="charset" content="utf-8">';
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
	echo '<title>'.strip_tags(SYSTEM_NAME).'</title>';
	echo '<meta name="generator" content="Tieba-Cloud-Sign Ver.'.SYSTEM_VER.'" />';
	echo '<link href="favicon.ico" rel="shortcut icon"/>';
	echo '<meta name="author" content="God.Kenvix\'s Blog (http://zhizhe8.net) and StusGame GROUP (http://www.stus8.com)" />';
	echo '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />';
	echo '<script src="source/js/jquery.min.js"></script>';
	echo '<link rel="stylesheet" href="source/css/bootstrap.min.css">';
	echo '<script src="source/js/bootstrap.min.js"></script>';
	echo '<style type="text/css">body { font-family:"微软雅黑","Microsoft YaHei";background: #eee; }</style>';
	echo '<script type="text/javascript" src="source/js/js.js"></script>';
	echo '<link rel="stylesheet" href="source/css/ui.css">';
	echo '<link rel="stylesheet" href="source/css/my.css">';
	echo '<script type="text/javascript" src="source/js/my.js"></script>';
	doAction('header');
	echo '</head><body>';
	if (option::get('trigger') == 1) {
		echo "<script>$.ajax({ async:true, url: '".SYSTEM_URL."do.php', type: 'GET', data : {},dataType: 'HTML'});</script>";
	}
	template('navi');
	doAction('body');
}
function loadfoot() {
	$icp=option::get('icp');
	if (!empty($icp)) {
		echo ' | <a href="http://www.miitbeian.gov.cn/" target="_blank">'.$icp.'</a>';
	}
	echo '<br/>'.option::get('footer');
	doAction('footer');
	echo '</div></div></div></div></body></html>';
}

function template($file) {
	include SYSTEM_ROOT.'/templates/'.$file.'.php';
}

/**
 * 加载所有激活的插件
 */
function loadplugins() {
	foreach (unserialize(option::get('actived_plugins')) as $value) {
		if (file_exists(SYSTEM_ROOT.'/plugins/'.$value.'/'.$value.'.php') && !is_dir(SYSTEM_ROOT.'/plugins/'.$value.'/'.$value.'.php')) {
			include SYSTEM_ROOT.'/plugins/'.$value.'/'.$value.'.php';
		}
	}
}