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
require_once BOSPATH . 'util/dao/Bucket.php';
class Bucket{

    private static $objDaoBucket = NULL;

    private static function getDaoInstance(){
        if (NULL === self::$objDaoBucket){
            self::$objDaoBucket = new Dao_Bucket();
        }
        return self::$objDaoBucket;
    }

    /**
     * 获取bucket信息
     *
     * @param $bucketId
     * @return array
     */
    public static function getBucketInfo($bucketId){
        $objDao   = self::getDaoInstance();
        $arrField = Dao_Bucket::$FIELDS;
        $arrConds = array(
            'bucket_id =' => $bucketId,
        );
        $arrRet   = $objDao->select($arrField, $arrConds);
        return $arrRet;
    }


    /**
     * [管理员]通过用户id获取bucket列表
     *
     * @param $userId
     * @return array
     */
    public static function adminGetBucketListByUserId($userId){
        $objDao   = self::getDaoInstance();
        $arrField = Dao_Bucket::$FIELDS;
        $arrConds = array(
            'user_id =' => $userId,
        );
        $arrRet   = $objDao->select($arrField, $arrConds);
        return $arrRet;
    }

    /**
     * 根据用户名获取bucket信息
     *
     * @param $userId
     * @param $accessKey
     * @return array
     */
    public static function getBucketListByUserId($userId, $accessKey){
        $objDao   = self::getDaoInstance();
        $arrField = Dao_Bucket::$FIELDS;
        $arrConds = array(
            'user_id    =' => $userId,
            'access_key =' => $accessKey,
        );
        $arrRet   = $objDao->select($arrField, $arrConds);
        //进行脱敏操作
        if ($arrRet){
            unset($arrRet['access_key']);
            unset($arrRet['secret_key']);
            return $arrRet;
        }
        return false;
    }

}
