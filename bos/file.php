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

//获取字段
$strFileIndex = !empty($_GET['file']) ? $_GET['file'] : null;
$strAccessKey = !empty($_GET['access_key']) ? $_GET['access_key'] : null;
$strSecretKey = !empty($_GET['secret_key']) ? $_GET['secret_key'] : null;
$strPrivateKey = !empty($_GET['private_key']) ? $_GET['private_key'] : null;

if (null === $strFileIndex){
    Response::responseErrorJson('InvalidHTTPAuthHeader', 'The HTTP authorization header is invalid. Consult the service documentation for details');
    exit;
}

//获取文件内容并通过文件内容获取bucket信息
$fileInfo = File::getFileInfo($strFileIndex);
if (empty($fileInfo)){
    Response::responseErrorJson('NoSuchKey', 'The specified key does not exist.');
}

$fileInfo = $fileInfo[0];
$bucketId = $fileInfo['bucket_id'];
$bucketInfo = Bucket::getBucketInfo($bucketId);
if (empty($bucketInfo)){
    Response::responseErrorJson('NoSuchBucket', 'The specified bucket does not exist.');
}

//反作弊
Anti::antiStealingLink($bucketInfo['enable_host_list'], $bucketInfo['enable_null_referer']);

//获取是否是私有分享文件
if ($fileInfo['private_share_key'] != $strPrivateKey){
    Response::responseErrorJson('PrivateShareKeyError', 'The private share key does not correct. Access denied.');
}

//获取是否为公共文件，如不是则需要accesskey
if ($fileInfo['is_public'] == 0 && $bucketInfo['access_key'] != $strAccessKey){
    Response::responseErrorJson('PrivateAccessKeyError', 'The access key for private file does not correct. Access denied');
}

//验证通过，根据输出文件
File::outPut($fileInfo, $bucketInfo);

//header('content-type: image/jpeg');
//echo file_get_contents('1324868113_61272293.jpg');
