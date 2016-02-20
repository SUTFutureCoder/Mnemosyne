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
require_once 'Uuid.php';
class MLog{

    private static $objCoreConst;

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
    }

    /**
     * 用于创建并获取log位置
     *
     * @param $strLogType
     * @param $strModule
     * @return string
     */
    private static function getPath($strLogType, $strModule){
        /**
         * log 文件夹设计
         *
         * trace和notice
         * 存放在
         * APPPATH/logs/$strModule/$strModule.log.2016012317
         *
         * warning和fatal
         * 存放在
         * APPPATH/logs/$strModule/$strModule.log.wf.2016012317
         *
         */
        if (!is_dir(APPPATH . 'logs/')){
            mkdir(APPPATH . 'logs/');
        }

        $strLogPath = APPPATH . 'logs/';

        if (!is_dir($strLogPath . $strModule)){
            mkdir($strLogPath . $strModule);
        }

        $strLogPath .= $strModule;

        switch ($strLogType){
            case 'trace':
            case 'notice':
                $strLogPath .= '/' . $strModule . '.' . date('YmdH') . '.log';
                break;

            case 'warning':
            case 'fatal':
                $strLogPath .= '/' . $strModule . '.wf.' . date('YmdH') . '.log';
                break;
        }

        if (!is_file($strLogPath)){
            touch($strLogPath);
        }

        return $strLogPath;
    }

    /**
     * 向文件中输出日志文件
     *
     * @param $strLogPath
     * @param $strLogType
     * @param $strLogMsg
     */
    private static function putLogIntoFile($strLogPath, $strLogType, $strLogMsg){
        if (!is_string($strLogMsg)){
            $strLogMsg = json_encode($strLogMsg);
        }

        //获取函数调用顺序
        $strBackTrace  = debug_backtrace();

        $strLog = sprintf("%s: %s [%s:%s] args%s logId[%s] uri[%s] userId[%s] %s" . PHP_EOL ,
            $strLogType,
            date('y-m-d H:i:s'),
            $strBackTrace[1]['file'],
            $strBackTrace[1]['line'],
            json_encode($strBackTrace[1]['args']),
            self::$intLogUuid,
            $_SERVER['PATH_INFO'],
            CoreConst::$userId,
            $strLogMsg);

        file_put_contents($strLogPath, $strLog, FILE_APPEND);
    }

    //用于打trace级别log
    //执行每一步最好都打一下
    public static function trace($strModule, $strTraceMsg){
        if (false === self::getLogStatus()){
            return false;
        }

        $strLogPath = self::getPath(__FUNCTION__, $strModule);

        self::putLogIntoFile($strLogPath, __FUNCTION__, $strTraceMsg);
    }


    public static function notice($strModule, $strNoticeMsg){
        if (false === self::getLogStatus()){
            return false;
        }

        $strLogPath = self::getPath(__FUNCTION__, $strModule);

        self::putLogIntoFile($strLogPath, __FUNCTION__, $strNoticeMsg);
    }

    public static function warning($strModule, $strWarningMsg){
        if (false === self::getLogStatus()){
            return false;
        }

        $strLogPath = self::getPath(__FUNCTION__, $strModule);

        self::putLogIntoFile($strLogPath, __FUNCTION__, $strWarningMsg);
    }

    public static function fatal($strModule, $strFatalMsg){
        if (false === self::getLogStatus()){
            return false;
        }

        $strLogPath = self::getPath(__FUNCTION__, $strModule);

        self::putLogIntoFile($strLogPath, __FUNCTION__, $strFatalMsg);
    }
}