<?php
class Config{

    private function __construct(){
        //禁止new
    }

    /**
     * 定义BOS服务PATH
     */
    public static function definePath(){
        define('BOSPATH', '/var/www/html/Mnemosyne/bos/');
    }

    /**
     * 数据库配置
     *
     * @return array
     */
    public static function getDbConf(){
        return array(
            //数据库相关
            'host'     => 'localhost',
            'user'     => 'root',
            'password' => '000000',
            'database' => 'bos',
        );
    }

    /**
     * 获取bucket根目录
     *
     * @return string
     */
    public static function getBucketRoot(){
        return BOSPATH . 'resroot/';
    }
}
