<?php
require 'init.php';
global $i, $m;
if ($i['user']['role'] != 'admin') {
    die('非管理员无法执行调试');
}

//在下面编写欲调试的代码
echo '贴吧云签到调试模式';
?>