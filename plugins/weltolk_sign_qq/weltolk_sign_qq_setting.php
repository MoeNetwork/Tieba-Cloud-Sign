<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
if (ROLE !== 'admin') {
    msg('权限不足!');
    die;
}

$limit = option::get('weltolk_sign_qq_limit');

switch ($_GET['act']) {
    case 'ok'://成功回显
        echo '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>插件设置成功</div>';
        break;
    case 'store'://保存设置
        option::set('weltolk_sign_qq_limit', $_POST['limit']);
        ReDirect(SYSTEM_URL . 'index.php?mod=admin:setplug&plug=weltolk_sign_qq&act=ok');
        die;
    default:
        break;
}


?>
<h3>qq推送设置</h3><br/>
<form action="index.php?mod=admin:setplug&plug=weltolk_sign_qq&act=store" method="post">
    <table class="table table-striped">
        <thead>
        <tr>
            <th style="width:45%">参数</th>
            <th style="width:55%">值</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>单次计划任务连续推送次数<br/>越小效率越低，但太大也可能导致超时</td>
            <td><input type="number" min="1" step="1" name="limit" value="<?php echo $limit ?>"
                       class="form-control" required></td>
        </tr>
        </tbody>
    </table>

    <button type="submit" class="btn btn-info">保存设置</button>
</form>