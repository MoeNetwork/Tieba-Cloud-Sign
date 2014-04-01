<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
function loadhead() {
	echo '<!DOCTYPE html><html><head><title>'.SYSTEM_FN.'</title></head><body>';
	echo '<script src="'.SYSTEM_URL.'js/jquery.min.js"></script>';
	echo '<link rel="stylesheet" href="'.SYSTEM_URL.'css/bootstrap.min.css">';
	echo '<script src="'.SYSTEM_URL.'js/bootstrap.min.js"></script>';
	echo '<style type="text/css">body { font-family:"微软雅黑","Microsoft YaHei";background: #eee; }</style>';
	doAction('header');
	template('navi');
}
function loadfoot() {
	if (!empty(option::get('icp'))) {
		echo ' | <a href="http://www.miitbeian.gov.cn/" target="_blank">'.option::get('icp').'</a>';
	}
	echo '<br/>'.option::get('footer');
	doAction('footer');
	echo '</div></body></html>';
}
function template($file) {
	include SYSTEM_ROOT.'/templates/'.$file.'.php';
}
?>