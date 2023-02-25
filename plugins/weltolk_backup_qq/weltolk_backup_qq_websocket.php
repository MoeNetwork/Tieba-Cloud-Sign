<?php

class weltolk_backup_qq_BadUriException extends Exception
{
}

class weltolk_backup_qq_ServerConnectException extends Exception
{
}

class weltolk_backup_qq_HandshakeException extends Exception
{
}

class weltolk_backup_qq_BadFrameException extends Exception
{
}

class weltolk_backup_qq_SocketRWException extends Exception
{
}

class weltolk_backup_qq_WebSocketClient
{
    const PROTOCOL_WS = 'ws';
    const PROTOCOL_WSS = 'wss';

    const HTTP_HEADER_SEPARATION_MARK = "\r\n";
    const HTTP_HEADER_END_MARK = "\r\n\r\n";

    const UUID = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

    const PACKET_SIZE = (1 << 15);

    // 还有后续帧
    const OPCODE_CONTINUATION_FRAME = 0;
    // 文本帧
    const OPCODE_TEXT_FRAME = 1;
    // 二进制帧
    const OPCODE_BINARY_FRAME = 2;
    // 关闭连接
    const OPCODE_CLOSE = 8;
    // ping
    const OPCODE_PING = 9;
    // pong
    const OPCODE_PONG = 10;

    const FRAME_LENGTH_LEVEL_1_MAX = 125;
    const FRAME_LENGTH_LEVEL_2_MAX = 65535;

    private $protocol;

    private $host;

    private $port;

    private $path;

    private $sock;

    private $secWebSocketKey;

    private $handshakePass = false;

    private $readBuf;

    private $currentReadBufLen;

    private $readBufPos;

    private $closed = false;

    /**
     * WebSocketClient constructor.
     * @param string $wsUri
     * @param float $connectTimeout 设置连接服务器的超时时间
     * @param float $rwTimeout 设置读写数据的超时时间
     * @throws Exception
     */
    public function __construct($wsUri, $header = [], $connectTimeout = 1.0, $rwTimeout = 5.0)
    {
        $this->parseUri($wsUri);

        $this->connect($connectTimeout, $rwTimeout);

        $this->handshake($header);

        $this->initReadBuf();
    }

    /**
     * 解析websocket连接地址
     * @param $wsUri
     * @throws weltolk_backup_qq_BadUriException
     */
    protected function parseUri($wsUri)
    {
        $uriData = parse_url($wsUri);
        if (!$uriData) {
            throw new weltolk_backup_qq_BadUriException('不正确的ws uri格式', __LINE__);
        }
        if ($uriData['scheme'] != self::PROTOCOL_WS && $uriData['scheme'] != self::PROTOCOL_WSS) {
            throw new weltolk_backup_qq_BadUriException('ws的uri必须是以ws://或wss://开头', __LINE__);
        }
        $this->protocol = $uriData['scheme'];
        $this->host = $uriData['host'];

        if (isset($uriData['port']) && $uriData['port']) {
            $this->port = (int)$uriData['port'];
        } else {
            if ($this->protocol == self::PROTOCOL_WSS) {
                $this->port = 443;
            } else {
                $this->port = 80;
            }
        }
        $this->path = (isset($uriData['path']) && $uriData['path']) ? $uriData['path'] : '/';
        if (isset($uriData['query']) && $uriData['query']) {
            $this->path .= '?' . $uriData['query'];
        }
        if (isset($uriData['fragment']) && $uriData['fragment']) {
            $this->path .= '#' . $uriData['fragment'];
        }
    }

    /**
     * 连接websocket服务器
     * @param float $timeout 连接服务器的超时时间
     * @param float $rwTimeout 设置读写数据的超时时间
     * @throws weltolk_backup_qq_ServerConnectException
     */
    protected function connect($timeout, $rwTimeout)
    {
        $this->sock = stream_socket_client(
            ($this->protocol == self::PROTOCOL_WSS ? 'ssl://' : 'tcp://') . $this->host . ':' . $this->port,
            $errno,
            $errstr,
            $timeout
        );

        if (!$this->sock) {
            if ($errstr) {
                throw new weltolk_backup_qq_ServerConnectException('连接ws服务器失败：' . $errstr, $errno);
            }

            throw new weltolk_backup_qq_ServerConnectException('连接ws服务器失败: 未知错误', __LINE__);
        }

        $this->setSockTimeout($rwTimeout);
    }

    /**
     * 设置socket的读写超时时间
     * @param float $seconds
     */
    public function setSockTimeout($seconds)
    {
        if (strpos($seconds, '.') !== false) {
            $original = $seconds;
            $seconds = (int)$seconds;
            $microseconds = bcmul($original, 1000000, 0) - ($seconds * 1000000);
        } else {
            $microseconds = 0;
        }
        stream_set_timeout($this->sock, (int)$seconds, $microseconds);
    }

    /**
     * @param $data
     * @throws weltolk_backup_qq_SocketRWException
     */
    protected function writeToSock($data)
    {
        if ($this->closed) {
            throw new weltolk_backup_qq_SocketRWException('连接已关闭, 不允许再发送消息', __LINE__);
        }

        $dataLen = strlen($data);
        if ($dataLen > self::PACKET_SIZE) {
            $dataPieces = str_split($data, self::PACKET_SIZE);
            foreach ($dataPieces as $piece) {
                $this->writeN($piece);
            }
        } else {
            $this->writeN($data);
        }
    }

    /**
     * 向socket写入N个字节
     * @param $str
     * @throws weltolk_backup_qq_SocketRWException
     */
    protected function writeN($str)
    {
        if ($this->closed) {
            throw new weltolk_backup_qq_SocketRWException('连接已关闭, 不允许再发送消息', __LINE__);
        }

        $len = strlen($str);
        $writeLen = 0;
        do {
            if ($writeLen > 0) {
                $str = substr($str, $writeLen);
            }
            $n = fwrite($this->sock, $str);
            if ($n === false) {
                $meta = stream_get_meta_data($this->sock);
                if ($meta['timed_out']) {
                    throw new weltolk_backup_qq_SocketRWException('向服务器发送数据超时', __LINE__);
                }
                throw new weltolk_backup_qq_SocketRWException('无法发送数据，socket连接已断开？', __LINE__);
            }
            $writeLen += $n;
        } while ($writeLen < $len);
    }

    /**
     * 随机产生一个 Sec-WebSocket-Key
     * @return false|string
     */
    protected static function generateWsKey()
    {
        return base64_encode(md5(uniqid() . mt_rand(1, 8192), true));
    }

    /**
     * websocket握手
     * @throws Exception
     */
    protected function handshake($headers2 = [])
    {
        $this->secWebSocketKey = self::generateWsKey();
        $headers = [
            'GET ' . $this->path . ' HTTP/1.1',
            'Host: ' . $this->host . ':' . $this->port,
            'Upgrade: websocket',
            'Connection: Upgrade',
            'Sec-WebSocket-Key: ' . $this->secWebSocketKey,
            'Sec-WebSocket-Version: 13',
        ];
        $headers = array_merge($headers, $headers2);
        $htmlHeader = implode(self::HTTP_HEADER_SEPARATION_MARK, $headers) . self::HTTP_HEADER_END_MARK;
        $this->writeToSock($htmlHeader);
        $response = '';
        $end = false;
        do {
            $str = fread($this->sock, 8192);
            if (strlen($str) == 0) {
                break;
            }
            $response .= $str;
            $end = strpos($response, self::HTTP_HEADER_END_MARK);
        } while ($end === false);

        if ($end === false) {
            throw new weltolk_backup_qq_HandshakeException('握手失败：握手响应不是标准的http响应', __LINE__);
        }

        $resHeader = substr($response, 0, $end);
        $headers = explode(self::HTTP_HEADER_SEPARATION_MARK, $resHeader);

        if (strpos($headers[0], '101') === false) {
            throw new weltolk_backup_qq_HandshakeException('握手失败：服务器返回http状态码不是101', __LINE__);
        }
        for ($i = 1; $i < count($headers); $i++) {
            list($key, $val) = explode(':', $headers[$i]);
            if (strtolower(trim($key)) == 'sec-websocket-accept') {
                $accept = base64_encode(sha1($this->secWebSocketKey . self::UUID, true));
                if (trim($val) != $accept) {
                    throw new weltolk_backup_qq_HandshakeException('握手失败： sec-websocket-accept值校验失败', __LINE__);
                }
                $this->handshakePass = true;
                break;
            }
        }
        if (!$this->handshakePass) {
            throw new weltolk_backup_qq_HandshakeException('握手失败：缺少sec-websocket-accept http头', __LINE__);
        }
    }

    /**
     * @param int $opCode 帧类型
     * @param string $playLoad 携带的数据
     * @param bool $isMask 是否使用掩码
     * @param int $status 关闭帧状态
     * @return false|string
     */
    protected function packFrame($opCode, $playLoad = '', $isMask = true, $status = 1000)
    {
        $firstByte = 0x80 | $opCode;
        if ($isMask) {
            $secondByte = 0x80;
        } else {
            $secondByte = 0x00;
        }

        $playLoadLen = strlen($playLoad);
        if ($opCode == self::OPCODE_CLOSE) {
            // 协议规定关闭帧必须使用掩码
            $isMask = true;
            $playLoad = pack('CC', (($status >> 8) & 0xff), $status & 0xff) . $playLoad;
            $playLoadLen += 2;
        }
        if ($playLoadLen <= self::FRAME_LENGTH_LEVEL_1_MAX) {
            $secondByte |= $playLoadLen;
            $frame = pack('CC', $firstByte, $secondByte);
        } elseif ($playLoadLen <= self::FRAME_LENGTH_LEVEL_2_MAX) {
            $secondByte |= 126;
            $frame = pack('CCn', $firstByte, $secondByte, $playLoadLen);
        } else {
            $secondByte |= 127;
            $frame = pack('CCJ', $firstByte, $secondByte, $playLoadLen);
        }

        if ($isMask) {
            $maskBytes = [mt_rand(1, 255), mt_rand(1, 255), mt_rand(1, 255), mt_rand(1, 255)];
            $frame .= pack('CCCC', $maskBytes[0], $maskBytes[1], $maskBytes[2], $maskBytes[3]);
            if ($playLoadLen > 0) {
                for ($i = 0; $i < $playLoadLen; $i++) {
                    $playLoad[$i] = chr(ord($playLoad[$i]) ^ $maskBytes[$i % 4]);
                }
            }
        }

        $frame .= $playLoad;

        return $frame;
    }

    /**
     * ping服务器
     * @throws Exception
     */
    public function ping()
    {
        try {
            $frame = $this->packFrame(self::OPCODE_PING, '', true);
            $this->writeToSock($frame);
            do {
                $pong = $this->recv();
                if ($pong->opcode == self::OPCODE_PONG) {
                    return true;
                }
            } while ($pong->opcode != self::OPCODE_PONG);
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 响应服务器的ping
     * @throws Exception
     */
    public function pong()
    {
        $frame = $this->packFrame(self::OPCODE_PONG, '', true);
        $this->writeToSock($frame);
    }

    /**
     * 主动关闭与服务器的连接
     * @return bool
     * @throws Exception
     */
    public function close()
    {
        $frame = $this->packFrame(self::OPCODE_CLOSE, '', true, 1000);

        try {
            $this->writeToSock($frame);
            // 主动关闭需要再接收一次对端返回的确认消息
            $wsData = $this->recv();
            if ($wsData->opcode == self::OPCODE_CLOSE) {
                return true;
            }
        } catch (\Throwable $e) {
        } finally {
            $this->closed = true;
            stream_socket_shutdown($this->sock, STREAM_SHUT_RDWR);
        }
        return false;
    }

    /**
     * ping服务器失败或服务器响应异常时调用，用于关闭socket资源
     */
    public function abnormalClose()
    {
        if (!$this->closed && $this->sock) {
            $this->closed = true;
            try {
                stream_socket_shutdown($this->sock, STREAM_SHUT_RDWR);
            } catch (\Throwable $e) {
            }
        }
    }

    /**
     * 响应服务器的关闭消息
     * @throws weltolk_backup_qq_SocketRWException
     */
    protected function replyClosure()
    {
        $frame = $this->packFrame(self::OPCODE_CLOSE, '', true, 1000);
        $this->writeToSock($frame);
        $this->closed = true;
        stream_socket_shutdown($this->sock, STREAM_SHUT_RDWR);
    }

    /**
     * @param string $data 要发送的数据
     * @param int $opCode 发送的数据类型 WebSocketClient::OPCODE_TEXT_FRAME 或 WebSocketClient::OPCODE_BINARY_FRAME
     * @param bool $isMask 是否使用掩码，默认使用
     * @throws Exception
     */
    public function send($data, $opCode = self::OPCODE_TEXT_FRAME, $isMask = true)
    {
        if ($opCode != self::OPCODE_TEXT_FRAME && $opCode != self::OPCODE_BINARY_FRAME) {
            throw new \InvalidArgumentException('不支持的帧数据类型', __LINE__);
        }

        $frame = $this->packFrame($opCode, $data, $isMask);

        $this->writeToSock($frame);
    }

    /**
     * 初始化收取数据缓冲区
     */
    private function initReadBuf()
    {
        $this->readBuf = '';
        $this->currentReadBufLen = 0;
        $this->readBufPos = 0;
    }

    /**
     * 从读取缓冲区中当前位置返回指定长度字符串
     * @param int $len 返回长度
     * @return bool|string
     * @throws weltolk_backup_qq_SocketRWException
     */
    private function fetchStrFromReadBuf($len = 1)
    {
        $target = $this->readBufPos + $len;

        while ($target > $this->currentReadBufLen) {
            if ($this->closed) {
                throw new weltolk_backup_qq_SocketRWException('连接已关闭, 不允许再收取消息', __LINE__);
            }
            $read = fread($this->sock, self::PACKET_SIZE);
            if (!$read) {
                $meta = stream_get_meta_data($this->sock);
                if ($meta['timed_out']) {
                    throw new weltolk_backup_qq_SocketRWException('读取服务器数据超时', __LINE__);
                }
                throw new weltolk_backup_qq_SocketRWException('无法读取服务器数据，错误未知', __LINE__);
            }
            $this->readBuf .= $read;
            $this->currentReadBufLen += strlen($read);
        }
        $str = substr($this->readBuf, $this->readBufPos, $len);
        $this->readBufPos += $len;
        return $str;
    }

    /**
     * 返回读取缓冲区当前位置字符的ascii码
     * @return int
     * @throws weltolk_backup_qq_SocketRWException
     */
    private function fetchCharFromReadBuf()
    {
        $str = $this->FetchStrFromReadBuf(1);
        return ord($str[0]);
    }

    /**
     * 丢弃读取缓冲区已处理的指定长度数据
     * @param $len
     */
    private function discardReadBuf($len)
    {
        // 未处理的数据不会被丢弃
        if ($len > $this->readBufPos) {
            $len = $this->readBufPos;
        }
        if ($len > 0) {
            $this->readBuf = substr($this->readBuf, $len);
            $this->readBufPos -= $len;
            $this->currentReadBufLen -= $len;
        }
    }

    /**
     * @return weltolk_backup_qq_WsDataFrame
     * @throws Exception
     */
    public function recv()
    {
        $dataFrame = $this->readFrame();
        switch ($dataFrame->opcode) {
            case self::OPCODE_PING:
                $this->pong();
                break;

            case self::OPCODE_PONG:
                break;

            case self::OPCODE_TEXT_FRAME:
            case self::OPCODE_BINARY_FRAME:
            case self::OPCODE_CLOSE:
                if ($dataFrame->fin == 0) {
                    do {
                        $continueFrame = $this->readFrame();
                        $dataFrame->playload .= $continueFrame->playload;
                    } while ($continueFrame->fin == 0);
                }

                if ($dataFrame->opcode == self::OPCODE_CLOSE) {
                    $this->replyClosure();
                }
                break;
            default:
                throw new weltolk_backup_qq_BadFrameException('无法识别的frame数据', __LINE__);
                break;
        }
        return $dataFrame;
    }

    /**
     * 读取一个数据帧
     * @return weltolk_backup_qq_WsDataFrame
     * @throws weltolk_backup_qq_SocketRWException
     */
    protected function readFrame()
    {
        $firstByte = $this->fetchCharFromReadBuf();
        $fin = ($firstByte >> 7);
        $opcode = $firstByte & 0x0F;
        $secondByte = $this->fetchCharFromReadBuf();
        $isMasked = ($secondByte >> 7);
        $dataLen = $secondByte & 0x7F;
        if ($dataLen == 126) {
            // 2字节无符号整形
            $dataLen = ($this->fetchCharFromReadBuf() << 8) + $this->fetchCharFromReadBuf();
        } elseif ($dataLen == 127) {
            // 8字节无符号整形
            $dataLen = $this->fetchStrFromReadBuf(8);
            $res = unpack('Jlen', $dataLen);
            if (isset($res['len'])) {
                $dataLen = $res['len'];
            } else {
                $dataLen = (ord($dataLen[0]) << 56)
                    + (ord($dataLen[1]) << 48)
                    + (ord($dataLen[2]) << 40)
                    + (ord($dataLen[3]) << 32)
                    + (ord($dataLen[4]) << 24)
                    + (ord($dataLen[5]) << 16)
                    + (ord($dataLen[6]) << 8)
                    + ord($dataLen[7]);
            }
        }

        $data = '';
        $status = 0;
        if ($dataLen > 0) {
            if ($isMasked) {
                // 4字节掩码
                $maskChars = $this->fetchStrFromReadBuf(4);
                $maskSet = [ord($maskChars[0]), ord($maskChars[1]), ord($maskChars[2]), ord($maskChars[3])];
                $data = $this->fetchStrFromReadBuf($dataLen);
                for ($i = 0; $i < $dataLen; $i++) {
                    $data[$i] = chr(ord($data[$i]) ^ $maskSet[$i % 4]);
                }
            } else {
                $data = $this->fetchStrFromReadBuf($dataLen);
            }
            if ($opcode == self::OPCODE_CLOSE) {
                $status = (ord($data[0]) << 8) + ord($data[1]);
                $data = substr($data, 2);
            }
        }

        $this->discardReadBuf($this->readBufPos);

        $dataFrame = new weltolk_backup_qq_WsDataFrame();
        $dataFrame->opcode = $opcode;
        $dataFrame->fin = $fin;
        $dataFrame->status = $status;
        $dataFrame->playload = $data;
        return $dataFrame;
    }

    /**
     * __destruct
     */
    public function __destruct()
    {
        $this->abnormalClose();
    }
}

/**
 * websocket数据帧
 * Class weltolk_backup_qq_wsDataFrame
 */
class weltolk_backup_qq_WsDataFrame
{
    /**
     * @var int $opcode
     */
    public $opcode;

    /**
     * @var int $fin 标识数据包是否已结束
     */
    public $fin;

    /**
     * @var int $status 关闭时的状态码，如果有的话
     */
    public $status;

    /**
     * @var string 数据包携带的数据
     */
    public $playload;
}
