<?php
/**
 *
 * 用于接收文件
 *
 * 注意区分公用、私有文件
 *
 * 公用文件不需要key，私有文件需要access_key
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-10
 * Time: 下午5:01
 */
require 'config.php';
//载入目录
Config::definePath();
spl_autoload_register(function ($class){
    require BOSPATH . 'util/' . $class . '.php';
});
require BOSPATH . 'util/dao/Bucket.php';


//获取字段
$strFileIndex = !empty($_GET['file']) ? $_GET['file'] : null;

//额外参数
$arrOption    = array();
!empty($_GET['w']) ? $arrOption['w'] = $_GET['w'] : null;
!empty($_GET['h']) ? $arrOption['h'] = $_GET['h'] : null;
!empty($_GET['q']) ? $arrOption['q'] = $_GET['q'] : null;

//密钥相关字段
$strAccessKey = !empty($_GET['access_key']) ? $_GET['access_key'] : null;
$strSecretKey = !empty($_GET['secret_key']) ? $_GET['secret_key'] : null;
$strPrivateKey = !empty($_GET['private_key']) ? $_GET['private_key'] : null;

if (null === $strFileIndex){
    Response::responseErrorJson(ErrorCodes::ERROR_INVALID_HTTP_AUTH_HEADER);
    exit;
}

//获取文件内容并通过文件内容获取bucket信息
$fileInfo = File::getFileInfo($strFileIndex);
if (empty($fileInfo)){
    Response::responseErrorJson(ErrorCodes::ERROR_NO_SUCH_FILE);
}

$bucketId = $fileInfo['bucket_id'];
$bucketInfo = Bucket::getBucketInfo($bucketId);
if (empty($bucketInfo)){
    Response::responseErrorJson(ErrorCodes::ERROR_NO_SUCH_BUCKET);
}

//反作弊
if (false === Anti::antiStealingLink($bucketInfo['enable_host_list'], $bucketInfo['enable_null_referer'])){
    Response::responseErrorJson(ErrorCodes::ERROR_ANTI_STEAL_LINK);
}


//获取是否是私有分享文件
if ($fileInfo['private_share_key'] != $strPrivateKey){
    Response::responseErrorJson(ErrorCodes::ERROR_PRIVATE_SHARE_KEY_ERROR);
}

//获取是否为公共文件，如不是则需要accesskey
if ($fileInfo['is_public'] == 0 && $bucketInfo['access_key'] != $strAccessKey){
    Response::responseErrorJson(ErrorCodes::ERROR_PRIVATE_ACCESS_KEY_ERROR);
}

//验证通过，根据输出文件
File::outPut($fileInfo, $bucketInfo, $arrOption);