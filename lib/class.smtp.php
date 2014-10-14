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
    public $relay_host;
    public $debug;
    public $auth;
    public $user;
    public $pass;
    public $sock;
    public $log;
    public $error;

    public function __construct($relay_host = '', $smtp_port = 25, $auth = false, $user, $pass) {
        $this ->debug = false;
        $this ->smtp_port = $smtp_port;
        $this ->relay_host = $relay_host;
        $this ->time_out = 30;
        $this ->auth = $auth;
        $this ->user = $user;
        $this ->pass = $pass;
        $this ->host_name = "localhost";
        $this ->log_file = "";
        $this ->sock = false;
    }

    public function send($to, $from, $subject = "", $body = "", $reply = '', $mailtype = 'HTML', $cc = "", $bcc = "", $additional_headers = "") {
        if (empty($reply)) {
            $reply = $from;
        }
        $header = "";
        $mail_from = $this ->get_address($this ->strip_comment($from));
        $body = mb_ereg_replace("(^|(\r\n))(\\.)", "\\1.\\3", $body);
        $header .= "MIME-Version:1.0\r\n";
        if ($mailtype=="HTML") $header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $header .= "To: " . $to . "\r\n";
        if ($cc!="") $header .= "Cc: " . $cc . "\r\n";
        $header .= "From: " . $from . "\r\n";
        $header .= "Subject: " . $subject . "\r\n";
        $header .= $additional_headers;
        $header .= "Date: " . date("r") . "\r\n";
        $header .= 'Reply-To: ' . $reply . "\r\n";
        $header .= "X-Mailer:By (PHP/" . phpversion() . ")\r\n";
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

    public function smtp_send($helo, $from, $to, $header, $body = "") {
        if (!$this ->smtp_putcmd("HELO", $helo)) return $this ->smtp_error("sending HELO command");
        if ($this ->auth) {
            if (!$this ->smtp_putcmd("AUTH LOGIN", base64_encode($this ->user))) return $this ->smtp_error("sending HELO command");
            if (!$this ->smtp_putcmd("", base64_encode($this ->pass))) return $this ->smtp_error("sending HELO command");
        }
        if (!$this ->smtp_putcmd("MAIL", "FROM:<" . $from . ">")) return $this ->smtp_error("sending MAIL FROM command");
        if (!$this ->smtp_putcmd("RCPT", "TO:<" . $to . ">")) return $this ->smtp_error("sending RCPT TO command");
        if (!$this ->smtp_putcmd("DATA")) return $this ->smtp_error("sending DATA command");
        if (!$this ->smtp_message($header, $body)) return $this ->smtp_error("sending message");
        if (!$this ->smtp_eom()) return $this ->smtp_error("sending <CR><LF>.<CR><LF> [EOM]");
        if (!$this ->smtp_putcmd("QUIT")) return $this ->smtp_error("sending QUIT command");
        return true;
    }

    public function smtp_sockopen($address) {
        if ($this ->relay_host=="") return $this ->smtp_sockopen_mx($address); else return $this ->smtp_sockopen_relay();
    }

    public function smtp_sockopen_relay() {
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

    public function smtp_sockopen_mx($address) {
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

    public function smtp_message($header, $body) {
        fputs($this ->sock, $header . "\r\n" . $body);
        $this ->smtp_debug("> " . str_replace("\r\n", "\n" . "> ", $header . "\n> " . $body . "\n> "));
        return true;
    }

    public function smtp_eom() {
        fputs($this ->sock, "\r\n.\r\n");
        $this ->smtp_debug(". [EOM]\n");
        return $this ->smtp_ok();
    }

    public function smtp_ok() {
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

    public function smtp_putcmd($cmd, $arg = "") {
        if ($arg!="") {
            if ($cmd=="") $cmd = $arg; else
                $cmd = $cmd . " " . $arg;
        }
        fputs($this ->sock, $cmd . "\r\n");
        $this ->smtp_debug("> " . $cmd . "\n");

        return $this ->smtp_ok();
    }

    public function smtp_error($string) {
        $this ->error .= "<br/>Error: Error occurred while " . $string . ".<br/>";
        return false;
    }

    public function log_write($message) {
        $this->log .= '<br/>'.$message.'<br/>';
        return true;
    }

    public function strip_comment($address) {
        $comment = "\\([^()]*\\)";
        while (mb_ereg($comment, $address)) {
            $address = mb_ereg_replace($comment, "", $address);
        }
        return $address;
    }

    public function get_address($address) {
        $address = mb_ereg_replace("([ \t\r\n])+", "", $address);
        $address = mb_ereg_replace("^.*<(.+)>.*$", "\\1", $address);
        return $address;
    }

    public function smtp_debug($message) {
        if ($this ->debug) {
            echo $message . "<br>";
        }
    }

    public function get_attach_type($image_tag) {
        $filedata = array();
        $img_file_con = fopen($image_tag, "r");
        unset($image_data);
        while ($tem_buffer = AddSlashes(fread($img_file_con, filesize($image_tag)))) $image_data .= $tem_buffer;
        fclose($img_file_con);
        $filedata['context'] = $image_data;
        $filedata['filename'] = basename($image_tag);
        $extension = substr($image_tag, strrpos($image_tag, "."), strlen($image_tag)-strrpos($image_tag, "."));
        switch ($extension) {
            case ".gif":
                $filedata['type'] = "image/gif";
                break;
            case ".gz":
                $filedata['type'] = "application/x-gzip";
                break;
            case ".htm":
                $filedata['type'] = "text/html";
                break;
            case ".html":
                $filedata['type'] = "text/html";
                break;
            case ".jpg":
                $filedata['type'] = "image/jpeg";
                break;
            case ".tar":
                $filedata['type'] = "application/x-tar";
                break;
            case ".txt":
                $filedata['type'] = "text/plain";
                break;
            case ".zip":
                $filedata['type'] = "application/zip";
                break;
            default:
                $filedata['type'] = "application/octet-stream";
                break;
        }
        return $filedata;
    }
}

