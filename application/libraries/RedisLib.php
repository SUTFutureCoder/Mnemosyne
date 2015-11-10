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

    private function __construct(){
        //禁止new
    }

    public static function getInstance(){
        if (!self::$redis){
            self::$redis = new Redis();
        }
        return self::$redis;
    }
}