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
require BOSPATH . 'util/dao/Object.php';
class File{

    private static $objObject = NULL;

    private static function getDaoInstance(){
        if (NULL == self::$objObject){
            self::$objObject = new Dao_Object();
        }
        return self::$objObject;
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

        return $arrMime;
    }

    /**
     * 获取文件信息
     *
     * @param $strFile
     * @return array
     */
    public static function getFileInfo($strFile){
        $objDaoObject = self::getDaoInstance();
        $arrField     = Dao_Object::$FIELDS;
        $arrConds     = array(
            'object_index =' => $strFile,
        );
        return $objDaoObject->select($arrField, $arrConds);
    }

    /**
     * 输出文件内容
     *
     * @param $arrFileInfo
     * @param $arrBucketInfo
     * @param $arrOption
     */
    public static function outPut($arrFileInfo, $arrBucketInfo, $arrOption = array()){
        //通过mime决定如何返回（header），仅限audio，image，text，video
        $arrFileType = self::getFileTypeFromMime($arrFileInfo['mime']);
        //获取后缀名 
        $strFileExt  = substr(strrchr($arrFileInfo['name'], '.'), 1);
        $strFileUrl  = Config::getBucketRoot() . $arrBucketInfo['user_id'] . '/' . $arrBucketInfo['bucket_root'] . '/' . $arrFileInfo['object_index'];
        if (in_array($arrFileType[0], array('audio', 'image', 'text', 'video'))) {
            //先把视频情况排除
            switch ($arrFileType[0]) {
                case 'video':
                    include BOSPATH . 'util/VideoStream.php';
                    $objVideoStream = new VideoStream($strFileUrl);
                    $objVideoStream->start();
                    break;
                case 'image':
                    header('content-type: ' . $arrFileInfo['mime']);
                    if (!empty($arrOption)) {
                        //如果有附加值，且为图片执行这个操作
                        self::compressResizeImg($strFileUrl, $arrOption, $strFileExt);
                    } else {
                        readfile($strFileUrl);
                    }
                    break;
                case 'audio':
                    include BOSPATH . 'util/VideoStream.php';
                    $objVideoStream = new VideoStream($strFileUrl, $arrFileInfo['mime']);
                    $objVideoStream->start();
                    break;
            }
            exit;
        } elseif (in_array($arrFileType[1], array('pdf'))){
            //针对后缀名的情况
            switch ($arrFileType[1]){
                case 'pdf':
                    header('content-type: ' . $arrFileInfo['mime']);
                    readfile($strFileUrl);
                    break;
            }
        } else {
            //命令浏览器下载
            header("Content-Type: application/force-download");
            header("Accept-Ranges: bytes");
            header("Content-Length: " . $arrFileInfo['size']);
            header("Content-Disposition: attachment; filename=" . $arrFileInfo['name']);
            readfile($strFileUrl);
            exit;
        }
    }

    /**
     * 从文件流中保存文件
     *
     *
     * @param $arrData
     */
    public static function saveFileStream($arrData){
        //获取bucket信息
        $arrBucketInfo = Bucket::getBucketInfo($arrData['bucket_id']);

        if (empty($arrBucketInfo)){
            Response::responseErrorJson(ErrorCodes::ERROR_NO_SUCH_BUCKET);
        }

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
        $strSha1 = sha1($strData . Config::SALT);
        $strDir .= '/' . $strSha1;

        //检查库中是否已有此文件，如有则直接跳过，节省空间及实现秒传
        //如果能用缓存，这里用缓存。但虚拟主机太坑爹，节约经费……
        $arrFileInfoData = self::getFileInfo($strSha1);
        if (!empty($arrFileInfoData)){
            Response::responseResultJson(array(
                'url' => Config::FILE_URL . $strSha1,
            ));
        }

        $objFp   = fopen($strDir, 'wb');
        fwrite($objFp, $strData);
        fclose($objFp);

        $user = isset($_GET['user']) ? $_GET['user'] : 0;

        //数据库写入
        $daoObject   = new Dao_Object();
        $arrConds    = array(
            'object_id'    => Uuid::genUUID('object'),
            'object_index' => $strSha1,
            'name'         => $_GET['file_name'],
            'mime'         => $_GET['headers']['contentType'],
            'size'         => $_GET['headers']['Content-Length'],
            'sign'         => $_GET['headers']['Content-MD5'],
            'user'         => $user,
            'bucket_id'    => $_GET['bucket_id'],
            'is_public'    => $_GET['is_public'],
            'ctime'        => time(),
        );
        $objQueryRet = $daoObject->insert($arrConds);

        if ($objQueryRet){
            Response::responseResultJson(array(
                'url' => Config::FILE_URL . $strSha1,
            ));
        } else {
            Response::responseErrorJson(ErrorCodes::ERROR_DB_INSERT_OBJECT);
        }
    }

    public static function saveStringStream($arrData){
        //获取bucket信息
        $arrBucketInfo = Bucket::getBucketInfo($arrData['bucket_id']);

        if (empty($arrBucketInfo)){
            Response::responseErrorJson(ErrorCodes::ERROR_NO_SUCH_BUCKET);
        }

        //保存目录下
        $strDir = Config::getBucketRoot() . $arrBucketInfo['user_id'] . '/' . $arrBucketInfo['bucket_root'];

        if (!is_dir($strDir)){
            mkdir($strDir, 0777, true);
        }
        $originString = $_POST['compressedString'];
//        $compressedString = $_POST['compressedString'];
        //$originString     = gzuncompress($compressedString);


        //算出文件的sha1作为文件名
        $strSha1 = sha1($originString . Config::SALT);
        $strDir .= '/' . $strSha1;

        //检查库中是否已有此文件，如有则直接跳过，节省空间及实现秒传
        //如果能用缓存，这里用缓存。但虚拟主机太坑爹，节约经费……
        $arrFileInfoData = self::getFileInfo($strSha1);
        if (!empty($arrFileInfoData)){
            Response::responseResultJson(array(
                'url' => Config::FILE_URL . $strSha1,
            ));
        }

        $objFp   = fopen($strDir, 'wb');
        fwrite($objFp, base64_decode($originString));
        fclose($objFp);

        $user = isset($_GET['user']) ? $_GET['user'] : 0;

        //数据库写入
        $daoObject   = new Dao_Object();
        $arrConds    = array(
            'object_id'    => Uuid::genUUID('object'),
            'object_index' => $strSha1,
            'name'         => $_GET['file_name'],
            'mime'         => $_GET['headers']['contentType'],
            'size'         => $_GET['headers']['Content-Length'],
            'sign'         => $_GET['headers']['Content-MD5'],
            'user'         => $user,
            'bucket_id'    => $_GET['bucket_id'],
            'is_public'    => $_GET['is_public'],
            'ctime'        => time(),
        );
        $objQueryRet = $daoObject->insert($arrConds);

        if ($objQueryRet){
            Response::responseResultJson(array(
                'url' => Config::FILE_URL . $strSha1,
            ));
        } else {
            Response::responseErrorJson(ErrorCodes::ERROR_DB_INSERT_OBJECT);
        }
    }

    /**
     * 通过w、h、q对图片长宽、质量进行控制
     *
     * @param $filePath
     * @param $options
     * @param $strFileExt
     * @return bool
     */
    private static function compressResizeImg($filePath, $options, $strFileExt){
        switch ($strFileExt){
            case 'jpg':
            case 'jpeg':
                $objImg = imagecreatefromjpeg($filePath);
                break;

            case 'bmp':
                $objImg = imagecreatefromwbmp($filePath);
                break;

            case 'png':
                $objImg = imagecreatefrompng($filePath);
                break;

            case 'gif':
                $objImg = imagecreatefromgif($filePath);
                break;

            case 'xbm':
                $objImg = imagecreatefromxbm($filePath);
                break;

            case 'xpm':
                $objImg = imagecreatefromxpm($filePath);
                break;

            default:
                return false;
                break;
        }

        //获取原图大小
        list($defaultOptions['w'], $defaultOptions['h']) = getimagesize($filePath);
        $options = array_merge($defaultOptions, $options);
        //长宽控制按照指定的短边为准，并且等比缩放
        //比如原图750*528 即使指定750*600也不会有变化
        if ($options['w'] > $options['h']){
            //以h为准缩放
            if ($defaultOptions['h'] > $options['h']){
                //当小于原图尺寸时才有缩放价值
                //获取另一边长度
                $options['w'] *= ($options['h'] / $defaultOptions['h']);
                $options['w'] = (int)$options['w'];
            }
        } else {
            if ($defaultOptions['w'] > $options['w']){
                $options['h'] *= ($options['w'] / $defaultOptions['w']);
                $options['h'] = (int)$options['h'];
            }
        }

        $image = imagecreatetruecolor($options['w'], $options['h']);
        imagecopyresampled($image, $objImg, 0, 0, 0, 0, $options['w'], $options['h'], $defaultOptions['w'], $defaultOptions['h']);

        if (!isset($options['q'])){
            //压缩过程放到最后
            $options['q'] = 100;
        }

        switch ($strFileExt){
            case 'jpg':
            case 'jpeg':
            case 'bmp':
                imagejpeg($image, null, $options['q']);
                break;

            case 'png':
                imagepng($image, null, $options['q']);
                break;

            case 'gif':
                imagegif($image, null);
                break;

            case 'xbm':
            case 'xpm':
                imagexbm($image, null);
                break;

            default:
                return false;
                break;
        }

        imagedestroy ($image);
    }
}
