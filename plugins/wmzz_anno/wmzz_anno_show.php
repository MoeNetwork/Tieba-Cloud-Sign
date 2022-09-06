<?php
if (ROLE === 'admin') {
	$doa = empty($_POST['wmzz_anno_doa']) ? array() : $_POST['wmzz_anno_doa'];
	option::set('wmzz_anno_set', htmlspecialchars_decode($_POST['wmzz_anno_set']));
	option::set('wmzz_anno_tpl', htmlspecialchars_decode($_POST['wmzz_anno_tpl']));
	option::set('wmzz_anno_doa', serialize($doa));
	ReDirect('index.php?mod=admin:setplug&plug=wmzz_anno&ok');
}