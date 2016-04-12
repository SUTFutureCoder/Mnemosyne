<?php
defined('BOSPATH') OR exit('No direct script access allowed');
/**
 *
 * 密钥生成机制
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-12
 * Time: 下午3:44
 */
class Token{

    public static function getToken($userId){
        //共享主机无法调用exec……
//        exec('ifconfig', $serverMac);
        return hash('sha256', $userId . '@' . Config::SALT . '-' . microtime(true) . rand(0, 2147483647));
    }

}