<?php
/**
 * 用于追BUG
 *
 * 所有文件务必自动加载此类
 *
 * 唯一的LOG ID方便查询
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-1-23
 * Time: 下午3:19
 */
require_once 'CoreConst.php';
require_once 'Uuid.php';
class MLog{

    private static $objCoreConst;

    //LOG基础位置
    private static $strLogPath;

    private static $intLogUuid;

    //获取MLOG是否打开，以及存储位置。
    //调用本类所有函数请务必首先
    private static function getLogStatus(){
        if (empty(self::$objCoreConst)){
            self::$objCoreConst = new CoreConst();
        }

        $tempObjCoreConst = self::$objCoreConst;

        if (0 === $tempObjCoreConst::MNEMOSYNE_LOG){
            return false;
        }

        if (empty(self::$intLogUuid)){
            self::$intLogUuid = Uuid::genUUID(CoreConst::LOG_UUID);
        }

        self::$strLogPath = APPPATH . 'logs/';
    }

    //用于打trace级别log
    //执行每一步最好都打一下
    public static function trace($strTraceMsg){
        self::getLogStatus();


        echo self::$intLogUuid;
    }



}