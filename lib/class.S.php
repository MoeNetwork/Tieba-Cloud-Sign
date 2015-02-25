<?php
/**
 * 数据库类
 * @copyright (c) 无名智者
 */

class S extends wmysql {

	/**
	 * 构造函数
	 * @param string $host 数据库主机
	 * @param string $user 用户名
	 * @param string $pw 密码
	 * @param string $name 数据库名
	 * @param bool $long 是否开启长连接
	 */
	public function __construct($host , $user , $pw , $name , $long = false) {
		parent::__construct($host , $user , $pw , $name , $long);
	}

	/**
	 * MySQL 随机取记录
	 * 
	 * @param $t 表
	 * @param $c ID列，默认为id
	 * @param $n 取多少个
	 * @param $w 条件语句
	 * @param $f bool 是否强制以多维数组形式返回，默认false
	 * @param $p string 随机数据前缀，如果产生冲突，请修改本项
	 * @return array 取1个直接返回结果数组(除非$f为true)，取>1个返回多维数组，用foreach取出
	 */
	public function rand($t , $c = 'id' , $n = '1', $w = '' , $f = false , $p = 'tempval_') {
		if (!empty($w)) {
			$w = ' AND '.$w;
		}
	/* //格式化的原句
	SELECT * 
	FROM
		`{$t}` AS {$p}t1
	JOIN (
		SELECT
			ROUND(
				RAND() * (
					(SELECT MAX({$c}) FROM `{$t}`) - (SELECT MIN({$c}) FROM `{$t}`)
				)
			) AS {$p}id
	) AS {$p}t2
	WHERE
		{$p}t1.{$c} >= {$p}t2.{$p}id {$w}
	ORDER BY
		{$p}t1.{$c}
	LIMIT {$n};
	*/
		$sql = "SELECT * FROM `{$t}` AS {$p}t1 JOIN ( SELECT ROUND( RAND() * ((SELECT MAX({$c}) FROM `{$t}`) - (SELECT MIN({$c}) FROM `{$t}`))) AS {$p}id ) AS {$p}t2 WHERE {$p}t1.{$c} >= {$p}t2.{$p}id {$w} ORDER BY {$p}t1.{$c} LIMIT {$n};";
		$xq  = $this->query($sql);
		$r   = array();
		while ($x = $this->fetch_array($xq)) {
			$r[] = $x;
		}
		if ($f == false && count($r) == 1) {
			return $r[0];
		} else {
			return $r;
		}
	}

	/**
	 * 添加列，如果存在但列类型不一样则为更改列类型，如果存在且列类型一样则忽略
	 * @param string $table  表名，不需要带前缀
	 * @param string $column 列名
	 * @param string $type 列类型，如varchar(10)
	 * @param string $other 其他信息，如NOT NULL
	 * @return bool 忽略返回false，其它返回true
	 */
	public function addColumn($table , $column , $type , $other = '') {
		$table = self::_prefix($table);
		$d = $this->once_fetch_array("Describe `{$table}` `{$column}`");
		if (empty($d['Field'])) {
			return $this->query("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$type} {$other}");
		} elseif ($d['Type'] != $type) {
			return $this->query("ALTER TABLE `{$table}` MODIFY COLUMN `{$column}` {$type} {$other}");
		} 
		return false;
	}

	/**
	 * 如果存在，则移除列
	 * @param string $table  表名，不需要带前缀
	 * @param string $column 列名
	 * @return bool 忽略返回false，其它返回true
	 */
	public function delColumn($table , $column) {
		$table = self::_prefix($table);
		$d = $this->fetch_row($this->query("Describe `{$table}` `{$column}`"));
		if (!empty($d[0])) {
			return $this->query("ALTER TABLE `{$table}` DROP COLUMN `{$column}`");
		}
		return false;
	}

	/**
	 * 添加索引，存在则忽略
	 * @param string $table  表名，不需要带前缀
	 * @param string $index  索引名
	 * @param string $column 列名
	 * @param int $type 索引类型，0为普通索引，1为FULLTEXT，2为UNIQUE
	 * @param int $method 索引方法，0为空，1为普通B-TREE，2为HASH
	 * @return bool 忽略返回false，其它返回true
	 */
	public function addIndex($table , $index , $column , $type = 0 , $method = 1) {
		$table   = self::_prefix($table);
		$typee   = self::getIndexType($type);
		$methodd = self::getIndexMethod($method);
		$d = $this->once_fetch_array("SHOW INDEX FROM `{$table}` WHERE `Key_name` = '{$index}'");
		if (empty($d['Key_name'])) {
			return $this->query("ALTER TABLE `{$table}` ADD {$typee} INDEX `{$index}` ({$column}) {$methodd}");
		}
		return false;
	}

	/**
	 * 删除索引，不存在则忽略
	 * @param string $table  表名，不需要带前缀
	 * @param string $index  索引名
	 * @return bool 忽略返回false，其它返回true
	 */
	public function delIndex($table , $index) {
		$table = self::_prefix($table);
		$d = $this->fetch_row($this->query("SHOW INDEX FROM `{$table}` WHERE `Key_name` = '{$index}'"));
		if (!empty($d[0])) {
			return $this->query("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
		}
		return false;
	}

	/**
	 * 获取索引类型
	 * @param int $type 索引类型ID
	 * @return string
	 */
	public static function getIndexType($type) {
		if ($type == 1) {
			return 'FULLTEXT';
		} elseif ($type == 2) {
			return 'UNIQUE';
		} else {
			return '';
		}
	}

	/**
	 * 获取索引方法
	 * @param int $id 索引方法ID
	 * @return string
	 */
	public static function getIndexMethod($id) {
		if ($id == 1) {
			return 'USING BTREE';
		} elseif ($id == 2) {
			return 'USING HASH';
		} else {
			return '';
		}
	}

	/**
	 * 给表名加前缀
	 * @param string $table  表名，不需要带前缀
	 * @return 有前缀的表名
	 */
	private function _prefix($table) {
		return DB_PREFIX . $table;
	}

}