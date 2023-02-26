<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
global $m;
$uid = UID;
$us = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_userset` WHERE `uid` = {$uid}"));

$globalBanList = ver4_ban_global_ban_list_generate($i, $m);

if (isset($_GET["api"])) {
    $apiReturnArray = [
        "code" => 403,
        "message" => "禁止访问",
        "data" => [],
    ];
    if (($m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `uid` = {$uid}")))["c"] < 1) {
        $apiReturnArray["code"] = -1;
        $apiReturnArray["message"] = "您需要先绑定至少一个百度ID才可以使用本功能";
    } else {
        switch (isset($_GET["m"]) ? $_GET["m"] : "") {
            case "save":
                $con = isset($_POST['ban_c']) ? sqladds($_POST['ban_c']) : '';
                $open = isset($_POST['open']) ? $_POST['open'] : 0;
                if (!empty($open)) {
                    option::uset('ver4_ban_open', 1, $uid);
                } else {
                    option::uset('ver4_ban_open', 0, $uid);
                }
                if (empty($us['uid'])) {
                    $m->query("INSERT INTO `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_userset` (`uid`,`c`) VALUES ({$uid},'{$con}')");
                } else {
                    $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_userset` SET `c` = '{$con}' WHERE `uid` = {$uid}");
                }
                $apiReturnArray["code"] = 200;
                $apiReturnArray["message"] = "保存成功";
                break;
            case "add":
                $pid = isset($_POST['pid']) ? sqladds($_POST['pid']) : '';
                $user = isset($_POST['user']) ? sqladds($_POST['user']) : '';
                $tieba = isset($_POST['tieba']) ? sqladds($_POST['tieba']) : '';

                //判定吧务权限
                if (option::get('ver4_ban_break_check') === '0' && !ver4_is_manager($pid, $tieba)["isManager"]) {
                    $apiReturnArray["message"] = "您不是 {$tieba}吧 的吧务";
                }

                $rts = isset($_POST['rts']) && !empty($_POST['rts']) ? sqladds($_POST['rts']) : date('Y-m-d');
                $rte = isset($_POST['rte']) ? sqladds($_POST['rte']) : '2026-12-31';

                $sy = (int)substr($rts, 0, 4);//取得年份
                $sm = (int)substr($rts, 5, 2);//取得月份
                $sd = (int)substr($rts, 8, 2);//取得日期
                $stime = mktime(0, 0, 0, $sm, $sd, $sy);

                $ey = (int)substr($rte, 0, 4);//取得年份
                $em = (int)substr($rte, 5, 2);//取得月份
                $ed = (int)substr($rte, 8, 2);//取得日期
                $etime = mktime(0, 0, 0, $em, $ed, $ey);

                if (empty($pid) || empty($user) || empty($tieba)) {
                    $apiReturnArray["message"] = "信息不完整，添加失败";
                    break;
                }

                if ($stime > 1988150400 || $etime > 1988150400 || $stime < 0 || $etime < 0) {
                    $apiReturnArray["message"] = "开始或者结束时间格式不正确";
                    break;
                }

                if (date('Y-m-d', $stime) != $rts || date('Y-m-d', $etime) != $rte) {
                    $apiReturnArray["message"] = "开始或者结束时间格式不正确";
                    break;
                }

                if ($stime > $etime) {
                    $apiReturnArray["message"] = "开始时间不能大于结束时间";
                    break;
                }

                global $m;
                $p = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = '{$pid}'"));
                if ($p['uid'] != UID) {
                    $apiReturnArray["message"] = "你不能替他人添加帖子";
                    break;
                }

                $limit = option::get('ver4_ban_limit');
                $t = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` WHERE `uid` = {$uid}"));
                if ($t['c'] >= $limit) {
                    $apiReturnArray["message"] = "站点设置上限添加{$limit}个百度ID";
                    break;
                }
                $ru = explode("\n", $user);
                $notExistList = "";
                $callbackData = [];
                foreach ($ru as $k => $v) {
                    $v = trim(str_replace(["\r", '@'], '', $v));//去除特殊字符串
                    //获取信息
                    $banUserInfo = json_decode((new wcurl("https://tieba.baidu.com/home/get/panel?ie=utf-8&" . (preg_match('/^tb\.1\./', $v) ? "id={$v}" : "un={$v}")))->get(), true);
                    if ($banUserInfo["no"] === 0) {
                        $name = $banUserInfo["data"]["name"];
                        $name_show = $banUserInfo["data"]["name_show"];//昵称仅供标记, 谁都不想在没id的号里面看portrait对吧
                        $portrait = $banUserInfo["data"]["portrait"];
                        $callbackData[] = [
                            "name" => $banUserInfo["data"]["name"],
                            "name_show" => $banUserInfo["data"]["name_show"],
                            "portrait" => $banUserInfo["data"]["portrait"],
                            "tieba_name" => $tieba,
                            "end" => $etime,
                            "exist" => true
                        ];
                        $t = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` WHERE `uid` = {$uid}"));
                        if ($t['c'] < $limit && !empty($v)) {
                            $m->query("INSERT IGNORE INTO `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` (`uid`,`pid`,`name`,`name_show`,`portrait`,`tieba`,`stime`,`etime`,`date`) VALUES ({$uid},'{$pid}','{$name}','{$name_show}','{$portrait}','{$tieba}','{$stime}','{$etime}',0)");// ON DUPLICATE KEY UPDATE `uid`={$uid},`pid`='{$pid}',`name`='{$name}',`name_show`='{$name_show}',`portrait`='{$portrait}',`tieba`='{$tieba}',`stime`='{$stime}',`etime`='{$etime}'//TODO 插入时更新, 以后说不定用得上
                        }
                    } else {
                        $callbackData[] = ["name" => $v, "exist" => false];
                        $notExistList .= ", {$v}";//添加不存在之人//某些神秘人无法取得信息
                    }
                }
                $apiReturnArray["code"] = 200;
                $apiReturnArray["message"] = ($notExistList ? urlencode("部分ID添加成功{$notExistList}未能添加成功") : urlencode('所有ID已添加到封禁列表，如超出限制会自动舍弃，系统稍后会进行封禁~~哇咔咔')) . "。昵称仅供标记，对应用户修改后的昵称并不会实时反馈到本页";
                $apiReturnArray["data"] = $callbackData;
                break;
            case "delete":
                $id = isset($_GET['id']) ? sqladds($_GET['id']) : '';
                if (!empty($id)) {
                    $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` WHERE `id` = '{$id}' AND `uid` = {$uid}");
                    $apiReturnArray["code"] = 200;
                    $apiReturnArray["message"] = "已成功删除该被封禁ID，最迟24小时后该ID不会再被封禁";
                    $apiReturnArray["data"] = ["id" => $id, "uid" => $uid];
                } else {
                    $apiReturnArray["message"] = "ID不合法";
                }
                break;
            case "empty":
                $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` WHERE `uid` = {$uid}");
                $apiReturnArray["code"] = 200;
                $apiReturnArray["message"] = "循环云封禁列表已清空";
                break;
            case "list":
                $apiReturnArray["code"] = 200;
                $apiReturnArray["message"] = "成功";
                $apiReturnArray["data"] = $globalBanList;
                break;
            case "search":
                $apiReturnArray["data"] = ver4_ban_get_userinfo_by_words(isset($_GET["words"]) ? $_GET["words"] : "");
                if (count($apiReturnArray["data"]) === 0) {
                    $apiReturnArray["code"] = 404;
                    $apiReturnArray["message"] = "没有找到此用户";
                } else {
                    $apiReturnArray["code"] = 200;
                    $apiReturnArray["message"] = "成功";
                }
                break;
            case "precheck":
                $apiReturnArray["code"] = 200;
                $apiReturnArray["message"] = "成功";
                $apiReturnArray["data"] = option::get('ver4_ban_break_check') === '1' ? ["isManager" => false, "isBreak" => true] : ver4_is_manager(isset($_GET["pid"]) ? $_GET["pid"] : "", isset($_GET["tieba"]) ? $_GET["tieba"] : "");
                break;
        }
    }
    header("content-type: text/json");
    echo json_encode($apiReturnArray, JSON_UNESCAPED_UNICODE);
    die();
}
loadhead();
$b = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `uid` = {$uid}"));
if ($b['c'] < 1) {
    echo '<div class="alert alert-warning">您需要先绑定至少一个百度ID才可以使用本功能</div>';
    die;
}

?>
<h2>贴吧云封禁</h2>
<br>
<?php
if (isset($_GET['success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '</div>';
}
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
}

if (isset($_GET['save'])) {
    $con = isset($_POST['ban_c']) ? sqladds($_POST['ban_c']) : '';
    $open = isset($_POST['open']) ? $_POST['open'] : 0;
    if (!empty($open)) {
        option::uset('ver4_ban_open', 1, $uid);
    } else {
        option::uset('ver4_ban_open', 0, $uid);
    }
    if (empty($us['uid'])) {
        $m->query("INSERT INTO `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_userset` (`uid`,`c`) VALUES ({$uid},'{$con}')");
    } else {
        $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_userset` SET `c` = '{$con}' WHERE `uid` = {$uid}");
    }
    redirect('index.php?plugin=ver4_ban&success=' . urlencode('您的设置已成功保存'));
}

if (isset($_GET['duser'])) {
    $id = isset($_GET['id']) ? sqladds($_GET['id']) : '';
    if (!empty($id)) {
        global $m;
        $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` WHERE `id` = '{$id}' AND `uid` = {$uid}");
        redirect('index.php?plugin=ver4_ban&success=' . urlencode('已成功删除该被封禁ID，最迟24小时后该ID不会再被封禁！'));
    } else {
        redirect('index.php?plugin=ver4_ban&error=' . urlencode('ID不合法'));
    }
}
if (isset($_GET['dauser'])) {
    global $m;
    $m->query("DELETE FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` WHERE `uid` = {$uid}");
    redirect('index.php?plugin=ver4_ban&success=' . urlencode('循环云封禁列表已成功清空！'));
}
if (isset($_GET['newuser'])) {
    $pid = isset($_POST['pid']) ? sqladds($_POST['pid']) : '';
    $user = isset($_POST['user']) ? sqladds($_POST['user']) : '';
    $tieba = isset($_POST['tieba']) ? sqladds($_POST['tieba']) : '';

    //判定吧务权限
    if (option::get('ver4_ban_break_check') === '0' && !ver4_is_manager($pid, $tieba)["isManager"]) {
        redirect('index.php?plugin=ver4_ban&error=' . urlencode("您不是 {$tieba}吧 的吧务"));
    }

    $rts = isset($_POST['rts']) && !empty($_POST['rts']) ? sqladds($_POST['rts']) : date('Y-m-d');
    $rte = isset($_POST['rte']) ? sqladds($_POST['rte']) : '2026-12-31';

    $sy = (int)substr($rts, 0, 4);//取得年份
    $sm = (int)substr($rts, 5, 2);//取得月份
    $sd = (int)substr($rts, 8, 2);//取得日期
    $stime = mktime(0, 0, 0, $sm, $sd, $sy);

    $ey = (int)substr($rte, 0, 4);//取得年份
    $em = (int)substr($rte, 5, 2);//取得月份
    $ed = (int)substr($rte, 8, 2);//取得日期
    $etime = mktime(0, 0, 0, $em, $ed, $ey);

    if (empty($pid) || empty($user) || empty($tieba)) {
        redirect('index.php?plugin=ver4_ban&error=' . urlencode('信息不完整，添加失败！'));
    }

    if ($stime > 1988150400 || $etime > 1988150400 || $stime < 0 || $etime < 0) {
        redirect('index.php?plugin=ver4_ban&error=' . urlencode('开始或者结束时间格式不正确！'));
    }

    if (date('Y-m-d', $stime) != $rts || date('Y-m-d', $etime) != $rte) {
        redirect('index.php?plugin=ver4_ban&error=' . urlencode('开始或者结束时间格式不正确！'));
    }

    if ($stime > $etime) {
        redirect('index.php?plugin=ver4_ban&error=' . urlencode('开始时间不能大于结束时间！'));
    }

    global $m;
    $p = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = '{$pid}'"));
    if ($p['uid'] != UID) {
        redirect('index.php?plugin=ver4_ban&error=' . urlencode('你不能替他人添加帖子'));
    }

    $limit = option::get('ver4_ban_limit');
    $t = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` WHERE `uid` = {$uid}"));
    if ($t['c'] >= $limit) {
        redirect('index.php?plugin=ver4_ban&error=' . urlencode("站点设置上限添加{$limit}个百度ID"));
    }
    $ru = explode("\n", $user);
    $notExistList = "";
    foreach ($ru as $k => $v) {
        $v = trim(str_replace(["\r", '@'], '', $v));//去除特殊字符串
        //获取信息
        $banUserInfo = json_decode((new wcurl("https://tieba.baidu.com/home/get/panel?ie=utf-8&" . (preg_match('/^tb\.1\./', $v) ? "id={$v}" : "un={$v}")))->get(), true);
        if ($banUserInfo["no"] === 0) {
            $name = $banUserInfo["data"]["name"];
            $name_show = $banUserInfo["data"]["name_show"];//昵称仅供标记, 谁都不想在没id的号里面看portrait对吧
            $portrait = preg_replace('/([^?]+)(\?.*|)/', "$1", $banUserInfo["data"]["portrait"]);
            $t = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` WHERE `uid` = {$uid}"));
            if ($t['c'] < $limit && !empty($v)) {
                $m->query("INSERT IGNORE INTO `" . DB_NAME . "`.`" . DB_PREFIX . "ver4_ban_list` (`uid`,`pid`,`name`,`name_show`,`portrait`,`tieba`,`stime`,`etime`,`date`) VALUES ({$uid},'{$pid}','{$name}','{$name_show}','{$portrait}','{$tieba}','{$stime}','{$etime}',0)");// ON DUPLICATE KEY UPDATE `uid`={$uid},`pid`='{$pid}',`name`='{$name}',`name_show`='{$name_show}',`portrait`='{$portrait}',`tieba`='{$tieba}',`stime`='{$stime}',`etime`='{$etime}'//TODO 插入时更新, 以后说不定用得上
            }
        } else {
            $notExistList .= ", {$v}";//添加不存在之人//某些神秘人无法取得信息
        }
    }
    redirect('index.php?plugin=ver4_ban&success=' . ($notExistList ? urlencode("部分ID添加成功{$notExistList}未能添加成功") : urlencode('所有ID已添加到封禁列表，如超出限制会自动舍弃，系统稍后会进行封禁~~哇咔咔')) . "。昵称仅供标记，对应用户修改后的昵称并不会实时反馈到本页");
}
?>
<h4>基本设置</h4>
<br>
<form action="index.php?plugin=ver4_ban&save" method="post">
    <table class="table table-hover">
        <tbody>
        <tr>
            <td>
                <b>开启云封禁</b><br>
                开启后每天会对列表用户进行封禁处理
            </td>
            <td>
                <input type="radio" name="open"
                       value="1" <?php echo empty(option::uget('ver4_ban_open', $uid)) ? '' : 'checked' ?>> 开启
                <input type="radio" name="open"
                       value="0" <?php echo empty(option::uget('ver4_ban_open', $uid)) ? 'checked' : '' ?>> 关闭
            </td>
        </tr>
        <tr>
            <td>
                <b>封禁提示内容</b><br>
                用户被封禁后消息中心显示的提示内容
            </td>
            <td>
                <input type="text" class="form-control" name="ban_c" value="<?= isset($us['c']) ? $us["c"] : "" ?>"
                       placeholder='请设置用户被封禁提示的内容（留空使用默认"您因为违反吧规，已被吧务封禁，如有疑问请联系吧务"）'>
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
<h4>用户日志</h4>
<br>

<div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
    <ul id="myTabs" class="nav nav-tabs" role="tablist">
        <?php
        foreach ($globalBanList as $order => $list) {
            echo '<li role="presentation" class="' . ($order === array_keys($globalBanList)[0] ? 'active' : '') . '"><a href="#b' . $order . '" role="tab" data-toggle="tab">' . $list['name'] . '</a></li>';
        }
        ?>
    </ul>
    <hr>
    <div id="myTabContent" class="tab-content">
        <?php
        foreach ($globalBanList as $order => $list) {?>
        <div role="tabpanel" class="tab-pane fade <?= ($order === array_keys($globalBanList)[0] ? 'active in' : '') ?>" id="b<?= $order ?>">
            <?php
            if (count($list["list"]) === 0) {
                echo '<div class="text-center">封禁列表为空</div>';
            } else {
                foreach ($list["list"] as $itemOrder => $item) { ?>
                    <div class="panel panel-default" style="background-color: #F9F9F9;">
                        <div class="panel-body">
                            <div class="text-right">
                                <div class="label label-info"><?= $item["id"] ?></div>
                                <div class="label label-warning"><span class="glyphicon glyphicon-time" aria-hidden="true"></span> <?= ($item['date'] == 0 ? "未开始执行" : date('Y-m-d', $item['date'])) ?></div>
                                <a href="http://tieba.baidu.com/f?kw=<?= $item['tieba'] ?>" target="_blank" type="button" class="label label-success"><?= $item['tieba'] ?></a>
                            </div>
                            <div class="media">
                                <div class="media-left">
                                    <img class="thumbnail media-object" src="https://himg.bdimg.com/sys/portrait/item/<?= $item['portrait'] ?>.jpg" alt="header" style="height: 50px; width: 50px">
                                </div>
                                <div class="media-body">
                                    <h4 class="list-group-item-heading"><?php
                                        echo (empty($item["name"]) ? (empty($item["name_show"]) ? $item["portrait"] : $item["name_show"]) : ($item["name"] . (empty($item["name_show"]) ?: ' [ ' . $item["name_show"] . ' ]')));
                                    ?></h4>
                                    <p class="list-group-item-text">
                                        <?= $item['portrait'] ?><br>
                                        <div class="label label-default"><span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> <?= date('Y-m-d', $item['stime']) ?> ~ <?= date('Y-m-d', $item['etime']) ?></div>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <a href="http://tieba.baidu.com/home/main/?ie=utf-8&id=<?= $item['portrait'] ?>" class="btn btn-primary" type="button" target="_blank">个人主页</a>
                                <button class="btn btn-info" type="button" data-toggle="modal" data-target="#LogUser<?= $item['id'] ?>">日志</button>
                                <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#DelUser<?= $item['id'] ?>">删除</button>
                            </div>
                        </div>
                        <div class="modal fade" id="LogUser<?= $item['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <h4 class="modal-title">日志详情</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="input-group">
                                            <?= empty($item['log']) ? '暂无日志' : $item['log'] ?>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->

                        <div class="modal fade" id="DelUser<?= $item['id'] ?>" tabindex="-1" role="dialog"
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
                                        <form action="index.php?plugin=ver4_ban&duser&id=<?= $item['id'] ?>"
                                              method="post">
                                            <div class="input-group">
                                                您确定要删除这个被封禁用户嘛(删除后无法恢复)？
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
                    </div>
                <?php }
            } ?>
        </div>
        <?php } ?>
    </div>
</div>
<span class="btn btn-success" data-toggle="modal" data-target="#AddUser" style="sursor: pointer">添加用户</span>
<span class="btn btn-danger" data-toggle="modal" data-target="#DelUser" style="sursor: pointer">清空列表</span>
<hr>


<div class="modal fade" id="DelUser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span
                        aria-hidden="true">&times;</span><span
                        class="sr-only">Close</span></button>
                <h4 class="modal-title">温馨提示</h4>
            </div>
            <div class="modal-body">
                <form action="index.php?plugin=ver4_ban&dauser" method="post">
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

<div class="modal fade" id="AddUser" tabindex="-1" role="dialog" aria-labelledby="AddUser" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                        class="sr-only">Close</span></button>
                <h4 class="modal-title">添加被封禁用户信息</h4>
            </div>
            <div class="modal-body">
                <form action="index.php?plugin=ver4_ban&newuser" method="post">
                    <div class="input-group">
                        <span class="input-group-addon">请选择对应帐号</span>
                        <select name="pid" required="" class="form-control" id="selectUserPid">
                            <?php
                            foreach ($globalBanList as $order => $list) {
                                echo '<option value="' . $order . '">' . $list["name"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon">开始时间(日期)</span>
                        <input type="date" class="form-control" name="rts" placeholder="日期格式：yyyy-mm-dd,留空默认立即开始">
                    </div>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon">结束时间(日期)</span>
                        <input type="date" class="form-control" name="rte" value="2026-12-31"
                               placeholder="日期格式：yyyy-mm-dd" required>
                    </div>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon">贴吧</span>
                        <input type="text" class="form-control" id="forum_name" name="tieba" placeholder="输入贴吧名（不带末尾吧字）" required>
                    </div>
                    <label for="forum_name" style="display: none;" id="forum_name_label"></label>
                    <br>
                    <!--批量输入-->
                    <div id="legacyBanUserList" style="display: none;">
                        <label for="banUserList" id="labelForBanUserList" style="display: none;">文本已修改，切换到可视化输入将还原列表；直接提交将保存修改</label>
                        <textarea id="banUserList" name="user" class="form-control" rows="10" placeholder="输入待封禁的 用户名 或 Portrait，一行一个；用户名支持某些软件生成的例如：@AAA 格式 (自动清除@)，Portrait仅支持新版portrait，即 tb.1.xxx.xxxxx 格式，粘贴个人页链接会自动处理，贴吧uid请使用可视化编辑器添加"></textarea>
                    </div>
                    <!--可视化输入-->
                    <div id="visualBanUserList">
                      <label for="userList" id="labelForUserList" style="display: none;">点击名称即可移除</label>
                      <div id="userList" style="display: none;"></div>
                      
                      <div class="input-group">
                        <input type="text" class="form-control" placeholder="用户名，贴吧ID，百度uid，昵称" id="banUserSearchInput">
                        <div  class="input-group-btn">
                          <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true" onclick="$('#searchResultList').html('');$('#banUserSearchInput').val('');"></span></button>
                          <button class="btn btn-default" type="button" id="banUserSearch"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                        </div >
                      </div>
                      <hr>
                      <div class="list-group" id="searchResultList"></div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="switchInputMode">批量输入</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="submit" class="btn btn-primary">提交</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    <script>
    let globalBanUserList = <?php echo json_encode($globalBanList, JSON_UNESCAPED_UNICODE) ?>;
    let searchList = [];
    let tmpBanList = {
        portraitList: [],
        infoList: [],
    };
    let form = {
        pid: 0,
        start: "",
        end: "2026-01-01",
        userlist: "",
        fname: ""
    }
    let time = {
        forum_name_time: new Date()
    }
    $('#banUserList').bind('input propertychange', function(){
        if (((tmpBanList.portraitList.length > 0 && $(this).val() === "") || $(this).val() !== "") && $("#labelForBanUserList").css("display") === "none") {
            $('#labelForBanUserList').css("display", "block")
            //$('#switchInputMode').addClass("disabled")
        }

        if($(this).val() !== ""){
          $(this).val(Array.from(new Set($(this).val().split("\n").map(x => {
            x = x.replace(/@|\r/, "")
            let testPortrait = /tb.1.[\w-~]{0,8}.[\w-~]{0,22}/.exec(x)//检测portrait
            if (testPortrait !== null) {
              x = testPortrait[0]
            }
            return x
          }))).join("\n"))
        }
      })
      $('#forum_name, #selectUserPid').bind('input propertychange', function(){
        if($("#forum_name").val() !== "" && $("#forum_name").val() !== form.fname){
            $('#forum_name_label').text("检查权限中")
            window.stop();
            $.get("index.php?plugin=ver4_ban&api&m=precheck&pid=" + $("#selectUserPid").val() + '&tieba=' + $("#forum_name").val(),function(data){
                if (data.data.isManager) {
                    $('#forum_name_label').text("此帐号在" + $('#forum_name').val() + "吧为" + data.data.managerType)
                } else if(data.data.isBreak) {
                    $('#forum_name_label').text("已跳过权限检查")
                } else {
                    $('#forum_name_label').text("此帐号在" + $('#forum_name').val() + "吧没有封禁权限")
                }
                $('#forum_name_label').css("display", "block")
            })
        } else if ($("#forum_name").val() === "") {
            $('#forum_name_label').css("display", "none")
        }
      })
      //$("select.form-control[name='pid']").change(function(){
      //  let domText = ''
      //  globalBanUserList[$("select.form-control[name='pid']").val()].map(x => domText += '<button class="btn btn-danger" type="button" id="' + x.tieba+'_'+btoa(x.portrait) + '">' + (x.name ? x.name : (x.name_show ? x.name_show : x.portrait)) + ' <span class="badge">' + x.tieba + '</span></button> ')
      //  $('#userList').html(domText === '' ? '' : domText + '<hr>')
      //})
      $('#banUserSearch').click(function() {
        $('#searchResultList').html('<p id="searchResultList" class="text-center"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></p>')
        $.get("index.php?plugin=ver4_ban&api&m=search&words=" + $("#banUserSearchInput").val(),function(data){
            let domText = ''
            //console.log(data)
            if (data.code === 200) {
                searchList = data.data
                data.data.map((x, order) => domText += '<div class="list-group-item"><div class="text-right"><div class="label label-info">' + (x.tieba_uid !== "" ? ' 贴吧ID: ' + x.tieba_uid : '') + '</div> <div class="label label-success">' + (x.exact_match ? '   <span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>' : '') + '</div></div><div class="media"><div class="media-left"><img class="thumbnail media-object" src="https://himg.bdimg.com/sys/portrait/item/' + x.portrait + '.jpg" alt="header" style="height: 50px; width: 50px"></div><div class="media-body"><h4 class="list-group-item-heading">' + x.name + (x.show_name !== "" ? ' [ ' + x.show_name + ' ] ' : '') + '</h4><p class="list-group-item-text">' + x.portrait + '</p></div></div><div class="text-right"><button class="btn btn-primary btn-sm ' + (((tmpBanList.portraitList.indexOf(x.portrait) === -1)) ? '" onclick="clickToAdd(' + order + ')" type="button">添加' : 'disabled" type="button">已添加') + '</button></div></div>')
                $('#searchResultList').html(domText)
            } else {
                $('#searchResultList').html('<p class="text-center">' + data.message + '</p>')
            }
        },"json");
      })
      $('#switchInputMode').click(function() {
          if ($('#switchInputMode').text() === '可视化输入') {
            $('#legacyBanUserList').css("display", "none")
            $('#visualBanUserList').css("display", "block")
            $('#switchInputMode').text('批量输入')
          } else {
            //处理数据
            $('#banUserList').val(tmpBanList.portraitList.join("\n"))
            $('#labelForBanUserList').css("display", "none")
            $('#legacyBanUserList').css("display", "block")
            $('#visualBanUserList').css("display", "none")
            $('#switchInputMode').text('可视化输入')
          }
        
      })
      let clickToAdd = (order) => {
        let x = searchList[order]
        if (!$('#banItem_'+btoa(x.portrait)).text()) {
            if ($('#labelForUserList').css("display") === 'none') {
                $('#userList').after("<hr>")
                $('#labelForUserList').css("display", "block")
                $('#userList').css("display", "block")
            }
            tmpBanList.infoList.push(x)
            tmpBanList.portraitList.push(x.portrait)
            $('#userList').append(' <button class="btn btn-default" type="button" id="banItem_' + btoa(x.portrait) + '" onclick="clickToRemove(\'' + btoa(x.portrait) + '\')">' + (x.name ? x.name : (x.name_show ? x.name_show : x.portrait)) + '</button>')
            $('#banUserList').val(tmpBanList.portraitList.join("\n"))
        }
      }
      let clickToRemove = (base64Portrait) => {
        let order = tmpBanList.portraitList.indexOf(atob(base64Portrait))
        if (order > -1) {
            tmpBanList.infoList.splice(order, 1)
            tmpBanList.portraitList.splice(order, 1)
            $('#banItem_' + base64Portrait).remove()
            $('#banUserList').val(tmpBanList.portraitList.join("\n"))
        }
      }
    </script>
</div><!-- /.modal -->