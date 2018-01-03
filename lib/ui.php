<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }
ob_start();
/**
 * 加载头部
 * @param string $title 页面标题
 */
function loadhead($title = '') {
    if(defined('SYSTEM_NO_UI')) return;
	$title = empty($title) ? strip_tags(SYSTEM_NAME) : $title . ' - ' . strip_tags(SYSTEM_NAME);
    doAction('top');
	echo '<!DOCTYPE html><html><head>';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	echo '<meta http-equiv="charset" content="utf-8">';
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
	echo '<title>'.$title.'</title>';
	echo '<meta name="generator" content="Tieba Cloud Sign Ver.'.SYSTEM_VER.'" />';
	echo '<link href="favicon.ico" rel="shortcut icon"/>';
	echo '<meta name="author" content="Kenvix (https://kenvix.com) at StusGame (http://lovelive.us)" />';
	echo '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />';
	echo '<script src="source/js/jquery.min.js"></script>';
	echo '<link rel="stylesheet" href="source/css/bootstrap.min.css">';
	echo '<script src="source/js/bootstrap.min.js"></script>';
	echo '<style type="text/css">body { font-family:"微软雅黑","Microsoft YaHei";background: #eee; }</style>';
	echo '<script type="text/javascript" src="source/js/js.js"></script>';
	echo '<link rel="stylesheet" href="source/css/ui.css">';
	echo '<link rel="stylesheet" href="source/css/my.css">';
	echo '<script type="text/javascript" src="source/js/my.js"></script>';
	echo '<meta name="keywords" content="'.option::get('system_keywords').'" />';
	echo '<meta name="description" content="'.option::get('system_description').'" />';
	doAction('header');
	echo '</head><body>';
	template('navi');
	doAction('body');
}

/**
 * 加载底部
 * @param bool|string $copy 如果为string，则必须输入插件标识符，并显示插件版权，bool(true)则显示云签到版权
 */
function loadfoot($copy = false) {
    global $i;
    if(defined('SYSTEM_NO_UI')) return;
	$icp=option::get('icp');
	if (!empty($icp)) {
		echo ' | <a href="http://www.miitbeian.gov.cn/" target="_blank">'.$icp.'</a>';
	}
	echo '<br/>'.option::get('footer');
	doAction('footer');
    if(is_string(($copy))) {
        if(isset($i['plugins']['desc'][$copy])) {
            $plug = $i['plugins']['desc'][$copy];
            echo '<br/><br/>';
            if(!empty($plug['plugin']['url'])) {
                echo '<a href="'.htmlspecialchars($plug['plugin']['url']).'" target="_blank">';
            }
            echo $plug[ 'plugin' ][ 'name' ];
            if(!empty($plug['plugin']['url'])) {
                echo '</a>';
            }
            if(!empty($plug['plugin'][ 'version' ])) {
                echo ' V'.$plug['plugin'][ 'version' ];
            }
            echo ' // 作者：';
            if(!empty($plug['author']['url'])) {
                echo '<a href="'.htmlspecialchars($plug['author']['url']).'" target="_blank">';
            }
            echo $plug[ 'author' ][ 'author' ];
            if(!empty($plug['author']['url'])) {
                echo '</a>';
            }
        }
    }
    if($copy) {
        echo '<br/><br/>'.SYSTEM_FN.' V'.SYSTEM_VER.' // 作者: <a href="https://kenvix.com" target="_blank">Kenvix</a>  &amp; <a href="http://www.mokeyjay.com" target="_blank">mokeyjay</a> &amp;  <a href="http://fyy1999.lofter.com/" target="_blank">FYY</a> ';
    }
	echo '</div></div></div></div></body></html>';
}

/**
 * 加载系统或插件的模板（或文件）
 * @param string $file 文件，如果表示为plugin:file，则加载plugin插件的file.php文件
 * @return mixed
 */
function template($file) {
	if(strstr($file , ':')) {
		$parse = explode(':',$file);
		return include SYSTEM_ROOT . '/plugins/' . $parse[0] .'/' . $parse[1] . '.php';
	} else {
		return include SYSTEM_ROOT . '/templates/' . $file . '.php';
	}
}

/**
 * 检查是否应该标记导航为active
 * @param string $mod
 */
function checkIfActive($mod) {
	global $i;
	if ($mod == $i['mode'][0] && !isset($_GET['plugin']) && !isset($_GET['pub_plugin']) && !isset($_GET['vip_plugin']) && !isset($_GET['pri_plugin'])) {
		echo 'active';
	} elseif (strpos($mod, 'admin:') === 0) {
		$a = explode(':', $mod);
		if ($a[0] == $i['mode'][0] && $a[1] == $i['mode'][1] && !isset($_GET['plugin']) && !isset($_GET['pub_plugin']) && !isset($_GET['vip_plugin']) && !isset($_GET['pri_plugin'])) {
			echo 'active';
		}
	}
}
