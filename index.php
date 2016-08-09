<?php
/**
 * 贴吧云签到
 * Copyright (c) 2012~2016 StusGame All Rights Reserved.
 * 
 * 获取开发文档：https://git.oschina.net/kenvix/Tieba-Cloud-Sign/wikis/
 */
require dirname(__FILE__).'/init.php';
global $i;

if (!isset($_GET['plugin']) && !isset($_GET['pub_plugin']) && !isset($_GET['vip_plugin']) && !isset($_GET['pri_plugin']) && $i['mode'][0] != 'view') {
	template('control');
	loadfoot();
} elseif ($i['mode'][0] == 'view') {
	if (in_array($i['mode'][1], $i['plugins']['actived']) && file_exists(SYSTEM_ROOT . '/plugins/' . $i['mode'][1] . '/' . 'view_' . $i['mode'][2] . '.php')) {
		$plug = getPluginInfo($i['mode'][1]);
		if (in_array($i['mode'][2], $plug['page'])) {
			include SYSTEM_ROOT . '/plugins/' . $i['mode'][1] . '/' . 'view_' . $i['mode'][2] . '.php';
		} else {
			msg('无效的插件自定义页面，请在 插件名_desc.php 中的page键添加此页面');
		}
	} else {
		ReDirect('index.php');
	}
} elseif (isset($_GET['plugin'])) {
    $plug=strip_tags($_GET['plugin']);
    if (in_array($plug, $i['plugins']['actived'])) {
		if (file_exists(SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_show.php') && !is_dir(SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_show.php')) {
			require_once SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_show.php';
		} else {
			msg('插件前台显示模块不存在或不正确');
		}
	}
} elseif (isset($_GET['vip_plugin'])) {
	$plug=strip_tags($_GET['vip_plugin']);
	if (in_array($plug, $i['plugins']['actived'])) {
		if (file_exists(SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_vip.php') && !is_dir(SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_vip.php')) {
			if (ISVIP) {
				require_once SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_vip.php';
			} else {
				msg('权限不足');
			}
		} else {
			msg('插件前台显示模块不存在或不正确');
		}
	}
} elseif (isset($_GET['pri_plugin'])) {
	$plug=strip_tags($_GET['pri_plugin']);
	if (in_array($plug, $i['plugins']['actived'])) {
		if (file_exists(SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_private.php') && !is_dir(SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_private.php')) {
			if (ROLE == 'admin') {
				require_once SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_private.php';
			} else {
				msg('权限不足');
			}
			require_once SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_private.php';
		} else {
			msg('插件前台显示模块不存在或不正确');
		}
	}
} elseif (defined('SYSTEM_READY_LOAD_PUBPLUGIN')) {
	$i['user']['role'] = 'visitor';
	$plug = strip_tags($_GET['pub_plugin']);
	if (in_array($plug, $i['plugins']['actived'])) {
		if (file_exists(SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_public.php') && !is_dir(SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_public.php')) {
			require_once SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_public.php';
		} else {
			msg('插件前台显示模块不存在或不正确');
		}
	} else {
		ReDirect('index.php?mod=login');
	}
	die;
}