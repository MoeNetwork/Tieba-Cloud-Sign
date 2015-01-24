<?php
require dirname(__FILE__).'/init.php';

if (!isset($_GET['plugin']) && !isset($_GET['pub_plugin']) && !isset($_GET['vip_plugin']) && !isset($_GET['pri_plugin'])) {
	loadhead();
	template('control');
	loadfoot();
}