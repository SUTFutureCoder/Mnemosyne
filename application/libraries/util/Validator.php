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

    public static function isNumberic($value, $message = '', $filter = FILTER_VALIDATE_INT){
        if (false !== filter_var($value, $filter)){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function isTime($value, $message = ''){
        if (false !== strtotime($value)){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function isString($value, $message = ''){
        if (is_string($value)){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function Range($value, $start, $end, $message = '', $filter = FILTER_VALIDATE_INT){
        if (false !== filter_var($value, $filter) && $value >= $start && $value <= $end){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function greaterThan($value, $reference, $message = '', $filter = FILTER_VALIDATE_INT){
        if (false !== filter_var($value, $filter) && $value > $reference){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function greaterThanOrEqual($value, $reference, $message = '', $filter = FILTER_VALIDATE_INT){
        if (false !== filter_var($value, $filter) && $value >= $reference){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function lessThan($value, $reference, $message = '', $filter = FILTER_VALIDATE_INT){
        if (false !== filter_var($value, $filter) && $value < $reference){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function lessThanOrEqual($value, $reference, $message = '', $filter = FILTER_VALIDATE_INT){
        if (false !== filter_var($value, $filter) && $value <= $reference){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function isBool($value, $message = ''){
        if (true === $value || false === $value || 0 === $value
                || 1 === $value || '0' === $value || '1' === $value){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function isBoolInt($value, $message = ''){
        $value = filter_var($value, FILTER_VALIDATE_INT);
        if (1 === $value || 0 === $value){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function arrayHasKey($key, $array, $message = ''){
        if (isset($array[$key])){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function inArray($needle, $array, $message = ''){
        if (in_array($needle, $array)){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function arrayCountRange($array, $min, $max, $message = ''){
        if (is_array($array)){
            $count = count($array);
            if ($min <= $count && $count <= $max){
                return true;
            }
        }
        self::setMessage($message);
        return false;
    }

    public static function arrayNotEmpty($array, $message = ''){
        if (is_array($array) && count($array) > 0){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function isEqual($expect, $actual, $message = ''){
        if ($expect === $actual){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function any($arg1, $arg2, $message = ''){
        if ($arg1 || $arg2){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function isTrue($arg1, $message = ''){
        if (true === $arg1){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function isFalse($arg1, $message = ''){
        if (false === $arg1){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function isMobile($value, $message =''){
        if(preg_match('/^1[34578][0-9]{9}$/',$value)){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static  function stringRange($string, $min, $max, $message = ''){
        $len = strlen($string);
        if (is_string($string) && $len >= $min && $len <= $max){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function mbStringRange($string, $min, $max, $message = '', $charset = 'utf8'){
        $len = mb_strlen($string, $charset);
        if (is_string($string) && $len >= $min && $len <= $max){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function isEmail($value, $message = ''){
        if (false !== filter_var($value, FILTER_VALIDATE_EMAIL)){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function isUrl($value, $message = '', $flag = null){
        if (false !== filter_var($value, FILTER_VALIDATE_URL, $flag)){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function isIp($value, $message = '', $flag = null){
        if (false !== filter_var($value, FILTER_VALIDATE_URL, $flag)){
            return true;
        }
        self::setMessage($message);
        return false;
    }

    public static function setMessage($message){
//        if (isset(self::$message)){
//            self::$message .= '' . $message;
//        } else {
            self::$message = $message;
//        }
    }

    public static function getMessage(){
        if (isset(self::$message)){
            return self::$message;
        }
        return '';
    }

}