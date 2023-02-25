<?php

/*
Plugin Name: AmazeUI
Version: 1.0
Plugin URL: http://v4.hk
Description: 提供AmazeUI风格云签到
Author: Ver4签到联盟
Author Email: i@v4.hk
Author URL: http://v4.hk
For: V3.0+
*/

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}

function ver4_style_ui()
{
    echo '<link rel="stylesheet" href="' . SYSTEM_URL . 'plugins/ver4_style/css/ui.css">';
}

addAction('header', 'ver4_style_ui');
