<?php
defined('BOSPATH') OR exit('No direct script access allowed');
/**
 * 返回值控制
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-10
 * Time: 下午6:11
 */
class Response{

    private function __construct(){
        //禁止new
    }

    /**
     * 返回错误信息
     *
     * @param $code
     * @param $message
     */
    public static function responseErrorJson($code, $message = null){
        if (null === $message){
            if (isset(ErrorCodes::$message[$code])){
                $message = ErrorCodes::$message[$code];
            } else {
                $message = 'unknow error';
            }
        }
        echo json_encode(array('code' => $code, 'message' => $message));
        exit;
    }

    /**
     * 返回字符型数据信息
     *
     * @param $arrData
     */
    public static function responseResultJson($arrData){
        echo json_encode(array('code' => 0, 'data' => $arrData));
        exit;
    }
}