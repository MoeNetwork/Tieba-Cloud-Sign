<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
/*
Plugin Name: 每日数据库备份qq推送
Version: 1.0
Plugin URL: https://github.com/weltolk/weltolk_backup_qq
Description: 每日数据库备份qq推送，目前支持go-cqhttp的正向WebSocket和HTTP API，基于D丶L的版本重写
Author: Weltolk
Author Email: Null
Author URL: https://github.com/weltolk
For: V3.8+
*/

function weltolk_backup_qq_set_navi3()
{
    ?>
    <li><a href="index.php?mod=admin:setplug&plug=weltolk_backup_qq"><span
                    class="glyphicon glyphicon-ban-circle"></span> 每日数据库备份qq推送管理</a>
    </li>
    <?php
}

//addAction('set_save1', 'weltolk_backup_qq_set');
addAction('navi_3', 'weltolk_backup_qq_set_navi3');
?>
