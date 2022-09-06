<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
loadhead();
global $m;
$uid = UID;
$b = $m->fetch_array($m->query("SELECT count(id) AS `c`FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `uid` = {$uid}"));
if ($b['c'] < 1) {
    echo '<div class="alert alert-warning">您需要先绑定至少一个百度ID才可以使用本功能</div>';
    die;
}
if (isset($_GET['save'])) {
    $czd = isset($_POST['c_zd']) ? $_POST['c_zd'] : '0';
    if (!empty($czd)) {
        option::uset('ver4_zdwk_czd', 1, $uid);
    } else {
        option::uset('ver4_zdwk_czd', 0, $uid);
    }
    redirect('index.php?plugin=ver4_zdwk&success=' . urlencode('您的设置已成功保存'));
}
?>
<h2>知道签到</h2>
<br>
<?php
if (isset($_GET['success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '</div>';
}
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
}
?>
<h4>基本设置</h4>
<br>
<form action="index.php?plugin=ver4_zdwk&save" method="post">
	<table class="table table-hover">
		<tbody>
		<tr>
			<td>
				<b>开启知道签到</b><br>
				开启后每天会对百度知道进行签到
			</td>
			<td>
				<input type="radio" name="c_zd"
				       value="1" <?php echo empty(option::uget('ver4_zdwk_czd', $uid)) ? '' : 'checked' ?>> 开启
				<input type="radio" name="c_zd"
				       value="0" <?php echo empty(option::uget('ver4_zdwk_czd', $uid)) ? 'checked' : '' ?>> 关闭
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
<h4>签到日志</h4>
<br>
<div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
	<?php
    $a = 0;
    $bid = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `uid` = {$uid}");
    ?>
	<ul id="myTabs" class="nav nav-tabs" role="tablist">
		<?php
        while ($x = $m->fetch_array($bid)) {
            ?>
			<li role="presentation" class="<?php echo empty($a) ? 'active' : '' ?>"><a href="#b<?php echo $x['id'] ?>"
			                                                                           role="tab"
			                                                                           data-toggle="tab"><?php echo $x['name'] ?></a>
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
			<div role="tabpanel" class="tab-pane fade <?php echo empty($b) ? 'active in' : '' ?>"
			     id="b<?php echo $r['id'] ?>">
				<table class="table table-striped">
					<thead>
					<tr>
						<td>序号</td>
						<td>结果</td>
						<td>时间</td>
					</tr>
					</thead>
					<tbody>
					<?php
                    $a = 0;
            $lr = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_zdwk_log` WHERE `pid` = {$r['id']} ORDER BY `id` DESC");
            while ($x = $m->fetch_array($lr)) {
                $a++;
                $date = date('Y-m-d H:i:s', $x['date']);
                echo "<tr><td>{$x['id']}</td><td>{$x['result']}</td><td>{$date}</td></tr>";
            }
            if (empty($a)) {
                echo "<tr><td>暂无签到记录</td><td></td><td></td></tr>";
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