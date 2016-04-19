<?php
defined('BOSPATH') OR exit('No direct script access allowed');
/**
 * 用于数据库连接及SQL语句编译
 *
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-18
 * Time: 上午10:31
 */
require_once BOSPATH . 'util/DB.php';
class Dao_Base {

    protected $_table = null;

    //连接使用$cut分割的数组
    const LIST_COM = 0;
    //解析后and连接
    const LIST_AND = 1;
    // = 号连接
    const LIST_SET = 2;

    private $objDbconn = null;

    protected function __construct($strTable){
        $this->_table    = $strTable;
        $this->objDbconn = $this->getDbConn();
    }

    protected function getDbConn(){
        //获取配置文件
        $arrDbConf = Config::getDbConf();
        $objDbConn = DB::getDbConn($arrDbConf['host'], $arrDbConf['user'], $arrDbConf['password'], $arrDbConf['database']);
        return $objDbConn;
    }

    protected function realEscapeString($string){
        return DB::realEscapeString($string);
    }

    /**
     *
     * 封装select函数
     *
     * @param array $fields  获取列列表
     * @param array $conds
     * @param array $appends 附加参数 eg LIMIT ORDER BY
     * @param array $options eg select HIGH_PRIORITY SQL_BIG_RESULT OperatorID from log;
     * @param array $tables
     * @param int $fetchType 获取类型 FETCH_ASSOC
     * @return string
     */
    public function select($fields, $conds = NULL, $appends = NULL, $options = NULL, $tables = NULL, $fetchType = MYSQLI_ASSOC){
        $sql = 'SELECT ';

        //1.处理options
        if ($options !== NULL){
            $options = $this->makeList($options, self::LIST_COM, ' ');
            if (!strlen($options)){
                //出错的情况，鲁棒性
                return NULL;
            }
            $sql .= $options . ' ';
        }

        //2.处理fields
        $fields = $this->makeList($fields, self::LIST_COM);
        if (!strlen($fields)){
            return NULL;
        }
        $sql    .= $fields . ' FROM ';

        //3.from
        if (is_array($tables)){
            $tables = $this->makeList($tables, self::LIST_COM);
        } else {
            $tables = $this->_table;
        }
        if (!strlen($tables)){
            return NULL;
        }
        $sql .= $tables;

        //4.conditions
        if ($conds !== NULL){
            $conds = $this->makeList($conds, self::LIST_AND);
            if (!strlen($conds)){
                return NULL;
            }
            $sql .= ' WHERE ' . $conds;
        }

        //5.other append
        if ($appends !== NULL){
            $appends = $this->makeList($appends, self::LIST_COM, ' ');
            if (!strlen($appends)){
                return NULL;
            }
            $sql .= ' ' . $appends;
        }

        return $this->execute($sql, $fetchType);
    }

    /**
     * @param array $row 字段
     * @param null $options 选项
     * @param null $table   表名
     * @param null $onDup   键冲突时的字段值列表 eg ON DUPLICATE KEY UPDATE c=c+1;
     * @return null
     */
    public function insert($row, $options = NULL, $table = NULL, $onDup = NULL){
        $sql = 'INSERT INTO ';

        //1.options
        if ($options !== NULL){
            $options = $this->makeList($options, self::LIST_COM, ' ');
            if (!strlen($options)){
                return NULL;
            }
            $sql .= $options . ' ';
        }


        //2.table
        if (is_array($table)){
            $table = $this->makeList($table, self::LIST_COM);
        } else {
            $table .= $this->_table;
        }
        if (!strlen($table)){
            return NULL;
        }
        $sql .= $table . ' SET ';

        //3.clumns & value
        $row = $this->makeList($row, self::LIST_SET);
        if (!strlen($row)){
            return NULL;
        }
        $sql .= $row;

        if (!empty($onDup)){
            $sql   .= ' ON DUPLICATE KEY UPDATE ';
            $onDup  = $this->makeList($onDup, self::LIST_SET);
            if (!strlen($onDup)){
                return NULL;
            }
            $sql .= $onDup;
        }

        return $this->execute($sql);

    }

    /**
     * 用于生成UPDATE语句
     *
     * @param $row
     * @param null $conds
     * @param null $appends
     * @param null $options
     * @param null $table
     * @return bool|mixed|mysqli_result|null
     */
    public function update($row, $conds = NULL, $appends = NULL, $options = NULL, $table = NULL){
        if (empty($row)){
            return NULL;
        }

        if (NULL === $table){
            $table = $this->_table;
        }
        return $this->makeUpdateOrDelete($table, $row, $conds, $appends, $options);
    }

    /**
     * 用于构造删除sql语句
     *
     * @param null $conds
     * @param null $appends
     * @param null $options
     * @param null $table
     * @return bool|mixed|mysqli_result|null
     */
    public function delete($conds = NULL, $appends = NULL, $options = NULL, $table = NULL){
        if (NULL === $table){
            $table = $this->_table;
        }
        return $this->makeUpdateOrDelete($table, NULL, $conds, $appends, $options);
    }

    /**
     * 用于生成update和delete语句的编译逻辑
     *
     * @param $table
     * @param $row
     * @param $conds
     * @param $appends
     * @param $options
     * @return bool|mixed|mysqli_result|null
     */
    private function makeUpdateOrDelete($table, $row, $conds, $appends, $options){
        //1.options
        if ($options !== NULL){
            if (is_array($options)){
                $options = $this->makeList($options, self::LIST_COM, ' ');
            }
            if (!strlen($options)){
                return NULL;
            }
        }

        //2.fields
        //delete情景
        if (empty($row)){
            $sql = 'DELETE ' . $options . ' FROM ' . $table . ' ';
        } else {
            //table情景
            $sql = 'UPDATE ' . $options . ' ' . $table . ' SET ';
            $row = $this->makeList($row, self::LIST_SET);
            if (!strlen($row)){
                return NULL;
            }
            $sql .= $row . ' ';
        }

        //3.conditions  WHERE后
        if ($conds !== NULL){
            $conds = $this->makeList($conds, self::LIST_AND);
            if (!strlen($conds)){
                return NULL;
            }
            $sql .= ' WHERE ' . $conds . ' ';
        }

        //4. other append
        if ($appends !== NULL){
            $appends = $this->makeList($appends, self::LIST_COM, ' ');
            if (!strlen($appends)){
                return NULL;
            }
            $sql .= $appends;
        }

        return $this->execute($sql);
    }

    /**
     * 运行SQL语句，当$fetchType为NULL时表示为增删改操作，无需获取结果
     *
     * @param $sql
     * @param null $fetchType
     * @return bool|mixed|mysqli_result
     */
    private function execute($sql, $fetchType = NULL){
        $objRet = $this->objDbconn->query($sql);
        if (FALSE === $objRet){
            return false;
        }

        if (NULL !== $fetchType){
            return $objRet->fetch_array($fetchType);
        } else {
            return $objRet;
        }
    }

    /**
     *
     * 将数组列表利用type生成$sql语句
     *
     * 这个函数对于研究如何构建通过传参SQL十分有价值
     *
     * DAO层核心逻辑
     *
     * @param $arrList
     * @param int $type
     * @param string $cut
     * @return string
     */
    private function makeList($arrList, $type = self::LIST_SET, $cut = ', '){
        if (is_string($arrList)){
            return $arrList;
        }

        $sql = '';

        //专用于insert或update
        if ($type == self::LIST_SET){
            foreach ($arrList as $name => $value){
                if (is_int($name)){
                    //可以确定是非关联数组 eg 'b=2'
                    $sql .= $value . ', ';
                } else {
                    //关联数组
                    if (!is_int($value)){
                        //用于处理非int情况 1.null 2.需要过滤的字符串型
                        if ($value === NULL){
                            $value = 'NULL';
                        } else {
                            $value = '\'' . $this->realEscapeString($value) . '\'';
                        }
                    }
                    $sql .= $name . '=' . $value . ', ';
                }
            }
            //消除最后的逗号(2个)
            $sql = substr($sql, 0, strlen($sql) - 2);
        } else if($type == self::LIST_AND){
            //针对conds
            foreach ($arrList as $name => $value){
                if (is_int($name)){
                    //针对非关联数组 eg 'log_id = 6'
                    $sql .= '(' . $value . ') AND ';
                } else {
                    if (!is_int($value)){
                        //针对null或需要过滤的字符串情况
                        if ($value === NULL){
                            $value = 'NULL';
                        } else {
                            $value = '\'' . $this->realEscapeString($value) . '\'';
                        }
                    }
                    //eg 'log_id =' => '2'的情况
                    $sql .= '(' . $name . $value . ') AND ';
                }
            }
            $sql = substr($sql, 0, strlen($sql) - 5);
        } else {
            //针对LIST_COM的情况
            $sql = implode($cut, $arrList);
        }

        return $sql;
    }
}