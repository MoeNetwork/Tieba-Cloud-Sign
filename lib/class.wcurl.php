<?php
/**
 * StusGame Framework cURL操作封装类
 * @version 3.0 @ 2015-04-03
 * @new 兼容在没curl的主机上使用
 * @copyright (c) 无名智者
 */

class wcurl {
	/**
	 * 内部curl指针
	 * @var resourse
	 */
	private $conn;

	/**
	 * 构造函数，返回curl指针实例
	 * @param $file 网络文件
	 * @param $head array 可选，HTTP头
	 * ps:如果未来要使用POST提交文件，在文件路径前面加上@
	 */
	public function __construct($file, array $head = array('User-Agent: Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/537.36')) {
		if (!function_exists('curl_exec')) {
			throw new Exception('服务器不支持cURL');
		}
		$this->conn = curl_init(); 
		$this->set(CURLOPT_URL, $file);
		$this->set(CURLOPT_RETURNTRANSFER, 1); 
		$this->set(CURLOPT_HTTPHEADER, $head);
		$this->set(CURLOPT_SSL_VERIFYPEER, FALSE);
	}

	/**
	 * 当wcurl类被当成字符串时的操作:执行curl并返回结果
	 * @return string 返回值
	 */
	public function __tostring() {
		return $this->exec();
	}

	/**
	 * 设置一个cURL传输选项
	 * @param $option 需要设置的选项
	 * @param $value  将设置在option选项上的值
	 */
	public function set($option, $value) {
		curl_setopt($this->conn, $option, $value);
	}

	/**
	 * 执行curl并返回结果
	 * @return 返回值
	 */
	public function exec() {
		return curl_exec($this->conn);
	}

	/**
	 * 获取文件内容
	 * @return 获取的内容
	 */
	public function get() {
		return $this->exec();
	}

	/**
	 * POST 提交数据并获取返回获取的内容
	 * @param $data array|string 提交的数据
	 * @return 获取的内容
	 */
	public function post($data) {
		$this->set(CURLOPT_POST, 1);
		if (is_array($data)) {
			$this->set(CURLOPT_POSTFIELDS, http_build_query($data));
		} else {
			$this->set(CURLOPT_POSTFIELDS, $data);
		}
		return $this->exec();
	}

	/**
	 * 添加一些Cookies，在访问的时候会携带它们
	 * @param $ck Cookies，数组或cookies字符串
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
	}

	/**
	 * 静态，获取网页返回的所有Cookies [从已经获取到的网页搜索] [不写文件]
	 * @param 网页内容
 	 * @return array Cookies
 	 * ps: 搜索的网页需要打开CURLOPT_HTTPHEADER
	 */
	public static function readCookies($text) {
		preg_match("/set\-cookie:([^\r\n]*)/i", $text, $m1);
		preg_match_all("/(.*?)=(.*?);/", $m1[1], $m2, PREG_SET_ORDER);
		$r = array();
		foreach ($m2 as $value) {
			$r1 = trim($value[1]);
			$r[$r1] = trim($value[2]);
		}
		return $r;
	}

	/**
	 * GET/POST获取网页返回的所有Cookies [自行抓取网页] [不写文件]
	 * @param $postdata 是否POST提交数据，留空或false表示GET获取，若需要提交数据则传入数组
 	 * @return array Cookies
	 * ps: 将会自动打开CURLOPT_HTTPHEADER
	 */
	public function getCookies($postdata = false) {
		$this->set(CURLOPT_HEADER,1);
		if ($postdata != false) {
			return self::readcookies($this->post($postdata));
		} else {
			return self::readcookies($this->exec());
		}
	}

	/**
	 * 获取一个cURL连接资源句柄的信息
	 * @param $opt 要获取的信息，参见 http://cn2.php.net/manual/zh/function.curl-getinfo.php
	 * @return 信息
	 */
	public function getInfo($opt) {
		return curl_getinfo($this->conn, $opt);
	}

	/**
	 * 返回错误代码
	 * @return 错误代码
	 */
	public function errno() {
		return curl_errno($this->conn);
	}

	/**
	 * 返回错误信息
	 * @return 错误信息
	 */
	public function error() {
		return curl_error($this->conn);
	}

	/**
	 * 返回一个带错误代码的curl错误信息
	 * @return 错误信息
	 */
	public function errMsg() {
		return '#' . $this->errno() . ' - ' . $this->error();
	}

	/**
	 * 运行一个curl函数
	 * @param 函数名称，不需要带curl_
	 * @param ... 其他传给此函数的参数
	 * @return 此函数的返回值
	 */

	public function run($func) {
		$args = array_slice(func_get_args(), 1);
		return call_user_func_array('curl_'.$func, $args);
	}

	/**
	 * 关闭并释放cURL资源
	 */
	public function close() {
		curl_close($this->conn);
	}

	/**
	 * 静态，HTTP CURL GET 快速用法
	 * @param $url 要抓取的URL
	 * @return 抓取结果
	 */
	public static function xget($url) {
		$CN = __CLASS__;
		$x  = new $CN($url);
		return $x->exec();
	}

	/**
	 * 设置超时时间 单位:毫秒
	 * @param int $time 超时时间
	 */
	public function setTimeOut($time) {
		$this->set(CURLOPT_CONNECTTIMEOUT_MS , $time);
	}

	/**
	 * 销毁类的时候自动释放cURL资源
	 */
	/* //请确保有需要再解除注释
	public function __destruct() {
		$this->close();
	}
	*/
}
?>