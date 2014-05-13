<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

//加载所有激活的插件
foreach (unserialize(option::get('actived_plugins')) as $value) {
	if (file_exists(SYSTEM_ROOT.'/plugins/'.$value.'/'.$value.'.php') && !is_dir(SYSTEM_ROOT.'/plugins/'.$value.'/'.$value.'.php')) {
		include SYSTEM_ROOT.'/plugins/'.$value.'/'.$value.'.php';
	}
}

//加载插件前台页面
if (isset($_GET['plugin'])) {
	$plug=strip_tags($_GET['plugin']);
	if (in_array($_GET['plugin'], unserialize(option::get('actived_plugins')))) {
		if (file_exists(SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_show.php') && !is_dir(SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_show.php')) {
			require_once SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_show.php';
		} else {
			msg('插件前台显示模块不存在或不正确');
		}
	}
}
/**
* 激活插件
*/
function activePlugin($plugin) {
		$active_plugins = unserialize(option::get('actived_plugins'));

		$ret = false;

		if (in_array($plugin, $active_plugins)) {
			$ret = true;
		} else {
			$active_plugins[] = $plugin;
			$active_plugins = serialize($active_plugins);
			option::set('actived_plugins', $active_plugins);
			$ret = true;
		}

		//run init callback functions
		$callback_file = SYSTEM_ROOT."/plugins/$plugin/{$plugin}_callback.php";
		if (true === $ret && file_exists($callback_file)) {
			require_once $callback_file;
			if (function_exists('callback_init')) {
				callback_init();
			}
		}
		return $ret;
}

/**
 * 禁用插件
*/
	function inactivePlugin($plugin) {
		$active_plugins = unserialize(option::get('actived_plugins'));
		if (in_array($plugin, $active_plugins)) {
			$key = array_search($plugin, $active_plugins);
			unset($active_plugins[$key]);
		} else {
			return;
		}
		$active_plugins = serialize($active_plugins);
		option::set('actived_plugins', $active_plugins);

		//run inactive callback functions
		$callback_file = SYSTEM_ROOT."/plugins/$plugin/{$plugin}_callback.php";
		if (file_exists($callback_file)) {
			require_once $callback_file;
			if (function_exists('callback_inactive')) {
				callback_inactive();
			}
		}
	}
/**
* 获取所有插件列表，未定义插件名称的插件将不予获取
* 插件目录：/plugins
* 仅识别 插件目录/插件/插件.php 目录结构的插件
* @return array
*/
	function getPlugins() {
		global $PluginsList;
		if (isset($PluginsList)) {
			return $PluginsList;
		}
		$PluginsList = array();
		$pluginFiles = array();
		$pluginPath = SYSTEM_ROOT . '/plugins';
		$pluginDir = @ dir($pluginPath);
		if ($pluginDir) {
			while(($file = $pluginDir->read()) !== false) {
				if (preg_match('|^\.+$|', $file)) {
					continue;
				}
				if (is_dir($pluginPath . '/' . $file)) {
					$pluginsSubDir = @ dir($pluginPath . '/' . $file);
					if ($pluginsSubDir) {
						while(($subFile = $pluginsSubDir->read()) !== false) {
							if (preg_match('|^\.+$|', $subFile)) {
								continue;
							}
							if ($subFile == $file.'.php') {
								$pluginFiles[] = "$file/$subFile";
							}
						}
					}
				}
			}
		}
		if (!$pluginDir || !$pluginFiles) {
			return $PluginsList;
		}
		sort($pluginFiles);
		foreach ($pluginFiles as $pluginFile) {
			$pluginData = getPluginData($pluginFile);
			if (empty($pluginData['Name'])) {
				continue;
			}
			$PluginsList[$pluginFile] = $pluginData;
		}
		return $PluginsList;
	}
/**
* 卸载插件
*/
function uninstallPlugin($plugin) {
	inactivePlugin($plugin);
	//run remove callback functions
	$callback_file = SYSTEM_ROOT."/plugins/$plugin/{$plugin}_callback.php";
	if (file_exists($callback_file)) {
		require_once $callback_file;
		if (function_exists('callback_remove')) {
			callback_remove();
		}
	}
	DeleteFile(SYSTEM_ROOT.'/plugins/'.$plugin);
}

/**
 * 获取插件信息
 */
function getPluginData($pluginFile) {
        $pluginPath = SYSTEM_ROOT . '/plugins/';
		$pluginData = implode('', file($pluginPath . $pluginFile));
		preg_match("/Plugin Name:(.*)/i", $pluginData, $plugin_name);
		preg_match("/Version:(.*)/i", $pluginData, $version);
		preg_match("/Plugin URL:(.*)/i", $pluginData, $plugin_url);
		preg_match("/Description:(.*)/i", $pluginData, $description);
		preg_match("/For:(.*)/i", $pluginData, $For);
		preg_match("/Author:(.*)/i", $pluginData, $author_name);
		preg_match("/Author URL:(.*)/i", $pluginData, $author_url);

        $active_plugins = unserialize(option::get('actived_plugins'));
        $ret = explode('/', $pluginFile);
        $plugin = $ret[0];
        @$setting = (file_exists($pluginPath . $plugin . '/' . $plugin . '_setting.php') && in_array($pluginFile, $active_plugins)) ? true : false;

        $plugin_name = isset($plugin_name[1]) ? strip_tags(trim($plugin_name[1])) : '';
		$version = isset($version[1]) ? strip_tags(trim($version[1])) : '';
		$description = isset($description[1]) ? strip_tags(trim($description[1])) : '';
		$plugin_url = isset($plugin_url[1]) ? strip_tags(trim($plugin_url[1])) : '';
		$author = isset($author_name[1]) ?strip_tags( trim($author_name[1])) : '';
		$For = isset($For[1]) ? strip_tags(trim($For[1])) : '';
		$author_url = isset($author_url[1]) ? strip_tags(trim($author_url[1])) : '';

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
?>