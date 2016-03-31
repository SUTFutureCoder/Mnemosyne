<?php
/**
 * 针对日志记录的error code
 *
 * 请在此文件填充
 *
 *
 * response文件里面是云安全部的写法
 *
 * 这个是百度ODP的写法，可以逐渐迁到这来
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-3-31
 * Time: 下午4:57
 */
class ErrorCodes {

    //通用错误码,占用100,200开头
    const OK                        = 0;
    const ERROR_PARAM_ERROR         = 100;
    const ERROR_NETWORK_ERROR       = 101;
    const ERROR_USER_NOT_LOGIN      = 102;
    const ERROR_JSON_FORMAT_ERROR   = 103;
    const ERROR_PHP_INPUT_NULL      = 104;
    const ERROR_SAL                 = 105;
    const ERROR_JSON_DECODE         = 106;
    const ERROR_GEN_UUID            = 107;
    const ERROR_UUID_MAX            = 108;
    const ERROR_REDIS               = 109;
    const ERROR_FUNC_NON_EXISTS     = 110;
    const ERROR_IP_UNAUTHORIZED     = 111;

    //DB
    const ERROR_DB_CONNECT = 206;
    const ERROR_DB_INSERT  = 207;
    const ERROR_DB_UPDATE  = 208;
    const ERROR_DB_DELETE  = 209;
    const ERROR_DB_SELECT  = 210;

    public static $error_codes = array(
        self::ERROR_PARAM_ERROR     => 'param error',
        self::ERROR_NETWORK_ERROR   => 'network error',
        self::ERROR_USER_NOT_LOGIN  => 'user not login',
        self::ERROR_JSON_FORMAT_ERROR => 'json format error',
        self::ERROR_PHP_INPUT_NULL  => 'file get content php input error',
        self::ERROR_SAL             => 'do http query by sal failed',
        self::ERROR_DB_CONNECT      => 'db connect error',
        self::ERROR_DB_INSERT       => 'db insert error',
        self::ERROR_DB_UPDATE       => 'db update error',
        self::ERROR_DB_DELETE       => 'db delete error',
        self::ERROR_DB_SELECT       => 'db select error',
        self::ERROR_GEN_UUID        => 'gen uuid error',
        self::ERROR_UUID_MAX        => 'uuid too long error',
        self::ERROR_REDIS           => 'redis error',
        self::ERROR_FUNC_NON_EXISTS => 'function non exists error',
        self::ERROR_IP_UNAUTHORIZED => 'unauthorized IP',
    );

    /**
     * 用于根据常量获取的errno错误码获取错误信息
     *
     *
     * @param $errno
     * @return string
     */
    public static function errMsg($errno){
        $errMsg = self::$error_codes[$errno];
        if (empty($errMsg)){
            $errMsg = 'Errno msg not found. errno.:' . $errno;
        }

        return $errMsg;
    }

}
