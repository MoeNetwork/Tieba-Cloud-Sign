<?php
define('SYSTEM_DO_NOT_LOGIN', true);
require dirname(__FILE__).'/init.php';
error_reporting(0);
require SYSTEM_ROOT.'/sign.functions.php';
set_time_limit(0);
global $m,$today;
	$return = '';
	doAction('cron_1');

	if (option::get('cron_last_do_time') != $today) {
		option::set('cron_last_do_time',$today);
		option::set('cron_last_do','0');
	}

	///////////////
	$time = time();
	DoSign('tieba');
	$tcc = 1;

	foreach (unserialize(option::get('fb_tables')) as $value) {
		$return = DoSign($value);
		$tcc++;
	}

	/////////////// RUN ALL TASK IN THE CRON TABLE

	$cron = $m->query("SELECT *  FROM `".DB_NAME."`.`".DB_PREFIX."cron`");
	while ($cs = $m->fetch_array($cron)) {
		if ($cs['no'] != '1') {
			if ($cs['freq'] == '-1') {
				RunCron($cs['file'],$cs['name']);
				$m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."cron` WHERE `".DB_PREFIX."cron`.`id` = ".$cs['id']);
			}
			elseif (empty($cs['freq'])) {
				$return=RunCron($cs['file'],$cs['name']);
				$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX."cron` SET `lastdo` =  '{$time}',`log` = '{$return}' WHERE `".DB_PREFIX."cron`.`id` = ".$cs['id']);
			}
			elseif ($cs['lastdo'] - $cs['freq'] >= $cs['freq']) {
				$return=RunCron($cs['file'],$cs['name']);
				$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX."cron` SET `lastdo` =  '{$time}',`log` = '{$return}' WHERE `".DB_PREFIX."cron`.`id` = ".$cs['id']);
			}
		}
	}	

	/////////////// RUN ALL SIGN TASK

	$count = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `lastdo` != '".$today."'"));
	doAction('cron_2');

	/////////////// EXIT
	msg('本次计划任务完毕',false,false);
?>