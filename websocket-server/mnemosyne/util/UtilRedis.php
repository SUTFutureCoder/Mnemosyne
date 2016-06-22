<?php
/**
 * 为mnemosyne websocket设计的Redis基类，用于单例
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-2-27
 * Time: 下午10:38
 */
class UtilRedis{

    private static $redis = null;

    private static function getInstance(){
        if (is_null(self::$redis)){
            self::$redis = new Redis();
            self::$redis->connect("127.0.0.1", 6379);
        }
        return self::$redis;
    }

    public static function get($strKey){
        self::getInstance();
        return self::$redis->get($strKey);
    }

    public static function exists($strKey){
        self::getInstance();
        return self::$redis->exists($strKey);
    }

    public static function incr($strKey){
        self::getInstance();
        return self::$redis->incr($strKey);
    }

}