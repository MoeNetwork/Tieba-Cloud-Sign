<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
global $m;
if (isset($_GET['msg'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['msg']) . '</div>';
}
if (isset($_GET['id'])) {
    $id = isset($_GET['id']) ? sqladds($_GET['id']) : 0;
    $r = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = '{$id}'"));
    if (!empty($r['bduss'])) {
        misc::scanTiebaByPid($id);
        redirect('index.php?mod=admin:setplug&plug=ver4_ref&msg=' . urlencode('已成功刷新该用户贴吧列表！'));
    } else {
        redirect('index.php?mod=admin:setplug&plug=ver4_ref&msg=' . urlencode('非法操作！'));
    }
}
?>
<h2>手动刷新贴吧列表</h2>
<br>
<br>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>百度ID</th>
            <th>贴吧数</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    <?php

    $wr = $m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid`");
    while ($x = $m->fetch_array($wr)) {
        ?>
        <tr>
            <td><?= $x['id'] ?></td>
            <td><?= $x['name'] ?></td>
            <td>
                <?php
                $wt = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "tieba` WHERE `pid` = {$x['id']}"));
                echo $wt['c']; ?>
            </td>
            <td><a href="index.php?mod=admin:setplug&plug=ver4_ref&id=<?= $x['id'] ?>">刷新</a></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
