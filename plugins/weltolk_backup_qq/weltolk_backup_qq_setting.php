<?php if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
if (ROLE !== 'admin') {
    msg('权限不足!');
    die;
}

$limit = option::get('weltolk_backup_qq_limit');
$enable = option::get('weltolk_backup_qq_enable');
$log = option::get('weltolk_backup_qq_log');

global $i, $m;
$page = isset($_GET['page']) ? $_GET['page'] : '';
if ($page == 'user_settings') {
    $act = $_GET['act'];

    //保存用户设置
    if ($act == 'store') {
        ob_end_clean();
        $data = json_decode($_POST['info'], true);
        $cache_weltolk_backup_qq_enable = "";
        $cache_weltolk_backup_qq_limit = "";
        if (!empty($data['weltolk_backup_qq_enable'])) {
            $cache_weltolk_backup_qq_enable = addslashes(strip_tags($data['weltolk_backup_qq_enable']));
        }
        if (!empty($data['weltolk_backup_qq_limit'])) {
            $cache_weltolk_backup_qq_limit = addslashes(strip_tags($data['weltolk_backup_qq_limit']));
        }
        if (
            empty($cache_weltolk_backup_qq_enable
            || empty($cache_weltolk_backup_qq_limit))
        ) {
            $return_arr = array('code' => 0, 'msg' => '无效请求!');
        } else {
            option::set('weltolk_backup_qq_enable', $cache_weltolk_backup_qq_enable);
            option::set('weltolk_backup_qq_limit', $cache_weltolk_backup_qq_limit);
            $return_arr = array('code' => 1, 'msg' => '保存成功!');
        }
        echo json_encode($return_arr);
        die;
    }
}
if ($page == 'config') {
    switch ($_GET['act']) {
        case 'ok'://成功回显
            echo '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>插件设置成功</div>';
            break;
        case 'addconnect'://添加推送地址
            $client = !empty($_POST['client']) ? addslashes(strip_tags($_POST['client'])) : msg('请输入客户端');
            $connect_type = !empty($_POST['connect_type']) ? addslashes(strip_tags($_POST['connect_type'])) : msg('请输入连接方式');
            $address = !empty($_POST['address']) ? addslashes(strip_tags($_POST['address'])) : msg('请输入连接地址');
            $address = explode("\n", trim($address));
            $access_token = addslashes(strip_tags($_POST['access_token']));
            $anchor = addslashes(strip_tags($_POST['anchor']));
            foreach ($address as $address_i) {
                $address_i = trim($address_i);
                if (!empty($address_i)) {
                    $exists = $m->fetch_array($m->query(
                        "SELECT * FROM `" . DB_PREFIX . "weltolk_backup_qq_connect`"
                        . " WHERE `uid` = '" . UID . "' AND `client` = '{$client}' AND `connect_type` = '{$connect_type}'"
                        . " AND `address` = '{$address_i}' AND `access_token` = '{$access_token}' LIMIT 1"
                    ));
                    if (is_null($exists)) {
                        $m->query("INSERT INTO `" . DB_PREFIX . "weltolk_backup_qq_connect` (`uid`, `client`, `connect_type`, `address`, `access_token` )"
                            . " VALUES ('" . UID . "', '{$client}', '{$connect_type}', '{$address_i}', '{$access_token}')");
                    }
                }
            }
            ReDirect(SYSTEM_URL . 'index.php?mod=admin:setplug&plug=weltolk_backup_qq&act=ok' . '#' . $anchor);
            break;
        case 'batchedit'://批量/搜索 编辑推送地址
            $anchor = addslashes(strip_tags($_POST['anchor']));
            $client = !empty($_POST['client']) ? intval($_POST['client']) : "";
            $client2 = !empty($_POST['client2']) ? intval($_POST['client2']) : "";
            if ((empty($client) && !empty($client2)) || (!empty($client) && empty($client2))) {
                msg('请正确输入qq客户端');
            }
            $connect_type = !empty($_POST['connect_type']) ? addslashes(strip_tags($_POST['connect_type'])) : "";
            $connect_type2 = !empty($_POST['connect_type2']) ? addslashes(strip_tags($_POST['connect_type2'])) : "";
            if ((empty($connect_type) && !empty($connect_type2)) || (!empty($connect_type) && empty($connect_type2))) {
                msg('请正确选择连接方式');
            }
            $address = !empty($_POST['address']) ? addslashes(strip_tags($_POST['address'])) : "";
            $address2 = !empty($_POST['address2']) ? addslashes(strip_tags($_POST['address2'])) : "";
            if ((empty($address) && !empty($address2)) || (!empty($address) && empty($address2))) {
                msg('请正确输入连接地址');
            }
            $access_token = !empty($_POST['access_token']) ? addslashes(strip_tags($_POST['access_token'])) : "";
            $access_token2 = !empty($_POST['access_token2']) ? addslashes(strip_tags($_POST['access_token2'])) : "";
            if ((empty($access_token) && !empty($access_token2))) {
                msg('请正确输入access_token');
            }

            $status = false;
            $sql1 = "UPDATE `" . DB_PREFIX . "weltolk_backup_qq_connect` SET ";
            $sql2 = " WHERE ";
            if (!empty($client) && !empty($client2)) {
                if ($status) {
                    $sql1 .= ", `client` = '{$client2}'";
                    $sql2 .= " AND `client` = '{$client}'";
                } else {
                    $sql1 .= "`client` = '{$client2}'";
                    $sql2 .= "`client` = '{$client}'";
                }
                $status = true;
            }
            if (!empty($connect_type) && !empty($connect_type2)) {
                if ($status) {
                    $sql1 .= ", `connect_type` = '{$connect_type2}'";
                    $sql2 .= " AND `connect_type` = '{$connect_type}'";
                } else {
                    $sql1 .= "`connect_type` = '{$connect_type2}'";
                    $sql2 .= "`connect_type` = '{$connect_type}'";
                }
                $status = true;
            }
            if (!empty($address) && !empty($address2)) {
                if ($status) {
                    $sql1 .= ", `address` = '{$address2}'";
                    $sql2 .= " AND `address` = '{$address}'";
                } else {
                    $sql1 .= "`address` = '{$address2}'";
                    $sql2 .= "`address` = '{$address}'";
                }
                $status = true;
            }
            if (!empty($access_token)) {
                if ($status) {
                    $sql1 .= ", `access_token` = '{$access_token2}'";
                    $sql2 .= " AND `access_token` = '{$access_token}'";
                } else {
                    $sql1 .= "`access_token` = '{$access_token2}'";
                    $sql2 .= "`access_token` = '{$access_token}'";
                }
                $status = true;
            }

            $sql = $sql1 . $sql2;
            if ($status) {
                $m->query($sql);
            }
            ReDirect(SYSTEM_URL . 'index.php?mod=admin:setplug&plug=weltolk_backup_qq&ok' . '#' . $anchor);
            break;
        case 'update'://编辑单条推送地址
            $id = !empty($_POST['id']) ? addslashes(strip_tags($_POST['id'])) : msg('请输入ID');
            $client = !empty($_POST['client']) ? addslashes(strip_tags($_POST['client'])) : msg('请输入客户端');
            $connect_type = !empty($_POST['connect_type']) ? addslashes(strip_tags($_POST['connect_type'])) : msg('请输入连接方式');
            $address = !empty($_POST['address']) ? addslashes(strip_tags($_POST['address'])) : msg('请输入连接地址');
            $access_token = addslashes(strip_tags($_POST['access_token']));
            $anchor = addslashes(strip_tags($_POST['anchor']));
            $m->query("UPDATE `" . DB_PREFIX . "weltolk_backup_qq_connect` "
                . "SET `client` = '{$client}', `connect_type` = '{$connect_type}',"
                . " `address` = '{$address}', `access_token` = '{$access_token}'"
                . " WHERE `id` = '{$id}'");
            ReDirect(SYSTEM_URL . 'index.php?mod=admin:setplug&plug=weltolk_backup_qq&ok' . '#' . $anchor);
            break;
        case 'del'://删除推送地址
            $id = isset($_GET['id']) ? intval($_GET['id']) : msg('缺少ID');
            $anchor = addslashes(strip_tags($_GET['anchor']));
            $m->query("DELETE FROM `" . DB_PREFIX . "weltolk_backup_qq_connect` WHERE `uid` = " . UID . " AND `id` = " . $id);
            ReDirect(SYSTEM_URL . 'index.php?mod=admin:setplug&plug=weltolk_backup_qq&act=ok' . '#' . $anchor);
            break;
        default:
            break;
    }
} elseif ($page == 'list') {
    switch ($_GET['act']) {
        case 'ok'://成功回显
            echo '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>插件设置成功</div>';
            break;
        case 'debug'://测试qq推送回显
            ob_end_clean();
            $data = json_decode($_POST['info'], true);
            $cache_connect_id = "";
            if (!empty($data['connect_id'])) {
                $cache_connect_id = addslashes(strip_tags($data['connect_id']));
            }
            $cache_type = "";
            if (!empty($data['type'])) {
                $cache_type = addslashes(strip_tags($data['type']));
            }
            $cache_type_id = "";
            if (!empty($data['type_id'])) {
                $cache_type_id = addslashes(strip_tags($data['type_id']));
            }
            $cache_path = "";
            if (!empty($data['path'])) {
                $cache_path = addslashes(strip_tags($data['path']));
            }
            if (
                empty($cache_connect_id)
                || empty($cache_type)
                || empty($cache_type_id)
                || empty($cache_path)
            ) {
                $return_arr = array('code' => 0, 'msg' => '无效请求!');
            } else {
                require_once "weltolk_backup_qq_websocket.php";
                global $m;

                $y2 = $m->query("SELECT * FROM `" . DB_PREFIX . "weltolk_backup_qq_connect` WHERE `id` = '{$cache_connect_id}' LIMIT 1");
                $x2 = $m->fetch_array($y2);
                $sign = "sign" . mt_rand(1000, 9999);

                $date_cache = date('Y-m-d');

                $file_name = "test.sql";
                $msg1 = $date_cache
                    . "\n\n" . "Tieba-Cloud-Sign插件weltolk_backup_qq测试消息"
                    . "\n\n" . "文件: " . $file_name;
                $cache_status = true;

                $msg_dict = [];

                if ($x2['client'] == 'go-cqhttp') {
                    $access_token = $x2['access_token'];

                    if ($x2['connect_type'] == '正向WebSocket') {
                        $headers = [];
                        if (empty($access_token)) {
                        } else {
                            $headers = ["Authorization: Bearer " . $access_token];
                        }

                        $msg_dict["text"] = [
                            "action" => "send_msg",
                            "params" => [

                            ],
                            "echo" => $sign,
                        ];

                        $msg_dict["file"] = [
                            "action" => "",
                            "params" => [

                            ],
                            "echo" => $sign,
                        ];

                        if ($cache_type == '群') {
                            $msg_dict["text"]["params"]["message_type"] = "group";
                            $msg_dict["text"]["params"]["group_id"] = $cache_type_id;
                            $msg_dict["file"]["action"] = "upload_group_file";
                            $msg_dict["file"]["params"]["group_id"] = $cache_type_id;

                            $folder_id = "";
                            $path = $cache_path;
                            if (
                                empty($path)
                                || $path == "/"
                            ) {
                            } else {
                                $get_root_folder = json_encode(
                                    [
                                        "action" => "get_group_root_files",
                                        "params" => [
                                            "group_id" => $cache_type_id,
                                        ],
                                        "echo" => $sign,
                                    ]
                                );
                                $create_root_folder = json_encode(
                                    [
                                        "action" => "create_group_file_folder",
                                        "params" => [
                                            "group_id" => $cache_type_id,
                                            "name" => $path,
                                            "parent_id	" => "/",
                                        ],
                                        "echo" => $sign,
                                    ]
                                );

                                try {
                                    $ws = new weltolk_backup_qq_WebSocketClient($x2["address"], $headers);
//                            var_dump($ws->ping());
                                    $ws->ping();
                                    $ws->send($get_root_folder);
                                    $frame = $ws->recv();
                                    //                echo "收到服务器响应数据：" . $frame->playload . PHP_EOL;
//                            var_dump($ws->close());
                                    $ws->close();
                                    $result_json = json_decode(
                                        str_replace(
                                            ": None",
                                            ": []",
                                            trim($frame->playload)
                                        ),
                                        true
                                    );
                                    $cache_folders = $result_json["data"]["folders"];
                                    if (
                                        $result_json["echo"] == $sign
                                        && $result_json["retcode"] == 0
                                        && $result_json["status"] == "ok"
                                    ) {
                                        foreach ($cache_folders as $cache_folders_i) {
                                            if ($cache_folders_i["folder_name"] == $path) {
                                                $folder_id = $cache_folders_i["folder_id"];
                                            }
                                        }
                                        if (empty($folder_id)) {
                                            $ws = new weltolk_backup_qq_WebSocketClient($x2["address"], $headers);
//                            var_dump($ws->ping());
                                            $ws->ping();
                                            $ws->send($create_root_folder);
                                            $frame = $ws->recv();
                                            //                echo "收到服务器响应数据：" . $frame->playload . PHP_EOL;
//                            var_dump($ws->close());
                                            $ws->close();
                                            $result_json = json_decode(trim($frame->playload), true);

                                            $ws = new weltolk_backup_qq_WebSocketClient($x2["address"], $headers);
//                            var_dump($ws->ping());
                                            $ws->ping();
                                            $ws->send($get_root_folder);
                                            $frame = $ws->recv();
                                            //                echo "收到服务器响应数据：" . $frame->playload . PHP_EOL;
//                            var_dump($ws->close());
                                            $ws->close();
                                            $result_json = json_decode(
                                                str_replace(
                                                    ": None",
                                                    ": []",
                                                    trim($frame->playload)
                                                ),
                                                true
                                            );
                                            $cache_folders = $result_json["data"]["folders"];
                                            if (
                                                $result_json["echo"] == $sign
                                                && $result_json["retcode"] == 0
                                                && $result_json["status"] == "ok"
                                            ) {
                                                foreach ($cache_folders as $cache_folders_i) {
                                                    if ($cache_folders_i["folder_name"] == $path) {
                                                        $folder_id = $cache_folders_i["folder_id"];
                                                    }
                                                }
                                                if (empty($folder_id)) {
                                                    $cache_status = false;
                                                }
                                            } else {
                                                $cache_status = false;
                                            }
                                        }
                                    } else {
                                        $cache_status = false;
                                    }
                                } catch (\Exception $e) {
                                    echo "错误: ";
                                    var_dump($e->__toString());
                                    $cache_status = false;
                                }
                            }
                            if (!empty($folder_id)) {
                                $msg_dict["file"]["params"]["folder"] = $folder_id;
                            }
                        } elseif ($cache_type == '私聊') {
                            $msg_dict["text"]["params"]["message_type"] = "private";
                            $msg_dict["text"]["params"]["user_id"] = $cache_type_id;
                            $msg_dict["file"]["action"] = "upload_private_file";
                            $msg_dict["file"]["params"]["user_id"] = $cache_type_id;
                        } else {
                        }
                        $msg_dict["file"]["params"]["name"] = $file_name;
                        $msg_dict["file"]["params"]["file"] = SYSTEM_ROOT . DIRECTORY_SEPARATOR
                            . "plugins" . DIRECTORY_SEPARATOR . "weltolk_backup_qq"
                            . DIRECTORY_SEPARATOR . $file_name;

                        $msg_dict["text"]["params"]["message"] = $msg1;

                        foreach ($msg_dict as $msg_dict_i_key => $msg_dict_i_value) {
                            try {
                                $send_json = json_encode($msg_dict_i_value);
                                $ws = new weltolk_backup_qq_WebSocketClient($x2["address"], $headers);
//                            var_dump($ws->ping());
                                $ws->ping();
                                $ws->send($send_json);
                                $frame = $ws->recv();
                                //                echo "收到服务器响应数据：" . $frame->playload . PHP_EOL;
//                            var_dump($ws->close());
                                $ws->close();
                                $result_json = json_decode(trim($frame->playload), true);
                                if (
                                    $result_json["echo"] == $sign
                                    && $result_json["retcode"] == 0
                                    && $result_json["status"] == "ok"
                                ) {
                                } else {
                                    $cache_status = false;
                                }
                            } catch (\Exception $e) {
                                echo "错误: ";
                                var_dump($e->__toString());
                            }
                            usleep(250000);
                        }
                    } elseif ($x2['connect_type'] == 'HTTP API') {
                        $url = substr($x2["address"], -1) == "/"
                            ? substr($x2["address"], 0, -1)
                            : $x2["address"];
                        $msg_url = $url . "/send_msg";
                        $file_url = $url;

                        $headers = [];
                        if (empty($access_token)) {
                        } else {
                            $headers = [
                                "Content-Type" => "application/json",
                                "Authorization" => "Bearer " . $access_token,
                            ];
                        }

                        // go-cqhttp HTTP API post 未支持echo
//                        "echo" => $sign,

                        $msg_dict["text"] = [
                            "url" => $msg_url,
                            "data" => [

                            ]

                        ];

                        $msg_dict["file"] = [
                            "url" => "",
                            "data" => [

                            ]

                        ];

                        if ($cache_type == '群') {
                            $msg_dict["text"]["data"]["message_type"] = "group";
                            $msg_dict["text"]["data"]["group_id"] = $cache_type_id;
                            $msg_dict["file"]["url"] = $file_url . "/upload_group_file";
                            $msg_dict["file"]["data"]["group_id"] = $cache_type_id;

                            $folder_id = "";
                            $path = $cache_path;
                            if (
                                empty($path)
                                || $path == "/"
                            ) {
                            } else {
                                $get_url = $url . "get_group_root_files";
                                $create_url = $url . "create_group_file_folder";

                                $get_root_folder = json_encode(
                                    [
                                        "group_id" => $cache_type,
                                    ]
                                );
                                $create_root_folder = json_encode(
                                    [
                                        "group_id" => $cache_type,
                                        "name" => $path,
                                        "parent_id	" => "/",
                                    ]
                                );

                                try {
                                    $c = new wcurl($get_url, $headers);
                                    $c->setTimeOut(5000);
                                    $res = $c->post($get_root_folder);
                                    $result_json = json_decode(
                                        str_replace(
                                            ": None",
                                            ": []",
                                            trim($res)
                                        ),
                                        true
                                    );
                                    $cache_folders = $result_json["data"]["folders"];
                                    if (
                                        $result_json["retcode"] == 0
                                        && $result_json["status"] == "ok"
                                    ) {
                                        foreach ($cache_folders as $cache_folders_i) {
                                            if ($cache_folders_i["folder_name"] == $path) {
                                                $folder_id = $cache_folders_i["folder_id"];
                                            }
                                        }
                                        if (empty($folder_id)) {
                                            $c = new wcurl($create_url, $headers);
                                            $c->setTimeOut(5000);
                                            $res = $c->post($create_root_folder);
                                            $result_json = json_decode(
                                                str_replace(
                                                    ": None",
                                                    ": []",
                                                    trim($res)
                                                ),
                                                true
                                            );

                                            $c = new wcurl($get_url, $headers);
                                            $c->setTimeOut(5000);
                                            $res = $c->post($get_root_folder);
                                            $result_json = json_decode(
                                                str_replace(
                                                    ": None",
                                                    ": []",
                                                    trim($res)
                                                ),
                                                true
                                            );
                                            $cache_folders = $result_json["data"]["folders"];
                                            if (
                                                $result_json["echo"] == $sign
                                                && $result_json["retcode"] == 0
                                                && $result_json["status"] == "ok"
                                            ) {
                                                foreach ($cache_folders as $cache_folders_i) {
                                                    if ($cache_folders_i["folder_name"] == $path) {
                                                        $folder_id = $cache_folders_i["folder_id"];
                                                    }
                                                }
                                                if (empty($folder_id)) {
                                                    $cache_status = false;
                                                }
                                            } else {
                                                $cache_status = false;
                                            }
                                        }
                                    } else {
                                        $cache_status = false;
                                    }
                                } catch (\Exception $e) {
                                    echo "错误: ";
                                    var_dump($e->__toString());
                                    $cache_status = false;
                                }
                            }
                            if (!empty($folder_id)) {
                                $msg_dict["file"]["data"]["folder"] = $folder_id;
                            }
                        } elseif ($cache_type == '私聊') {
                            $msg_dict["text"]["data"]["message_type"] = "private";
                            $msg_dict["text"]["data"]["user_id"] = $cache_type_id;
                            $msg_dict["file"]["url"] = $file_url . "/upload_private_file";
                            $msg_dict["file"]["data"]["user_id"] = $cache_type_id;
                        } else {
                        }
                        $msg_dict["file"]["data"]["name"] = $file_name;
                        $msg_dict["file"]["data"]["file"] = SYSTEM_ROOT . DIRECTORY_SEPARATOR
                            . "plugins" . DIRECTORY_SEPARATOR . "weltolk_backup_qq"
                            . DIRECTORY_SEPARATOR . $file_name;

                        $msg_dict["text"]["data"]["message"] = $msg1;

                        foreach ($msg_dict as $msg_dict_i_key => $msg_dict_i_value) {
                            $send_json = json_encode($msg_dict_i_value["data"]);

                            $c = new wcurl($msg_dict_i_value["url"], $headers);
                            $c->setTimeOut(5000);
                            $res = $c->post($send_json);
                            $res = json_decode($res, true);
                            if (
                                $res['retcode'] == 0
                                && $res['status'] == 'ok'
                                // go-cqhttp HTTP API post 未支持echo
//                        && $res['echo'] == $sign
                            ) {
                            } else {
                                $cache_status = false;
                            }

                            usleep(250000);
                        }
                    } else {
                    }
                } else {
                }
            }
            if ($cache_status) {
                $return_arr = array('code' => 1, 'msg' => '两条测试消息已经发送到' . $cache_type_id . ',请注意查看,其中包含一个测试文件,请注意删除!');
            } else {
                $return_arr = array('code' => 0, 'msg' => '发送给' . $cache_type_id . '的测试消息发送失败!');
            }
            if ($return_arr['code'] == 1) {
                $msg_html = '<div class="text-success"><span class="glyphicon glyphicon-ok-sign"></span>';
            } else {
                $msg_html = '<div class="text-danger"><span class="glyphicon glyphicon-remove-sign"></span>';
            }
            $msg_html .= '&nbsp;' . $return_arr['msg'] . '</div>';

            echo json_encode(array('code' => $return_arr['code'], 'msg' => $msg_html));
            die;
            break;
        case 'addqq'://添加推送目标
            $connect_id = !empty($_POST['connect_id']) ? addslashes(strip_tags($_POST['connect_id'])) : msg('请输入使用的推送地址');
            $hour = $_POST['hour'];
            if ($hour == '0') {
            } elseif (empty($hour)) {
                msg('请输入每日推送时间');
            } else {
                $hour = addslashes(strip_tags($hour));
            }
            if ($hour >= 0 && $hour <= 23) {
            } else {
                msg("请输入正确的每日推送时间");
            }
            $path = !empty($_POST['path']) ? addslashes(strip_tags($_POST['path'])) : "/";
            $date_cache = strtotime(date('Y-m-d'));
            $type = !empty($_POST['type']) ? addslashes(strip_tags($_POST['type'])) : msg('请输入类型');
            $type_id = !empty($_POST['type_id']) ? addslashes(strip_tags($_POST['type_id'])) : msg('请输入号');
            $type_id = explode("\n", trim($type_id));
            $anchor = addslashes(strip_tags($_POST['anchor']));
            foreach ($type_id as $type_id_i) {
                $type_id_i = trim($type_id_i);
                if (!empty($type_id_i)) {
                    $exists = $m->fetch_array($m->query(
                        "SELECT * FROM `" . DB_PREFIX . "weltolk_backup_qq_target`"
                        . " WHERE `uid` = '" . UID . "' AND `connect_id` = '{$connect_id}' AND `hour` = '{$hour}'"
                        . " AND `type` = '{$type}' AND `type_id` = '{$type_id_i}' LIMIT 1"
                    ));
                    if (is_null($exists)) {
                        $m->query("INSERT INTO `" . DB_PREFIX . "weltolk_backup_qq_target` (`uid`, `connect_id`, `hour`, `type`, `type_id`, `path`,`nextdo`)"
                            . " VALUES ('" . UID . "', '{$connect_id}', '{$hour}', '{$type}', '{$type_id_i}', '{$path}', '{$date_cache}')");
                    }
                }
            }
            ReDirect(SYSTEM_URL . 'index.php?mod=admin:setplug&plug=weltolk_backup_qq&act=ok' . '#' . $anchor);
            break;
        case 'batchqqedit'://批量/搜索 编辑推送目标
            $anchor = addslashes(strip_tags($_POST['anchor']));
            $connect_id = !empty($_POST['connect_id']) ? addslashes(strip_tags($_POST['connect_id'])) : "";
            $connect_id2 = !empty($_POST['connect_id2']) ? addslashes(strip_tags($_POST['connect_id2'])) : "";
            if ((empty($connect_id) && !empty($connect_id2)) || (!empty($connect_id) && empty($connect_id2))) {
                msg('请正确选择推送地址');
            }
            $hour = (!empty($_POST['hour']) or $_POST['hour'] == 0) ? addslashes(strip_tags($_POST['hour'])) : "";
            $hour2 = (!empty($_POST['hour2']) or $_POST['hour2'] == 0) ? addslashes(strip_tags($_POST['hour2'])) : "";
            if ($hour >= 0 && $hour <= 23) {
            } else {
                msg("请输入正确的每日推送时间");
            }
            if ($hour2 >= 0 && $hour2 <= 23) {
            } else {
                msg("请输入正确的每日推送时间");
            }
            if (
                ((empty($hour) && $hour != 0) && (!empty($hour2) || $hour2 == 0))
                || ((!empty($hour) || $hour == 0) && (empty($hour2) && $hour2 != 0))
            ) {
                msg('请正确输入每日推送时间');
            }
            $path = !empty($_POST['path']) ? addslashes(strip_tags($_POST['path'])) : "/";
            $path2 = !empty($_POST['path2']) ? addslashes(strip_tags($_POST['path2'])) : "/";
            $type = !empty($_POST['type']) ? addslashes(strip_tags($_POST['type'])) : "";
            $type2 = !empty($_POST['type2']) ? addslashes(strip_tags($_POST['type2'])) : "";
            if ((empty($type) && !empty($type2)) || (!empty($type) && empty($type2))) {
                msg('请正确选择类型');
            }
            $type_id = !empty($_POST['type_id']) ? addslashes(strip_tags($_POST['type_id'])) : "";
            $type_id2 = !empty($_POST['type_id2']) ? addslashes(strip_tags($_POST['type_id2'])) : "";
            if ((empty($type_id) && !empty($type_id2)) || (!empty($type_id) && empty($type_id2))) {
                msg('请正确输入号');
            }
            $nextdo = !empty($_POST['nextdo']) ? addslashes(strip_tags($_POST['nextdo'])) : "";
            $nextdo2 = !empty($_POST['nextdo2']) ? addslashes(strip_tags($_POST['nextdo2'])) : "";
            if ((empty($nextdo) && !empty($nextdo2)) || (!empty($nextdo) && empty($nextdo2))) {
                msg('请正确输入下次推送日期');
            }
            $nextdo = strtotime($nextdo);
            $nextdo2 = strtotime($nextdo2);

            $status = false;
            $sql1 = "UPDATE `" . DB_PREFIX . "weltolk_backup_qq_target` SET ";
            $sql2 = " WHERE ";
            if (
                !empty($connect_id) && !empty($connect_id2)
                && ($connect_id != $connect_id2)
            ) {
                if ($status) {
                    $sql1 .= ", `connect_id` = '{$connect_id2}'";
                    $sql2 .= " AND `connect_id` = '{$connect_id}'";
                } else {
                    $sql1 .= "`connect_id` = '{$connect_id2}'";
                    $sql2 .= "`connect_id` = '{$connect_id}'";
                }
                $status = true;
            }
            if (
                (!empty($hour) or $hour == 0)
                && (!empty($hour2) or $hour == 0)
                && ($hour != $hour2)
            ) {
                if ($status) {
                    $sql1 .= ", `hour` = '{$hour2}'";
                    $sql2 .= " AND `hour` = '{$hour}'";
                } else {
                    $sql1 .= "`hour` = '{$hour2}'";
                    $sql2 .= "`hour` = '{$hour}'";
                }
                $status = true;
            }
            if (
                !empty($path) && !empty($path2)
                && ($path != $path2)
            ) {
                if ($status) {
                    $sql1 .= ", `path` = '{$path2}'";
                    $sql2 .= " AND `path` = '{$path}'";
                } else {
                    $sql1 .= "`path` = '{$path2}'";
                    $sql2 .= "`path` = '{$path}'";
                }
                $status = true;
            }
            if (
                !empty($type) && !empty($type2)
                && ($type != $type2)
            ) {
                if ($status) {
                    $sql1 .= ", `type` = '{$type2}'";
                    $sql2 .= " AND `type` = '{$type}'";
                } else {
                    $sql1 .= "`type` = '{$type2}'";
                    $sql2 .= "`type` = '{$type}'";
                }
                $status = true;
            }
            if (
                !empty($type_id) && !empty($type_id2)
                && ($type_id != $type_id2)
            ) {
                if ($status) {
                    $sql1 .= ", `type_id` = '{$type_id2}'";
                    $sql2 .= " AND `type_id` = '{$type_id}'";
                } else {
                    $sql1 .= "`type_id` = '{$type_id2}'";
                    $sql2 .= "`type_id` = '{$type_id}'";
                }
                $status = true;
            }
            if (
                !empty($nextdo) && !empty($nextdo2)
                && ($nextdo != $nextdo2)
            ) {
                if ($status) {
                    $sql1 .= ", `nextdo` = '{$nextdo2}'";
                    $sql2 .= " AND `nextdo` = '{$nextdo}'";
                } else {
                    $sql1 .= "`nextdo` = '{$nextdo2}'";
                    $sql2 .= "`nextdo` = '{$nextdo}'";
                }
                $status = true;
            }

            $sql = $sql1 . $sql2;
            if ($status) {
                $m->query($sql);
            }
            ReDirect(SYSTEM_URL . 'index.php?mod=admin:setplug&plug=weltolk_backup_qq&ok' . '#' . $anchor);
            break;
        case 'update'://编辑单条推送地址
            $id = !empty($_POST['id']) ? addslashes(strip_tags($_POST['id'])) : msg('请输入ID');
            $connect_id = !empty($_POST['connect_id']) ? addslashes(strip_tags($_POST['connect_id'])) : msg('请输入使用的推送地址');
            $hour = $_POST['hour'];
            if ($hour == '0') {
            } elseif (empty($hour)) {
                msg('请输入每日推送时间');
            } else {
                $hour = addslashes(strip_tags($hour));
            }
            if ($hour >= 0 && $hour <= 23) {
            } else {
                msg("请输入正确的每日推送时间");
            }
            $path = !empty($_POST['path']) ? addslashes(strip_tags($_POST['path'])) : "/";
            $type = !empty($_POST['type']) ? addslashes(strip_tags($_POST['type'])) : msg('请输入类型');
            $type_id = !empty($_POST['type_id']) ? addslashes(strip_tags($_POST['type_id'])) : msg('请输入号');
            $nextdo = !empty($_POST['nextdo']) ? addslashes(strip_tags($_POST['nextdo'])) : msg('请输入下次推送日期');
            $nextdo = strtotime($nextdo);
            $anchor = addslashes(strip_tags($_POST['anchor']));
            $m->query("UPDATE `" . DB_PREFIX . "weltolk_backup_qq_target` "
                . "SET `connect_id` = '{$connect_id}', `hour` = '{$hour}',"
                . " `type` = '{$type}', `type_id` = '{$type_id}',"
                . " `path` = '{$path}', `nextdo` = '{$nextdo}'"
                . " WHERE `id` = '{$id}'");
            ReDirect(SYSTEM_URL . 'index.php?mod=admin:setplug&plug=weltolk_backup_qq&ok' . '#' . $anchor);
            break;
        case 'del'://删除推送地址
            $id = isset($_GET['id']) ? intval($_GET['id']) : msg('缺少ID');
            $anchor = addslashes(strip_tags($_GET['anchor']));
            $m->query("DELETE FROM `" . DB_PREFIX . "weltolk_backup_qq_target` WHERE `uid` = " . UID . " AND `id` = " . $id);
            ReDirect(SYSTEM_URL . 'index.php?mod=admin:setplug&plug=weltolk_backup_qq&act=ok' . '#' . $anchor);
            break;
        default:
            break;
    }
} else {
}


?>
<div id="head"></div>

<h2>备份设置</h2>
<b>立即备份之前先保存设定</b>
<br/>
<br/>
<table class="table table-striped" id="user_settings">
    <tr id="weltolk_backup_qq_enable">
        <td>是否开启每日数据库备份qq推送</td>
        <td id="values">
            <input type="radio" name="weltolk_backup_qq_enable"
                   value="on" <?php echo $enable == "on" ? 'checked' : ''; ?> >开启<br/>
            <input type="radio" name="weltolk_backup_qq_enable"
                   value="off" <?php echo $enable == "off" ? 'checked' : ''; ?> >关闭

        </td>
    </tr>
    <tr id="weltolk_backup_qq_limit">
        <td>单次计划任务连续推送次数<br/>越小效率越低，但太大也可能导致超时</td>
        <td id="values">
            <input type="number" min="1" step="1" name="limit" value="<?php echo $limit ?>"
                   class="form-control" required>
        </td>
    </tr>
    <tr>
        <td>
            <a id="save_button"
               onclick="save_event('user_settings','save_button')"
               href="javascript:void(0)" class="btn btn-primary">
                保存设定
            </a>
        </td>
        <td>
            <button type="button"
                    onclick="backup_now()"
                    class="btn btn-danger">立即备份
            </button>
        </td>
    </tr>
</table>

<b>最新日志：<?php echo $log ?></b>
</br>
<b>注：请将计划任务顺序设置为0，以防止计划任务卡住导致没有备份！使用立即备份功能请确保已经设置了接收邮箱并保存！</b>
<br/>
<br/>

<!-- NAVI -->
<ul class="nav nav-tabs" id="PageTab">
    <li class="active"><a href="#page_list" data-toggle="tab"
                          onclick="$('#page_list').css('display','');$('#page_config').css('display','none');">推送列表</a>
    </li>
    <li><a href="#page_config" data-toggle="tab"
           onclick="$('#page_list').css('display','none');$('#page_config').css('display','');">推送地址</a>
    </li>
</ul>
<!-- END NAVI -->

<!-- PAGE1: page_config-->
<div class="tab-pane fade in active" id="page_list">
    <a name="#page_list"></a>

    <br/>
    <br/>
    <input type="button" data-toggle="modal" data-target="#addqq"
           class="btn btn-info btn-lg" value="+ 增加推送目标"
           style="float:right;">
    <input type="button" data-toggle="modal" data-target="#batchqqedit"
           onclick="batch_qq_values()"
           class="btn btn-info btn-lg" value="* 批量/搜索 修改"
           style="float:right;">

    <table class="table table-striped" id="qqtable">
        <thead>
        <tr>
            <th id="id">ID</th>
            <th id="connect_id">使用的推送地址</th>
            <th id="hour">每日推送时间</th>
            <th id="type">类型</th>
            <th id="type_id">号</th>
            <th id="path">群文件路径</th>
            <th id="debug"></th>
            <th id="func"></th>
            <th id="nextdo">下次推送日期</th>
            <th id="editqq_button">修改</th>
            <th id="delqq_button">删除</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $x = $m->query("SELECT * FROM `" . DB_PREFIX . "weltolk_backup_qq_target` WHERE `uid` = " . UID);
        while ($v = $m->fetch_array($x)) {
            ?>
            <tr id="addedqq<?php echo $v['id'] ?>">
                <td id="use"><?php echo $v['id'] ?></td>
                <td id="use"><?php echo $v['connect_id'] ?></td>
                <td id="use"><?php echo $v['hour'] ?></td>
                <td id="use"><?php echo $v['type'] ?></td>
                <td id="use"><?php echo $v['type_id'] ?></td>
                <td id="use"><?php echo $v['path'] ?></td>
                <td>
                    <a id="debug_btn<?php echo $v['id'] ?>"
                       onclick="debug_event('addedqq<?php echo $v['id'] ?>', '<?php echo $v['id'] ?>')"
                       href="javascript:void(0)" class="btn btn-warning">
                        测试推送</a>
                </td>
                <td><a href="javascript:scroll(0,0)">返回顶部</a></td>
                <td id="use"><?php echo date('Y-m-d', $v['nextdo']) ?></td>
                <td><a class="btn btn-default" data-toggle="modal" data-target="#editqq"
                       onclick="edit_qq_values('addedqq<?php echo $v['id'] ?>')"
                       title="修改"><span class="glyphicon glyphicon-edit"></span> </a></td>
                <td><a class="btn btn-default"
                       href="index.php?mod=admin:setplug&plug=weltolk_backup_qq&page=list&act=del&id=<?php echo $v['id'] ?>&anchor=page_list"
                       title="删除"><span class="glyphicon glyphicon-remove"></span> </a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- END PAGE1 -->

<!-- PAGE2: page_list -->
<div class="tab-pane fade" id="page_config" style="display:none">
    <a name="#page_config"></a>

    <br/><br/>
    <input type="button" data-toggle="modal" data-target="#addconnect"
           class="btn btn-info btn-lg" value="+ 增加推送地址"
           style="float:right;">
    <input type="button" data-toggle="modal" data-target="#batchedit"
           onclick="batch_values()"
           class="btn btn-info btn-lg" value="* 批量/搜索 修改"
           style="float:right;">

    <table class="table table-striped" id="connecttable">
        <thead>
        <tr>
            <th id="id">ID</th>
            <th id="client" style="width:20%">qq客户端</th>
            <th id="connect_type" style="width:20%">连接方式</th>
            <th id="address" style="width:50%">连接地址</th>
            <th id="access_token" style="width:10%">access_token</th>
            <th id="editconnect_button">修改</th>
            <th id="delconnect_button">删除</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $x = $m->query("SELECT * FROM `" . DB_PREFIX . "weltolk_backup_qq_connect` WHERE `uid` = " . UID);
        while ($v = $m->fetch_array($x)) {
            ?>
            <tr id="addedconnect<?php echo $v['id'] ?>">
                <td id="use"><?php echo $v['id'] ?></td>
                <td id="use"><?php echo $v['client'] ?></td>
                <td id="use"><?php echo $v['connect_type'] ?></td>
                <td id="use"><?php echo $v['address'] ?></td>
                <td id="use">******<br/>
                    <a href="javascript:scroll(0,0)">返回顶部</a>
                </td>
                <input id="addedconnect<?php echo $v['id'] ?>_access_token_hidden" type="hidden"
                       value="<?php echo $v['access_token'] ?>">
                <td><a class="btn btn-default" data-toggle="modal" data-target="#editconnect"
                       onclick="edit_values('addedconnect<?php echo $v['id'] ?>')"
                       title="修改"><span class="glyphicon glyphicon-edit"></span> </a></td>
                <td><a class="btn btn-default"
                       href="index.php?mod=admin:setplug&plug=weltolk_backup_qq&page=config&act=del&id=<?php echo $v['id'] ?>&anchor=page_config"
                       title="删除"><span class="glyphicon glyphicon-remove"></span> </a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>

<!-- END PAGE2 -->

<div class="modal fade" id="addconnect" tabindex="-1" role="dialog" aria-labelledby="addconnect"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;
                </button>
                <h4 class="modal-title" id="addconnect_title">添加推送地址</h4>
            </div>
            <form id="addconnect_form"
                  action="index.php?mod=admin:setplug&plug=weltolk_backup_qq&page=config&act=addconnect"
                  method="post">
                <div class="modal-body">
                    <div class="input-group">
                        <span class="input-group-addon">qq客户端</span>
                        <input type="text" name="client" readonly class="form-control" id="addconnect_client"
                               value="go-cqhttp">
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">连接方式</span>
                        <select name="connect_type" id="addconnect_connect_type" class="form-control">
                            <option value="" selected hidden>
                            </option>
                            <option value="正向WebSocket">
                                正向WebSocket
                            </option>
                            <option value="HTTP API">
                                HTTP API
                            </option>
                        </select>
                    </div>
                    <br/>
                    <div id="addconnect_address_list">
                        <div class="input-group">
                                <span class="input-group-addon">连接地址
                                    <br/><br/>（可添加多个）<br/><br/>（用回车分隔）</span>
                            <textarea class="form-control" name="address" style="height:260px;"
                                      id="addconnect_address" placeholder="连接地址"></textarea>
                        </div>
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">access_token</span>
                        <input type="text" name="access_token" id="addconnect_access_token" class="form-control"
                               value="" placeholder="access_token">
                    </div>
                </div>
                <input type="hidden" name="anchor" id="anchor" value="page_config"/>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary" id="runsql_button">提交更改</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="addqq" tabindex="-1" role="dialog" aria-labelledby="addqq"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;
                </button>
                <h4 class="modal-title" id="addqq_title">添加推送目标</h4>
            </div>
            <form id="addqq_form" action="index.php?mod=admin:setplug&plug=weltolk_backup_qq&page=list&act=addqq"
                  method="post">
                <div class="modal-body">
                    <div class="input-group">
                        <span class="input-group-addon">使用的推送地址</span>
                        <select name="connect_id" id="addqq_connect_id" class="form-control">
                            <option value="" selected hidden>
                            </option>
                            <?php
                            $x = $m->query("SELECT `id` FROM `" . DB_PREFIX . "weltolk_backup_qq_connect` WHERE `uid` = " . UID);
                            while ($v = $m->fetch_array($x)) {
                                echo '<option value="' . $v['id'] . '">' . $v['id'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">每日推送时间（0-23）</span>
                        <input type="number" name="hour" class="form-control" id="addqq_hour"
                               min="0" max="23" step="1"
                               value="0" placeholder="每日几点推送（0-23）">
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">类型</span>
                        <select name="type" id="addqq_type" class="form-control">
                            <option value="" selected hidden>
                            </option>
                            <option value="群">
                                群
                            </option>
                            <option value="私聊">
                                私聊
                            </option>
                        </select>
                    </div>
                    <br/>
                    <div class="input-group">
                            <span class="input-group-addon">号
                            <br/><br/>（可添加多个）<br/><br/>（用回车分隔）</span>
                        <textarea class="form-control" name="type_id" style="height:260px;"
                                  id="addqq_type_id" placeholder="群号或qq号"></textarea>
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">群文件路径</span>
                        <input type="text" class="form-control" name="path"
                               id="addqq_path" placeholder="默认根目录,不存在时自动创建,只支持一层,仅当类型为 群 时生效"
                               value="">
                    </div>
                </div>
                <input type="hidden" name="anchor" id="anchor" value="page_config"/>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary" id="runsql_button">提交更改</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="editconnect" tabindex="-1" role="dialog" aria-labelledby="editconnect"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;
                </button>
                <h4 class="modal-title" id="editconnect_title">修改推送地址</h4>
            </div>
            <form id="editconnect_form"
                  action="index.php?mod=admin:setplug&plug=weltolk_backup_qq&page=config&act=update"
                  method="post">
                <div class="modal-body">
                    <div class="input-group">
                        <span class="input-group-addon">ID</span>
                        <input type="text" name="id" readonly class="form-control" id="editconnect_id"
                               value="">
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">qq客户端</span>
                        <input type="text" name="client" readonly class="form-control" id="editconnect_client"
                               value="">
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon" id="editconnect_connect_type_info">连接方式</span>
                        <select name="connect_type" id="editconnect_connect_type" class="form-control">
                        </select>
                    </div>
                    <br/>
                    <div id="editconnect_address_list">
                        <div class="input-group">
                            <span class="input-group-addon">连接地址</span>
                            <textarea class="form-control" name="address" style="height:260px;"
                                      id="editconnect_address" placeholder="连接地址"></textarea>
                        </div>
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">access_token</span>
                        <input type="text" name="access_token" id="editconnect_access_token" class="form-control"
                               value="" placeholder="access_token">
                    </div>
                    <br/>
                </div>
                <input type="hidden" name="anchor" id="anchor" value="page_config"/>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary" id="runsql_button">提交更改</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="editqq" tabindex="-1" role="dialog" aria-labelledby="editconnect"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;
                </button>
                <h4 class="modal-title" id="editqq_title">修改推送目标</h4>
            </div>
            <form id="editqq_form" action="index.php?mod=admin:setplug&plug=weltolk_backup_qq&page=list&act=update"
                  method="post">
                <div class="modal-body">
                    <b>日期格式为yyyy-mm-dd</b>
                    <br/>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">ID</span>
                        <input type="text" name="id" readonly class="form-control" id="editqq_id"
                               value="">
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon" id="editqq_connect_id_info">使用的推送地址</span>
                        <select name="connect_id" id="editqq_connect_id" class="form-control">
                        </select>
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">每日推送时间（0-23）</span>
                        <input type="number" name="hour" class="form-control" id="editqq_hour"
                               min="0" max="23" step="1"
                               value="" placeholder="每日几点推送（0-23）">
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">类型</span>
                        <select name="type" id="editqq_type" class="form-control">
                        </select>
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">号</span>
                        <textarea class="form-control" name="type_id" style="height:260px;"
                                  id="editqq_type_id" placeholder="群号或qq号"></textarea>
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">群文件路径</span>
                        <input type="text" class="form-control" name="path"
                               id="editqq_path"
                               placeholder="默认根目录,不存在时自动创建,只支持一层,仅当类型为 群 时生效" value="">
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">下次推送日期</span>
                        <input type="text" class="form-control" name="nextdo"
                               id="editqq_nextdo" value="" placeholder="下次推送日期"/>
                    </div>
                </div>
                <input type="hidden" name="anchor" id="anchor" value="page_list"/>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary" id="runsql_button">提交更改</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="batchedit" tabindex="-1" role="dialog" aria-labelledby="batchedit"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;
                </button>
                <h4 class="modal-title" id="batchedit_title">批量修改推送地址</h4>
            </div>
            <form id="batchedit_form"
                  action="index.php?mod=admin:setplug&plug=weltolk_backup_qq&page=config&act=batchedit"
                  method="post">
                <div class="modal-body">
                    <b>填了多个选项的情况下是判断条件都满足（条件和）</b>
                    <br/>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">要修改的qq客户端</span>
                        <input type="text" name="client" class="form-control" id="batchedit_client"
                               value="">
                        <input type="text" name="client2" class="form-control" id="batchedit_client2"
                               value="">
                    </div>
                    <br/>
                    <div class="input-group">
                            <span class="input-group-addon"
                                  id="batchedit_connect_type_info">要修改的连接方式<br/><br/>支持搜索，下拉框选择和手动输入的值</span>
                        <input name="connect_type" type="text" list="batchedit_connect_type_list"
                               class="form-control"
                               id="batchedit_connect_type" value="">
                        <datalist id="batchedit_connect_type_list">

                        </datalist>
                        <select name="connect_type2" class="form-control" id="batchedit_connect_type2">

                        </select>
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">要修改的连接地址</span>
                        <input type="text" name="address" class="form-control" id="batchedit_address"
                               value="">
                        <input type="text" name="address2" class="form-control" id="batchedit_address2"
                               value="">
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">要修改的access_token</span>
                        <input type="text" name="access_token" id="batchedit_access_token" class="form-control"
                               value="">
                        <input type="text" name="access_token2" id="batchedit_access_token2" class="form-control"
                               value="">
                    </div>
                    <br/>
                </div>
                <input type="hidden" name="anchor" id="anchor" value="page_config"/>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary" id="runsql_button">提交更改</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="batchqqedit" tabindex="-1" role="dialog" aria-labelledby="batchqqedit"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;
                </button>
                <h4 class="modal-title" id="batchqqedit_title">批量修改推送目标</h4>
            </div>
            <form id="batchqqedit_form"
                  action="index.php?mod=admin:setplug&plug=weltolk_backup_qq&page=list&act=batchqqedit"
                  method="post">
                <div class="modal-body">
                    <b>填了多个选项的情况下是判断条件都满足（条件和）</b>
                    <br/>
                    <br/>
                    <b>日期格式为yyyy-mm-dd</b>
                    <br/>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">要修改的推送地址</span>
                        <input name="connect_id" type="text" list="batchqqedit_connect_id_list"
                               class="form-control"
                               id="batchedit_connect_id" value="">
                        <datalist id="batchqqedit_connect_id_list">
                        </datalist>
                        <select name="connect_id2" id="batchqqedit_connect_id2" class="form-control">
                        </select>
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">要修改的每日推送时间（0-23）</span>
                        <input type="number" name="hour" class="form-control" id="batchqqedit_hour"
                               min="0" max="23" step="1"
                               value="">
                        <input type="number" name="hour2" class="form-control" id="batchqqedit_hour2"
                               min="0" max="23" step="1"
                               value="">
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">要修改的类型</span>
                        <input name="type" type="text" list="batchqqedit_type_list"
                               class="form-control"
                               id="batchedit_type" value="">
                        <datalist id="batchqqedit_type_list">
                        </datalist>
                        <select name="type2" id="batchqqedit_type2" class="form-control">
                        </select>
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">要修改的号</span>
                        <input type="text" name="type_id" id="batchqqedit_type_id" class="form-control"
                               value="">
                        <input type="text" name="type_id2" id="batchqqedit_type_id2" class="form-control"
                               value="">
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">要修改的群文件路径</span>
                        <input type="text" class="form-control" name="path"
                               id="batchqqedit_path">
                        <input type="text" class="form-control" name="path2"
                               id="batchqqedit_path2">
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">要修改的下次推送日期</span>
                        <input type="text" class="form-control" name="nextdo"
                               id="batchqqedit_nextdo" value=""/>
                        <input type="text" class="form-control" name="nextdo2"
                               id="batchqqedit_nextdo2" value=""/>
                    </div>
                </div>
                <input type="hidden" name="anchor" id="anchor" value="page_config"/>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary" id="runsql_button">提交更改</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="application/javascript">
    const connect_type_list = [
        "正向WebSocket",
        "HTTP API",
    ];

    const type_list = [
        "群",
        "私聊",
    ];

    $(function () {
        $('.editconnect').modal("hide");
        $('.batchedit').modal("hide");
        $('.editqq').modal("hide");
    });

    function edit_values(id) {
        const prefix = "editconnect_";

        const args = document.getElementById(id).getElementsByTagName("td");
        const names = document.getElementById("connecttable").getElementsByTagName("thead")[0]
            .getElementsByTagName("tr")[0].getElementsByTagName("th");


        for (let i = 0; i < args.length; i++) {
            if (args[i].id === "use") {
                if (names[i].id.trim() === "address") {
                    document.getElementById(prefix + names[i].id.trim()).innerText = args[i].innerText.trim();
                } else if (names[i].id.trim() === "access_token") {
                    const access_token = document.getElementById(id.trim() + "_access_token_hidden").getAttribute("value");
                    document.getElementById(prefix + names[i].id.trim()).setAttribute("value", access_token);
                } else if (names[i].id.trim() === "connect_type") {
                    let connect_type_html = "";
                    if (connect_type_list.includes(args[i].innerHTML.trim())) {
                        document.getElementById(prefix + "connect_type_info").innerHTML = "连接方式";
                        connect_type_html = "<option value=\"" + args[i].innerHTML.trim() + "\" selected hidden>" + args[i].innerHTML.trim() + "</option>";
                    } else {
                        document.getElementById(prefix + "connect_type_info").innerHTML = "连接方式<br/><b>（当前连接方式已失效）</b>";
                        connect_type_html = "<option value=\"" + "\" selected hidden>" + "</option>";
                    }
                    for (let ii = 0; ii < connect_type_list.length; ii++) {
                        connect_type_html += "<option value=\"" + connect_type_list[ii] + "\">" + connect_type_list[ii] + "</option>";
                    }
                    document.getElementById(prefix + names[i].id.trim()).innerHTML = connect_type_html;
                } else {
                    document.getElementById(prefix + names[i].id.trim()).setAttribute("value", args[i].innerHTML.trim());
                }
            }
        }
    }

    function edit_qq_values(id) {
        const prefix = "editqq_";

        const args = document.getElementById(id).getElementsByTagName("td");
        const names = document.getElementById("qqtable").getElementsByTagName("thead")[0]
            .getElementsByTagName("tr")[0].getElementsByTagName("th");

        const connect_id_list = [
            <?php
            $x = $m->query("SELECT `id` FROM `" . DB_PREFIX . "weltolk_backup_qq_connect` WHERE `uid` = " . UID);
            while ($v = $m->fetch_array($x)) {
                echo '"' . $v['id'] . '",';
            }
            ?>
        ]

        for (let i = 0; i < args.length; i++) {
            if (args[i].id === "use") {
                if (names[i].id.trim() === "connect_id") {
                    let connect_id_html = "";
                    if (connect_id_list.includes(args[i].innerHTML.trim())) {
                        connect_id_html = "<option value=\"" + args[i].innerHTML.trim() + "\" selected hidden>" + args[i].innerHTML.trim() + "</option>";
                    } else {
                        document.getElementById(prefix + "connect_id_info").innerHTML = "使用的推送地址<br/><br/>（已失效，请重新选择）";
                        connect_id_html = "<option value=\"" + "\" selected hidden>" + "</option>";
                    }
                    for (let ii = 0; ii < connect_id_list.length; ii++) {
                        connect_id_html += "<option value=\"" + connect_id_list[ii] + "\">" + connect_id_list[ii] + "</option>";
                    }
                    document.getElementById(prefix + names[i].id.trim()).innerHTML = connect_id_html;
                } else if (names[i].id.trim() === "type_id") {
                    document.getElementById(prefix + names[i].id.trim()).innerText = args[i].innerText.trim();
                } else if (names[i].id.trim() === "type") {
                    let type_html = "";
                    if (type_list.includes(args[i].innerHTML.trim())) {
                        type_html = "<option value=\"" + args[i].innerHTML.trim() + "\" selected hidden>" + args[i].innerHTML.trim() + "</option>";
                    } else {
                        type_html = "<option value=\"" + "\" selected hidden>" + "</option>";
                    }
                    for (let ii = 0; ii < type_list.length; ii++) {
                        type_html += "<option value=\"" + type_list[ii] + "\">" + type_list[ii] + "</option>";
                    }
                    document.getElementById(prefix + names[i].id.trim()).innerHTML = type_html;
                } else {
                    document.getElementById(prefix + names[i].id.trim()).setAttribute("value", args[i].innerHTML.trim());
                }
            }
        }
    }

    function batch_values() {
        const prefix = "batchedit_";

        let connect_type_html = "<option value=\"" + "\" selected hidden>" + "</option>";

        for (let i = 0; i < connect_type_list.length; i++) {
            connect_type_html += "<option value=\"" + connect_type_list[i] + "\">" + connect_type_list[i] + "</option>";

        }
        document.getElementById(prefix + "connect_type_list").innerHTML = connect_type_html;
        document.getElementById(prefix + "connect_type2").innerHTML = connect_type_html;

    }

    function batch_qq_values() {
        const prefix = "batchqqedit_";

        const connect_id_list = [
            <?php
            $x = $m->query("SELECT `id` FROM `" . DB_PREFIX . "weltolk_backup_qq_connect` WHERE `uid` = " . UID);
            while ($v = $m->fetch_array($x)) {
                echo '"' . $v['id'] . '",';
            }
            ?>
        ];

        let connect_id_html = "<option value=\"" + "\" selected hidden>" + "</option>";

        for (let i = 0; i < connect_id_list.length; i++) {
            connect_id_html += "<option value=\"" + connect_id_list[i] + "\">" + connect_id_list[i] + "</option>";

        }
        document.getElementById(prefix + "connect_id_list").innerHTML = connect_id_html;
        document.getElementById(prefix + "connect_id2").innerHTML = connect_id_html;

        let type_html = "<option value=\"" + "\" selected hidden>" + "</option>";

        for (let i = 0; i < type_list.length; i++) {
            type_html += "<option value=\"" + type_list[i] + "\">" + type_list[i] + "</option>";

        }
        document.getElementById(prefix + "type_list").innerHTML = type_html;
        document.getElementById(prefix + "type2").innerHTML = type_html;

    }

    function debug_event(id, id2) {
        $('#debug_btn' + id2).attr('disabled', true);
        $('#debug_btn' + id2).text('正在发送');
        const args = document.getElementById(id).getElementsByTagName("td");
        const names = document.getElementById("qqtable").getElementsByTagName("thead")[0]
            .getElementsByTagName("tr")[0].getElementsByTagName("th");

        let debug_data = {};
        for (let i = 0; i < args.length; i++) {
            if (names[i].id.trim() === "connect_id") {
                debug_data["connect_id"] = args[i].innerHTML.trim();
            } else if (names[i].id.trim() === "type") {
                debug_data["type"] = args[i].innerHTML.trim();
            } else if (names[i].id.trim() === "type_id") {
                debug_data["type_id"] = args[i].innerHTML.trim();
            } else if (names[i].id.trim() === "path") {
                debug_data["path"] = args[i].innerHTML.trim();
            }
        }
        $.ajax({
            url: 'index.php?mod=admin:setplug&plug=weltolk_backup_qq&page=list&act=debug&anchor=page_list',
            type: 'POST',
            dataType: 'json',
            data: {
                info: JSON.stringify(debug_data),
            },
            success: function (result) {
                switch (result.code) {
                    case 1:
                        alert(result.msg);
                        break;
                    case 0:
                        alert(result.msg);
                        break;
                    default:
                        alert('请求异常!请刷新页面后重试');
                        break;
                }
                $('#debug_btn' + id2).attr('disabled', false);
                $('#debug_btn' + id2).text('测试推送');
            },
            error: function () {
                alert('网络异常!请刷新页面后重试');
                $('#debug_btn' + id2).attr('disabled', false);
                $('#debug_btn' + id2).text('测试推送');
            }
        });
    }

    function save_event(id, id2) {
        $('#' + id2).attr('disabled', true);
        $('#' + id2).text('正在保存');
        const args = document.getElementById(id).getElementsByTagName("tr");

        let data = {};
        for (let i = 0; i < args.length; i++) {
            if (args[i].id.trim() === "weltolk_backup_qq_enable") {
                let is_open = true;
                const tds = args[i].getElementsByTagName("td")
                for (let ii = 0; ii < tds.length; ii++) {
                    if (tds[ii].id.trim() === "values") {
                        const radios = tds[ii].getElementsByTagName("input");
                        for (let iii = 0; iii < radios.length; iii++) {
                            if (radios[iii].name === "weltolk_backup_qq_enable"
                                && radios[iii].checked
                            ) {
                                is_open = radios[iii].value;
                            }
                        }
                    }
                }

                data["weltolk_backup_qq_enable"] = is_open;
            } else if (args[i].id.trim() === "weltolk_backup_qq_limit") {
                let limit_value = 10;
                const tds = args[i].getElementsByTagName("td")
                for (let ii = 0; ii < tds.length; ii++) {
                    if (tds[ii].id.trim() === "values") {
                        const limits = tds[ii].getElementsByTagName("input");
                        for (let iii = 0; iii < limits.length; iii++) {
                            if (limits[iii].name === "limit") {
                                limit_value = limits[iii].value;
                            }
                        }
                    }
                }

                data["weltolk_backup_qq_limit"] = limit_value;
            }
        }
        $.ajax({
            url: 'index.php?mod=admin:setplug&plug=weltolk_backup_qq&page=user_settings&act=store',
            type: 'POST',
            dataType: 'json',
            data: {
                info: JSON.stringify(data),
            },
            success: function (result) {
                switch (result.code) {
                    case 1:
                        document.getElementById("head").innerHTML =
                            '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + result.msg + '</div>';
                        break;
                    case 0:
                        document.getElementById("head").innerHTML =
                            '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + result.msg + '</div>';
                        break;
                    default:
                        alert('请求异常!请刷新页面后重试');
                        break;
                }
                $('#' + id2).attr('disabled', false);
                $('#' + id2).text('保存设定');
            },
            error: function () {
                alert('网络异常!请刷新页面后重试');
                $('#' + id2).attr('disabled', false);
                $('#' + id2).text('保存设定');
            }
        });
    }

    function backup_now() {
        alert('<div class="text-danger"><span class="glyphicon glyphicon-remove-sign"></span>&nbsp;' +
            '打开云签到平台的 计划任务 页面,点击本插件计划任务的 运行 按钮来进行 立即备份'
            + '</div>');
    }
</script>
