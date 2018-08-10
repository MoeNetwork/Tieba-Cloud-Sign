<?php
/**
 * Kenvix cURL类
 * @version 4.0 @ 2017-05-12
 * @copyright (c) Kenvix
 * @see https://kenvix.com
 * Class wcurl
 */
class wcurl {
    /**
     * curl句柄
     * @var resource
     */
    public $conn;

    /**
     * 构造函数，返回curl指针实例
     * @param string $file 网络文件 如果要使用POST提交文件，在文件路径前面加上@
     * @param array $head 可选，HTTP头
     * @throws Exception
     */
    public function __construct($file = '', array $head = array()) {
        if (!function_exists('curl_exec')) {
            throw new Exception('服务器不支持cURL', -1);
        }
        $this->conn = curl_init();
        if($this->conn === FALSE) throw new Exception('初始化cURL失败', -1);
        $this->init($file, $head);
    }

    /**
     * 当wcurl类被当成字符串时的操作:执行curl并返回结果
     * @return string 返回值
     */
    public function __toString() {
        return $this->exec();
    }

    /**
     * 设置自定义的 Method 来代替"GET"或"HEAD"
     * @param $method
     * @return $this
     */
    public function setRequestMethod($method) {
        $this->set(CURLOPT_CUSTOMREQUEST, $method);
        return $this;
    }

    /**
     * 设置一个cURL传输选项
     * @param string $option 需要设置的选项
     * @param string $value  将设置在option选项上的值
     * @return $this
     */
    public function set($option, $value) {
        curl_setopt($this->conn, $option, $value);
        return $this;
    }

    /**
     * 通过数组批量设置cURL传输选项
     * @param array $option 需要设置的选项
     * @return $this
     */
    public function setAll($option) {
        curl_setopt_array($this->conn, $option);
        return $this;
    }

    /**
     * 执行curl并返回结果
     * @return string 返回值
     */
    public function exec() {
        return curl_exec($this->conn);
    }

    /**
     * 获取文件内容
     * @return string 获取的内容
     */
    public function get() {
        return $this->exec();
    }

    /**
     * POST 提交数据并获取返回获取的内容
     * @param $data array|string 提交的数据
     * @return string 获取的内容
     */
    public function post($data) {
        $this->set(CURLOPT_POST, 1);
        $this->set(CURLOPT_POSTFIELDS, self::buildFields($data));
        return $this->exec();
    }

    /**
     * PUT 并获取返回获取的内容
     * @param $data array|string 可选。提交的数据
     * @return string 获取的内容
     */
    public function put($data = null) {
        $this->set(CURLOPT_CUSTOMREQUEST, 'PUT');
        if(!is_null($data)) $this->set(CURLOPT_POSTFIELDS, self::buildFields($data));
        return $this->exec();
    }

    /**
     * DELETE 并获取返回获取的内容
     * @param $data array|string 可选。提交的数据
     * @return string 获取的内容
     */
    public function delete($data = null) {
        $this->set(CURLOPT_CUSTOMREQUEST, 'DELETE');
        if(!is_null($data)) $this->set(CURLOPT_POSTFIELDS, self::buildFields($data));
        return $this->exec();
    }

    /**
     * 添加一些Cookies，在访问的时候会携带它们
     * @param string|array $ck Cookies，数组或cookies字符串
     * @return $this
     */
    public function addCookie($ck) {
        if (is_array($ck)) {
            $r = '';
            foreach ($ck as $key => $value) {
                $r .= "{$key}={$value}; ";
            }
        } else {
            $r = $ck;
        }
        $this->set(CURLOPT_COOKIE, $r);
        return $this;
    }

    /**
     * 静态，获取网页返回的所有Cookies [从已经获取到的网页搜索] [不写文件]
     * ps: 搜索的网页需要打开CURLOPT_HEADER
     * @param string $text 网页内容
     * @return array Cookies
     */
	public static function readCookies($text) {
		$r=Array();
		$sz=0;
		preg_match_all("/set\-cookie:([^\r\n]*)/i", $text, $m1,PREG_SET_ORDER);
		while(!empty($m1[$sz][1])) {
			preg_match_all("/(.*?)=(.*?);/", $m1[$sz][1], $m2, PREG_SET_ORDER);
			foreach ($m2 as $value) {
				$r1 = trim($value[1]);
				$r[$r1] = trim($value[2]);
			}
			$sz++;
		}
		return $r;
	}

    /**
     * GET/POST获取网页返回的所有Cookies [自行抓取网页] [不写文件]
     * ps: 将会自动打开CURLOPT_HEADER
     * @param string|bool $postdata 是否POST提交数据，留空或false表示GET获取，若需要提交数据则传入数组
     * @return array Cookies
     */
    public function getCookies($postdata = false) {
        $this->set(CURLOPT_HEADER,1);
        if ($postdata != false) {
            return self::readCookies($this->post($postdata));
        } else {
            return self::readCookies($this->exec());
        }
    }

    /**
     * 获取一个cURL连接资源句柄的信息
     * @param string $opt 要获取的信息，参见 http://cn2.php.net/manual/zh/function.curl-getinfo.php
     * @return string 信息
     */
    public function getInfo($opt) {
        return curl_getinfo($this->conn, $opt);
    }

    /**
     * 返回错误代码
     * @return string 错误代码
     */
    public function errno() {
        return curl_errno($this->conn);
    }

    /**
     * 返回错误信息
     * @return string 错误信息
     */
    public function error() {
        return curl_error($this->conn);
    }

    /**
     * 返回一个带错误代码的curl错误信息
     * @return string 错误信息
     */
    public function errMsg() {
        return '#' . $this->errno() . ' - ' . $this->error();
    }

    /**
     * 运行一个curl函数
     * @param string $func 函数名称，不需要带curl_
     * @param ... 其他传给此函数的参数
     * @return string 此函数的返回值
     */

    public function run($func) {
        $args = array_slice(func_get_args(), 1);
        return call_user_func_array('curl_'.$func, $args);
    }

    /**
     * 关闭并释放cURL资源
     */
    public function close() {
        @curl_close($this->conn);
    }

    /**
     * 静态 HTTP CURL GET 快速用法
     * @param string $url 要抓取的URL
     * @return string 抓取结果
     */
    public static function xget($url) {
        $fuckoldphp = new self($url);
        return $fuckoldphp->exec();
    }

    /**
     * 设置超时时间 单位:毫秒
     * @param int $time 超时时间
     * @return $this
     */
    public function setTimeOut($time) {
        $this->set(CURLOPT_NOSIGNAL, 1); // 参见：http://www.laruence.com/2014/01/21/2939.html
        $this->set(CURLOPT_TIMEOUT_MS , $time);
        return $this;
    }

    /**
     * 设置全部的HTTP Header
     * @param array $head
     * @return $this
     */
    public function setHeader(array $head) {
        $this->set(CURLOPT_HTTPHEADER, $head);
        return $this;
    }

    /**
     * 设置URL
     * @param string $url 网络文件 如果要使用POST提交文件，在文件路径前面加上@
     * @return $this
     */
    public function setUrl($url) {
        $this->set(CURLOPT_URL, $url);
        return $this;
    }

    /**
     * 设置Referrer(Referer)
     * @param $ref
     * @return $this
     */
    public function setReferrer($ref) {
        $this->set(CURLOPT_REFERER, $ref);
        return $this;
    }

    /**
     * 是否允许自动跟随
     * @param bool $v
     * @return $this
     */
    public function allowAutoReferrer($v) {
        $this->set(CURLOPT_AUTOREFERER, $v);
        return $this;
    }

    /**
     * 重置会话句柄的所有的选项到LIBCURL默认值。要初始化到WCURL默认值，使用init()方法
     * @return $this
     */
    public function reset() {
        if(function_exists('curl_reset')){
            curl_reset($this->conn);
        } else {
            if(is_resource($this->conn)) curl_close($this->conn);
            $this->conn = curl_init();
        }
        return $this;
    }

    /**
     * 初始化wcurl或重置会话句柄的所有的选项到wcurl默认值
     * @param string $file
     * @param array  $head
     * @return $this
     */
    public function init($file = '', array $head = array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36')) {
        $this->reset();
        if(!empty($file)) $this->setUrl($file);
        $this->setHeader($head)->setAll(array(//wcurl默认设定
            CURLOPT_RETURNTRANSFER  => true, //将curl获取的信息以文件流的形式返回，而不是直接输出
            CURLOPT_SSL_VERIFYPEER  => false, //cURL将终止从服务端进行验证
            CURLOPT_FOLLOWLOCATION  => true //跟随重定向 会将服务器服务器返回的"Location: "放在header中递归的返回给服务器
        ));
        return $this;
    }

    /**
     * 构建用于CURL的POST表单
     * @param string|array $data
     * @return string
     */
    public static function buildFields($data) {
        if (is_array($data)) {
            return http_build_query($data);
        } else {
            return $data;
        }
    }

    /**
     * 获取CURL连接
     * @return resource
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * 销毁类的时候自动释放cURL资源
     */
    public function __destruct() {
        $this->close();
    }
}