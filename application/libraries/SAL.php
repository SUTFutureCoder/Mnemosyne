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

    public function __construct(){
        $this->_ci =& get_instance();
    }

    public static function doHttp($method, $url, $data, $header = array()){
        if (!in_array($method, array('get', 'post',))){
            MLog::fatal(CoreConst::MODULE_SAL, sprintf('curl method error method[%s]', $method));
            return false;
        }

        Timer::start('curl');
        $objCurl    = curl_init();
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
}