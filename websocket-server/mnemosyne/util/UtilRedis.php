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

    public static $redis = null;

    public function getInstance(){
        if (is_null(self::$redis)){
            self::$redis = new Redis();
        }
        return self::$redis;
    }

    public static function get($strKey){
        self::getInstance();
        return self::$redis->get($strKey);
    }

    public static function exists($arrKeys){
        self::getInstance();
        return self::$redis->exists($arrKeys);
    }

}