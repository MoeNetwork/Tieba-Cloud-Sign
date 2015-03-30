<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
global $i;

/**
 * 加载所有激活的插件
 */
function loadplugins() {
	global $i;
	if (defined('SYSTEM_PLUGINS_LOADED')) {
		return;
	}
	foreach ($i['plugins']['actived'] as $value) {
		if (file_exists(SYSTEM_ROOT.'/plugins/'.$value.'/'.$value.'.php') && !is_dir(SYSTEM_ROOT.'/plugins/'.$value.'/'.$value.'.php')) {
			include SYSTEM_ROOT.'/plugins/'.$value.'/'.$value.'.php';
		}
	}
	define('SYSTEM_PLUGINS_LOADED', true);
}

/**
 * 激活插件
 * @return bool
 */
function activePlugin($plugin) {
	global $m;
	if (file_exists(SYSTEM_ROOT . '/plugins/' . $plugin . '/' . $plugin . '.php')) {
		$m->query("UPDATE `" . DB_PREFIX . "plugins` SET `status` = '1' WHERE `name` = '{$plugin}';");
		$callback_file =  SYSTEM_ROOT . '/plugins/' . $plugin . '/' . $plugin . '_callback.php';
		if (file_exists($callback_file)) {
			require_once $callback_file;
			if (function_exists('callback_init')) {
				callback_init();
			}
		}
		return true;
	} else {
		return false;
	}
}

/**
 * 禁用插件
 * @return bool
 */
function inactivePlugin($plugin) {
	global $m;
	if (file_exists(SYSTEM_ROOT . '/plugins/' . $plugin . '/' . $plugin . '.php')) {
		$m->query("UPDATE `" . DB_PREFIX . "plugins` SET `status` = '0' WHERE `name` = '{$plugin}';");
		$callback_file =  SYSTEM_ROOT . '/plugins/' . $plugin . '/' . $plugin . '_callback.php';
		if (file_exists($callback_file)) {
			require_once $callback_file;
			if (function_exists('callback_inactive')) {
				callback_inactive();
			}
		}
		return true;
	} else {
		return false;
	}
}

/**
 * 安装插件
 * @return  bool	
 */
function installPlugin($plugin) {
	global $m;
	if (file_exists(SYSTEM_ROOT . '/plugins/' . $plugin . '/' . $plugin . '.php')) {
		$m->query("INSERT IGNORE INTO `" . DB_PREFIX . "plugins` (`name`,`status`,`options`) VALUES ('{$plugin}','0','');");
		$callback_file =  SYSTEM_ROOT . '/plugins/' . $plugin . '/' . $plugin . '_callback.php';
		if (file_exists($callback_file)) {
			require_once $callback_file;
			if (function_exists('callback_install')) {
				callback_install();
			}
		}
		return true;
	} else {
		return false;
	}
}

/**
 * 卸载插件
 * @return bool 
 */
function uninstallPlugin($plugin) {
	global $m;
	inactivePlugin($plugin);
	$callback_file =  SYSTEM_ROOT . '/plugins/' . $plugin . '/' . $plugin . '_callback.php';
	if (file_exists($callback_file)) {
		require_once $callback_file;
		if (function_exists('callback_remove')) {
			callback_remove();
		}
	}
	$m->query("DELETE FROM `" . DB_PREFIX . "plugins` WHERE `name` = '{$plugin}';");
	$isapp = option::get('isapp');
	if (empty($isapp)) {
		DeleteFile(SYSTEM_ROOT . '/plugins/' . $plugin);
	}
}

/**
 * 调用插件自定义保存设置函数
 * 插件调用方法：setting.php?mod=setplugin:插件名称
 * 然后系统会调用 插件名_callback.php 的 callback_setting()
 */
function settingPlugin($plugin) {
	global $m;
	$callback_file =  SYSTEM_ROOT . '/plugins/' . $plugin . '/' . $plugin . '_callback.php';
	if (file_exists($callback_file)) {
		require_once $callback_file;
		if (function_exists('callback_setting')) {
			callback_setting();
		}
	}
}

/**
* 获取所有插件列表，未定义插件名称的插件将不予获取
* 插件目录：/plugins
* 仅识别 插件目录/插件/插件.php 目录结构的插件
* @param bool true为获取完整信息，false为获取标识符列表
* @return array 插件信息
*/
function getPlugins($full = true) {
	$path = SYSTEM_ROOT . '/plugins/';
	$res  = scandir($path);
	$r    = array();
	foreach ($res as $x) {
		if (is_dir($path . $x) && file_exists($path . $x . '/' . $x . '.php')) {
			if ($full) {
				$r[] = getPluginInfo($x);
			} else {
				$r[] = $x;
			}
		}
	}
	return $r;
}

/**
 * 获取插件信息
 * @param string $plugin 插件标识符
 * @return bool|array 无效插件返回false，成功返回 插件名_desc.php 中的信息
 */
function getPluginInfo($plugin) {
	$path = SYSTEM_ROOT . '/plugins/' . $plugin . '/';
	if (!file_exists($path . $plugin . '.php')) {
		return false;
	}
	if (file_exists($path . $plugin . '_desc.php')) {
		$r = include $path . $plugin . '_desc.php';
	} else {
		$d = getOldPluginData($plugin . '/' . $plugin . '.php');
		$r = array(
			'plugin' => array(
				'name'        => $d['Name'],
				'version'     => $d['Version'],
				'description' => $d['Description'],
				'url'         => $d['Url'],
				'for'         => $d['For']
			),
			'view'   => array(
				'setting'     => true,
				'show'        => true,
				'vip'         => true,
				'private'     => true,
				'public'      => true
			),
			'author' => array(
				'author'      => $d['Author'],
				'url'         => $d['AuthorUrl']
			)
		);
	}
	$r['plugin']['id'] = $plugin;
	if (file_exists($path . $plugin . '_setting.php'))
		$r['core']['setting'] = true;
	if (file_exists($path . $plugin . '_show.php'))
		$r['core']['show'] = true;
	if (file_exists($path . $plugin . '_vip.php'))
		$r['core']['vip'] = true;
	if (file_exists($path . $plugin . '_private.php'))
		$r['core']['private'] = true;
	if (file_exists($path . $plugin . '_public.php'))
		$r['core']['public'] = true;
	return $r;
}

/**
 * 获取旧式插件信息
 */
function getOldPluginData($pluginFile) {
	global $i;
    $pluginPath = SYSTEM_ROOT . '/plugins/';
	$pluginData = file_get_contents($pluginPath . $pluginFile);
	preg_match("/Plugin Name:(.*)/i", $pluginData, $plugin_name);
	preg_match("/Version:(.*)/i", $pluginData, $version);
	preg_match("/Plugin URL:(.*)/i", $pluginData, $plugin_url);
	preg_match("/Description:(.*)/i", $pluginData, $description);
	preg_match("/For:(.*)/i", $pluginData, $For);
	preg_match("/Author:(.*)/i", $pluginData, $author_name);
	preg_match("/Author URL:(.*)/i", $pluginData, $author_url);

    $ret = explode('/', $pluginFile);
    $plugin = $ret[0];
    @$setting = (file_exists($pluginPath . $plugin . '/' . $plugin . '_setting.php') && in_array($pluginFile, $i['plugins']['actived'])) ? true : false;

    $plugin_name = isset($plugin_name[1]) ? strip_tags(trim($plugin_name[1])) : '';
	$version = isset($version[1]) ? strip_tags(trim($version[1])) : '';
	$description = isset($description[1]) ? strip_tags(trim($description[1])) : '';
	$plugin_url = isset($plugin_url[1]) ? strip_tags(trim($plugin_url[1])) : '';
	$author = isset($author_name[1]) ?strip_tags( trim($author_name[1])) : '';
	$For = isset($For[1]) ? strip_tags(trim($For[1])) : '';
	$author_url = isset($author_url[1]) ? strip_tags(trim($author_url[1])) : '';

	$For = str_ireplace(array('v',"\r",'+',' '),'',$For);
	if(!is_numeric($For)){
		$For = '不限';
	}

	return array(
		'Name' => $plugin_name,
		'Version' => $version,
		'Description' => $description,
		'Url' => $plugin_url,
		'Author' => $author,
		'For' => $For,
		'AuthorUrl' => $author_url,
        'Setting' => $setting,
        'Plugin' => $plugin,
	);
}

foreach ($i['plugins']['actived'] as $pluginInfo) {
	$i['plugins']['desc'][$pluginInfo] = getPluginInfo($pluginInfo);
}