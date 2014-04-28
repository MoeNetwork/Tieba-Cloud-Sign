<?php
require dirname(__FILE__).'/init.php';

switch (SYSTEM_PAGE) {
	case 'ajax:status':
		global $today;
		global $m;
		$count1 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `lastdo` = '".$today."' AND `uid` = ".UID));
		$count1 = $count1[0];
		$count2 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.TABLE."` WHERE `lastdo` != '".$today."' AND `uid` = ".UID));
		$count2 = $count2[0];
		echo "<b>签到状态：</b>已签到 {$count1} 个贴吧，还有 {$count2} 个贴吧等待签到";
		if (ROLE == 'admin') {
		$count1 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `lastdo` = '".$today."' AND `no` != '1'"));
		$count1 = $count1[0];
		$count2 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `lastdo` != '".$today."' AND `no` != '1'"));
		$count2 = $count2[0];
		$count5 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `no` = '1' AND `status` = '0'"));
		$count5 = $count5[0];
		$count6 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `status` != '0' AND `no` != '1'"));
		$count6 = $count6[0];
		$othertable = unserialize(option::get('fb_tables'));
		if (!empty($othertable)) {
			foreach ($othertable as $value) {
				$count3 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.$value."` WHERE `lastdo` = '".$today."' AND `no` != '1'"));
				$count4 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.$value."` WHERE `lastdo` != '".$today."' AND `no` != '1'"));
				$count1 = $count1 + $count3[0];
				$count2 = $count2 + $count4[0];
				$count7 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.$value."` WHERE `no` = '1' AND `status` = '0'"));
				$count8 = $m->fetch_row($m->query("SELECT COUNT(*) FROM `".DB_NAME."`.`".DB_PREFIX.$value."` WHERE `status` != '0' AND `no` != '1'"));
				$count5 = $count5 + $count7[0];
				$count6 = $count6 + $count8[0];
			}
		}


		echo "<br/><br/><b>签到状态[总体]：</b>已签到 {$count1} 个贴吧，还有 {$count2} 个贴吧等待签到";
		echo "<br/><br/><b>贴吧状态[总体]：</b>有 {$count5} 个贴吧签到出错，{$count6} 个贴吧已被设定为忽略";
		}
		break;
}
?>