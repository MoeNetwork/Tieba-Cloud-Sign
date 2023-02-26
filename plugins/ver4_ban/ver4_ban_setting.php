<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
} ?>
<h2>云封禁设置</h2>
<br>
<?php
if (isset($_GET['msg'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['msg']) . '</div>';
}
if (isset($_GET['save'])) {
    option::set('ver4_ban_limit', $_POST['limit']);
    option::set('ver4_ban_break_check', $_POST['break_check'] === 'on' ? '1' : '0');
    redirect('index.php?mod=admin:setplug&plug=ver4_ban&msg=' . urlencode('设置已保存成功！'));
}
?>
<br>
<form method="post" action="index.php?mod=admin:setplug&plug=ver4_ban&save">
<div class="input-group">
        <span class="input-group-addon">添加限制（个）</span>
        <input type="text" class="form-control" name="limit" placeholder="此处填写用户最多可以添加多少条封禁" value="<?= option::get('ver4_ban_limit') ?>" required>
    </div>
    <br>
    <label for="break-check">允许跳过权限检查</label>   <input type="checkbox" id="break-check" name="break_check" <?= option::get('ver4_ban_break_check') === '1' ? 'checked' : '' ?>>
    <br>
    <br>
    <input type="submit" class="btn btn-primary" value="保存设置">
</form>