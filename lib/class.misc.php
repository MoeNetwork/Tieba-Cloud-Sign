<?php

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}

/**
 * 其他功能类
 */

class misc
{
    /**
     * 快捷发送一封邮件
     * @param string $to 收件人
     * @param string $sub 邮件主题
     * @param string $msg 邮件内容(HTML)
     * @param array $att 附件，每个键为文件名称，值为附件内容（可以为二进制文件），例如array('a.txt' => 'abcd' , 'b.png' => file_get_contents('x.png'))
     * @return bool 成功:true 失败：错误消息
     */
    public static function mail($to, $sub = '无主题', $msg = '无内容', $att = array())
    {
        if (defined("SAE_MYSQL_DB") && class_exists('SaeMail')) {
            $mail = new SaeMail();
            $options = array(
                'from'          => option::get('mail_name'),
                'to'            => $to,
                'smtp_host'     => option::get('mail_host'),
                'smtp_port'     => option::get('mail_port'), //端口号（默认为25，一般不需修改）
                'smtp_username' => option::get('mail_smtpname'), //smtp账号
                'smtp_password' => option::get('mail_smtppw'), //smtp密码
                'subject'       => $sub, //邮件标题
                'content'       => $msg, //邮件内容
                'content_type'  => 'HTML' //HTML格式发送
            );
            $mail->setOpt($options);
            $ret = $mail->send();
            if ($ret === false) {
                return 'Mail Send Error: #' . $mail->errno() . ' - ' . $mail->errmsg();
            } else {
                return true;
            }
        } else {
            $From = option::get('mail_name');
            if (option::get('mail_mode') == 'SMTP') {
                $Host = option::get('mail_host');
                $Port = intval(option::get('mail_port'));
                $SMTPAuth = (bool) option::get('mail_auth');
                $Username = option::get('mail_smtpname');
                $Password = option::get('mail_smtppw');
                $Nickname = option::get('mail_yourname');
                $Secure = option::get('mail_secure');
                $mail = new SMTP($Host, $Port, $SMTPAuth, $Username, $Password, $Secure);
                $mail->att = $att;
                if ($mail->send($to, $From, $sub, $msg, $Nickname)) {
                    return true;
                } else {
                    return $mail->log;
                }
            } else {
                $header  = "MIME-Version:1.0\r\n";
                $header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                $header .= "To: " . $to . "\r\n";
                $header .= "From: " . $From . "\r\n";
                $header .= "Subject: " . $sub . "\r\n";
                $header .= 'Reply-To: ' . $From . "\r\n";
                $header .= "Date: " . date("r") . "\r\n";
                $header .= "Content-Transfer-Encoding: base64\r\n";
                return mail($to, $sub, base64_encode($msg), $header);
            }
        }
    }

    /**
     * 通过UID判断某个用户是不是VIP
     * @param string $uid UID
     * @return bool VIP=true
     */
    public static function isvip($uid)
    {
        global $m;
        $x = $m->once_fetch_array("SELECT * FROM `" . DB_PREFIX . "users` WHERE `id` = '{$uid}';");
        if ($x['role'] == 'vip' || $x['role'] == 'admin') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 通过UID获得指定用户的贴吧数据表
     * @param string $uid UID
     */
    public static function getTable($uid)
    {
        global $m;
        $x = $m->once_fetch_array("SELECT * FROM `" . DB_PREFIX . "users` WHERE `id` = '{$uid}';");
        return $x['t'];
    }

    /**
     * 寻找已缓存的贴吧 FID
     * @param string $kw 贴吧名
     * @return string|boolean FID，如果没有缓存则返回false
     */
    public static function findFid($kw)
    {
        global $i;
        global $m;
        foreach ($i['table'] as $v) {
            $r = $m->once_fetch_array("SELECT * FROM `" . DB_PREFIX . $v . "` WHERE `tieba` = '{$kw}' AND `fid` IS NOT NULL LIMIT 1");
            if (!empty($r['fid'])) {
                return $r['fid'];
                break;
            }
        }
        return false;
    }

    /**
     * 批量设置贴吧 FID
     * @param string $kw 贴吧名
     * @param string $fid FID
     */

    public static function mSetFid($kw, $fid)
    {
        global $m,$i;
        if (empty($fid)) {
            return false;
        }
        foreach ($i['table'] as $v) {
            $r = $m->query("UPDATE  `" . DB_PREFIX . $v . "` SET  `fid` =  '{$fid}' WHERE  `" . DB_PREFIX . $v . "`.`tieba` = '{$kw}';");
        }
    }

    /**
     * 得到贴吧 FID
     * @param string $kw 贴吧名
     * @return string FID
     */

    public static function getFid($kw)
    {
        global $m;
        $f  = misc::findFid($kw);
        if ($f) {
            return $f;
        } else {
            $ch = new wcurl('http://tieba.baidu.com/f/commit/share/fnameShareApi?ie=utf-8&fname=' . urlencode($kw), array('User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.1 Mobile/15E148 Safari/604.1','Referer: http://tieba.baidu.com/'));
            $r = json_decode($ch->exec(), true);
            return intval($r['no']) === 0 ? $r['data']['fid'] : false;
        }
    }

    /**
     * 得到TBS
     */

    public static function getTbs($uid, $bduss)
    {
        $ch = new wcurl('http://tieba.baidu.com/dc/common/tbs');
        $ch->addcookie("BDUSS=" . $bduss);
        $x = json_decode($ch->exec(), true);
        return $x['tbs'];
    }

    /**
     * 对输入的数组添加客户端验证代码（tiebaclient!!!）
     * @param array $data 数组
     */
    public static function addTiebaSign(&$data, $withClientType = true)
    {
        $data["_client_version"] = "12.22.1.0";
        if ($withClientType) {
            $data["_client_type"] = "4";
        }
        ksort($data);
        $x = '';
        foreach ($data as $k => $v) {
            $x .= $k . '=' . $v;
        }
        $data['sign'] = strtoupper(md5($x . 'tiebaclient!!!'));
    }

    /**
     * 得到BDUSS
     * @param int|string $pid 用户PID
     * @param bool $withStoken 是否带有stoken
     */
    public static function getCookie($pid, $withStoken = false)
    {
        global $m;
        if (empty($pid)) {
            return false;
        }
        $temp = $m->fetch_array($m->query("SELECT * FROM `" . DB_NAME . "`.`" . DB_PREFIX . "baiduid` WHERE `id` = {$pid} LIMIT 1"));
        return $withStoken ? ["bduss" => $temp['bduss'], "stoken" => $temp['stoken']] : $temp['bduss'];
    }

    /**
     * 50贴吧客户端一键签到
     */
    public static function DoSign_Onekey($uid, $kw, $id, $pid, $fid, $ck)
    {
        $ch = new wcurl('http://c.tieba.baidu.com/c/c/forum/msign', array(
            'User-Agent: bdtb for Android 6.5.8'
        ));
        $ch->addcookie(array('BDUSS' => $ck));
        $temp = array(
            'fid' => $fid,
            'kw' => $kw,
            'tbs' => misc::getTbs($uid, $ck)
        );
        self::addTiebaSign($temp);
        return $ch->post($temp);
    }

    /**
     * 手机网页签到
     */
    public static function DoSign_Mobile($uid, $kw, $id, $pid, $fid, $ck)
    {
        //没问题了
        $ch = new wcurl('http://tieba.baidu.com/mo/q/sign?tbs=' . misc::getTbs($uid, $ck) . '&kw=' . urlencode($kw) . '&is_like=1&fid=' . $fid, array('User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.1 Mobile/15E148 Safari/604.1','Referer: http://tieba.baidu.com/f?kw=' . $kw , 'Host: tieba.baidu.com','X-Forwarded-For: ' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255), 'Origin: http://tieba.baidu.com', 'Connection: Keep-Alive'));
        $ch->addcookie(array('BDUSS' => $ck,'BAIDUID' => strtoupper(md5(time()))));
        return $ch->exec();
    }

    /**
     * 网页签到
     */
    public static function DoSign_Default($uid, $kw, $id, $pid, $fid, $ck)
    {
        global $m,$today;
        $cookie = array('BDUSS' => $ck,'BAIDUID' => strtoupper(md5(time())));
        $ch = new wcurl('http://tieba.baidu.com/mo/m?kw=' . urlencode($kw) . '&fid=' . $fid, array('User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.1 Mobile/15E148 Safari/604.1','Referer: http://wapp.baidu.com/','Content-Type: application/x-www-form-urlencoded'));
        $ch->addcookie($cookie);
        $s  = $ch->exec();
        $ch->close();
        preg_match('/\<td style=\"text-align:right;\"\>\<a href=\"(.*)\"\>签到\<\/a\>\<\/td\>\<\/tr\>/', $s, $s);
        if (isset($s[1])) {
            $ch = new wcurl(
                'http://tieba.baidu.com' . $s[1],
                array(
                    'Accept: text/html, application/xhtml+xml, */*',
                    'Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3',
                    'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.1 Mobile/15E148 Safari/604.1'
                )
            );
            $ch->addcookie($cookie);
            $ch->exec();
            $ch->close();
//临时判断解决方案
            $ch = new wcurl('http://tieba.baidu.com/mo/m?kw=' . urlencode($kw) . '&fid=' . $fid, array('User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.1 Mobile/15E148 Safari/604.1','Referer: http://wapp.baidu.com/','Content-Type: application/x-www-form-urlencoded'));
            $ch->addcookie($cookie);
            $s = $ch->exec();
            $ch->close();
//如果找不到这段html则表示没有签到则stripos()返回false，同时is_bool()返回true，最终返回false
            return !is_bool(stripos($s, '<td style="text-align:right;"><span >已签到</span></td>'));
        } else {
            return true;
        }
    }

    /**
     * 客户端签到
     */
    public static function DoSign_Client($uid, $kw, $id, $pid, $fid, $ck)
    {
        $ch = new wcurl('http://c.tieba.baidu.com/c/c/forum/sign');
        $ch->addcookie("BDUSS=" . $ck);
        $temp = array(
            'BDUSS' => misc::getCookie($pid),
            'fid' => $fid,
            'kw' => $kw,
            'tbs' => misc::getTbs($uid, $ck)
        );
        self::addTiebaSign($temp);
        return $ch->post($temp);
    }

    /**
     * 对一个贴吧执行完整的签到任务
     */
    public static function DoSign_All($uid, $kw, $id, $table, $sign_mode, $pid, $fid)
    {
        global $m;
        $again_error_id     = 160002;
//重复签到错误代码
        $again_error_id_2   = 1101;
//特殊的重复签到错误代码！！！签到过快=已签到
        $again_error_id_3   = 1102;
//特殊的重复签到错误代码！！！签到过快=已签到
        $status_succ    = false;
        $ck = misc::getCookie($pid);
        $kw = addslashes($kw);
        $today = date('d');
        if (empty($fid)) {
            $fid = misc::getFid($kw);
            $m->query("UPDATE  `" . DB_PREFIX . $table . "` SET  `fid` =  '{$fid}' WHERE  `" . DB_PREFIX . $table . "`.`id` = '{$id}';", true);
        }

        //dump(json_decode(self::DoSign_Client($uid,$kw,$id,$pid,$fid,$ck),true),true);die;

        if (!empty($sign_mode) && in_array('1', $sign_mode) && $status_succ === false) {
            $r = self::DoSign_Client($uid, $kw, $id, $pid, $fid, $ck);
            $v = json_decode($r, true);
            if ($v != $r && $v != null) {
            //decode失败时会直接返回原文或NULL
                if (empty($v['error_code']) || $v['error_code'] == $again_error_id) {
                    $status_succ = true;
                } else {
                    $error_code = $v['error_code'];
                    $error_msg  = $v['error_msg'];
                }
            }
        }

        if (!empty($sign_mode) && in_array('3', $sign_mode) && $status_succ === false) {
            $r = self::DoSign_Mobile($uid, $kw, $id, $pid, $fid, $ck);
            $v = json_decode($r, true);
            if ($v != $r && $v != null) {
            //decode失败时会直接返回原文或NULL
                if (empty($v['no']) || $v['no'] == $again_error_id_2 || $v['no'] == $again_error_id_3) {
                    $status_succ = true;
                } else {
                    $error_code  = $v['no'];
                    $error_msg   = $v['error'];
                }
            }
        }

        if (!empty($sign_mode) && in_array('2', $sign_mode) && $status_succ === false) {
            if (self::DoSign_Default($uid, $kw, $id, $pid, $fid, $ck) === true) {
                $status_succ = true;
            }
        }

        if ($status_succ === true) {
            $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . $table . "` SET  `latest` =  '" . $today . "',`status` =  '0',`last_error` = NULL WHERE `" . DB_PREFIX . $table . "`.`id` = '{$id}'", true);
        } else {
            $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . $table . "` SET  `latest` =  '" . $today . "',`status` =  '" . $error_code . "',`last_error` = '" . $error_msg . "' WHERE `" . DB_PREFIX . $table . "`.`id` = '{$id}'", true);
        }

        usleep(option::get('sign_sleep') * 1000);
    }

    /**
     * 执行一个表的签到任务
     * @param string $table 表
     */
    public static function DoSign($table)
    {
        global $m;
        $sign_mode = unserialize(option::get('sign_mode'));
        $today = date('d');
        if (date('H') <= option::get('sign_hour')) {
            return option::get('sign_hour') . '点时忽略签到';
        }

        $limit = option::get('cron_limit');
//处理所有未签到的贴吧
        if ($limit == 0) {
            $q  = array();
            $qs = $m->query("SELECT * FROM  `" . DB_NAME . "`.`" . DB_PREFIX . $table . "` WHERE `no` = 0 AND `latest` != '" . $today . "'");
            while ($qss = $m->fetch_array($qs)) {
                $q[] = array(
                    'id'     => $qss['id'],
                    'uid'    => $qss['uid'],
                    'pid'    => $qss['pid'],
                    'fid'    => $qss['fid'],
                    'tieba'  => $qss['tieba'],
                    'no'     => $qss['no'],
                    'status' => $qss['status'],
                    'latest' => $qss['latest'],
                    'last_error'    => $qss['last_error']
                );
            }
            shuffle($q);
        } else {
            $q = rand_row(DB_PREFIX . $table, 'id', $limit, "`no` = 0 AND `latest` != '{$today}'", true);
        }

        foreach ($q as $x) {
            self::DoSign_All($x['uid'], $x['tieba'], $x['id'], $table, $sign_mode, $x['pid'], $x['fid']);
        }
    }

    /**
     * 执行一个表的签到重試任务
     * @param string $table 表
     */
    public static function DoSign_retry($table)
    {
        global $m;
        $today = date('d');
        if (date('H') <= option::get('sign_hour')) {
            return option::get('sign_hour') . '点时忽略签到';
        }

        $x = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . $table . "` WHERE `no` = 0 AND `latest` != {$today} "));
        if ($x['c'] == 0) {
            $limit = option::get('cron_limit');
            $sign_mode = unserialize(option::get('sign_mode'));
            $sign_again = unserialize(option::get('cron_sign_again'));
            $retry_max  = option::get('retry_max');
            $q = array();
            $x = array();
        //重新尝试签到出错的贴吧
            if ($limit == 0) {
                if ($retry_max == '0' || ($sign_again['lastdo'] == $today && $sign_again['num'] <= $retry_max && $retry_max != '-1')) {
                    $qs = $m->query("SELECT * FROM  `" . DB_NAME . "`.`" . DB_PREFIX . $table . "` WHERE `no` = 0 AND `status` IN (340011,2280007,110001,1989004,255)");
                    while ($qss = $m->fetch_array($qs)) {
                        $q[] = array(
                            'id'     => $qss['id'],
                            'uid'    => $qss['uid'],
                            'pid'    => $qss['pid'],
                            'fid'    => $qss['fid'],
                            'tieba'  => $qss['tieba'],
                            'no'     => $qss['no'],
                            'status' => $qss['status'],
                            'latest' => $qss['latest'],
                            'last_error'    => $qss['last_error']
                        );
                    }
                    shuffle($q);
                }
            } else {
                if ($retry_max == '0' || ($sign_again['lastdo'] == $today && $sign_again['num'] <= $retry_max && $retry_max != '-1')) {
                    $q = rand_row(DB_PREFIX . $table, 'id', $limit, "`no` = 0 AND `status` IN (340011,2280007,110001,1989004,255) AND `latest` = '{$today}'", true);
                }
            }

            foreach ($q as $x) {
                self::DoSign_All($x['uid'], $x['tieba'], $x['id'], $table, $sign_mode, $x['pid'], $x['fid']);
            }
        }
    }

    /**
     * 登录百度
     * @param string $bd_name 百度用户名
     * @param string $bd_pw百度密码
     * @param string $verifycode 验证码
     * @param string $vcodestr 验证字符
     * @return array [0成功|-1网络请求失败|-2json解析失败|-3表示需要验证码或验证码错误|2表示登陆失败|其他为百度提供的错误代码, 成功为BDUSS|需要验证码则返回vcodestr|其他错误返回百度提供的错误信息, 如果登陆成功，返回百度用户名|如果需要验证码，则此处返回验证图片地址 ]
     */
    public static function loginBaidu($bd_name, $bd_pw, $verifycode = '', $vcodestr = '')
    {
        $x = new wcurl('http://c.tieba.baidu.com/c/s/login');
        $p = array(
                'passwd'      => base64_encode($bd_pw),
                'timestamp'   => time() . '156',
                'un'          => $bd_name,
        );
        if (!empty($verifycode) && !empty($vcodestr)) {
            $p['vcode'] = $verifycode;
            $p['vcode_md5'] = $vcodestr;
        }
        self::addTiebaSign($p);
        if (!$data = $x->post($p)) {
            return array(-1, '网络请求失败');
        }
        if (!$v = json_decode($data, true)) {
            return array(-2, 'json解析失败');
        }
        if (!empty($v['user'])) {
            $md5pos = strpos($v['user']['BDUSS'], '|');
            if (!empty($md5pos)) {
                $bduss = substr($v['user']['BDUSS'], 0, $md5pos);
            } else {
                $bduss = $v['user']['BDUSS'];
            }
        }
        if ($v['error_code'] == '0') {
            return array(0, $bduss, $v['user']['name']);
        } else {
            switch ($v['error_code']) {
                case '5': //需要验证码或验证码输入错误
                case '6':
                    return array(-3, $v['anti']['vcode_md5'], $v['anti']['vcode_pic_url']);
                break;
                default: //其他错误
                    return array((int)$v['error_code'], $v['error_msg']);
                break;
            }
        }
    }

    /*
     * 获取指定pid用户userid
     */
    // out to date
    public static function getUserid($pid)
    {

        global $m;
        $ub  = $m->once_fetch_array("SELECT portrait FROM `" . DB_PREFIX . "baiduid` WHERE `id` = '{$pid}';");
        return self::getUseridByPortrait($ub['portrait']);
    }
    /*
     * 获取指定portrait的userid
     */
    // out to date
    public static function getUseridByPortrait($portrait)
    {

        $ur = getUserInfo($portrait, false);
        $userid = (isset($ur["no"]) && $ur["no"] === 0) ? $ur['data']['id'] : 0;
        return $userid;
    }
    /*
    * uid转旧版portrait
     */
    public static function uid2LegacyPortrait($uid)
    {

        //貌似对uid较大的新账号没啥效果?
        $stric = str_pad(dechex(trim($uid)), 8, 0, STR_PAD_LEFT);
        $sc = '';
        for ($i = 6; $i >= 0; $i -= 2) {
            $sc .= substr($stric, $i, 2);
        }
        return $sc;
    }
    /*
     * 获取指定pid
     */
    public static function getTieba($userid, $bduss, $pn)
    {

        $head = array();
        $head[] = 'Content-Type: application/x-www-form-urlencoded';
        $head[] = 'User-Agent: Mozilla/5.0 (SymbianOS/9.3; Series60/3.2 NokiaE72-1/021.021; Profile/MIDP-2.1 Configuration/CLDC-1.1 ) AppleWebKit/525 (KHTML, like Gecko) Version/3.0 BrowserNG/7.1.16352';
        $tl = new wcurl('http://c.tieba.baidu.com/c/f/forum/like', $head);
        $data = array(
            'BDUSS' => $bduss,
            'friend_uid' => $userid,
            'page_no' => $pn,
            'page_size' => 200,
        );
        self::addTiebaSign($data, false);
        $tl->addCookie(array('BDUSS' => $bduss));
        $tl->set(CURLOPT_RETURNTRANSFER, true);
        $rt = $tl->post($data);
        return $rt;
    }

    public static function getTieba2($bduss, $stoken, $pn = 1)
    {

        $tl = new wcurl("https://tieba.baidu.com/mg/o/getForumHome?st=0&pn={$pn}&rn=200", ['User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.102 Safari/537.36']);
        $tl->addCookie(array('BDUSS' => $bduss, 'STOKEN' => $stoken));
        return $tl->get();
    }

    /**
     * 扫描指定PID的所有贴吧
     * @param string $pid PID
     */
    public static function scanTiebaByPid($pid)
    {

        global $m;
        $cma    = $m->once_fetch_array("SELECT * FROM `" . DB_PREFIX . "baiduid` WHERE `id` = '{$pid}';");
        $uid    = $cma['uid'];
        $table  = self::getTable($uid);
        $tb     = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . $table . "` WHERE `uid` = {$uid}"));
        $bduss  = $cma['bduss'];
        $stoken = $cma['stoken'];
        $isvip  = self::isvip($uid);
        $pid    = $cma['id'];
        //$bid    = self::getUserid($pid);
        $o      = option::get('tb_max');
        $pn     = 1;
        $a      = 0;
        while (true) {
            //if (empty($bid)) break;
            $rc = self::getTieba2($bduss, $stoken, $pn);//fetch forum list //default 200 per page
            $rc = json_decode($rc, true);
            if (!$rc) {
                break;
            }
            $ngf = isset($rc["data"]["like_forum"]["list"]) ? $rc["data"]["like_forum"]["list"] : [];
            foreach ($ngf as $v) {
                if ($tb['c'] + $a >= $o && !empty($o) && !$isvip) {
                    break;
                }
                $vn  = addslashes(htmlspecialchars($v['forum_name']));
                $ist = $m->once_fetch_array("SELECT COUNT(id) AS `c` FROM `" . DB_NAME . "`.`" . DB_PREFIX . $table . "` WHERE `pid` = {$pid} AND `tieba` = '{$vn}';");
                if ($ist['c'] == 0) {
                    $a++;
                    $m->query("INSERT INTO `" . DB_NAME . "`.`" . DB_PREFIX . $table . "` (`pid`,`fid`, `uid`, `tieba`) VALUES ({$pid},'{$v['forum_id']}', {$uid}, '{$vn}');");
                }
            }
            $pn++;
            if ($pn > $rc["data"]["like_forum"]["page"]["total_page"]) {
                break;
            }
        }
    }

    /**
     * 扫描指定用户的所有贴吧并储存
     * @param UID，如果留空，表示当前用户的UID
     */
    public static function scanTiebaByUser($uid = '')
    {

        global $i;
        global $m;
        set_time_limit(0);
        if (empty($uid)) {
            $bduss = $i['user']['bduss'];
        } else {
            $bx = $m->query("SELECT * FROM `" . DB_PREFIX . "baiduid` WHERE `uid` = '{$uid}';");
            while ($by = $m->fetch_array($bx)) {
                $upid         = $by['id'];
                $bduss[$upid] = $by['bduss'];
            }
        }
        $n      = 0;
        foreach ($bduss as $pid => $ubduss) {
            $t = self::scanTiebaByPid($pid);
        }
    }

    /**
     * 获得二维码及sign
     */
    public static function get_login_qrcode()
    {

        $resp = ["sign" => null, "imgurl" => null];
        $get_qrcode = json_decode((new wcurl("https://passport.baidu.com/v2/api/getqrcode?lp=pc"))->get(), true);
        if (isset($get_qrcode["imgurl"]) && isset($get_qrcode["sign"])) {
            $resp = ["sign" => $get_qrcode["sign"], "imgurl" => $get_qrcode["imgurl"]];
        }
        return $resp;
    }
    public static function get_real_bduss($sign)
    {

        //status code
        //errno不等于0或1时需要要求更换二维码及sign
        //-1 更换二维码
        //0 进入下一步
        //1 无需操作
        //2 已确认
        $r = ["error" => 1, "bduss" => "", "stoken" => "", "msg" => ""];
        $response = (new wcurl("https://passport.baidu.com/channel/unicast?channel_id={$sign}&callback="))->get();
        if ($response) {
            $responseParse = json_decode(str_replace(array("(",")"), '', $response), true);
            if (!$responseParse["errno"]) {
                $channel_v = json_decode($responseParse["channel_v"], true);
                if ($channel_v["status"]) {
                    $r["error"] = -1;
                    $r["msg"] = "Continue";
                } else {
                    $s_bduss = json_decode(preg_replace("/'([^'']+)'/", '"$1"', str_replace("\\&", "&", (new wcurl('https://passport.baidu.com/v3/login/main/qrbdusslogin?bduss=' . $channel_v["v"]))->get())), true);
                    if ($s_bduss && $s_bduss["code"] === "110000") {
                        $r["error"] = 0;
                        $r["msg"] = "Success";
                        $r["bduss"] = $s_bduss["data"]["session"]["bduss"];
                        $r["stoken"] = self::qrlogin_parse_stoken($s_bduss["data"]["session"]["stokenList"])["tb"];
                    }
                }
            } else {
                $r["error"] = $responseParse["errno"];
            }
        } else {
            $r["error"] = -2;
            $r["msg"] = "Invalid QR Code";
        }
        return $r;
    }

    public static function qrlogin_parse_stoken(string $stokenList)
    {

        $tmpStokenList = [];
        foreach (json_decode(str_replace("&quot;", '"', $stokenList), true) as $stoken) {
            preg_match("/([\w]+)#(.*)/", $stoken, $tmpStoken);
            $tmpStokenList[$tmpStoken[1]] = $tmpStoken[2];
        }
        return $tmpStokenList;
    }
}
