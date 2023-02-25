<?php

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}


function cron_weltolk_backup_qq()
{
    foreach (glob('*.cache') as $file) {
        unlink($file);
    }

    require_once "weltolk_backup_qq_websocket.php";
    global $m;
    $limit = option::get('weltolk_backup_qq_limit');
    $enable = option::get('weltolk_backup_qq_enable');
    $is_open = $enable == "on";
    $today = date('Y-m-d');
    $now = time();
    $hour = date('H');
    $y = $m->query("SELECT * FROM `" . DB_PREFIX . "weltolk_backup_qq_target` WHERE `nextdo` <= '{$now}' LIMIT {$limit}");
    $log = "";
    $e = $m->query('SHOW TABLES');
    $dump = '/*' . PHP_EOL;
    $dump .= 'Warning: Do not change the comments!!!' . PHP_EOL . PHP_EOL;
    $dump .= 'Tieba-Cloud-Sign Database Backup' . PHP_EOL;
    $dump .= 'Tieba-Cloud-Sign Version : ' . SYSTEM_VER . PHP_EOL;
    $dump .= 'Tieba-Cloud-Sign Name : ' . SYSTEM_NAME . PHP_EOL;
    $dump .= 'MySQL Server Version : ' . $m->getMysqlVersion() . PHP_EOL;
    $dump .= 'Date: ' . date('Y-m-d H:i:s') . PHP_EOL;
    $dump .= '*/' . PHP_EOL . PHP_EOL;
    $dump .= '-------------- Start --------------' . PHP_EOL . PHP_EOL;
    $dump .= 'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";' . PHP_EOL;
    $dump .= 'SET FOREIGN_KEY_CHECKS=0;' . PHP_EOL;
    $dump .= 'SET time_zone = "+8:00";' . PHP_EOL . PHP_EOL;
    while ($v = $m->fetch_array($e)) {
        $list = $v;
        foreach ($list as $table) {
            $dump .= dataBak($table);
        }
    }
    $dump .= PHP_EOL . '-------------- End --------------';
    $message_file_name = 'backup-' . date('Ymd') . '.sql';
    $file_name = SYSTEM_ROOT . DIRECTORY_SEPARATOR
        . "plugins" . DIRECTORY_SEPARATOR . "weltolk_backup_qq"
        . DIRECTORY_SEPARATOR;
    $file_name_length = 18;
//字符组合
    $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $len = strlen($str) - 1;
    for ($i = 0; $i < $file_name_length; $i++) {
        $num = mt_rand(0, $len);
        $file_name .= $str[$num];
    }
    $file_name .= ".cache";
    file_put_contents($file_name, $dump);
    while ($x = $m->fetch_array($y)) {
        if ($is_open) {
            if ($hour >= $x['hour']) {
                $y2 = $m->query("SELECT * FROM `" . DB_PREFIX . "weltolk_backup_qq_connect` WHERE `id` = '{$x['connect_id']}' LIMIT 1");
                $x2 = $m->fetch_array($y2);
                $sign = "sign" . mt_rand(1000, 9999);
                $text = $today . " 备份文件已附上,请查看文件: ";
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
                        if ($x['type'] == '群') {
                            $msg_dict["text"]["params"]["message_type"] = "group";
                            $msg_dict["text"]["params"]["group_id"] = $x['type_id'];
                            $msg_dict["file"]["action"] = "upload_group_file";
                            $msg_dict["file"]["params"]["group_id"] = $x['type_id'];
                            $folder_id = "";
                            $path = $x["path"];
                            if (
                                empty($path)
                                || $path == "/"
                            ) {
                                $text .= $message_file_name;
                            } else {
                                $text .= $path . '/' . $message_file_name;
                                $get_root_folder = json_encode([
                                        "action" => "get_group_root_files",
                                        "params" => [
                                            "group_id" => $x["type_id"],
                                        ],
                                        "echo" => $sign,
                                    ]);
                                $create_root_folder = json_encode([
                                        "action" => "create_group_file_folder",
                                        "params" => [
                                            "group_id" => $x["type_id"],
                                            "name" => $path,
                                            "parent_id	" => "/",
                                        ],
                                        "echo" => $sign,
                                    ]);
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
                                                    $log .= $today . " 失败 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                                        . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                                        . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . "根目录创建后仍未空"
                                                        . "\n";
                                                    continue;
                                                }
                                            } else {
                                                            $log .= $today . " 失败 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                                            . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                                            . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . "创建后获取根目录"
                                                            . "\n";
                                                            continue;
                                            }
                                        }
                                    } else {
                                        $log .= $today . " 失败 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                        . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                        . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . "获取根目录"
                                        . "\n";
                                        continue;
                                    }
                                } catch (\Exception $e) {
                                            echo "错误: ";
                                            var_dump($e->__toString());
                                            $log .= $today . " 失败 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                        . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                        . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . "目录处理"
                                        . "\n";
                                            continue;
                                }
                            }
                            if (!empty($folder_id)) {
                                $msg_dict["file"]["params"]["folder"] = $folder_id;
                            }
                        } elseif ($x['type'] == '私聊') {
                            $text .= $message_file_name;
                            $msg_dict["text"]["params"]["message_type"] = "private";
                            $msg_dict["text"]["params"]["user_id"] = $x['type_id'];
                            $msg_dict["file"]["action"] = "upload_private_file";
                            $msg_dict["file"]["params"]["user_id"] = $x['type_id'];
                        } else {
                                continue;
                        }
                        $msg_dict["file"]["params"]["name"] = $message_file_name;
                        $msg_dict["file"]["params"]["file"] = $file_name;
                        $msg_dict["text"]["params"]["message"] = $text;
                        $send_status = false;
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
                                        $send_status = true;
                                        $log .= $today . " 成功 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                    . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                    . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . $message_file_name . "(" . $file_name . ")"
                                    . "\n";
                                } else {
                                    $log .= $today . " 失败 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                        . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                        . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . $message_file_name . "(" . $file_name . ")"
                                        . "\n";
                                }
                            } catch (\Exception $e) {
                                echo "错误: ";
                                var_dump($e->__toString());
                            }
                            usleep(250000);
                        }
                        if ($send_status) {
                                $next = strtotime($today) + 86400 + $x['hour'] * 3600;
                                $m->query("UPDATE `" . DB_PREFIX . "weltolk_backup_qq_target` SET `nextdo` = '{$next}' WHERE `id` = '{$x['id']}'");
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
                        if ($x['type'] == '群') {
                                $msg_dict["text"]["data"]["message_type"] = "group";
                                $msg_dict["text"]["data"]["group_id"] = $x['type_id'];
                                $msg_dict["file"]["url"] = $file_url . "/upload_group_file";
                                $msg_dict["file"]["data"]["group_id"] = $x['type_id'];
                                $folder_id = "";
                                $path = $x["path"];
                            if (
                                    empty($path)
                                || $path == "/"
                            ) {
                                $text .= $message_file_name;
                            } else {
                                $text .= $path . '/' . $message_file_name;
                                $get_url = $url . "get_group_root_files";
                                $create_url = $url . "create_group_file_folder";
                                $get_root_folder = json_encode([
                                    "group_id" => $x["type_id"],
                                ]);
                                $create_root_folder = json_encode([
                                    "group_id" => $x["type_id"],
                                    "name" => $path,
                                    "parent_id	" => "/",
                                ]);
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
                                                    $log .= $today . " 失败 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                                    . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                                    . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . "根目录创建后仍未空"
                                                    . "\n";
                                                    continue;
                                                }
                                            } else {
                                                $log .= $today . " 失败 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                                . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                                . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . "创建后获取根目录"
                                                . "\n";
                                                continue;
                                            }
                                        }
                                    } else {
                                        $log .= $today . " 失败 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                        . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                        . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . "获取根目录"
                                        . "\n";
                                        continue;
                                    }
                                } catch (\Exception $e) {
                                        echo "错误: ";
                                        var_dump($e->__toString());
                                        $log .= $today . " 失败 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                        . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                        . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . "获取根目录"
                                        . "\n";
                                        continue;
                                }
                            }
                            if (!empty($folder_id)) {
                                $msg_dict["file"]["data"]["folder"] = $folder_id;
                            }
                        } elseif ($x['type'] == '私聊') {
                            $text .= $message_file_name;
                            $msg_dict["text"]["data"]["message_type"] = "private";
                            $msg_dict["text"]["data"]["user_id"] = $x['type_id'];
                            $msg_dict["file"]["url"] = $file_url . "/upload_private_file";
                            $msg_dict["file"]["data"]["user_id"] = $x['type_id'];
                        } else {
                            continue;
                        }
                            $msg_dict["file"]["data"]["name"] = $message_file_name;
                            $msg_dict["file"]["data"]["file"] = $file_name;
                            $msg_dict["text"]["data"]["message"] = $text;
                            $send_status = false;
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
                                $send_status = true;
                                $log .= $today . " 成功 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . $message_file_name . "(" . $file_name . ")"
                                . "\n";
                            } else {
                                $log .= $today . " 失败 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . $message_file_name . "(" . $file_name . ")"
                                . "\n";
                            }

                            usleep(250000);
                        }
                        if ($send_status) {
                            $next = strtotime($today) + 86400 + $x['hour'] * 3600;
                            $m->query("UPDATE `" . DB_PREFIX . "weltolk_backup_qq_target` SET `nextdo` = '{$next}' WHERE `id` = '{$x['id']}'");
                        }
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
            } else {
                $next = strtotime($today) + $x['hour'] * 3600;
                $m->query("UPDATE `" . DB_PREFIX . "weltolk_backup_qq_target` SET `nextdo` = '{$next}' WHERE `id` = '{$x['id']}'");
            }
        } else {
            $next = strtotime($today) + $x['hour'] * 3600;
            $m->query("UPDATE `" . DB_PREFIX . "weltolk_backup_qq_target` SET `nextdo` = '{$next}' WHERE `id` = '{$x['id']}'");
        }
    }

    unlink($file_name);
    $log = trim($log);
    if (empty($log)) {
        return option::get('weltolk_backup_qq_log');
    } else {
        option::set('weltolk_backup_qq_log', $log);
        return $log;
    }
}
