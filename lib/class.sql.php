<?php
/**
 * 数据库类
 * @copyright (c) 无名智者
 */

class sql extends wmysql {

	/**
	 * 构造函数
	 */
	public function __construct($host , $user , $pw , $name) {
		parent::__construct($host , $user , $pw , $name);
	}

	/**
	 * 添加列，如果存在但列类型不一样则为更改列类型，如果存在且列类型一样则忽略
	 * @param string $table  表名，不需要带前缀
	 * @param string $column 列名
	 * @param string $type 列类型，如varchar(10)
	 * @param string $other 其他信息，如NOT NULL
	 * @return mysql_result|bool 忽略返回false
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
	 * @return mysql_result|bool 忽略返回false
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
	 * @return mysql_result|bool 忽略返回false
	 */
	public function addIndex($table , $index , $column , $type = 0 , $method = 1) {
		$table   = self::_prefix($table);
		$typee   = self::getIndexType($type);
		$methodd = self::getIndexMethod($method);
		$d = $this->once_fetch_array("SHOW INDEX FROM `{$table}` WHERE `Key_name` = '{$index}'");
		if (empty($d['Key_name'])) {
			return $this->query("ALTER TABLE `{$table}` ADD {$typee} INDEX `{$index}` ({$column}) USING {$methodd}");
		}
		return false;
	}

	/**
	 * 删除索引，不存在则忽略
	 * @param string $table  表名，不需要带前缀
	 * @param string $index  索引名
	 * @return mysql_result|bool 忽略返回false
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
	 * @return  string
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
	 * @return  string
	 */
	public static function getIndexMethod($id) {
		if ($type == 1) {
			return 'BTREE';
		} elseif ($type == 2) {
			return 'HASH';
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