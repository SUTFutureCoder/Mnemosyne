<?php
/**
 * websocket日志系统
 *
 *
 * Mnemosyne日志系统最小实现
 *
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-2-28
 * Time: 上午11:06
 */
class UtilLog{
    private static $lastErrorMessage = null;

    public static function fatal($strFatalMsg){
        self::putLogIntoFile('websocket', $strFatalMsg);
        self::$lastErrorMessage = $strFatalMsg;
    }

    private static function getPath(){
        return __DIR__ . '/../../log/websocket.wf.' . date('YmdH') . '.log';
    }


    /**
     * 向文件中输出日志文件
     *
     * @param $strLogType
     * @param $strLogMsg
     */
    private static function putLogIntoFile($strLogType, $strLogMsg){
        if (!is_string($strLogMsg)){
            $strLogMsg = json_encode($strLogMsg);
        }

        //获取函数调用顺序
        $strBackTrace  = debug_backtrace();

        $strLog = sprintf("%s: %s [%s:%s] args%s %s" . PHP_EOL ,
            $strLogType,
            date('y-m-d H:i:s'),
            $strBackTrace[1]['file'],
            $strBackTrace[1]['line'],
            json_encode($strBackTrace[1]['args']),
            $strLogMsg);

        file_put_contents(self::getPath(), $strLog, FILE_APPEND);
    }

    public static function getLastError(){
        return self::$lastErrorMessage;
    }
}