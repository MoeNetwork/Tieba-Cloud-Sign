<?php
/* SMTP Class
 * Example:
 * $x = new SMTP('smtp.qq.com',25,true,'kenvix@qq.com','*************');
 * $x->send('God.Kenvix <kenvix@vip.qq.com>','God.Kenvix <kenvix@qq.com>','f','fff');
 */

class SMTP {
    public $smtp_port;
    public $time_out;
    public $host_name;
    public $log_file;
    public $part_boundary = '--PART-BOUNDARY-ID-WRG11-Y4RD1-5AS1D-RE4D1-AF1EG---';
    public $relay_host;
    public $debug;
    public $auth;
    public $user;
    public $pass;
    public $sock;
    public $log;
    public $error;
    public $att = array(); //附件内容
    public $ssl = false;

    public function __construct($relay_host = '', $smtp_port = 25, $auth = false, $user, $pass , $ssl = false) {
        $this ->debug = false;
        $this ->smtp_port = $smtp_port;
        if ($ssl == true) {
            $this->ssl = true;
            $relay_host = 'ssl://' . $relay_host;
        }
        $this ->relay_host = $relay_host;
        $this ->time_out = 30;
        $this ->auth = $auth;
        $this ->user = $user;
        $this ->pass = $pass;
        $this ->host_name = "localhost";
        $this ->log_file = "";
    }

    /**
     * 添加一个附件
     * @param string $name 文件名
     * @param string $value 文件内容
     */ 
    public function addatt($name , $value = '') {
        $this->att[$name] = $value;
    }

    public function send($to, $from, $subject = "", $body = "", $fromname = "贴吧云签到", $reply = '', $cc = "", $bcc = "", $additional_headers = "") {
        if (empty($reply)) {
            $reply = $from;
        }
        $header = "";
        $mail_from = $this ->get_address($this ->strip_comment($from));
        $from = "=?UTF-8?B?".base64_encode($fromname)."?= " . "<$from>";
        $body = mb_ereg_replace("(^|(\r\n))(\\.)", "\\1.\\3", $body);
        $header .= "MIME-Version:1.0\r\n";
        $header .= 'Content-Type: multipart/mixed; boundary="'.$this->part_boundary.'"' . "\r\n";
        $header .= "To: " . $to . "\r\n";
        if ($cc!="") $header .= "Cc: " . $cc . "\r\n";
        $header .= "From: " . $from . "\r\n";
        $header .= "Subject: " . "=?UTF-8?B?".base64_encode($subject)."?= " . "\r\n";
        $header .= $additional_headers;
        $header .= "Date: " . date("r") . "\r\n";
        $header .= 'Reply-To: ' . $reply . "\r\n";
        $header .= "Content-Transfer-Encoding: base64\r\n";
        list($msec, $sec) = explode(" ", microtime());
        $header .= "Message-ID: <" . date("YmdHis", $sec) . "." . ($msec*1000000) . "." . $mail_from . ">\r\n";
        $TO = explode(",", $this ->strip_comment($to));
        if ($cc!="") $TO = array_merge($TO, explode(",", $this ->strip_comment($cc)));
        if ($bcc!="") $TO = array_merge($TO, explode(",", $this ->strip_comment($bcc)));
        $sent = true;
        foreach ($TO as $rcpt_to) {
            $rcpt_to = $this ->get_address($rcpt_to);
            if (!$this ->smtp_sockopen($rcpt_to)) {
                $this ->log_write("Error: Cannot send email to [ " . $rcpt_to . " ] (Step 1)<br/>" . $this->error);
                $sent = false;
                continue;
            }
            if ($this ->smtp_send($this ->host_name, $mail_from, $rcpt_to, $header, $body)) {
                $this ->log_write("邮件已成功发送到 [" . $rcpt_to . "]\n");
            } else {
                $this ->log_write("Error: Cannot send email to [ " . $rcpt_to . " ] (Step 2)<br/>" . $this->error);
                $sent = false;
            }
            fclose($this ->sock);
        }
        return $sent;
    }

    private function smtp_send($helo, $from, $to, $header, $body = "") {
        if (!$this ->smtp_putcmd("HELO", $helo)) return $this ->smtp_error("sending HELO command");
        if ($this ->auth) {
            if (!$this ->smtp_putcmd("AUTH LOGIN", base64_encode($this ->user))) return $this ->smtp_error("sending HELO command");
            if (!$this ->smtp_putcmd("", base64_encode($this ->pass))) return $this ->smtp_error("sending HELO command");
        }
        if (!$this ->smtp_putcmd("MAIL", "FROM:<" . $from . ">")) return $this ->smtp_error("sending MAIL FROM command");
        if (!$this ->smtp_putcmd("RCPT", "TO:<" . $to . ">")) return $this ->smtp_error("sending RCPT TO command");
        if (!$this ->smtp_putcmd("DATA")) return $this ->smtp_error("sending DATA command");
        if (!$this ->smtp_message($header)) return $this ->smtp_error("sending head message");
        if (!$this ->smtp_sendbody($body)) return $this ->smtp_error("sending body message");
        if (!$this ->smtp_sendatt()) return $this ->smtp_error("sending attachments message");
        if (!$this ->smtp_sendend()) return $this ->smtp_error("sending end message");
        if (!$this ->smtp_eom()) return $this ->smtp_error("sending <CR><LF>.<CR><LF> [EOM]");
        if (!$this ->smtp_putcmd("QUIT")) return $this ->smtp_error("sending QUIT command");
        return true;
    }

    private function smtp_sockopen($address) {
        if ($this ->relay_host=="") return $this ->smtp_sockopen_mx($address); else return $this ->smtp_sockopen_relay();
    }

    private function smtp_sockopen_relay() {
        $this ->log_write("Trying to " . $this ->relay_host . ":" . $this ->smtp_port . "\n");
        $this ->sock = @fsockopen($this ->relay_host, $this ->smtp_port, $errno, $errstr, $this ->time_out);
        if (!($this ->sock && $this ->smtp_ok())) {
            $this ->log_write("Error: Cannot connenct to relay host " . $this ->relay_host . "\n");
            $this ->log_write("Error: " . $errstr . " (" . $errno . ")\n");
            return false;
        }
        $this ->log_write("Connected to relay host " . $this ->relay_host . "\n");
        return true;;
    }

    private function smtp_sockopen_mx($address) {
        $domain = ereg_replace("^.+@([^@]+)$", "\\1", $address);
        if (!@getmxrr($domain, $MXHOSTS)) {
            $this ->log_write("Error: Cannot resolve MX \"" . $domain . "\"\n");
            return false;
        }
        foreach ($MXHOSTS as $host) {
            $this ->log_write("Trying to " . $host . ":" . $this ->smtp_port . "\n");
            $this ->sock = @fsockopen($host, $this ->smtp_port, $errno, $errstr, $this ->time_out);
            if (!($this ->sock && $this ->smtp_ok())) {
                $this ->log_write("Warning: Cannot connect to mx host " . $host . "\n");
                $this ->log_write("Error: " . $errstr . " (" . $errno . ")\n");
                continue;
            }
            $this ->log_write("Connected to mx host " . $host . "\n");
            return true;
        }
        $this ->log_write("Error: Cannot connect to any mx hosts (" . implode(", ", $MXHOSTS) . ")\n");
        return false;
    }

    private function smtp_message($header) {
        fputs($this ->sock, $header . "\r\n");
        $this ->smtp_debug("> " . str_replace("\r\n", "\n" . "> ", $header . "\n>"));
        return true;
    }

    private function smtp_sendbody($body) {
        $head  = "\r\n\r\n" . '--' .  $this->part_boundary;
        $head .= "\r\n" . 'Content-Type: text/html; charset="utf-8"';
        $head .= "\r\n" . 'Content-Transfer-Encoding: base64';
        $head .= "\r\n\r\n" . base64_encode($body);
        return fputs($this ->sock, $head . "\r\n");
    }

    private function smtp_sendatt() {
        $head = '';
        foreach ($this->att as $n => $v) {
            $head .= "\r\n\r\n" . '--' .  $this->part_boundary;
            $head .= "\r\n" . 'Content-Type: ' . get_mime(get_extname($n)) . '; charset="utf-8"; name="=?UTF-8?B?'.base64_encode($n).'?= "';
            $head .= "\r\n" . 'Content-Disposition: attachment; filename="=?UTF-8?B?'.base64_encode($n).'?= "';
            $head .= "\r\n" . 'Content-Transfer-Encoding: base64';
            $head .= "\r\n\r\n" . base64_encode($v);
        }
        return fputs($this ->sock, $head . "\r\n");
    }

    private function smtp_sendend() {
        return fputs($this ->sock, "\r\n\r\n" . '--' . $this->part_boundary . '--');
    }

    private function smtp_eom() {
        fputs($this ->sock, "\r\n.\r\n");
        $this ->smtp_debug(". [EOM]\n");
        return $this ->smtp_ok();
    }

    private function smtp_ok() {
        $response = str_replace("\r\n", "", fgets($this ->sock, 512));
        $this ->smtp_debug($response . "\n");
        if (!mb_ereg("^[23]", $response)) {
            fputs($this ->sock, "QUIT\r\n");
            fgets($this ->sock, 512);
            $this ->log_write("Error: Remote host returned \"" . $response . "\"\n");
            return false;
        }
        return true;
    }

    private function smtp_putcmd($cmd, $arg = "") {
        if ($arg!="") {
            if ($cmd=="") $cmd = $arg; else
                $cmd = $cmd . " " . $arg;
        }
        fputs($this ->sock, $cmd . "\r\n");
        $this ->smtp_debug("> " . $cmd . "\n");

        return $this ->smtp_ok();
    }

    private function smtp_error($string) {
        $this ->error .= "<br/>Error: Error occurred while " . $string . ".<br/>";
        return false;
    }

    private function log_write($message) {
        $this->log .= '<br/>'.$message.'<br/>';
        return true;
    }

    private function strip_comment($address) {
        $comment = "\\([^()]*\\)";
        while (mb_ereg($comment, $address)) {
            $address = mb_ereg_replace($comment, "", $address);
        }
        return $address;
    }

    private function get_address($address) {
        $address = mb_ereg_replace("([ \t\r\n])+", "", $address);
        $address = mb_ereg_replace("^.*<(.+)>.*$", "\\1", $address);
        return $address;
    }

    public function smtp_debug($message) {
        if ($this ->debug) {
            return $message . "<br>";
        }
    }
}
