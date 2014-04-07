<?php
define('SYSTEM_DO_NOT_LOGIN', true);
require 'init.php';
require SYSTEM_ROOT.'/sign.functions.php';
set_time_limit(0);
global $m,$today;
if (option::get('cron_isdoing') == 0) {
	option::set('cron_isdoing',1);
	$return = '';
	doAction('cron_1');

	if (option::get('cron_last_do_time') != $today) {
		option::set('cron_last_do_time',$today);
	}

	////////////////

	DoSign('tieba');
	$tcc = 1;

	foreach (unserialize(option::get('fb_tables')) as $value) {
		$return = DoSign($value);
		$tcc++;
	}

	///////////////

	option::set('cron_last_do', (option::get('cron_last_do') + option::get('cron_limit')));
	$count = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `lastdo` != '".$today."'"));
	doAction('cron_2');
	option::set('cron_isdoing',0);
	msg('本次计划任务完毕',false,false);
} else {
	msg('已经有一个计划任务正在运行中，本次运行已被系统阻止',false,false);
}
?>