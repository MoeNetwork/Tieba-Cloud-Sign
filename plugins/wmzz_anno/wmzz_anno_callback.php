<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

function callback_init() {
	global $m;
	$data = '<br/><div class="alert alert-info alert-dismissable" style="width:60%;">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  {$anno}
</div>';

	option::add('wmzz_anno_set');
	option::add('wmzz_anno_tpl',$data);
	option::add('wmzz_anno_doa','a:0:{}');
}

function callback_remove() {
	global $m;
	option::del('wmzz_anno_set');
	option::del('wmzz_anno_tpl');
	option::del('wmzz_anno_doa');
}
?>