<?php
defined('BOSPATH') OR exit('No direct script access allowed');
/**
 *
 * 虚拟主机无法使用redis……
 *
 * 只能使用文件系统
 *
 * 不过可以扩展一下原有uuid生成函数
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-10
 * Time: 下午6:11
 */
class Uuid{
    /**
     * 用于生成UUID
     *
     * @param $strUuidModule
     * @return bool|string
     */
    public static function genUUID($strUuidModule){
        if (!in_array($strUuidModule, Config::$modules)){
            return false;
        }

        $UUID_DIR = BOSPATH . 'resroot/kernel/uuid/';

        $intTimeStamp = time();
        $currentNum   = file_get_contents($UUID_DIR . $strUuidModule);
        $currentNum++;
        file_put_contents($UUID_DIR . $strUuidModule, $currentNum);
        $intAllocUUID = $currentNum;

        //使用timestamp即可知道什么时候生成的
        $intRet       = $intTimeStamp . $intAllocUUID;

        if (intval($intRet) >= PHP_INT_MAX){
            //此处打log，抛异常。log也可以集成于异常中
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
