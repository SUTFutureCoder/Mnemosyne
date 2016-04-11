<?php
defined('BOSPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-10
 * Time: 下午11:15
 */
require_once BOSPATH . 'util/DB.php';
class File{

    private static function getDbConn(){
        //获取配置文件
        $arrDbConf = Config::getDbConf();
        $objDbConn = DB::getDbConn($arrDbConf['host'], $arrDbConf['user'], $arrDbConf['password'], $arrDbConf['database']);
        return $objDbConn;
    }

    /**
     * 从mime中获取文件类型
     *
     * @param $strMime
     * @return bool
     */
    public static function getFileTypeFromMime($strMime){
        if (empty($strMime)){
            return false;
        }

        $arrMime = explode('/', $strMime);
        if (empty($arrMime[0])){
            return false;
        }

        return $arrMime[0];
    }

    /**
     * 获取文件信息
     *
     * @param $strFile
     * @return array
     */
    public static function getFileInfo($strFile){
        $objDbConn = self::getDbConn();
        $strQuery  = 'SELECT * FROM bos_object WHERE object_index="' . DB::realEscapeString($strFile) . '"';
        $objQueryResult = $objDbConn->query($strQuery);
        return $objQueryResult->fetch_assoc();
    }

    /**
     * 输出文件内容
     *
     * @param $arrFileInfo
     * @param $arrBucketInfo
     */
    public static function outPut($arrFileInfo, $arrBucketInfo){
        //通过mime决定如何返回（header），仅限audio，image，text，video
        $strFileType = self::getFileTypeFromMime($arrFileInfo['mime']);
        if (in_array($strFileType, array('audio', 'image', 'text', 'video'))){
            header('content-type: ' . $arrFileInfo['mime']);
            echo file_get_contents($arrBucketInfo['bucket_root'] . '/' . $arrFileInfo['object_index']);
            exit;
        }
    }
}