<?php
/**
 * BOS服务客户端
 *
 * Bricky Open Store
 *
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-3-19
 * Time: 下午5:04
 */
require 'Bos/BosOptions.php';
require 'Bos/BosHash.php';
require 'Bos/BosHttpHeaders.php';

require_once 'SAL.php';
class BosClient {

    //最大用户定义meta数据大小
    const MAX_USER_METADATA_SIZE = 2048;

    const DATA_TYPE_STRING   = 'string';
    const DATA_TYPE_RESOURCE = 'resource';



    /**
     * 用于获取文件MIME类型
     *
     * @param $strFileName
     * @return string
     */
    private static function getMimeType($strFileName){
        $objFinfo       = new finfo(FILEINFO_MIME_TYPE);
        return $objFinfo->file($strFileName);
    }

    /**
     * 用于直接获取用户ID
     *
     * @return int
     */
    private static function getUserId(){
        //获取用户ID
        $objCi  =& get_instance();
        $objCi->load->library('session');
        $userId =  $objCi->session->user_id;
        return $userId ? $userId : 0;
    }

    /**
     * 通过文件上传到BOS服务
     *
     * @param $bucketId
     * @param $key
     * @param $fileName
     * @param $isPublic
     * @param array $options
     * @return bool|mixed
     * @throws MException
     */
    public static function putObjectFromFile($bucketId, $key, $fileName, $isPublic = 1, $options = array()){
        if (!is_file($fileName)){
            throw new MException(CoreConst::MODULE_BOS, ErrorCodes::ERROR_BOS_FILE_NOT_EXIST);
        }

        //百度库首先获取mime
        $options[BosOptions::CONTENT_TYPE] = self::getMimeType($fileName);
        $contentLength = isset($options[BosOptions::CONTENT_LENGTH]) ? $options[BosOptions::CONTENT_LENGTH] : null;
        $contentMd5    = isset($options[BosOptions::CONTENT_MD5])    ? $options[BosOptions::CONTENT_MD5]    : null;

        if ($contentLength === null){
            //重新获取
            $contentLength = filesize($fileName);
        } else {
            if (!is_int($contentLength) && !is_long($contentLength)){
                throw new MException(CoreConst::MODULE_BOS, ErrorCodes::ERROR_BOS_CONTENT_LENGTH);
            }
            unset($options[BosOptions::CONTENT_LENGTH]);
        }

        //文件操作
        $objFp = fopen($fileName, 'rb');
        if ($contentMd5 === null){
            $contentMd5 = base64_encode(BosHash::md5FromStream($objFp, 0, $contentLength));
        } else {
            unset($options[BosOptions::CONTENT_MD5]);
        }

        try {
            $response = self::putObject(
                $bucketId,
                $key,
                $objFp,
                basename($fileName),
                $contentLength,
                $contentMd5,
                $isPublic,
                self::getUserId(),
                BosOptions::FILE,
                BosOptions::putObjectFromFile,
                $options
            );
            if (is_resource($objFp)){
                fclose($objFp);
            }
            return $response;
        } catch (MException $e){
            if (is_resource($objFp)){
                fclose($objFp);
            }
            throw $e;
        }
    }

    /**
     * string必须为base64类型
     *
     * @param $bucketId
     * @param $key
     * @param $data
     * @param $fileName
     * @param int $isPublic
     * @param array $options
     * @return mixed
     * @throws MException
     */
    public static function putObjectFromString($bucketId, $key, $data, $fileName, $isPublic = 1, $options = array()){
        if (empty($fileName)){
            throw new MException(CoreConst::MODULE_BOS, ErrorCodes::ERROR_BOS_STRING_FILE_NAME_EMPTY);
        }

        if (!is_string($data)){
            throw new MException(CoreConst::MODULE_BOS, ErrorCodes::ERROR_BOS_STRING_DATA_NOT_VALID);
        }

        if (empty($options[BosOptions::CONTENT_TYPE]) || false === strpos($options[BosOptions::CONTENT_TYPE], '/')){
            throw new MException(CoreConst::MODULE_BOS, ErrorCodes::ERROR_BOS_STRING_MIME_NOT_VALID);
        }

        return self::putObject(
            $bucketId, 
            $key, 
            $data, 
            $fileName, 
            strlen($data), 
            base64_encode(md5($data, true)), 
            $isPublic,
            self::getUserId(),
            BosOptions::FILE,
            BosOptions::putObjectFromString,
            $options);
    }

    /**
     * 向BOS服务器放置资源
     *
     * @param int $bucketId bucket UUID
     * @param int $key      操作bucket密钥
     * @param resource $data    文件指针
     * @param string $fileName  原文件名
     * @param int $contentLength 文件大小
     * @param string $contentMd5 文件MD5信息
     * @param int $isPublic     是否为公开文件
     * @param int $user        上传文件用户
     * @param string $funcType    执行BOS服务函数类型
     * @param string $funcQt      执行BOS服务函数
     * @param array $options    附加信息
     * @return mixed
     * @throws MException
     */
    public static function putObject($bucketId, $key, $data, $fileName, $contentLength, $contentMd5, $isPublic, $user, $funcType, $funcQt, $options = array()){
        if (empty($key)){
            throw new MException(CoreConst::MODULE_BOS, ErrorCodes::ERROR_BOS_KEY_EMPTY);
        }

        if (!is_int($contentLength) && !is_long($contentMd5)){
            throw new MException(CoreConst::MODULE_BOS, ErrorCodes::ERROR_BOS_CONTENT_LENGTH);
        }
        
        if ($contentLength < 0){
            throw new MException(CoreConst::MODULE_BOS, ErrorCodes::ERROR_BOS_CONTENT_LENGTH, 'content length should not be negative');
        }
        
        if (empty($contentMd5)){
            throw new MException(CoreConst::MODULE_BOS, ErrorCodes::ERROR_BOS_CONTENT_MD5);
        }

        //获取数据类型 可能为string或resource
        $dataType = self::checkData($data);
        
        $headers  = array();
        $headers[BosHttpHeaders::CONTENT_LENGTH] = $contentLength;
        $headers[BosHttpHeaders::CONTENT_MD5]    = $contentMd5;
        //将options中信息放入headers
        $headers[BosOptions::CONTENT_TYPE]   = isset($options[BosOptions::CONTENT_TYPE])   ? $options[BosOptions::CONTENT_TYPE]   : null;
        $headers[BosOptions::CONTENT_SHA256] = isset($options[BosOptions::CONTENT_SHA256]) ? $options[BosOptions::CONTENT_SHA256] : null;
        $arrUserMeta = isset($options[BosOptions::USER_METADATA])  ? $options[BosOptions::USER_METADATA]  : null;
        if (is_array($arrUserMeta)){
            $metaSize = 0;
            foreach ($arrUserMeta as $key => $value){
                $key   = BosHttpHeaders::USER_METADATA_PREFIX . urlencode(trim($key));
                $value = urlencode($value);
                $metaSize += strlen($key) + strlen($value);
                if ($metaSize > self::MAX_USER_METADATA_SIZE){
                    throw new MException(CoreConst::MODULE_BOS, ErrorCodes::ERROR_BOS_MAX_USER_METADATA, 'User metadata size should not be greater than ' . self::MAX_USER_METADATA_SIZE);
                }
                $headers[$key] = $value;
            }
        }

        return self::sendRequest(
            $dataType,
            array(
                //指定执行的方法
                'type'      => $funcType,
                'qt'        => $funcQt,

                //操作用户ID
                'user'      => $user,

                'file_name' => $fileName,
                'bucket_id' => $bucketId,
                'is_public' => $isPublic ? 1 : 0,
                'key'       => $key,
                'body'      => $data,
                'headers'   => $headers,
            )
        );
    }

    /**
     * 检验数据格式是否正确，仅接收字符串或资源上传
     *
     * @param $data
     * @throws MException
     */
    private static function checkData($data){
        switch (gettype($data)){
            case self::DATA_TYPE_STRING:
                if (!is_string($data)){
                    throw new MException(CoreConst::MODULE_BOS, ErrorCodes::ERROR_BOS_STRING_DATA_NOT_VALID);
                }
                return self::DATA_TYPE_STRING;
                break;
            case self::DATA_TYPE_RESOURCE:
                $streamMetaData = stream_get_meta_data($data);
                if (!$streamMetaData['seekable']){
                    throw new MException(CoreConst::MODULE_BOS, ErrorCodes::ERROR_BOS_CHECK_DATA_FAIL, 'data should be seekable');
                }
                return self::DATA_TYPE_RESOURCE;
                break;
            default:
                throw new MException(CoreConst::MODULE_BOS, ErrorCodes::ERROR_BOS_CHECK_DATA_FAIL, 'invalid data type:'
                    . gettype($data) . ' Only string or resource is accepted.');
        }
    }

    private static function sendRequest($requestType, array $arrArgs){
        //这个真是设置默认值的好方法！
        $defaultArgs = array(
            //指定执行的方法
            'type'      => null,
            'qt'        => null,

            //操作用户ID
            'user'      => null,

            //BUCKET相关
            'file_name' => null,
            'bucket_id' => null,
            'key'       => null,
            'body'      => null,
            'headers'   => array(),
            'params'    => array(),
            'outputStream'      => null,
            'parseUserMetadata' => false,
        );

        $arrArgs = array_merge($defaultArgs, $arrArgs);
        
        if (!isset($arrArgs['headers'][BosHttpHeaders::CONTENT_TYPE])){
            $arrArgs['headers'][BosHttpHeaders::CONTENT_TYPE] = BosHttpHeaders::JSON;
        }

        //开始传输
        $objCi =& get_instance();
        $strBosHost = $objCi->config->item('bos_host');

        if ($requestType == self::DATA_TYPE_RESOURCE){
            $outPut = SAL::uploadFileStream($strBosHost, $arrArgs['body'], $arrArgs['headers'][BosHttpHeaders::CONTENT_LENGTH], $arrArgs);
        } else if ($requestType == self::DATA_TYPE_STRING){
            $outPut = SAL::uploadString($strBosHost, $arrArgs['body'], $arrArgs);
        }


        return $outPut;
    }
}