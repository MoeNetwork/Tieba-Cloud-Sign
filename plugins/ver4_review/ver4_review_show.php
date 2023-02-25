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
    $crv = isset($_POST['c_rv']) ? $_POST['c_rv'] : '0';
    if (!empty($crv)) {
        option::uset('ver4_review_crv', 1, $uid);
    } else {
        option::uset('ver4_review_crv', 0, $uid);
    }
    redirect('index.php?plugin=ver4_review&success=' . urlencode('您的设置已成功保存'));
}
?>
<h2>贴吧云审查</h2>
<br>
<?php
if (isset($_GET['success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '</div>';
}
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
}

if (isset($_GET['dtieba'])) {
    $id = isset($_GET['id']) ? sqladds($_GET['id']) : '';
    if (!empty($id)) {
        global $m;
        $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_review_list` WHERE `id` = '{$id}' AND `uid` = {$uid}");
        redirect('index.php?plugin=ver4_review&success=' . urlencode('已成功删除该贴吧，系统将不再扫描此吧！'));
    } else {
        redirect('index.php?plugin=ver4_review&error=' . urlencode('ID不合法'));
    }
}

if (isset($_GET['datieba'])) {
    global $m;
    $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_review_list` WHERE `uid` = {$uid}");
    redirect('index.php?plugin=ver4_review&success=' . urlencode('云审查列表已成功清空！'));
}
if (isset($_GET['newtieba'])) {
    $pid = isset($_POST['pid']) ? sqladds($_POST['pid']) : '';
    $kw = isset($_POST['kw']) ? sqladds($_POST['kw']) : '';
    $tieba = isset($_POST['tieba']) ? sqladds($_POST['tieba']) : '';
    $space = isset($_POST['space']) && is_numeric($_POST['space']) ? sqladds($_POST['space']) : option::get('ver4_review_time');

    $rc = explode("\n", $kw);
    foreach ($rc as $k => $v) {
        $v = str_replace("\n", '', $v);
        $v = str_replace("\r", '', $v);
        if (empty($v)) {
            unset($rc[$k]);
        }
    }
    $rc = array_values($rc);

    if (empty($pid) || count($rc) < 1 || empty($tieba) || empty($space)) {
        redirect('index.php?plugin=ver4_review&error=' . urlencode('信息不完整，添加失败！'));
    }

    $time = option::get('ver4_review_time');
    if ($space < $time || $space > 86400) {
        redirect('index.php?plugin=ver4_review&error=' . urlencode('间隔设置不正确！'));
    }

    $p = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = '{$pid}'"));
    if ($p['uid'] != UID) {
        redirect('index.php?plugin=ver4_review&error=' . urlencode('你不能替他人添加需要扫描的贴吧'));
    }

    $limit = option::get('ver4_review_limit');
    $t = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_review_list` WHERE `pid` = '{$pid}'"));
    if ($t['c'] >= $limit) {
        redirect('index.php?plugin=ver4_review&error=' . urlencode('啊哦，您只能添加' . $limit . '个扫描吧哦！'));
    }

    $kw = json_encode($rc, JSON_UNESCAPED_UNICODE);
    $kw = str_replace('\r', '', $kw);

    $m->query("INSERT INTO `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_review_list` (`uid`,`pid`,`tname`,`kw`,`space`) VALUES ({$uid},'{$pid}','{$tieba}','{$kw}','{$space}')");
    redirect('index.php?plugin=ver4_review&success=' . urlencode('该贴吧已加入扫描列表，请耐心等待系统扫描吧~~哇咔咔'));
}
if (isset($_GET['ctieba'])) {
    $id = isset($_GET['id']) ? sqladds($_GET['id']) : '';
    $kw = isset($_POST['kw']) ? sqladds($_POST['kw']) : '';
    $space = isset($_POST['space']) && is_numeric($_POST['space']) ? sqladds($_POST['space']) : option::get('ver4_review_time');

    $rc = explode("\n", $kw);
    foreach ($rc as $k => $v) {
        $v = str_replace("\n", '', $v);
        $v = str_replace("\r", '', $v);
        if (empty($v)) {
            unset($rc[$k]);
        }
    }
    $rc = array_values($rc);
    $time = option::get('ver4_review_time');

    if (!is_array($rc) || empty($space)) {
        redirect('index.php?plugin=ver4_review&error=' . urlencode('信息不完整，添加失败！'));
    }
    if ($space < $time || $space > 86400) {
        redirect('index.php?plugin=ver4_review&error=' . urlencode('间隔设置不正确！'));
    }
    $p = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_review_list` WHERE `id` = '{$id}'"));
    if ($p['uid'] != UID) {
        redirect('index.php?plugin=ver4_review&error=' . urlencode('你不能修改他人的贴吧设置'));
    }


    $kw = json_encode($rc, JSON_UNESCAPED_UNICODE);
    $kw = str_replace('\r', '', $kw);

    $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_review_list` SET `space` = '{$space}',`kw` = '{$kw}' WHERE `id` = '{$id}'");
    redirect('index.php?plugin=ver4_review&success=' . urlencode('设置已成功修改~~哇咔咔'));
}
?>
<h4>基本设置</h4>
<br>
<form action="index.php?plugin=ver4_review&save" method="post">
    <table class="table table-hover">
        <tbody>
        <tr>
            <td>
                <b>开启审查功能</b><br>
                开启后会对设置的贴吧的首页内容进行关键字审查
            </td>
            <td>
                <input type="radio" name="c_rv"
                       value="1" <?php echo empty(option::uget('ver4_review_crv', $uid)) ? '' : 'checked' ?>> 开启
                <input type="radio" name="c_rv"
                       value="0" <?php echo empty(option::uget('ver4_review_crv', $uid)) ? 'checked' : '' ?>> 关闭
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
<h4>贴吧扫描日志</h4>
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
                        <td>贴吧名称</td>
                        <td>间隔(秒)</td>
                        <td>上次执行</td>
                        <td>可选操作</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $a = 0;
                    $uu = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_review_list` WHERE `pid` = {$r['id']}");
                    while ($r1 = $m->fetch_array($uu)) {
                        $a++;
                        $b = 0;
                        $con = '';
                        $rk = json_decode($r1['kw'], true);
                        foreach ($rk as $v) {
                            if (!empty($v)) {
                                if (empty($b)) {
                                    $con .= $v;
                                } else {
                                    $con .= "\n" . $v;
                                }
                                $b++;
                            }
                        } ?>
                        <tr>
                            <td><?= $r1['id'] ?></td>
                            <td><a href="http://tieba.baidu.com/f?kw=<?= $r1['tieba'] ?>"
                                   target="_blank"><?= $r1['tname'] ?></a></td>
                            <td><?= $r1['space'] ?></td>
                            <td><?= date('Y-m-d H:i:s', $r1['date']) ?></td>
                            <td>
                                <a href="javascript:;" data-toggle="modal" data-target="#LogUser<?= $r1['id'] ?>">编辑</a>
                                <a href="javascript:;" data-toggle="modal"
                                   data-target="#DelTieba<?= $r1['id'] ?>">删除</a>
                            </td>
                        </tr>
                        <div class="modal fade" id="LogUser<?= $r1['id'] ?>" tabindex="-1" role="dialog"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span
                                                aria-hidden="true">&times;</span><span
                                                class="sr-only">Close</span></button>
                                        <h4 class="modal-title">编辑信息</h4>
                                    </div>
                                    <form action="index.php?plugin=ver4_review&ctieba&id=<?= $r1['id'] ?>"
                                          method="post">
                                        <div class="modal-body">
                                            <div class="input-group">
                                                <span class="input-group-addon">扫描间隔(秒)</span>
                                                <input type="number" class="form-control" name="space" min="<?= option::get('ver4_review_time')?>"
                                                       max="86400" value="<?= $r1['space'] ?>" placeholder="最少<?= option::get('ver4_review_time')?>秒"
                                                       required>
                                            </div>
                                            <br>
                                            <textarea name="kw" class="form-control" rows="10"
                                                      placeholder="请在此处输入审查关键字，一行一个关键字"><?= $con ?></textarea>
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

                        <div class="modal fade" id="DelTieba<?= $r1['id'] ?>" tabindex="-1" role="dialog"
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
                                        <form action="index.php?plugin=ver4_review&dtieba&id=<?= $r1['id'] ?>"
                                              method="post">
                                            <div class="input-group">
                                                您确定要删除这个贴吧嘛(删除后无法恢复)？
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
                        echo '<tr><td>暂无需要扫描的贴吧</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
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

<a class="btn btn-success" href="javascript:;" data-toggle="modal" data-target="#AddTieba">添加贴吧</a>
<a class="btn btn-danger" href="javascript:;" data-toggle="modal" data-target="#DelTieba">清空列表</a>

<div class="modal fade" id="DelTieba" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span
                        aria-hidden="true">&times;</span><span
                        class="sr-only">Close</span></button>
                <h4 class="modal-title">温馨提示</h4>
            </div>
            <div class="modal-body">
                <form action="index.php?plugin=ver4_review&datieba" method="post">
                    <div class="input-group">
                        您确定要清空列表（该执行后无法恢复）？
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

<div class="modal fade" id="AddTieba" tabindex="-1" role="dialog" aria-labelledby="AddTieba" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                        class="sr-only">Close</span></button>
                <h4 class="modal-title">添加扫描贴吧信息</h4>
            </div>
            <div class="modal-body">
                <form action="index.php?plugin=ver4_review&newtieba" method="post">
                    <div class="input-group">
                        <span class="input-group-addon">请选择对应账号</span>
                        <select name="pid" required="" class="form-control">
                            <?php
                            global $m;
                            $b = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `uid` = {$uid}");
                            while ($x = $m->fetch_array($b)) {
                                echo '<option value="' . $x['id'] . '">' . $x['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon">贴吧名称</span>
                        <input type="text" class="form-control" name="tieba" placeholder="输入要扫描的贴吧吧名" required>
                    </div>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon">扫描间隔(秒)</span>
                        <input type="number" class="form-control" name="space" min="<?= option::get('ver4_review_time')?>" max="86400"
                               placeholder="最少<?= option::get('ver4_review_time')?>秒" required>
                    </div>
                    <br>
                    <textarea name="kw" class="form-control" rows="10" placeholder="请在此处输入审查关键字，一行一个关键字"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="submit" class="btn btn-primary">提交</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->