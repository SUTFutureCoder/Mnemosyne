<?php
/**
 * 用于生成全局唯一UUID
 *
 * Redis可用版
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-1-23
 * Time: 下午3:58
 */
require_once 'RedisLib.php';
class Uuid{

    /**
     * 用于生成对应模块的UUID
     *
     * @param $strUuidModule
     * @return bool|string
     */
    public static function genUUID($strUuidModule){
        if (!in_array($strUuidModule, CoreConst::$uuid)){
            //此处打log
            MLog::fatal(CoreConst::MODULE_KERNEL, sprintf('uuid module not valid uuidModule[%s]', $strUuidModule));
            return false;
        }

        $intTimeStamp = time();
        $intAllocUUID = RedisLib::incr($strUuidModule);

        //使用timestamp即可知道什么时候生成的
        $intRet       = $intTimeStamp . $intAllocUUID;

        if (intval($intRet) >= PHP_INT_MAX){
            //此处打log，抛异常。log也可以集成于异常中
            MLog::fatal(CoreConst::MODULE_KERNEL, 'UUID gen failed - php int max exceed uuid[%s]', $intRet);
            return false;
        }

        return $intRet;
    }


    /**
     * 用于解析uuid生成的时间戳
     *
     * @param $intUuid
     * @return string
     */
    public static function getTimeStamp($intUuid){
        return substr($intUuid, 0, 10);
    }


}