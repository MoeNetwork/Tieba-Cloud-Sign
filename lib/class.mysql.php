<?php
/**
 * StusGame Framework MYSQL数据操作方法封装类
 * [MySQL]
 * @note 实际我是为了兼容不支持MySQLi的JB云的，一般不要使用此类
 * @copyright (c) Kenvix
 */

class wmysql {

	/**
	 * 查询次数
	 * @var int
	 */
	public $queryCount = 0;

	/**
	 * 内部数据连接对象
	 * @var resource
	 */
	private $conn;

	/**
	 * 内部数据结果
	 * @var resource
	 */
	private $result;

	/**
	 * 内部实例对象
	 * @var object MySql
	 */
	private static $instance = null;

	/**
	 * 构造函数
	 * @param string $host 数据库主机
	 * @param string $user 用户名
	 * @param string $pw 密码
	 * @param string $name 数据库名
	 * @param bool $long 是否开启长连接
	 */
	public function __construct($host , $user , $pw , $name , $long = false) {
		if (!function_exists('mysql_connect')) {
			throw new Exception('服务器PHP不支持MySql数据库');
		}
		if ($long) {
			$this->conn = @mysql_pconnect($host , $user , $pw);
		} else {
			$this->conn = @mysql_connect($host , $user , $pw);
		}
		if (!$this->conn) {
            switch ($this->geterrno()) {
                case 2005:
                    throw new Exception("连接数据库失败，数据库地址错误或者数据库服务器不可用",10000);
                    break;
                case 2003:
                    throw new Exception("连接数据库失败，数据库端口错误",10000);
                    break;
                case 2006:
                    throw new Exception("连接数据库失败，数据库服务器不可用",10000);
                    break;
                case 1045:
                    throw new Exception("连接数据库失败，数据库用户名或密码错误",10000);
                    break;
                default :
                    throw new Exception("连接数据库失败，请检查数据库信息。错误编号：" . $this->geterrno(),10000);
                    break;
            }
		}
		if ($this->getMysqlVersion() > '4.1') {
			mysql_query("SET NAMES 'utf8'");
		}
		if(!mysql_select_db($name, $this->conn)) {
			throw new Exception("连接数据库失败，未找到您填写的数据库",10000);
		}
		self::$instance = $this->conn;
		return self::$instance;
	}

	/**
	 * [弃用] 静态方法，返回数据库连接实例
	 */
	public static function con() {
		return self::$instance;
	}

	/**
	 * 关闭数据库连接
	 */
	public function close() {
		return mysql_close($this->conn);
	}

	/**
	 * 发送查询语句
	 *
	 */
	public function query($sql,$noerror = false) {
		$this->result = @mysql_query($sql, $this->conn);
		$this->queryCount++;
		if (!$this->result) {
			if ($noerror == true) {
				return false;
			} else {
				throw new Exception("MySQL 语句执行错误：<br/><b>语句：</b>$sql<br/><b>错误：</b>" . $this->geterror(),10000);
			}
		}else {
			return $this->result;
		}
	}

	/**
	 * 发送批量查询语句
	 * 记作一次查询，只返回最后的查询结果
	 */
	public function xquery($sql,$noerror = false) {
		$sql  = str_ireplace("\n", '', $sql);
		$sql2 = explode(';', $sql);
		foreach ($sql2 as $value) {
			$this->result = mysql_query($value);
		}
		$this->queryCount++;
		if (!$this->result) {
			if ($noerror == true) {
				return false;
			} else {
				throw new Exception("MySQL 批量语句执行错误：<br/><b>语句：</b>$sql<br/><b>错误：</b>" . $this->geterror(),10000);
			}	
		} else {
			return $this->result;
		}
	}
	// 别名
	public function multi_query($sql,$noerror = false) { 
		return $this->xquery($sql,$noerror = false);
	}

	/**
	 * 从结果集中取得一行作为关联数组/数字索引数组
	 *
	 */
	public function fetch_array($query , $type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $type);
	}

	public function once_fetch_array($sql) {
		$this->result = $this->query($sql);
		return $this->fetch_array($this->result);
	}

	/**
	 * 从结果集中取得一行作为数字索引数组
	 *
	 */
	public function fetch_row($query) {
		return mysql_fetch_row($query);
	}

	/**
	 * 取得行的数目
	 *
	 */
	public function num_rows($query) {
		return mysql_num_rows($query);
	}

	/**
	 * 取得结果集中字段的数目
	 */
	public function num_fields($query) {
		return mysql_num_fields($query);
	}
	/**
	 * 取得上一步 INSERT 操作产生的 ID 自增值
	 */
	public function insert_id() {
		return mysql_insert_id($this->conn);
	}

	/**
	 * 获取mysql错误
	 */
	public function geterror() {
		return '#' . mysql_errno() . ' - ' . mysql_error();
	}

    /**
	 * 获取mysql错误编码
	 */
	public function geterrno() {
		return mysql_errno();
	}

	/**
	 * 释放所有与上次查询结果所关联的内存
	 * @return bool
	 */
	public function free() {
		return mysql_free_result($this->result);
	}

	/**
	 * Get number of affected rows in previous MySQL operation
	 */
	public function affected_rows() {
		return mysql_affected_rows();
	}

	/**
	 * 取得数据库版本信息
	 */
	public function getMysqlVersion() {
		return mysql_get_server_info();
	}

	/**
	 * 取得数据库查询次数
	 */
	public function getQueryCount() {
		return $this->queryCount;
	}
}
