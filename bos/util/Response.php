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

    public static function responseErrorJson($code, $message){
        echo json_encode(array('code' => $code, 'message' => $message));
        exit;
    }
}