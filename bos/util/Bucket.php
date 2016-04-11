<?php
defined('BOSPATH') OR exit('No direct script access allowed');
/**
 * Bucket相关
 *
 * 创建bucket需要在bos/resroot下面建立
 *
 * 【求赞助经费～】
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-10
 * Time: 下午10:23
 */
require_once BOSPATH . 'util/DB.php';
class Bucket{

    private static function getDbConn(){
        //获取配置文件
        $arrDbConf = Config::getDbConf();
        $objDbConn = DB::getDbConn($arrDbConf['host'], $arrDbConf['user'], $arrDbConf['password'], $arrDbConf['database']);
        return $objDbConn;
    }

    public static function getBucketInfo($bucketId){
        $objDbConn = self::getDbConn();
        $strQuery  = 'SELECT * FROM bos_bucket WHERE bucket_id="' . DB::realEscapeString($bucketId) . '"';
        $objQueryResult = $objDbConn->query($strQuery);
        return $objQueryResult->fetch_assoc();
    }

    public static function getBucketList(){

    }

}
