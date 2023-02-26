<?php

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}

function cron_weltolk_sign_qq()
{
    require_once "websocketclass.php";

    global $m, $i;
    if ($i['opt']['core_version'] >= 4.0) {
        $zt = 'latest';
    } else {
        $zt = 'lastdo';
    }
    $limit = option::get('weltolk_sign_qq_limit');
    $date = date("Y-m-j", strtotime("-1 day"));
    $now = time();
    $hour = date('H');
    $y = $m->query("SELECT * FROM `" . DB_PREFIX . "weltolk_sign_qq_target` WHERE `nextdo` <= '{$now}' LIMIT {$limit}");
    while ($x = $m->fetch_array($y)) {
        $is_open = option::uget('weltolk_sign_qq_enable', $x['uid']);
        if (!$is_open) {
            option::uset('weltolk_sign_qq_enable', 'on', $x['uid']);
            $is_open = true;
        } else {
            if ($is_open == "on") {
                $is_open = true;
            } elseif ($is_open == "off") {
                $is_open = false;
            }
        }
        if ($is_open) {
            if ($hour >= $x['hour']) {
                $msg_body = "";
                $query = $m->query("SELECT * FROM  `" . DB_NAME . "`.`" . DB_PREFIX . "tieba` WHERE `uid`=" . $x['uid']);
                $tieba_count = 0;
                while ($tieba = $m->fetch_array($query)) {
                    $tieba_count++;
                    $status = true;
                    if ((date("Y-m-") . $tieba[$zt] == date("Y-m-j", strtotime("-1 day"))) || empty($tieba[$zt])) {
                        $style_prefix = '--';
                        $status_str = '还未签到';
                        $msg = '该贴吧尚未签到！';
                    } elseif (!empty($tieba['no'])) {
                        // 忽略
                        $status = false;
//                    $style_prefix = '**';
//                    $status_str = '签到忽略';
//                    $msg = '您设置了忽略此贴吧的签到';
                    } elseif ($tieba['status'] == 0) {
                        // 忽略
                        $status = false;
//                    $style_prefix = '++';
//                    $status_str = '签到成功';
//                    $msg = '-';
                    } else {
                        $style_prefix = '!!';
                        $status_str = '签到失败';
                        $msg = $tieba['last_error'];
                    }

                    if ($status) {
                        $msg_body .= $style_prefix
                            . ' ' . ($i['user']['baidu'][$tieba['pid']])
                            . ' 账号的 ' . $tieba['tieba'] . ' 吧: '
                            . $status_str . "\n"
                            . $style_prefix . ' 详细信息: ' . $msg
                            . "\n\n";
                    }
                }
                if (empty($tieba_count)) {
                    $msg_body .= "没有关注的贴吧！请添加关注贴吧后在【云签到设置和日志】中刷新贴吧列表！";
                }
                if (empty($msg_body)) {
                    $msg_body .= "今日全部贴吧签到成功";
                }

                $msg_dict = [];
                if (substr_count($msg_body, "\n\n") <= 20) {
                    $msg_cache = $date;
                    $msg_cache .= "\n\n" . $msg_body . "\n\n第1/1页";
                    $msg_dict = [
                        $msg_cache,
                    ];
                } else {
                    $msg_array = explode("\n\n", $msg_body);
                    $max_page = ceil($msg_array / 20);
                    $page = 1;
                    $msg_cache = $date;
                    foreach ($msg_array as $keyyy => $valueee) {
                        if ($keyyy % 20 == 0 && $keyyy != 0) {
                            $msg_cache .=
                                "\n\n第" . $page . "/" . $max_page . "页";
                            $msg_dict[] = $msg_cache;
                            $page += 1;
                            $msg_cache = $date;
                        } elseif ($keyyy == (count($msg_array) - 1) && $keyyy % 20 != 0) {
                            $msg_cache .=
                                "\n\n第" . $page . "/" . $max_page . "页";
                            $msg_dict[] = $msg_cache;
                        } else {
                            $msg_cache .= "\n\n" . $valueee;
                        }
                    }
                }


                $y2 = $m->query("SELECT * FROM `" . DB_PREFIX . "weltolk_sign_qq_connect` WHERE `id` = '{$x['connect_id']}' LIMIT 1");
                $x2 = $m->fetch_array($y2);
                $sign = "sign" . mt_rand(1000, 9999);

                if ($x2['client'] == 'go-cqhttp') {
                    $access_token = $x2['access_token'];

                    if ($x2['connect_type'] == '正向WebSocket') {
                        $headers = [];
                        if (empty($access_token)) {
                        } else {
                            $headers = ["Authorization: Bearer " . $access_token];
                        }

                        $send = [
                            "action" => "send_msg",
                            "params" => [

                            ],
                            "echo" => $sign,
                        ];

                        if ($x['type'] == '群') {
                            $send["params"]["message_type"] = "group";
                            $send["params"]["group_id"] = $x['type_id'];
                        } elseif ($x['type'] == '私聊') {
                            $send["params"]["message_type"] = "private";
                            $send["params"]["user_id"] = $x['type_id'];
                        } else {
                            continue;
                        }

                        $send_status = false;
                        foreach ($msg_dict as $msg_dict_i) {
                            $send["params"]["message"] = $msg_dict_i;

                            try {
                                $send_json = json_encode($send);
                                $ws = new WebSocketClient($x2["address"], $headers);
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
                                    $log .= date('Y-m-d') . " 成功 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                        . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                        . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . $send["params"]["message"]
                                        . "\n";
                                } else {
                                    $log .= date('Y-m-d') . " 失败 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                        . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                        . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . $send["params"]["message"]
                                        . "\n";
                                }
                            } catch (\Exception $e) {
                                echo "错误: ";
                                var_dump($e->__toString());
                            }
                            usleep(250000);
                        }
                        if ($send_status) {
                            $next = strtotime(date('Y-m-d')) + 86400 + $x['hour'] * 3600;
                            $m->query("UPDATE `" . DB_PREFIX . "weltolk_sign_qq_target` SET `nextdo` = '{$next}' WHERE `id` = '{$x['id']}'");
                        }
                    } elseif ($x2['connect_type'] == 'HTTP API') {
                        $url = substr($x2["address"], -1) == "/"
                            ? substr($x2["address"], 0, -1)
                            : $x2["address"];
                        $url .= "/send_msg";

                        $headers = [];
                        if (empty($access_token)) {
                        } else {
                            $headers = [
                                "Content-Type" => "application/json",
                                "Authorization" => "Bearer " . $access_token,
                            ];
                        }

                        $send = [

                            // go-cqhttp HTTP API post 未支持echo
//                        "echo" => $sign,
                        ];

                        if ($x['type'] == '群') {
                            $send["message_type"] = "group";
                            $send["group_id"] = $x['type_id'];
                        } elseif ($x['type'] == '私聊') {
                            $send["message_type"] = "private";
                            $send["user_id"] = $x['type_id'];
                        } else {
                            continue;
                        }

                        $send_status = false;
                        foreach ($msg_dict as $msg_dict_i) {
                            $send["message"] = $msg_dict_i;

                            $c = new wcurl($url, $headers);
                            $c->setTimeOut(5000);

                            $res = $c->post($send);
                            $res = json_decode($res, true);
                            if (
                                $res['retcode'] == 0
                                && $res['status'] == 'ok'
                                // go-cqhttp HTTP API post 未支持echo
//                        && $res['echo'] == $sign
                            ) {
                                $send_status = true;
                                $log .= date('Y-m-d') . " 成功 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                    . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                    . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . $send["params"]["message"]
                                    . "\n";
                            } else {
                                $log .= date('Y-m-d') . " 失败 " . $x2["client"] . " 客户端通过 " . $x2["connect_type"] . " 方式给 "
                                    . $x2["address"] . " 地址推送access_token为 " . $x2["access_token"]
                                    . " 的消息: " . $x["type"] . " " . $x["type_id"] . " " . $send["params"]["message"]
                                    . "\n";
                            }

                            usleep(250000);
                        }
                        if ($send_status) {
                            $next = strtotime(date('Y-m-d')) + 86400 + $x['hour'] * 3600;
                            $m->query("UPDATE `" . DB_PREFIX . "weltolk_sign_qq_target` SET `nextdo` = '{$next}' WHERE `id` = '{$x['id']}'");
                        }
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
            } else {
                $next = strtotime(date('Y-m-d')) + $x['hour'] * 3600;
                $m->query("UPDATE `" . DB_PREFIX . "weltolk_sign_qq_target` SET `nextdo` = '{$next}' WHERE `id` = '{$x['id']}'");
            }
        } else {
            $next = strtotime(date('Y-m-d')) + $x['hour'] * 3600;
            $m->query("UPDATE `" . DB_PREFIX . "weltolk_sign_qq_target` SET `nextdo` = '{$next}' WHERE `id` = '{$x['id']}'");
        }
    }
    $log = trim($log);
    if (empty($log)) {
        return option::get('weltolk_sign_qq_log');
    } else {
        option::set('weltolk_sign_qq_log', $log);
        return $log;
    }
}
