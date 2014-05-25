<?php
define('SYSTEM_DO_NOT_LOGIN', true);
require dirname(__FILE__).'/init.php';
error_reporting(0);
require SYSTEM_ROOT.'/sign.functions.php';
set_time_limit(0);
$sign_multith = option::get('sign_multith');
if (!isset($_GET['donnot_sign_multith']) && empty($sign_multith) && function_exists('fsockopen')) {
	for ($i=0; $i < $sign_multith; $i++) { 
		XFSockOpen(SYSTEM_URL.'do.php?donnot_sign_multith',0,'','',false,'',0);
	}
}
global $m,$today;
	$return = '';
	doAction('cron_1');

	if (option::get('cron_last_do_time') != $today) {
		option::set('cron_last_do_time',$today);
		option::set('cron_last_do','0');
	}

	$sign_again = unserialize(option::get('cron_sign_again'));
	if ($sign_again['lastdo'] != $today) {
		option::set('cron_sign_again',serialize(array('num' => 0, 'lastdo' => $today)));
	}
	/////////////// RUN ALL TASK IN THE CRON TABLE
	if (option::get('cron_order') == '1') {
		cron::runall();
	}
	/////////////// RUN ALL SIGN TASK

	$sign_mode = unserialize(option::get('sign_mode'));

	if (option::get('cron_order') != '2') {
		$time = time();
		DoSign('tieba',$sign_mode);
		$tcc = 1;

		$fb_tables = unserialize(option::get('fb_tables'));
		if (!empty($fb_tables)) {
			foreach ($fb_tables as $value) {
				$return = DoSign($value,$sign_mode);
				$tcc++;
			}
		}

		$sign_again_num = empty($sign_again['num']) ? 1 : $sign_again['num'] + 1;
		option::set('cron_sign_again',serialize(array('num' => $sign_again_num, 'lastdo' => $today)));
	}
	doAction('cron_3');

	/////////////// RUN ALL TASK IN THE CRON TABLE
	if (option::get('cron_order') == '0') {
		cron::runall();
	}

	doAction('cron_2');
	/////////////// EXIT
	msg('本次计划任务完毕',false,false);
?>