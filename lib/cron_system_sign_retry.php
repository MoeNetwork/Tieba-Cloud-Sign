<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

/**
 * 云签到内部计划任务
 * [重新尝试签到出错的贴吧]
 */

function cron_system_sign_retry() {
	global $i;

	$today = date('Y-m-d');

	$sign_again = unserialize(option::get('cron_sign_again'));
	if ($sign_again['lastdo'] != $today) {
		option::set('cron_sign_again',serialize(array('num' => 0, 'lastdo' => $today)));
	}

	foreach ($i['table'] as $value) {
		misc::DoSign_retry($value);
	}
}
