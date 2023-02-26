<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}?>
<h2>云审查设置</h2>
<br>
<?php
if (isset($_GET['msg'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['msg']) . '</div>';
}
if (isset($_GET['save'])) {
    option::set('ver4_review_limit', $_POST['limit']);
    option::set('ver4_review_time', $_POST['time']);
    redirect('index.php?mod=admin:setplug&plug=ver4_review&msg=' . urlencode('设置已保存成功！'));
}
?>
<br>
<form method="post" action="index.php?mod=admin:setplug&plug=ver4_review&save">
    <div class="input-group">
        <span class="input-group-addon">用户可添加贴吧数量</span>
        <input type="number" class="form-control" name="limit" placeholder="用户可以添加多少个贴吧" value="<?= option::get('ver4_review_limit') ?>" required="">
    </div>
    <br>
    <div class="input-group">
        <span class="input-group-addon">最低扫描时间</span>
        <input type="number" class="form-control" name="time" placeholder="间隔多久扫描一次指定贴吧" value="<?= option::get('ver4_review_time') ?>" required="">
    </div>
    <br>
    <br>
    <input type="submit" class="btn btn-primary" value="保存设置">
</form>