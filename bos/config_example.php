<?php
/**
 * 配置
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-10
 * Time: 下午5:03
 */
class Config{

    //BOS服务文件地址
    const FILE_URL = 'http://localhost:10090/Mnemosyne/bos/file.php?file=';

    //用户最大bucket数量
    const USER_MAX_BUCKET_SUM = 5;

    //uuid相关
    public static $modules = array('bucket', 'user', 'object', 'file');

    //允许调用的函数白名单
    public static $funcWhiteList = array(
        'Bucket' => array(
            'getBucketListByUserId',
        ),
        'File'   => array(
            //保存文件流
            'saveFileStream',
            //保存字符串流
            'saveStringStream',
        )
    );

    //Token Salt
    const SALT = '';

    private function __construct(){
        //禁止new
    }

    /**
     * 定义BOS服务PATH
     */
    public static function definePath(){
        define('BOSPATH', '');
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
            'user'     => '',
            'password' => '',
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
