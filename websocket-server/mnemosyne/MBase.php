<?php
/**
 * 用于统一管理Mnemosyne相关脚本
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-2-28
 * Time: 上午10:21
 */
define('APPNAME', 'MWebsocket');
define('APPPATH', __DIR__);
define('BASEPATH', __DIR__);

include BASEPATH . '/../../application/config/config.php';

include BASEPATH . '/util/UtilLog.php';
include BASEPATH . '/util/UtilRedis.php';
include BASEPATH . '/util/UtilToken.php';

include BASEPATH . '/MLogin.php';
include BASEPATH . '/OnMessage.php';

class MBase{
    //提供一些超公共方法

    /**
     * 解析传入串
     *
     * @param $strData
     * @return bool|mixed
     */
    public static function parseData($strData){
        $arrDecodeRet = json_decode($strData, true);
        if (is_string($arrDecodeRet) || $strData == $arrDecodeRet){
            return false;
        }
        return $arrDecodeRet;
    }

    public static function genUUID(){
        $intTimeStamp = time();
        $intAllocUUID = UtilRedis::incr(APPNAME);

        $intRet = $intTimeStamp . $intAllocUUID;

        if (intval($intRet) >= PHP_INT_MAX){
            UtilLog::fatal('UUID gen failed - php int max exceed uuid[%s]', $intRet);
            return false;
        }

        return $intRet;
    }

}