<?php
/**
 * SAL - Service Access Layer
 *
 * 目前支持http协议、websocket协议、共享内存协议
 * 可用string/json/from三种数据打包协议
 *
 *
 * 预计支持nshead、fcgi协议 可能会支持消息队列push？未来的吧
 * mcpack1/mcpack2数据打包协议
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-1-31
 * Time: 下午5:20
 */
require_once 'Timer.php';
class SAL {

    private $_ci;

    private static $objCurl = null;

    public function __construct(){
        $this->_ci =& get_instance();
    }

    private static function getCurlInstance(){
        if (NULL === self::$objCurl){
            self::$objCurl = curl_init();
        }
        return self::$objCurl;
    }

    public static function doHttp($method, $url, $data, $header = array()){
        if (!in_array($method, array('get', 'post',))){
            MLog::fatal(CoreConst::MODULE_SAL, sprintf('curl method error method[%s]', $method));
            return false;
        }

        Timer::start('curl');
        $objCurl    = self::getCurlInstance();
        $arrOptions = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
        );

        if (is_array($header)){
            if (!empty($header)){
                $arrOptions[CURLOPT_HTTPHEADER] = $header;
            }
        } else {
            MLog::fatal(CoreConst::MODULE_SAL, sprintf('You must pass either an object or an array with the CURLOPT_HTTPHEADER argument header[%s]', $header));
            return false;
        }

        if ($method == 'post'){
            $arrOptions += array(CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => http_build_query($data),);
        } else {
            //get
            $arrOptions[CURLOPT_URL] = $url . '?' . http_build_query($data);
        }

        curl_setopt_array($objCurl, $arrOptions);
        $strResult = curl_exec($objCurl);
        curl_close($objCurl);
        Timer::stop('curl');

        MLog::trace(CoreConst::MODULE_SAL, sprintf('doHttp cost[%s]', Timer::get('curl')));

        if (false === $strResult){
            MLog::fatal(CoreConst::MODULE_SAL, sprintf('doHttp curl fail method[%s] url[%s] data[%s] header[%s]',
                    json_encode($method),
                    json_encode($url),
                    json_encode($data),
                    json_encode($header)
                ));
        }

        return $strResult;
    }

    public static function uploadFileStream($url, $filePointer, $fileSize, $urlAppend = NULL){
        $objCurl      = self::getCurlInstance();

        //处理附加URL内容
        if (NULL !== $urlAppend){
            $strUrlAppend = http_build_query($urlAppend);
            $url         .= '?' . $strUrlAppend;
        }

        Timer::start();
        curl_setopt($objCurl, CURLOPT_URL, $url);
        curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, 1);

        //上传相关
        curl_setopt($objCurl, CURLOPT_PUT, 1);
        curl_setopt($objCurl, CURLOPT_UPLOAD, 1);
        curl_setopt($objCurl, CURLOPT_INFILE, $filePointer);
        curl_setopt($objCurl, CURLOPT_INFILESIZE, $fileSize);

        $outPut = curl_exec($objCurl);
        curl_close($objCurl);
        Timer::stop();
        $time   = Timer::get();
        MLog::trace(CoreConst::MODULE_SAL, sprintf('upload file stream cost[%s]', $time));

        $ret    = json_decode($outPut, true);
        if (empty($ret) || !is_array($ret) || $ret['code'] != 0){
            MLog::warning(CoreConst::MODULE_SAL, sprintf('upload file stream error cost[%s] errMessage[%s]', $time, json_encode($ret['data'])));
        }

        return $ret;
    }

    public static function uploadString($url, $string, $urlAppend = NULL){
        $objCurl = self::getCurlInstance();

        if (null !== $urlAppend){
            //和resources类型不同，字符串不需要传输到body
            unset($urlAppend['body']);

            $strUrlAppend = http_build_query($urlAppend);
            $url         .= '?' . $strUrlAppend;
        }
        Timer::start();
        curl_setopt($objCurl, CURLOPT_URL, $url);
        curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, 1);

        //上传相关
        //压缩字符串 尼玛共享主机不支持压缩……
        //$compressedString = gzcompress($string, 9);
        curl_setopt($objCurl, CURLOPT_POST, 1);
        curl_setopt($objCurl, CURLOPT_POSTFIELDS, array('compressedString' => $string));


        $outPut = curl_exec($objCurl);
        curl_close($objCurl);
        Timer::stop();
        $time   = Timer::get();
        MLog::trace(CoreConst::MODULE_BOS, sprintf('upload compressed base64 string cost[%s]', $time));

        $ret    = json_decode($outPut, true);
        if (empty($ret) || !is_array($ret) || $ret['code'] != 0){
            MLog::warning(CoreConst::MODULE_SAL, sprintf('upload compressed base64 string error cost[%s] errMessage[%s]', $time, json_encode($ret['data'])));
        }
        return $ret;
    }

}