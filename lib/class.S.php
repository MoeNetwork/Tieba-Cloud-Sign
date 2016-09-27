<?php
/**
 * 数据库类
 * @copyright (c) Kenvix
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
		try {
			parent::__construct($host , $user , $pw , $name , $long);
		} catch(Exception $ex) {
			msg($ex->getMessage());
		}
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
		switch(option::get('sign_scan')) {
            case '0':
                $sql  = "SELECT * FROM `{$t}` ";
                if(!empty($w)) {
                    $sql .= " WHERE {$w} ";
                }
                $sql .= " LIMIT {$n};";
                break;

            case '2':
                $sql  = "SELECT * FROM `{$t}` ";
                if(!empty($w)) {
                    $sql .= " WHERE {$w} ";
                }
                $sql .= " ORDER BY RAND() LIMIT {$n};";
                break;

            default:
                if (!empty($w)) {
                    $w = ' AND '.$w;
                }
                $sql = "SELECT * FROM `{$t}` AS {$p}t1 JOIN ( SELECT ROUND( RAND() * ((SELECT MAX({$c}) FROM `{$t}`) - (SELECT MIN({$c}) FROM `{$t}`))) AS {$p}id ) AS {$p}t2 WHERE {$p}t1.{$c} >= {$p}t2.{$p}id {$w} ORDER BY {$p}t1.{$c} LIMIT {$n};";
                break;
        }
        $xq   = $this->query($sql);
        $r    = array();
        while ($x = $this->fetch_array($xq)) {
            $r[] = $x;
        }
        if ($f == false && count($r) == 1) {
            return $r[0];
        } else {
            return $r;
        }
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
	 * @return string 有前缀的表名
	 */
	private function _prefix($table) {
		return DB_PREFIX . $table;
	}

    /**
     * 解析Where数组返回转义、拼接后的where字符串
     * @param null|string|array $where 条件数组或字符串
     * @return string
     */
    private function _parseWhere($where)
    {
        if ($where === NULL) return '';
        if (is_string($where)) return ' WHERE ' . $where;

        $w = array();
        foreach ($where as $k => $v){
            if (is_int($k) && is_string($v)){
                $w[] = $v;
            } elseif (preg_match('/(<|>|!|=)/', trim($k))) {
                // 如果$k（即字段名）带有比较符（例如 > < != 之类）
                $loc = strpos($k, ' ');
                $field = substr($k, 0, $loc); // 取字段名
                $code = substr($k, $loc + 1); // 取符号
                $w[] = "`{$field}`{$code}'{$this->escape_string($v)}'";
            } else {
                // 没有比较符的话默认为 =
                $w[] = "`{$k}`='{$this->escape_string($v)}'";
            }
        }
        return ' WHERE ' . implode(' AND ', $w);
    }

    /**
     * 解析字段名数组返回转义、拼接后的字符串
     * @param string|array $field 字段数组或字符串
     * @return string
     */
    private function _parseField($field)
    {
        if ( !is_array($field)) return $field;
        return '`' . implode('`,`', $field) . '`';
    }

    /**
     * 解析limit值
     * @param string|int $limit
     * @return string
     */
    private function _parseLimit($limit)
    {
        if ($limit === NULL) return '';
        return ' LIMIT ' . (string)$limit;
    }

    /**
     * 取一条数据。返回数组格式
     * @param string      $table 表名
     * @param null|string $where 条件
     * @param string      $field 字段名
     * @return array
     */
    public function one($table, $where, $field = '*')
    {
        $table = $this->_prefix($table);
        $where = $this->_parseWhere($where);
        $field = $this->_parseField($field);
        return $this->once_fetch_array("SELECT {$field} FROM `{$table}` {$where} LIMIT 1");
    }

    /**
     * 取多行数据。返回结果集，自行使用fetch_array方法读取数据
     * @param string   $table 表名
     * @param string   $where 条件
     * @param string   $field 字段名
     * @param int|null $limit 数量限制。默认不限制
     * @return mysqli_result
     */
    public function all($table, $where = NULL, $field = '*', $limit = NULL)
    {
        $table = $this->_prefix($table);
        $field = $this->_parseField($field);
        $where = $this->_parseWhere($where);
        $limit = $this->_parseLimit($limit);

        return $this->query("SELECT {$field} FROM `{$table}` {$where} {$limit}");
    }

    /**
     * 统计数据数量
     * @param string $table 表名
     * @param string $where 条件
     * @param string $field 字段名
     * @return int
     */
    public function count($table, $where = NULL, $field = '*')
    {
        $table = $this->_prefix($table);
        $where = $this->_parseWhere($where);
        $field = $this->_parseField($field);
        $q = $this->once_fetch_array("SELECT COUNT({$field}) as `_s_num` FROM `{$table}` {$where}");
        return (int)$q['_s_num'];
    }

    /**
     * 插入一条数据
     * @param string $table 表名
     * @param array  $data  要插入的数据 [字段名=>值, 字段名=>值, ...]
     * @return bool|mysqli_result
     */
    public function insert($table, $data)
    {
        // 解析data
        $field = array();
        $value = array();
        if (count($data) == 0) return FALSE;
        foreach ($data as $k => $v){
            $field[] = $k;
            $value[] = $this->escape_string($v);
        }
        $field = '`' . implode('`,`', $field) . '`';
        $value = "'" . implode("','", $value) . "'";
        $table = $this->_prefix($table);

        return $this->query("INSERT INTO `{$table}` ({$field}) VALUES ({$value})");
    }

    /**
     * 更新数据
     * @param string      $table 表名
     * @param array       $set   新的数据
     * @param null|string $where 条件
     * @param null|int    $limit 数量限制。默认不限制
     * @return bool|mysqli_result
     */
    public function update($table, $set, $where = NULL, $limit = NULL)
    {
        $sql = "UPDATE `" . $this->_prefix($table) . "` SET ";
        // 解析set
        $s = array();
        if (count($set) == 0) return FALSE;
        foreach ($set as $k => $v){
            $s[] = "`{$k}`='{$this->escape_string($v)}'";
        }
        $set = implode(', ', $s);
        $sql .= $set;

        $where = $this->_parseWhere($where);
        $limit = $this->_parseLimit($limit);
        $sql .= " {$where} {$limit}";

        return $this->query($sql);
    }

    /**
     * 删除数据
     * @param string   $table 表名
     * @param string   $where 条件
     * @param null|int $limit 数量限制。默认不限制
     * @return bool|mysqli_result
     */
    public function delete($table, $where, $limit = NULL)
    {
        $table = $this->_prefix($table);
        $where = $this->_parseWhere($where);
        $limit = $this->_parseLimit($limit);

        return $this->query("DELETE FROM `{$table}` {$where} {$limit}");
    }
}