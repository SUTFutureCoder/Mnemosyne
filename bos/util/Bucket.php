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

    /**
     * 获取bucket信息
     *
     * @param $bucketId
     * @return array
     */
    public static function getBucketInfo($bucketId){
        $objDbConn = self::getDbConn();
        $strQuery  = 'SELECT * FROM bos_bucket WHERE bucket_id="' . DB::realEscapeString($bucketId) . '"';
        $objQueryResult = $objDbConn->query($strQuery);
        return $objQueryResult->fetch_assoc();
    }


    /**
     * [管理员]通过用户id获取bucket列表
     *
     * @param $userId
     * @return array
     */
    public static function adminGetBucketListByUserId($userId){
        $objDbConn = self::getDbConn();
        $strQuery  = 'SELECT * FROM bos_bucket WHERE user_id="' . DB::realEscapeString($userId) . '"';
        $objQueryResult = $objDbConn->query($strQuery);
        return $objQueryResult->fetch_assoc();
    }

    /**
     * 根据用户名获取bucket信息
     *
     * @param $userId
     * @param $accessKey
     * @return array
     */
    public static function getBucketListByUserId($userId, $accessKey){
        $objDbConn = self::getDbConn();
        $strQuery  = 'SELECT * FROM bos_bucket WHERE user_id="' . DB::realEscapeString($userId)
            . '" AND access_key="' . DB::realEscapeString($accessKey) . '"';
        $objQueryResult = $objDbConn->query($strQuery);
        $arrRet    = $objQueryResult->fetch_assoc();
        //进行脱敏操作
        if ($arrRet){
            unset($arrRet['access_key']);
            unset($arrRet['secret_key']);
            return $arrRet;
        }
        return false;
    }

}
