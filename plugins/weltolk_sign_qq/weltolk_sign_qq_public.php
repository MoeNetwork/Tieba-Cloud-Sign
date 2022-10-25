<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
loadhead();
if (!isset($_REQUEST['username']) || empty($_REQUEST['username']) || !isset($_REQUEST['token']) || empty($_REQUEST['token'])) {
    die('警告：缺少必要参数！请复制邮件中的完整链接在浏览器中打开');
}
global $m;
global $i;

$user = sqladds($_REQUEST['username']);
$token = $_REQUEST['token'];
$userinfo = $m->fetch_array($m->query('select * from `' . DB_NAME . '`.`' . DB_PREFIX . 'users` where `name` = "' . $user . '"'));
if (empty($userinfo['email'])) {
    die('用户不存在');
}
$system_token = md5(md5($userinfo['name'] . $userinfo['id'] . date('Y-m-d')) . md5($userinfo['id']));
if ($token != $system_token) {
    die('链接已失效！请使用当天的最新邮件中附带的链接');
}
if ($i['opt']['core_version'] >= 4.0) {
    $zt = 'latest';
} else {
    $zt = 'lastdo';
}

?>
<h2 align="center">每日签到报告</h2>
<div class="alert alert-active">
    <h3 align="center">【<?php echo date('Y-m-d'); ?>】贴吧云签到结果</h3>
    <p>用户名称：<?php echo $userinfo['name'] ?></p>
    <p>站点地址：<a href="<?php echo SYSTEM_URL ?>"><?php echo SYSTEM_URL ?></a></p>
    <table class="table table-striped">
        <thead>
        <th>百度账号</th>
        <th>最近签到日期</th>
        <th>吧名(点击直达)</th>
        <th>状态</th>
        <th>结果</th>
        </thead>
        <tbody>
        <?php
        $query = $m->query("SELECT * FROM  `" . DB_NAME . "`.`" . DB_PREFIX . "tieba` WHERE `uid`=" . $userinfo['id']);
        $tieba_count = 0;
        while ($tieba = $m->fetch_array($query)) {
            $tieba_count++;
            if ((date("Y-m-") . $tieba[$zt] == date("Y-m-j", strtotime("-1 day"))) || empty($tieba[$zt])) {
                $style_str = 'warning';
                $status_str = '还未签到';
                $status_icon = 'question-sign';
                $msg = '该贴吧尚未签到！';
            } elseif (!empty($tieba['no'])) {
                $style_str = 'info';
                $status_str = '签到忽略';
                $status_icon = 'info-sign';
                $msg = '您设置了忽略此贴吧的签到';
            } elseif ($tieba['status'] == 0) {
                $style_str = 'success';
                $status_str = '签到成功';
                $status_icon = 'ok-sign';
                $msg = '-';
            } else {
                $style_str = 'danger';
                $status_str = '签到失败';
                $status_icon = 'remove-sign';
                $msg = $tieba['last_error'];
            }
            $out_put_str = '<tr class="alert alert-' . $style_str
                . '"><td>' . ($i['user']['baidu'][$tieba['pid']])
                . '</td><td>' . (date("Y-m-") . $tieba[$zt] ? date("Y-m-") . $tieba[$zt] : '-')
                . '</td><td><a href="http://tieba.baidu.com/f?kw='
                . urlencode($tieba['tieba']) . '" target="_blank">'
                . $tieba['tieba'] . '</a></td><td>'
                . '<span class="glyphicon glyphicon-' . $status_icon
                . '"></span> ' . $status_str . '</td><td>'
                . $msg . '</td></tr>';
            echo $out_put_str;
        }
        if (empty($tieba_count)) {
            echo '<tr class="alert alert-info" align="center"><td colspan="4"><span class="glyphicon glyphicon-info-sign">&nbsp;没有关注的贴吧！请添加关注贴吧后在【云签到设置和日志】中刷新贴吧列表！</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<div>
    插件作者：
    <a href="http://www.tbsign.cn" target="_blank">D丶L</a> &
    <a href="http://tb.xueyuanblog.cn" target="_blank">Pisces</a>&
    <a href="https://quericy.me" target="_blank">quericy</a>
    <a href="https://github.com/Weltolk" target="_blank">Weltolk</a>
</div>
<div>
    程序作者：<a href="http://zhizhe8.net" target="_blank">无名智者</a> &
    <a href="http://longtings.com" target="_blank">Mokeyjay</a> &
    <a href="http://fyy.l19l.com/" target="_blank">FYY</a>
</div>
