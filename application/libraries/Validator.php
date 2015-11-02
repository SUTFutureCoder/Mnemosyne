<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-2
 * Time: 上午12:22
 */
//用于对传入数据进行统一验证
class Validator{

    protected static $message;

    public static function isArray($value, $message = ''){
        if (is_array($value)){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function isNotEmpty($value, $message = ''){
        if ('' === $value || null === $value){
            self::setMessage($message);
            return false;
        }
        return true;
    }

    public static function setMessage($message){
        if (isset(self::$message)){
            self::$message .= '或' . $message;
        } else {
            self::$message = $message;
        }
    }

    public static function getMessage(){
        if (isset(self::$message)){
            return self::$message;
        }
        return '';
    }

}