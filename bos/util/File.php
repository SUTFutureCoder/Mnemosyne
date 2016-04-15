<?php
defined('BOSPATH') OR exit('No direct script access allowed');
/**
 * 上传文件时，如目录不存在则创建
 *
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
        $strFileUrl  = BOSPATH . $arrBucketInfo['user_id'] . '/' . $arrBucketInfo['bucket_root'] . '/' . $arrFileInfo['object_index'];
        if (in_array($strFileType, array('audio', 'image', 'text', 'video'))) {
            //可以在header中标记原有属性，例如原文件名
            header('content-type: ' . $arrFileInfo['mime']);
            readfile($strFileUrl);
            exit;
        } else {
            //命令浏览器下载
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=" . basename($strFileUrl));
            readfile($strFileUrl);
            exit;
        }
    }

    public static function saveFileStream($arrData){
        //获取bucket信息
        $arrBucketInfo = Bucket::getBucketInfo($arrData['bucket_id']);

        //保存目录下
        $strDir = Config::getBucketRoot() . $arrBucketInfo['user_id'] . '/' . $arrBucketInfo['bucket_root'];

        if (!is_dir($strDir)){
            mkdir($strDir, 0777, true);
        }

        $objInput = fopen('php://input', 'rb');
        $strData  = '';
        while (!feof($objInput)){
            $strData .= fgets($objInput);
        }
        fclose($objInput);

        //算出文件的sha1作为文件名
        $strSha1 = sha1($strData);
        $strDir .= '/' . $strSha1;

        $objFp   = fopen($strDir, 'wb');
        fwrite($objFp, $strData);
        fclose($objFp);




        //TODO数据库操作
    }
}