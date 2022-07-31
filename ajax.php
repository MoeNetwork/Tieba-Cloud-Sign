<?php
require dirname(__FILE__) . '/init.php';

switch (SYSTEM_PAGE) {
    case 'ajax:status':
        global $m,$i;
        $today = date('d');
        $count = array(
            'userSigned'  => 0,
            'userWaiting' => 0,
            'userError'   => 0,
            'allSigned'   => 0,
            'allWaiting'  => 0,
            'allNo'       => 0,
            'allError'    => 0
        );
        $signUser = $m->query("SELECT `latest`,`status` FROM `" . DB_NAME . "`.`" . DB_PREFIX . TABLE . "` WHERE `uid` = " . UID . " AND `no` = '0'");
        while ($countUser = $m->fetch_array($signUser)) {
            if ($countUser['latest'] == $today) {
                if ($countUser['status'] != '0') {
                    $count['userError']++;
                } else {
                    $count['userSigned']++;
                }
            } else {
                $count['userWaiting']++;
            }
        }
        echo "<br/><b>签到状态：</b>已签到 {$count['userSigned']} 个贴吧，{$count['userError']} 个出错， {$count['userWaiting']} 个贴吧等待签到";
        echo '<br/><b>您的签到数据表：</b>' . DB_PREFIX . TABLE;

        if (ROLE == 'admin') {
            foreach ($i['table'] as $value) {
                $signTab = $m->query("SELECT `latest`,`status`,`no` FROM `" . DB_NAME . "`.`" . DB_PREFIX . $value . "`");
                while ($countTab = $m->fetch_array($signTab)) {
                    if ($countTab['no'] != '0') {
                        $count['allNo']++;
                    } elseif ($countTab['latest'] == $today) {
                        if ($countTab['status'] != '0') {
                            $count['allError']++;
                        } else {
                            $count['allSigned']++;
                        }
                    } else {
                        $count['allWaiting']++;
                    }
                }
            }
            echo "<br/><b>签到状态[总体]：</b>已签到 {$count['allSigned']} 个贴吧，还有 {$count['allWaiting']} 个贴吧等待签到";
            echo "<br/><b>贴吧状态[总体]：</b>有 {$count['allError']} 个贴吧签到出错，{$count['allNo']} 个贴吧已被设定为忽略";
            echo '<br/><b>用户注册/添加用户首选表：</b>' . DB_PREFIX . option::get('freetable');
        }
        break;

    case 'admin:server':
        ?>
        <li class="list-group-item">
            <b>PHP 版本：</b><?php echo PHP_VERSION ?>
            <?php if (ini_get('safe_mode')) {
                echo '线程安全';
            } else {
                echo '非线程安全';
            } ?>
        </li>
        <li class="list-group-item">
            <b>MySQL 版本：</b><?php echo $m->getMysqlVersion() ?>
        </li>
        <?php if (!empty($_SERVER['SERVER_ADDR'])) { ?>
        <li class="list-group-item">
            <b>服务器地址：</b><?php echo $_SERVER['SERVER_ADDR'] ?>
        </li>
        <?php } ?>
        <li class="list-group-item">
            <b>服务器软件：</b><?php echo $_SERVER['SERVER_SOFTWARE'] ?>
        </li>
        <li class="list-group-item">
            <b>服务器系统：</b><?php echo php_uname('a') ?>
        </li>
        <li class="list-group-item">
            <b>程序最大运行时间：</b><?php echo ini_get('max_execution_time') ?>s
        </li>
        <li class="list-group-item">
            <b>POST许可：</b><?php echo ini_get('post_max_size'); ?>
        </li>
        <li class="list-group-item">
            <b>文件上传许可：</b><?php echo ini_get('upload_max_filesize'); ?>
        </li>
        <?php
        break;

    case 'admin:update':
        $c = new wcurl('https://api.github.com/repos/moenetwork/tieba-cloud-sign/commits?per_page=1', ['User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36']);
        $data = json_decode($c->exec(), true);
        if (!isset($data[0]["sha"])) {
            die('<div class="alert alert-danger"><b>检查更新失败：无法获取最新版本信息。</b></div>');
        }
        $c = new wcurl('https://api.github.com/repos/moenetwork/tieba-cloud-sign/commits/' . $data[0]["sha"], ['User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36']);
        $data = json_decode($c->exec(), true);
        if (empty($data)) {
            die('<div class="alert alert-danger"><b>检查更新失败：无法获取最新版本信息。</b></div>');
        } else {
            $tip = "";
            if (isset($_GET['ok'])) {
                $tip .= '<b>更新成功，下面是最新版本信息。</b><hr/>';
            }
            $tip .= '<div class="text-right"> <div class="label label-primary">commit</div> <a href="https://github.com/MoeNetwork/Tieba-Cloud-Sign/commit/' . $data["sha"] . '" target="_blank" class="label label-info">' . substr($data["sha"], 0, 7) . '</a> <div class="label label-warning"><span class="glyphicon glyphicon-time" aria-hidden="true"></span> ' . $data["commit"]["committer"]["date"] . '</div> <div class="label label-' . ($data["commit"]["verification"]["verified"] ? 'success' : 'warning') . '">' . ($data["commit"]["verification"]["verified"] ? 'Verified' : 'Not verified') . '</div></div><br /><div>' . nl2br($data["commit"]["message"]) . '</div>';
            //check update scripts
            foreach ($data["files"] as $file_info) {
                if ($file_info["status"] === "added" && strpos($file_info["filename"], 'setup/update') !== false) {
                    $tip .= '<hr>本提交包含一个升级脚本，更新后请及时执行 <b>' . $file_info["filename"] . '</b>';
                }
            }
            echo '<div class="panel panel-default" style="background-color: #F9F9F9;"><div class="panel-body">' . $tip . '<hr/><div class="text-right"><a href="ajax.php?mod=admin:update:updnow" class="btn btn-primary" type="button" onclick="waitup();">立即更新</a></div></div></div>';
        }
        $c->close();
        break;

    case 'admin:update:updnow':
        if (!file_exists(UPDATE_CACHE)) {
            mkdir(UPDATE_CACHE, 0777, true);
        }
        $commit_mode = isset($_GET["commit"]) && $_GET["commit"];
        //下载zip包
        //switch (option::get('update_server')){
        //  //OSCGIT禁止了直接下载
        //  //CODING仓库都没了
        //  default:
        if ($commit_mode) {
            $c = new wcurl("https://github.com/MoeNetwork/Tieba-Cloud-Sign/archive/{$_GET["commit"]}.zip");
        } else {
            $c = new wcurl(UPDATE_SERVER_GITHUB);
        }
        $floderName = $commit_mode ? 'Tieba-Cloud-Sign-' . $_GET["commit"] : UPDATE_FNAME_GITHUB;
        //      break;
        //}
        $file = $c->exec();
        $c->close();
        $zipPath = UPDATE_CACHE . 'update_' . time() . '.zip';
        if (file_put_contents($zipPath, $file) === false) {
            DeleteFile(UPDATE_CACHE);
            msg('错误 - 更新失败：<br/><br/>无法从更新服务器下载更新包');
        }


        //解压缩
        $z = new zip();
        $z->open($zipPath);
        $z->extract(UPDATE_CACHE);
        $z->close();

        //检查更新文件
        $floderName = UPDATE_CACHE . $floderName;
        if (!is_dir($floderName)) {
            DeleteFile(UPDATE_CACHE);
            msg('错误 - 更新失败：<br/><br/>无法解压缩更新包');
        }
        //删除配置文件
        if (file_exists($floderName . '/config.php')) {
            unlink($floderName . '/config.php');
        }
        if (file_exists($floderName . '/app.conf')) {
            unlink($floderName . '/app.conf');
        }
        if (file_exists($floderName . '/config.yaml')) {
            unlink($floderName . '/config.yaml');
        }
        //覆盖文件
        if (CopyAll($floderName, SYSTEM_ROOT) !== true) {
            DeleteFile(UPDATE_CACHE);
            msg('错误 - 更新失败：<br/><br/>无法更新文件');
        }
        DeleteFile(UPDATE_CACHE);
        ////获取最新的版本号
        //$c = new wcurl('http://kenvix.oschina.io/tieba-cloud-sign/');
        //$data = json_decode($c->exec(),true);
        //$c->close();
        ////修改版本号
        //option::set('vid',$data['vid']);
        //暂不支持更新脚本
        msg('恭喜您，更新成功！', SYSTEM_URL . 'index.php?mod=admin:update&ok');
        break;

    case 'admin:update:changeServer':
        if (isset($_GET['server'])) {
            option::set('update_server', $_GET['server']);
        }
        break;

    case 'baiduid:getverify':
        global $m;
        if (option::get('bduss_num') == '-1' && ROLE != 'admin') {
            msg('本站禁止绑定新账号');
        }
        if (option::get('bduss_num') != '0' && ISVIP == false) {
            $count = $m->once_fetch_array("SELECT COUNT(*) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `uid` = " . UID);
            if (($count['c'] + 1) > option::get('bduss_num')) {
                msg('您当前绑定的账号数已达到管理员设置的上限<br/><br/>您当前已绑定 ' . $count['c'] . ' 个账号，最多只能绑定 ' . option::get('bduss_num') . ' 个账号');
            }
        }
        $name  = !empty($_POST['bd_name']) ? $_POST['bd_name'] :
        die();
        $pw    = !empty($_POST['bd_pw'])   ? $_POST['bd_pw']   : die();
        $vcode = !empty($_POST['vcode'])   ? $_POST['vcode']   : '';
        $vcodestr = !empty($_POST['vcodestr']) ? $_POST['vcodestr'] : '';
        $loginResult = misc::loginBaidu($name, $pw, $vcode, $vcodestr);
    if ($loginResult[0] == -3) {
        echo '{"error":"-3","msg":"请输入验证码","vcodestr":"' . $loginResult[1] . '","img":"' . $loginResult[2] . '"}';
        /*
            echo '<img onclick="addbdid_getcode();" src="'.$loginResult[2].'"style="float:left;">&nbsp;&nbsp;&nbsp;请在下面输入左图中的字符<br>&nbsp;&nbsp;&nbsp;点击图片更换验证码';
            echo '<br/><br/><div class="input-group"><span class="input-group-addon">验证码</span>';
            echo '<input type="text" class="form-control" id="bd_v" name="bd_v" placeholder="请输入上图的字符" required></div><br/>';
            echo '<input type="hidden" id="vcodeStr" name="vcodestr" value="'.$loginResult[1].'"/>';
        */
    } elseif ($loginResult[0] == 0) {
        if ((option::get('same_pid') == '1' || option::get('same_pid') == '2') && !ISADMIN) {
            $checkSame = $m->once_fetch_array("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `name` = '{$loginResult[2]}'");
            if (!empty($checkSame)) {
                if (option::get('same_pid') == '2') {
                    echo '{"error":"-11","msg":"你已经绑定了这个百度账号或者该账号已被其他人绑定，若要重新绑定，请先解绑"}';
                } elseif (option::get('same_pid') == '1' && $checkSame['uid'] == UID) {
                    echo '{"error":"-10","msg":"你已经绑定了这个百度账号，若要重新绑定，请先解绑"}';
                }
                die;
            }
        }
        $m->query("INSERT INTO `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` (`uid`,`bduss`,`name`) VALUES  (" . UID . ", '{$loginResult[1]}', '{$loginResult[2]}')");
        echo '{"error":"0","msg":"获取BDUSS成功","bduss":"' . $loginResult[1] . '","name":"' . $loginResult[2] . '"}';
    } else {
        echo '{"error":"' . $loginResult[0] . '","msg":"' . $loginResult[1] . '"}';
    }
        break;
    case "baiduid:qrlogin":
        global $m;
        if (option::get('bduss_num') == '-1' && ROLE != 'admin') {
            msg('本站禁止绑定新账号');
        }
        if (option::get('bduss_num') != '0' && ISVIP == false) {
            $count = $m->once_fetch_array("SELECT COUNT(*) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `uid` = " . UID);
            if (($count['c'] + 1) > option::get('bduss_num')) {
                msg('您当前绑定的账号数已达到管理员设置的上限<br/><br/>您当前已绑定 ' . $count['c'] . ' 个账号，最多只能绑定 ' . option::get('bduss_num') . ' 个账号');
            }
        }
        $sign = !empty($_POST['sign']) ? $_POST['sign'] :
        die();
        $loginResult = misc::get_real_bduss($sign);
    if ($loginResult["error"] == 0) {
        $baiduUserInfo = getBaiduUserInfo($loginResult["bduss"]);
        if (!empty($baiduUserInfo["portrait"])) {
            $baidu_name = $baiduUserInfo["name"];
            $baidu_name_portrait = sqladds($baiduUserInfo["portrait"]);
            $checkSame = $m->once_fetch_array("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `portrait` = '{$baidu_name_portrait}'");
            if (option::get('same_pid') != '3' && (option::get('same_pid') == '1' || option::get('same_pid') == '2') && !ISADMIN) {
                if (!empty($checkSame)) {
                    if (option::get('same_pid') == '2') {
                        $loginResult["error"] = -11;
                        $loginResult["msg"] = "你已经绑定了这个百度账号或者该账号已被其他人绑定，若要重新绑定，请先解绑";
                    } elseif (option::get('same_pid') == '1' && $checkSame['uid'] == UID) {
                        $loginResult["error"] = -10;
                        $loginResult["msg"] = "你已经绑定了这个百度账号，若要重新绑定，请先解绑";
                    }
                    $loginResult["bduss"] = "";
                } else {
                    $m->query("INSERT INTO `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` (`id`,`uid`,`bduss`,`stoken`,`name`,`portrait`) VALUES  (NULL,'" . UID . "', '{$loginResult["bduss"]}', '{$loginResult["stoken"]}', '{$baidu_name}', '{$baidu_name_portrait}')");
                    $loginResult["msg"] = "获取BDUSS成功";
                    $loginResult["name"] = "{$baidu_name} [{$baidu_name_portrait}]";
                }
            } else {
                if (option::get('same_pid') == '3' && !empty($checkSame)) {
                    $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` SET `bduss`='{$loginResult["bduss"]}', `stoken`='{$loginResult["stoken"]}' WHERE `id` = '{$checkSame["id"]}';");
                    $loginResult["msg"] = "更新BDUSS成功";
                } else {
                    $m->query("INSERT INTO `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` (`id`,`uid`,`bduss`,`stoken`,`name`,`portrait`) VALUES  (NULL,'" . UID . "', '{$loginResult["bduss"]}', '{$loginResult["stoken"]}', '{$baidu_name}', '{$baidu_name_portrait}')");
                    $loginResult["msg"] = "获取BDUSS成功";
                }
                $loginResult["name"] = "{$baidu_name} [{$baidu_name_portrait}]";
            }
        }
    }
        echo json_encode($loginResult, JSON_UNESCAPED_UNICODE);

        break;
    default:
        msg('未定义操作');
        break;
}
?>
