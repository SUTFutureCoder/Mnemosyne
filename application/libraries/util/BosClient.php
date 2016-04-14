<?php
/**
 * BOS服务客户端
 *
 * Bricky Open Store
 *
 * 此刻板砖已经连续四周没有贡献代码了……
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-3-19
 * Time: 下午5:04
 */
spl_autoload_register(function ($class){
    require 'Bos/' . $class . '.php';
});
class BosClient {

    //最大用户定义meta数据大小
    const MAX_USER_METADATA_SIZE = 2048;

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

    public static function putObjectFromFile($bucketId, $key, $fileName, $options = array()){
        if (!is_file($fileName)){
            return false;
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
                $contentLength,
                $contentMd5,
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

    public static function putObject($bucketId, $key, $data, $contentLength, $contentMd5, $options = array()){
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

        self::checkData($data);
        
        $headers = array();
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
            BosOptions::PUT,
            array(
                'bucket_id' => $bucketId,
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
            case 'string':
                break;
            case 'resource':
                $streamMetaData = stream_get_meta_data($data);
                if (!$streamMetaData['seekable']){
                    throw new MException(CoreConst::MODULE_BOS, ErrorCodes::ERROR_BOS_CHECK_DATA_FAIL, 'data should be seekable');
                }
                break;
            default:
                throw new MException(CoreConst::MODULE_BOS, ErrorCodes::ERROR_BOS_CHECK_DATA_FAIL, 'invalid data type:'
                    . gettype($data) . ' Only string or resource is accepted.');
        }
    }

    private static function sendRequest($httpMethod, array $arrArgs){
        //这个真是设置默认值的好方法！
        $defaultArgs = array(
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
        //调试好了，迁到SAL
        $objCh = curl_init();
        curl_setopt($objCh, CURLOPT_URL, $strBosHost);
        curl_setopt($objCh, CURLOPT_RETURNTRANSFER, 1);
        //上传相关
        curl_setopt($objCh, CURLOPT_PUT, 1);
        curl_setopt($objCh, CURLOPT_UPLOAD, 1);
        curl_setopt($objCh, CURLOPT_INFILE, $arrArgs['body']);
        curl_setopt($objCh, CURLOPT_INFILESIZE, $arrArgs['headers'][BosHttpHeaders::CONTENT_LENGTH]);
        $output = curl_exec($objCh);
        curl_close($objCh);
        header('content-type: image/jpeg');
        echo $output;
        return $output;
        
    }

}