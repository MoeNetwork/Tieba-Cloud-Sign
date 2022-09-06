<?php
if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
loadhead();

global $m;
$uid = UID;
$now = time();
$all = option::get('ver4_post_all');

$b = $m->fetch_array($m->query("SELECT count(id) AS `c`FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `uid` = {$uid}"));
if ($b['c'] < 1) {
    echo '<div class="alert alert-warning">您需要先绑定至少一个百度ID才可以使用本功能</div>';
    die;
}
?>
<h2>客户端回帖</h2>
<br>
<?php
if (isset($_GET['success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '</div>';
}
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
}
if (isset($_GET['save'])) {
    $usl = isset($_POST['usl']) && is_numeric($_POST['usl']) ? sqladds($_POST['usl']) : 5;
    $bcs = isset($_POST['ban_cs']) ? sqladds($_POST['ban_cs']) : '';
    $bce = isset($_POST['ban_ce']) ? sqladds($_POST['ban_ce']) : '';
    $open = isset($_POST['open']) ? $_POST['open'] : 0;
    $randtime = isset($_POST['randtime']) ? $_POST['randtime'] : 0;

    if (!empty($open)) {
        option::uset('ver4_post_open', 1, $uid);
    } else {
        option::uset('ver4_post_open', 0, $uid);
    }

    if (!empty($randtime)) {
        option::uset('ver4_post_randtime', 1, $uid);
    } else {
        option::uset('ver4_post_randtime', 0, $uid);
    }

    if ($usl > 5 || $usl < 1) {
        $usl = 5;
    }//判断客户端选择范围

    $cc = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_userset` WHERE `uid` = {$uid}"));
    if (empty($cc['uid'])) {
        $m->query("INSERT INTO `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_userset` (`uid`,`cat`,`cs`,`ce`) VALUES ({$uid},'{$usl}','{$bcs}','{$bce}')");
    } else {
        $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_userset` SET `cat` = '{$usl}',`cs` = '{$bcs}',`ce`='{$bce}' WHERE `uid` = {$uid}");
    }
    redirect('index.php?plugin=ver4_post&success=' . urlencode('您的设置已成功保存'));
}
if (isset($_GET['newtiebaurl'])) {
    $pid = isset($_POST['pid']) ? sqladds($_POST['pid']) : '';
    $url = isset($_POST['tiebaurl']) ? sqladds($_POST['tiebaurl']) : '';

    $rts = isset($_POST['rts']) && is_numeric($_POST['rts']) ? sqladds($_POST['rts']) : 0;
    $rte = isset($_POST['rte']) && is_numeric($_POST['rte']) ? sqladds($_POST['rte']) : 24;

    $nqoute = isset($_POST['nqoute']) && is_numeric($_POST['nqoute']) ? sqladds($_POST['nqoute']) : 0;
    $npage = isset($_POST['npage']) && is_numeric($_POST['npage']) ? sqladds($_POST['npage']) : 0;

    $ptime = isset($_POST['time']) && is_numeric($_POST['time']) ? sqladds($_POST['time']) : 1;
    $space = isset($_POST['space']) && is_numeric($_POST['space']) ? sqladds($_POST['space']) : 30;

    global $m;
    $p = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = '{$pid}'"));
    if ($p['uid'] != UID) {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('你不能替他人添加帖子'));
    }

    $up1 = $m->fetch_array($m->query("SELECT SUM(`all`) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `pid` = '{$pid}'"));
    $ln = $all - $up1['c'];

    if ($ptime <= 0) {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('啊哦，您输入参数非法！'));
    }
    if ($ln < $ptime) {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('啊哦，您的剩余可用每日回复次数不足，添加失败！'));
    }
    $s = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_userset` WHERE `uid` = " . UID));
    if ($s['c'] < 1) {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('你必须先完成基本设置'));
    }
    if ($space < 30) {
        $space = 30;
    }


    if ($rts > $rte) {
        $rt = $rts;
        $rts = $rte;
        $rte = $rt;
    }
    if (!empty($url)) {
        $r = getPage(getTid($url));
        if (!empty($r['fid'])) {
            if (!empty($nqoute) && !empty($npage)) {
                $qid = getFloorInfo($r['tid'], $npage, $nqoute);
                if (empty($qid)) {
                    redirect('index.php?plugin=ver4_post&error=' . urlencode('楼层信息错误，没有获取到楼层ID！'));
                } else {
                    $r['pname'] = "【{$nqoute}楼】" . $r['pname'];
                }
            } else {
                $qid = 0;
            }
            $now = time();
            $m->query("INSERT INTO `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` (`uid`,`pid`,`fid`,`tid`,`qid`,`rts`,`rte`,`all`,`space`,`tname`,`pname`) VALUES (" . UID . ",'{$pid}','{$r['fid']}','{$r['tid']}','{$qid}','{$rts}','{$rte}','{$ptime}','{$space}','{$r['tname']}','{$r['pname']}')");
            redirect('index.php?plugin=ver4_post&success=' . urlencode('帖子添加成功啦，静静的等待回复吧~~哇咔咔'));
        } else {
            redirect('index.php?plugin=ver4_post&error=' . urlencode('没有获取到帖子和贴吧信息'));
        }
    } else {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('您输入的URL不合法或者为空'));
    }
}

if (isset($_GET['newtiebaname'])) {
    $pid = isset($_POST['pid']) ? sqladds($_POST['pid']) : '';
    $tname = isset($_POST['tname']) ? sqladds($_POST['tname']) : '';

    $rts = isset($_POST['rts']) && is_numeric($_POST['rts']) ? sqladds($_POST['rts']) : 0;
    $rte = isset($_POST['rte']) && is_numeric($_POST['rte']) ? sqladds($_POST['rte']) : 24;

    $ptime = isset($_POST['time']) && is_numeric($_POST['time']) ? sqladds($_POST['time']) : 1;
    $space = isset($_POST['space']) && is_numeric($_POST['space']) ? sqladds($_POST['space']) : 30;

    global $m;
    $p = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = '{$pid}'"));

    if ($p['uid'] != UID) {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('你不能替他人添加贴吧'));
    }
    if ($ptime <= 0) {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('啊哦，您输入参数非法！'));
    }

    $up2 = $m->fetch_array($m->query("SELECT SUM(`all`) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `pid` = '{$pid}'"));
    $ln = $all - $up2['c'];
    if ($ln < $ptime) {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('啊哦，您的剩余可用每日回复次数不足,添加失败！'));
    }

    $s = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_userset` WHERE `uid` = " . UID));
    if ($s['c'] < 1) {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('你必须先完成基本设置'));
    }

    if ($space < 30) {
        $space = 30;
    }

    if ($rts > $rte) {
        $rt = $rts;
        $rts = $rte;
        $rte = $rt;
    }
    if (!empty($tname)) {
        if (count(getFirstPageTid($tname)) > 0) {
            $now = time();
            $fid = getFid($tname);
            $m->query("INSERT INTO `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` (`uid`,`pid`,`fid`,`tid`,`rts`,`rte`,`all`,`space`,`tname`,`pname`) VALUES (" . UID . ",'{$pid}','{$fid}',0,'{$rts}','{$rte}','{$ptime}','{$space}','{$tname}',0)");
            redirect('index.php?plugin=ver4_post&success=' . urlencode('贴吧添加成功啦，静静的等待回复吧~~哇咔咔'));
        } else {
            redirect('index.php?plugin=ver4_post&error=' . urlencode('没有获取到贴吧信息'));
        }
    } else {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('您输入的吧名不合法或者为空'));
    }
}

if (isset($_GET['newtiebacontent'])) {
    $con = isset($_POST['content']) ? sqladds($_POST['content']) : '';
    $tid = isset($_POST['tid']) && is_numeric($_POST['tid']) ? sqladds($_POST['tid']) : 0;

    global $m;
    if (!empty($tid)) {
        $tieba = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `id` = '{$tid}'"));
        if ($tieba['uid'] != UID) {
            redirect('index.php?plugin=ver4_post&error=' . urlencode('您不可以替他人添加内容'));
        }
    }

    if (!empty($con)) {
        $rc = explode("\n", $con);
        $now = time();
        foreach ($rc as $v) {
            $m->query("INSERT INTO `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_content` (`uid`,`tid`,`content`,`date`) VALUES (" . UID . ",'{$tid}','{$v}',{$now})");
        }
        redirect('index.php?plugin=ver4_post&success=' . urlencode('内容添加成功啦，静静的等待出现在回复吧~~哇咔咔'));
    } else {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('您输入的内容为空'));
    }
}
if (isset($_GET['deltiebaurl'])) {
    global $m;
    $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `uid` = " . UID);
    redirect('index.php?plugin=ver4_post&success=' . urlencode('帖子列表已被清空'));
}
if (isset($_GET['delallcontent'])) {
    global $m;
    $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_content` WHERE `uid` = " . UID);
    redirect('index.php?plugin=ver4_post&success=' . urlencode('内容列表已被清空'));
}
if (isset($_GET['delurl'])) {
    $id = isset($_GET['id']) ? sqladds($_GET['id']) : '';
    if (!empty($id)) {
        global $m;
        $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `id` = '{$id}' AND `uid` = " . UID);
        $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_content` SET `tid` = 0 WHERE `tid` = '{$id}' AND `uid` = " . UID);
        redirect('index.php?plugin=ver4_post&success=' . urlencode('已成功删除帖子地址'));
    } else {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('ID不合法'));
    }
}
if (isset($_GET['delcon'])) {
    $id = isset($_GET['id']) ? sqladds($_GET['id']) : '';
    if (!empty($id)) {
        global $m;
        $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_content` WHERE `id` = '{$id}' AND `uid` = " . UID);
        redirect('index.php?plugin=ver4_post&success=' . urlencode('已成功删除该内容'));
    } else {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('ID不合法'));
    }
}
if (isset($_GET['cturl'])) {
    $id = isset($_GET['id']) && is_numeric($_GET['id']) ? sqladds($_GET['id']) : '';
    $rts = isset($_POST['rts']) && is_numeric($_POST['rts']) ? sqladds($_POST['rts']) : 0;
    $rte = isset($_POST['rte']) && is_numeric($_POST['rte']) ? sqladds($_POST['rte']) : 24;
    $ptime = isset($_POST['time']) && is_numeric($_POST['time']) ? sqladds($_POST['time']) : 1;
    $space = isset($_POST['space']) && is_numeric($_POST['space']) ? sqladds($_POST['space']) : 60;

    if ($space < 60) {
        $space = 60;
    }
    if ($rts > $rte) {
        $rt = $rts;
        $rts = $rte;
        $rte = $rt;
    }

    $x = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `id` = '{$id}'"));
    $xc = $m->fetch_array($m->query("SELECT SUM(`all`) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `id` != '{$id}' AND `pid` = {$x['pid']}"));
    $ln = $all - $xc['c'];
    if ($ptime <= 0) {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('啊哦，您输入参数非法！'));
    }
    if ($ln < $ptime) {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('啊哦，该百度ID剩余可用每日回复次数不足，修改失败！'));
    }
    if ($x['uid'] == UID) {
        $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` SET `rts` = '{$rts}',`rte` = '{$rte}',`space` = '{$space}',`all` = '{$ptime}' WHERE `id` = '{$id}'");
        redirect('index.php?plugin=ver4_post&success=' . urlencode('已成功修改'));
    } else {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('ID不合法'));
    }
}
if (isset($_GET['ctcon'])) {
    $id = isset($_GET['id']) && is_numeric($_GET['id']) ? sqladds($_GET['id']) : '';
    $con = isset($_POST['content']) ? sqladds($_POST['content']) : '';
    $tid = isset($_POST['tid']) && is_numeric($_POST['tid']) ? sqladds($_POST['tid']) : 0;

    global $m;
    $x = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_content` WHERE `id` = '{$id}'"));
    if ($x['uid'] == UID) {
        $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_content` SET `tid` = '{$tid}',`content` = '{$con}' WHERE `id` = '{$id}'");
        redirect('index.php?plugin=ver4_post&success=' . urlencode('已成功修改'));
    } else {
        redirect('index.php?plugin=ver4_post&error=' . urlencode('ID不合法'));
    }
}
?>
<ul class="nav nav-tabs">
	<li role="presentation" <?= isset($_GET['r']) || isset($_GET['n']) ? '' : 'class="active"' ?>><a
			href="index.php?plugin=ver4_post">云回设置</a></li>
	<li role="presentation" <?= isset($_GET['r']) ? 'class="active"' : '' ?>><a
			href="index.php?plugin=ver4_post&r">回帖记录</a>
	</li>
	<li role="presentation" <?= isset($_GET['n']) ? 'class="active"' : '' ?>><a
			href="index.php?plugin=ver4_post&n">使用说明</a>
	</li>
</ul>
<br>
<?php if (isset($_GET['r'])) { ?>
	<h4>当天的回帖记录</h4>
	<b>新添加帖子或者贴吧及次数修改第二天生效</b>
	<br><br>
	<div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
		<?php
        $a = 0;
        $bid = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `uid` = {$uid}");
        ?>
		<ul id="myTabs" class="nav nav-tabs" role="tablist">
			<?php
            while ($x = $m->fetch_array($bid)) {
                ?>
				<li role="presentation" class="<?= empty($a) ? 'active' : '' ?>"><a href="#b<?= $x['id'] ?>" role="tab"
				                                                                    data-toggle="tab"><?= $x['name'] ?></a>
				</li>
				<?php
                $a++;
            }
            ?>
		</ul>
		<div id="myTabContent" class="tab-content">
			<?php
            $b = 0;
            $bid = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `uid` = {$uid}");
            while ($r = $m->fetch_array($bid)) {
                ?>
				<div role="tabpanel" class="tab-pane fade <?= empty($b) ? 'active in' : '' ?>" id="b<?= $r['id'] ?>">
					<table class="table table-striped">
						<thead>
						<tr>
							<td>序号</td>
							<td>贴吧</td>
							<td>帖子</td>
							<td>剩余</td>
							<td>成功</td>
							<td>失败</td>
							<td>日志</td>
						</tr>
						</thead>
						<tbody>
						<?php
                        $a = 0;
                $tt = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `pid` = {$r['id']}");
                while ($r1 = $m->fetch_array($tt)) {
                    $a++; ?>
							<tr>
								<td><?= $r1['id'] ?></td>
								<td><a href="http://tieba.baidu.com/f?kw=<?= $r1['tname'] ?>"
								       target="_blank"><?= $r1['tname'] ?></a>
								</td>
								<td><?= !empty($r1['pname']) ? '<a href="http://tieba.baidu.com/p/' . $r1['tid'] . '?pid=' . $r1['qid'] . '#' . $r1['qid'] . '" target="_blank">' . $r1['pname'] . '</a>' : '随机回复' ?></td>
								<td><?= $r1['remain'] ?></td>
								<td><?= $r1['success'] ?></td>
								<td><?= $r1['error'] ?></td>
								<td>
									<a class="btn btn-info btn-xs" href="javascript:;" data-toggle="modal"
									   data-target="#LogPost<?= $r1['id'] ?>">查看</a>
								</td>
							</tr>
							<div class="modal fade" id="LogPost<?= $r1['id'] ?>" tabindex="-1" role="dialog"
							     aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal"><span
													aria-hidden="true">&times;</span><span
													class="sr-only">Close</span></button>
											<h4 class="modal-title">灌水日志</h4>
										</div>
										<div class="modal-body">
											<div class="input-group">
												<?= empty($r1['log']) ? '暂无日志' : $r1['log'] ?>
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">取消
											</button>
										</div>
									</div><!-- /.modal-content -->
								</div><!-- /.modal-dialog -->
							</div><!-- /.modal -->
							<?php
                }
                if (empty($a)) {
                    echo '<tr><td>暂无帖子</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
                } ?>
						</tbody>
					</table>
				</div>
				<?php
                $b++;
            }
            ?>
		</div>
	</div>
	
	<?php
} elseif (isset($_GET['n'])) {
                ?>
	<h4>温馨提示</h4>
	<br>
	<p>
		1、使用云灌水(客户端回帖)有被全吧封禁的危险！
	</p>
	<p>
		2、尽量在水楼里使用呦，避免影响吧务工作~~
	</p>
	<p>
		3、因使用云灌水导致被全吧本站概不负责
	</p>
	<br>
	<h4>扫帖说明</h4>
	<p>
		1、系统自动识别置顶帖并跳过
	</p>
	<?php
            } else {
                global $m;
                $x = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_userset` WHERE `uid` = " . UID)); ?>
<h4>基本设置</h4>
<br>
<form action="index.php?plugin=ver4_post&save" method="post">
	<table class="table table-hover">
		<tbody>
		<tr>
			<td>
				<b>客户端回帖总开关</b><br>
				设置为关闭则不会执行云灌水
			</td>
			<td>
				<input type="radio" name="open" value="1" <?php echo empty(option::uget('ver4_post_open', $uid)) ? '' : 'checked' ?>> 开启
				<input type="radio" name="open" value="0" <?php echo empty(option::uget('ver4_post_open', $uid)) ? 'checked' : '' ?>> 关闭
			</td>
		</tr>
		<tr>
			<td>
				<b>回帖间隔总开关</b><br>
				开启则回帖间隔会在你设置的基础上随机
			</td>
			<td>
				<input type="radio" name="randtime" value="1" <?php echo empty(option::uget('ver4_post_randtime', $uid)) ? '' : 'checked' ?>> 开启
				<input type="radio" name="randtime" value="0" <?php echo empty(option::uget('ver4_post_randtime', $uid)) ? 'checked' : '' ?>> 关闭
			</td>
		</tr>
		<tr>
			<td>
				<b>客户端类型</b><br>
				你可以自定义回帖来源
			</td>
			<td>
				<select name="usl" class="form-control">
					<option value="0">请选择</option>
					<option value="1"<?= $x['cat'] == 1 ? ' selected' : '' ?>>iPhone</option>
					<option value="2"<?= $x['cat'] == 2 ? ' selected' : '' ?>>Android</option>
					<option value="3"<?= $x['cat'] == 3 ? ' selected' : '' ?>>Windows Phone</option>
					<option value="4"<?= $x['cat'] == 4 ? ' selected' : '' ?>>Windows 8</option>
					<option value="5"<?= $x['cat'] == 5 ? ' selected' : '' ?>>随机选择一种</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<b>回帖内容前后缀</b><br>
				你可以自定义回帖内容的前缀和后缀
			</td>
			<td class="form-inline">
				<input type="text" class="form-control" name="ban_cs" value="<?= $x['cs'] ?>" placeholder="前缀">
				<input type="text" class="form-control" name="ban_ce" value="<?= $x['ce'] ?>" placeholder="后缀">
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" class="btn btn-primary" value="保存设置">
			</td>
			<td></td>
		</tr>
		</tbody>
	</table>
</form>
<br>
<h4>添加要回复的帖子(每个百度ID次数：<?= $all ?>次)</h4>
	<br>
	<p>不添加帖子/贴吧系统会直接跳过呦</p>
	<br>
	<div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
		<?php
        $a = 0;
                $bid = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `uid` = {$uid}"); ?>
		<ul id="myTabs" class="nav nav-tabs" role="tablist">
			<?php
            while ($x = $m->fetch_array($bid)) {
                $cb = $m->fetch_array($m->query("SELECT SUM(`all`) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `pid` = {$x['id']}"));
                $have = $all - $cb['c']; ?>
				<li role="presentation" class="<?= empty($a) ? 'active' : '' ?>">
					<a href="#b<?= $x['id'] ?>" role="tab" data-toggle="tab"><?= $x['name'] . '(' . $have . '次)' ?></a>
				</li>
				<?php
                $a++;
            } ?>
		</ul>
		<div id="myTabContent" class="tab-content">
			<?php
            $b = 0;
                $bid = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `uid` = {$uid}");
                while ($r = $m->fetch_array($bid)) {
                    ?>
				<div role="tabpanel" class="tab-pane fade <?= empty($b) ? 'active in' : '' ?>" id="b<?= $r['id'] ?>">
					<table class="table table-striped">
						<thead>
						<tr>
							<td>ID</td>
							<td>时间</td>
							<td>贴吧</td>
							<td>帖子</td>
							<td>每日(次)</td>
							<td>间隔(s)</td>
							<td>操作</td>
						</tr>
						</thead>
						<tbody>
						<?php
                        $a = 0;
                    $tt = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `pid` = {$r['id']}");
                    while ($r1 = $m->fetch_array($tt)) {
                        $a++; ?>
							<tr>
								<td><?= $r1['id'] ?></td>
								<td><?= $r1['rts'] . '-' . $r1['rte'] ?></td>
								<td><a href="http://tieba.baidu.com/f?kw=<?= $r1['tname'] ?>"
								       target="_blank"><?= $r1['tname'] ?></a>
								</td>
								<td><?= !empty($r1['pname']) ? '<a href="http://tieba.baidu.com/p/' . $r1['tid'] . '?pid=' . $r1['qid'] . '#' . $r1['qid'] . '" target="_blank">' . $r1['pname'] . '</a>' : '随机回复' ?></td>
								<td><?= $r1['all'] ?></td>
								<td><?= $r1['space'] ?></td>
								<td>
									<a href="javascript:;" data-toggle="modal"
									   data-target="#Cturl<?= $r1['id'] ?>">编辑</a>
									<a href="javascript:;" data-toggle="modal" data-target="#DelATieba<?= $r1['id'] ?>">删除</a>
								</td>
							</tr>
							<div class="modal fade" id="Cturl<?= $r1['id'] ?>" tabindex="-1" role="dialog"
							     aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal"><span
													aria-hidden="true">&times;</span><span
													class="sr-only">Close</span></button>
											<h4 class="modal-title">编辑:<?= $r1['tname'] ?></h4>
										</div>
										<form action="index.php?plugin=ver4_post&cturl&id=<?= $r1['id'] ?>"
										      method="post">
											<div class="modal-body">
												<div class="input-group">
													<span class="input-group-addon">开始(时)</span>
													<input type="number" class="form-control" min="0" max="24"
													       name="rts"
													       value="<?= $r1['rts'] ?>" required>
												</div>
												<br>
												<div class="input-group">
													<span class="input-group-addon">结束(时)</span>
													<input type="number" class="form-control" min="0" max="24"
													       name="rte"
													       value="<?= $r1['rte'] ?>" required>
												</div>
												<br>
												<div class="input-group">
													<span class="input-group-addon">间隔(秒)</span>
													<input type="number" class="form-control" min="30" max="99999"
													       name="space"
													       value="<?= $r1['space'] ?>">
												</div>
												<br>
												<div class="input-group">
													<span class="input-group-addon">每天(次)</span>
													<input type="number" class="form-control" min="1" max="99999"
													       name="time"
													       value="<?= $r1['all'] ?>">
												</div>
												<br>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">取消
												</button>
												<button type="submit" class="btn btn-primary">提交</button>
											</div>

										</form>
									</div><!-- /.modal-content -->
								</div><!-- /.modal-dialog -->
							</div><!-- /.modal -->
							<div class="modal fade" id="DelATieba<?= $r1['id'] ?>" tabindex="-1" role="dialog"
							     aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal"><span
													aria-hidden="true">&times;</span><span
													class="sr-only">Close</span></button>
											<h4 class="modal-title">温馨提示</h4>
										</div>
										<div class="modal-body">
											<form action="index.php?plugin=ver4_post&delurl&id=<?= $r1['id'] ?>"
											      method="post">
												<div class="input-group">
													您确定要删除这个帖子/贴吧嘛(删除后无法恢复)？
												</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">取消
											</button>
											<button type="submit" class="btn btn-primary">确定</button>
										</div>
										</form>
									</div><!-- /.modal-content -->
								</div><!-- /.modal-dialog -->
							</div><!-- /.modal -->
							<?php
                    }
                    if (empty($a)) {
                        echo '<tr><td>暂无帖子</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
                    } ?>
						</tbody>
					</table>
				</div>
				<?php
                $b++;
                } ?>
		</div>
	</div>

	<a class="btn btn-default" href="javascript:;" data-toggle="modal" data-target="#AddTieba">添加帖子</a>
	<a class="btn btn-info" href="javascript:;" data-toggle="modal" data-target="#AddTname">添加贴吧</a>
	<a class="btn btn-danger" href="javascript:;" data-toggle="modal" data-target="#DelTieba">清空列表</a>
	<br><br><br>
	<h4>添加回帖内容</h4>
	<br>
	<p>回帖时随机使用其中之一，不添加的话会自动从句子迷获取灌水内容</p>
	<table class="table table-striped">
		<thead>
		<tr>

			<td>序号</td>
			<td>帖子ID</td>
			<td>内容</td>
			<td>操作</td>
		</tr>
		</thead>
		<tbody>
		<?php
        $a = 0;
                $cc = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_content` WHERE `uid` = " . UID);
                while ($r2 = $m->fetch_array($cc)) {
                    $a++; ?>
			<tr>
				<td><?= $r2['id'] ?></td>
				<td><?= empty($r2['tid']) ? '全部' : $r2['tid'] ?></td>
				<td><?= $r2['content'] ?></td>
				<td>
					<a href="javascript:;" data-toggle="modal" data-target="#Ctcon<?= $r2['id'] ?>">编辑</a>
					<a href="javascript:;" data-toggle="modal" data-target="#DelACon<?= $r2['id'] ?>">删除</a>
				</td>
			</tr>
			<div class="modal fade" id="Ctcon<?= $r2['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span
									aria-hidden="true">&times;</span><span
									class="sr-only">Close</span></button>
							<h4 class="modal-title">修改内容(仅限单行)</h4>
						</div>
						<form action="index.php?plugin=ver4_post&ctcon&id=<?= $r2['id'] ?>" method="post">
							<div class="modal-body">
								<div class="input-group">
									<span class="input-group-addon">选择帖子</span>
									<select name="tid" required="" class="form-control">
										<?php
                                        echo '<option value="0">针对全部帖子/贴吧随机</option>';
                    global $m;
                    $b = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `uid` = " . UID);
                    while ($x = $m->fetch_array($b)) {
                        echo '<option ' . ($r2['tid'] == $x['id'] ? 'selected' : '') . ' value="' . $x['id'] . '">' . $x['pname'] . '</option>';
                    } ?>
									</select>
								</div>
								<br>
								<textarea name="content" class="form-control" rows="15"><?= $r2['content'] ?></textarea>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
									<button type="submit" class="btn btn-primary">提交</button>
								</div>
							</div>
						</form>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			<div class="modal fade" id="DelACon<?= $r2['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span
									aria-hidden="true">&times;</span><span
									class="sr-only">Close</span></button>
							<h4 class="modal-title">温馨提示</h4>
						</div>
						<div class="modal-body">
							<form action="index.php?plugin=ver4_post&delcon&id=<?= $r2['id'] ?>" method="post">
								<div class="input-group">
									您确定要删除这个内容嘛(删除后无法恢复)？
								</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">确定</button>
						</div>
						</form>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			<?php
                }
                if (empty($a)) {
                    echo '<tr><td>暂无内容</td><td></td><td></td><td></td><td></td><td></td></tr>';
                } ?>

		</tbody>
	</table>
	<a class="btn btn-default" href="javascript:;" data-toggle="modal" data-target="#AddContent">添加内容</a>
	<a class="btn btn-danger" href="javascript:;" data-toggle="modal" data-target="#DelContent">清空列表</a>
	<br>
	<br>
	<?php
            } ?>
	<?php loadfoot() ?>
	<div class="modal fade" id="AddTieba" tabindex="-1" role="dialog" aria-labelledby="AddTieba" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span
							aria-hidden="true">&times;</span><span
							class="sr-only">Close</span></button>
					<h4 class="modal-title">添加帖子</h4>
				</div>
				<div class="modal-body">
					<form action="index.php?plugin=ver4_post&newtiebaurl" method="post">
						<div class="input-group">
							<span class="input-group-addon">请选择对应账号</span>
							<select name="pid" required="" class="form-control">
								<?php
                                global $m;
                                $b = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `uid` = " . UID);
                                while ($x = $m->fetch_array($b)) {
                                    echo '<option value="' . $x['id'] . '">' . $x['name'] . '</option>';
                                }
                                ?>
							</select>
						</div>
						<br>
						<div class="input-group">
							<span class="input-group-addon">开始(时)</span>
							<input type="number" class="form-control" min="0" max="24" name="rts" value="0" required>
						</div>
						<br>
						<div class="input-group">
							<span class="input-group-addon">结束(时)</span>
							<input type="number" class="form-control" min="0" max="24" name="rte" value="24" required>
						</div>
						<br>
						<div class="input-group">
							<span class="input-group-addon">每天(次)</span>
							<input type="number" class="form-control" min="1" max="99999" name="time"
							       placeholder="设置指定帖子每天回复次数" value="<?= option::get('ver4_post_dt') ?>">
						</div>
						<br>
						<div class="input-group">
							<span class="input-group-addon">间隔(秒)</span>
							<input type="number" class="form-control" min="30" max="99999" name="space"
							       placeholder="默认30秒，留空为默认">
						</div>
						<br>
						<div class="input-group">
							<span class="input-group-addon">帖子地址</span>
							<input type="url" class="form-control" name="tiebaurl" placeholder="填写帖子地址（网址）" required>
						</div>
						<br>
						<div class="input-group">
							<span class="input-group-addon">回复楼层</span>
							<input type="number" class="form-control" name="nqoute" min="0"
							       placeholder="楼中楼（填写回复楼层），无需楼中楼请留空">
						</div>
						<br>
						<div class="input-group">
							<span class="input-group-addon">楼层页数</span>
							<input type="number" class="form-control" name="npage" min="0"
							       placeholder="楼中楼（楼层在第几页），无需楼中楼请留空">
						</div>
						<br>
						<div class="alert alert-success">
							<p>开始、结束：例如设置为5和9，则只会在5点-9点进行回帖</p>
							<p>每天（次）：每天要回复这个帖子几次</p>
							<p>间隔（秒）：这个帖子间隔多久回复一次</p>
							<p>帖子地址：你要回复的帖子的网址(url)</p>
							<p>回复楼层：填写你要回复的楼层，3楼就填数字3</p>
							<p>楼层页数：如上面3楼在帖子哪一页，第1页就填数字1</p>
							<p>注：楼中楼成功率比较低，没有直接回帖高</p>
						</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
					<button type="submit" class="btn btn-primary">提交</button>
				</div>
				</form>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div class="modal fade" id="AddTname" tabindex="-1" role="dialog" aria-labelledby="AddTname" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span
							aria-hidden="true">&times;</span><span
							class="sr-only">Close</span></button>
					<h4 class="modal-title">添加贴吧</h4>
				</div>
				<div class="modal-body">
					<form action="index.php?plugin=ver4_post&newtiebaname" method="post">
						<div class="input-group">
							<span class="input-group-addon">请选择对应账号</span>
							<select name="pid" required="" class="form-control">
								<?php
                                global $m;
                                $b = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `uid` = " . UID);
                                while ($x = $m->fetch_array($b)) {
                                    echo '<option value="' . $x['id'] . '">' . $x['name'] . '</option>';
                                }
                                ?>
							</select>
						</div>
						<br>
						<div class="input-group">
							<span class="input-group-addon">开始(时)</span>
							<input type="number" class="form-control" min="0" max="24" name="rts" value="0" required>
						</div>
						<br>
						<div class="input-group">
							<span class="input-group-addon">结束(时)</span>
							<input type="number" class="form-control" min="0" max="24" name="rte" value="24" required>
						</div>
						<br>
						<div class="input-group">
							<span class="input-group-addon">间隔(秒)</span>
							<input type="number" class="form-control" min="30" max="99999" name="space"
							       placeholder="默认30秒，留空为默认">
						</div>
						<br>
						<div class="input-group">
							<span class="input-group-addon">每天(次)</span>
							<input type="number" class="form-control" min="1" max="99999" name="time"
							       placeholder="设置指定贴吧每天回复次数" value="<?= option::get('ver4_post_dt') ?>">
						</div>
						<br>
						<div class="input-group">
							<span class="input-group-addon">贴吧吧名</span>
							<input type="text" class="form-control" name="tname" required>
						</div>
						<br>
						<div class="alert alert-success">
							<p>开始、结束：例如设置为5和9，则只会在5点-9点进行回帖</p>
							<p>每天（次）：每天要回复这个贴吧中的帖子几次</p>
							<p>间隔（秒）：这个贴吧里的帖子间隔多久回复一次</p>
							<p>贴吧吧名：你要随机回复的贴吧的吧名(不用写最后的吧字)</p>
						</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
					<button type="submit" class="btn btn-primary">提交</button>
				</div>
				</form>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<div class="modal fade" id="DelTieba" tabindex="-1" role="dialog" aria-labelledby="DelTieba" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span
							aria-hidden="true">&times;</span><span
							class="sr-only">Close</span></button>
					<h4 class="modal-title">温馨提示</h4>
				</div>
				<div class="modal-body">
					<form action="index.php?plugin=ver4_post&deltiebaurl" method="post">
						<div class="input-group">
							您确定要清空帖子/贴吧列表嘛(清空后无法恢复)？
						</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
					<button type="submit" class="btn btn-primary">确定</button>
				</div>
				</form>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<div class="modal fade" id="AddContent" tabindex="-1" role="dialog" aria-labelledby="AddContent" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span
							aria-hidden="true">&times;</span><span
							class="sr-only">Close</span></button>
					<h4 class="modal-title">添加内容(每句一行)</h4>
				</div>
				<form action="index.php?plugin=ver4_post&newtiebacontent" method="post">
					<div class="modal-body">
						<div class="input-group">
							<span class="input-group-addon">选择帖子/贴吧</span>
							<select name="tid" required="" class="form-control">
								<option value="0">针对全部帖子/贴吧随机</option>
								<?php
                                global $m;
                                $b = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_post_tieba` WHERE `uid` = " . UID);
                                while ($x = $m->fetch_array($b)) {
                                    echo '<option value="' . $x['id'] . '">' . (empty($x['pname']) ? $x['tname'] : $x['pname']) . '</option>';
                                }
                                ?>
							</select>
						</div>
						<br>
						<textarea name="content" class="form-control" rows="15"></textarea>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">提交</button>
						</div>
					</div>

				</form>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div class="modal fade" id="DelContent" tabindex="-1" role="dialog" aria-labelledby="DelContent" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span
							aria-hidden="true">&times;</span><span
							class="sr-only">Close</span></button>
					<h4 class="modal-title">温馨提示</h4>
				</div>
				<div class="modal-body">
					<form action="index.php?plugin=ver4_post&delallcontent" method="post">
						<div class="input-group">
							您确定要清空内容列表嘛(清空后无法恢复)？
						</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
					<button type="submit" class="btn btn-primary">确定</button>
				</div>
				</form>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->