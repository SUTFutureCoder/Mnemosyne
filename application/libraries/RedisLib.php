<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-10
 * Time: 下午11:47
 */
class RedisLib{
    private static $redis;

    public static $prefix = 'Mnemosyne_';

    public function __construct(){
        //禁止new
    }

    public static function getInstance(){
        if (!self::$redis){
            self::$redis = new Redis();
            self::$redis->connect("127.0.0.1",6379);
        }
        return self::$redis;
    }
}