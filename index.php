<?php
require dirname(__FILE__).'/init.php';

if (!isset($_GET['plugin'])) {
	loadhead();
	template('control');
	loadfoot();
}
?>