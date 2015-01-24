<?php
define('SYSTEM_DO_NOT_LOGIN', true);
require dirname(__FILE__).'/init.php';
global $m,$today,$i;
set_time_limit(0);
$cron_pw = option::get('cron_pw');
$cmd_pw = function_exists('getopt') ? getopt("p:") : false;
if (!empty($cron_pw)) {
	if ((empty($_REQUEST['pw']) || $_REQUEST['pw'] != $cron_pw) && ($cmd_pw === false || $cmd_pw['p'] != $cron_pw)) {
		msg('计划任务执行失败：密码错误<br/><br/>你需要通过访问 <b>do.php?pw=密码</b> 才能执行计划任务',false);
	}
}
$sign_multith = option::get('sign_multith');
if (!isset($_GET['donnot_sign_multith']) && !empty($sign_multith) && function_exists('fsockopen')) {
	for ($ii=0; $ii < $sign_multith; $ii++) { 
		XFSockOpen(SYSTEM_URL.'do.php?donnot_sign_multith',0,'','',false,'',0);
	}
}
	$return = '';
	doAction('cron_1');

	if (option::get('cron_last_do_time') != $today) {
		option::set('cron_last_do_time',$today);
		option::set('cron_last_do','0');
	}
	
	cron::runall();

	doAction('cron_2');
	/////////////// EXIT
	msg('本次计划任务完成',false,false);
?>