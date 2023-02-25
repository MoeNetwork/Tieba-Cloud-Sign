<?php

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}

function ref_nav()
{
    echo '<li ';
    if (isset($_GET['plug']) && $_GET['plug'] == 'ver4_ref') {
        echo 'class="active"';
    }
    echo '><a href="index.php?mod=admin:setplug&plug=ver4_ref"><span class="glyphicon glyphicon-check"></span> 刷新列表</a></li>';
}

addAction('navi_2', 'ref_nav');
addAction('navi_8', 'ref_nav');
