<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
function loadhead() {
	doAction('top');
	echo '<!DOCTYPE html><html><head>';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
	echo '<title>'.SYSTEM_FN.'</title><meta name="generator" content="God.Kenvix\'s Blog (http://zhizhe8.net) and StusGame GROUP (http://www.stus8.com)" /></head><body>';
	echo '<script src="'.SYSTEM_URL.'js/jquery.min.js"></script>';
	echo '<link rel="stylesheet" href="'.SYSTEM_URL.'css/bootstrap.min.css">';
	echo '<script src="'.SYSTEM_URL.'js/bootstrap.min.js"></script>';
	echo '<style type="text/css">body { font-family:"微软雅黑","Microsoft YaHei";background: #eee; }</style>';
	echo '<script type="text/javascript" src="'.SYSTEM_URL.'js/js.js"></script>';
	doAction('header');
	if (option::get('trigger') == 1) {
		echo "<script>$.ajax({ async:true, url: '".SYSTEM_URL."do.php', type: 'GET', data : {},dataType: 'HTML'});</script>";
	}
	template('navi');
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
