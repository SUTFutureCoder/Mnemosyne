<?php
defined('BOSPATH') OR exit('No direct script access allowed');
/**
 * 数据库连接
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-10
 * Time: 下午5:08
 */
class DB{

    private static $db;

    /**
     * 工厂单例返回数据库连接
     *
     * @param $host
     * @param $user
     * @param $password
     * @param $database
     * @return mysqli
     */
    public static function getDbConn($host, $user, $password, $database){
        if (!self::$db){
            self::$db = new mysqli($host, $user, $password, $database);
        }
        return self::$db;
    }

    /**
     * 过滤字符串，防注入
     *
     * @param $string
     * @return string
     */
    public static function realEscapeString($string){
        if (!self::$db){
            return false;
        }

        return mysqli_real_escape_string(self::$db, $string);
    }

}