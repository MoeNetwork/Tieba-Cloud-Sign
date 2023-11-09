<?php

/**
 * [PDO]
 * BANKA2017 & MoeNetwork
 */

class wmysql
{
    /**
     * 查询次数
     * @var int
     */
    public $queryCount = 0;

    /**
     * 内部数据连接对象
     * @var pdo
     */
    private $conn;

    /**
     * 内部数据结果
     * @var PDOStatement
     */
    private $result;

    /**
     * 内部实例对象
     * @var object PDO
     */
    private static $instance = null;

    public function __construct($host, $user, $pw, $name, $long = false)
    {
        if (!class_exists('pdo')) {
            throw new Exception('服务器不支持PDO类');
        }
        $coninfo = strpos($host, ':');

        $port = "3306";
        if ($coninfo) {
            $port = substr($host, $coninfo + 1);
            $host = substr($host, 0, $coninfo);
        }
        try {
            if ($host === "sqlite") {
                $this->conn = new PDO("sqlite:$port", $user, $pw, [PDO::ATTR_PERSISTENT => $long]);
            } else {
                $this->conn = new PDO("mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4", $user, $pw, [PDO::ATTR_PERSISTENT => $long]);
            }
        } catch (PDOException $e) {
            switch ($e->getCode()) {
                case 1044:
                case 1045:
                    throw new Exception("连接数据库失败，数据库用户名或密码错误", 10000);
                    break;

                case 1049:
                    throw new Exception("连接数据库失败，未找到您填写的数据库", 10000);
                    break;

                case 2003:
                    throw new Exception("连接数据库失败，数据库端口错误", 10000);
                    break;

                case 2005:
                    throw new Exception("连接数据库失败，数据库地址错误或者数据库服务器不可用", 10000);
                    break;

                case 2006:
                    throw new Exception("连接数据库失败，数据库服务器不可用", 10000);
                    break;

                default:
                    throw new Exception("连接数据库失败，请检查数据库信息。错误编号：" . $e->getCode(), 10000);
            }
        }
        self::$instance = $this->conn;
        return self::$instance;
    }

    /**
     * [弃用] 静态方法，返回数据库连接实例
     */
    public static function con()
    {
        return self::$instance;
    }

    /**
     * 关闭数据库连接
     */
    public function close()
    {
        return $this->conn = null;
    }

    /**
     * 发送查询语句
     *
     */
    public function query($sql, $noerror = false)
    {
        try {
            $this->result = $this->conn->query($sql);
            $this->queryCount++;
            return $this->result;
        } catch (PDOException $e) {
            if ($noerror) {
                return false;
            } else {
                throw new Exception("SQL 语句执行错误：<br/><b>语句：</b>$sql<br/><b>错误：</b>" . $e->getMessage(), 10000);
            }
        }
    }

    /**
     * 发送批量查询语句
     * 记作一次查询，只返回最后的查询结果
     * pdo 并不支持多语句查询
     */
    public function xquery($sql = [], $noerror = false)
    {
        if (is_string($sql)) {
            $sql = preg_split("/;\r\n$|;\n$|;\r$/", $sql);
        }
        try {
            foreach ($sql as $single_sql) {
                $single_sql = trim($single_sql);
                if (strlen($single_sql) > 0) {
                    if ($single_sql === ";") {
                        continue;
                    } elseif ($single_sql[-1] !== ";") {
                        $single_sql .= ";";
                    }
                    $this->result = $this->query($single_sql, $noerror);
                }
            }
            $this->queryCount++;
            return $this->result;
        } catch (Exception $e) {
            if ($noerror) {
                return false;
            } else {
                throw new Exception("SQL 批量语句执行错误：<br/><b>语句：</b>" . implode(";\n", $sql) . "<br/><b>错误：</b>" . $e->getMessage(), 10000);
            }
        }
    }
    // 别名
    public function multi_query($sql, $noerror = false)
    {
        return $this->xquery($sql, $noerror);
    }

    /**
     * 从结果集中取得一行作为关联数组/数字索引数组
     * @param PDOStatement $query 结果集
     * @param int $type 可选 PDO::FETCH_ASSOC，PDO::FETCH_NUM，PDO::FETCH_BOTH
     * @return array
     */
    public function fetch_array(PDOStatement $query, $type = PDO::FETCH_ASSOC)
    {
        return $query->fetch($type);
    }

    public function once_fetch_array($sql)
    {
        $this->result = $this->query($sql);
        return $this->fetch_array($this->result);
    }

    /**
     * 从结果集中取得一行作为数字索引数组
     *
     */
    public function fetch_row(PDOStatement $query)
    {
        return $query->fetch(PDO::FETCH_NUM);
    }

    /**
     * 取得行的数目
     *
     */
    public function num_rows(PDOStatement $query)
    {
        return $query->rowCount();
    }

    /**
     * 取得结果集中字段的数目
     */
    public function num_fields(PDOStatement $query)
    {
        return $query->columnCount();
    }

    /**
     * 取得上一步 INSERT 操作产生的 ID 自增值
     */
    public function insert_id()
    {
        return $this->conn->lastInsertId();
    }

    /**
     * 获取mysql错误
     */
    public function geterror()
    {
        return '#' . $this->geterrno() . ' - ' . $this->conn->errorInfo()[2]; //TODO check value
    }

    /**
     * 获取mysql错误编码
     */
    public function geterrno()
    {
        return $this->conn->errorCode();
    }

    /**
     * 释放所有与上次查询结果所关联的内存
     * @return bool
     */
    public function free()
    {
        if ($this->result !== null) {
            return $this->result->closeCursor();
        } else {
            return true;
        }
    }

    /**
     * 获取上次操作受影响行数
     */
    public function affected_rows()
    {
        if ($this->result !== null) {
            return $this->result->rowCount();
        } else {
            return 0;
        }
    }

    /**
     * 取得数据库版本信息
     */
    public function getMysqlVersion()
    {
        return $this->conn->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    /**
     * 取得数据库查询次数
     */
    public function getQueryCount()
    {
        return $this->queryCount;
    }

    /**
     * 由于 pdo 不支持 real_escape_string，所以暂时只能加引号
     * @param string $sql
     * @return string
     */
    public function escape_string($sql)
    {
        return $this->conn->quote($sql);
    }
}
