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
		echo '<br/><br/><b>用户注册/添加用户首选表：</b>'.DB_PREFIX.option::get('freetable');
		}
		break;


	case 'admin:update': 
		$c  = new wcurl(SUPPORT_URL . 'download.xml');
		$x  = simplexml_load_string($c->exec());
		$c->close();
		$n  = 0;
		$v1 = $x->children()->items; //文件列表

		echo '<input type="hidden" name="updfile" value="'. $x->children()->info->updatefile .'">';

		foreach ($v1->dir as $valu2) {
			echo '<input type="hidden" name="dir[]" value="'.$valu2.'">';
		}

		foreach ($v1->item as $value) {
			$md5  = (string) $v1->item[$n]->attributes();
			$file = (string) $value;
			if (file_exists(SYSTEM_ROOT.'/'.$file)) {
				$mymd5 = md5_file(SYSTEM_ROOT.'/'.$file);
				if ($mymd5 != $md5) {
					echo "- {$file} <input type=\"hidden\" name=\"file[]\" value=\"{$file}\"><br/>";
				}
			} else {
					echo "- {$file} <input type=\"hidden\" name=\"file[]\" value=\"{$file}\"><br/>";
			}
		}

		$c->close();
		break;

	case 'admin:update:updnow':
		if (!is_dir(SYSTEM_ROOT.'/setup/update_cache')) {
			mkdir(SYSTEM_ROOT.'/setup/update_cache');
		}
		foreach ($_POST['dir'] as $valu2) {
			if (!is_dir(SYSTEM_ROOT.'/update_cache/'.$valu2)) {
				mkdir(SYSTEM_ROOT.'/setup/update_cache/'.$valu2);
			}
		}
		foreach ($_POST['file'] as $value) {
			$c = new wcurl(SUPPORT_URL . 'download.php?file='.$value);
			file_put_contents(SYSTEM_ROOT.'/setup/update_cache/'.$value, $c->exec());
			$c->close();
		}
		ReDirect('ajax.php?mod=admin:update:install&updfile=' . $_POST['updfile']);
		break;

	case 'admin:update:install':
		CopyAll(SYSTEM_ROOT.'/setup/update_cache',SYSTEM_ROOT);
		DeleteFile(SYSTEM_ROOT.'/setup/update_cache');
		if (!empty($_GET['updfile'])) {
			ReDirect(SYSTEM_URL . $_GET['updfile']);
		} else {
			msg('站点升级完毕', SYSTEM_URL);
		}
		break;
}
?>