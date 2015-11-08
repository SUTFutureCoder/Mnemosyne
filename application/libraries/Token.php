<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-1
 * Time: 下午12:00
 */
class Token{
    private $_ci;

    private $_prefix = 'token_';

    public function __construct(){
        $this->_ci =& get_instance();
    }

    public function getToken($userId){
        //确认敏感操作时必须验证密码并重新赋予token
        exec('ifconfig', $serverMac);
        return md5($userId . '-' . $serverMac[0] . '-' . time());
    }

    public function setTokenToRedis($userId, $token = '', $tokenType = 'access', $expire = 86400){
        $this->_ci->load->library('RedisLib');
        if (empty($token)){
            $token = $this->getToken($userId);
        }

        $redis = RedisLib::getInstance();
        return $redis->setex(RedisLib::$prefix . $this->_prefix . $tokenType . $userId, $expire, $token);
    }

    public function checkToken($userId, $userToken, $tokenType = 'access'){
        if (empty($userId) || empty($userToken)){
            return false;
        }

        $this->_ci->load->library('RedisLib');
        $redis = RedisLib::getInstance();

        if ($userToken !== $redis->get(RedisLib::$prefix . $this->_prefix . $tokenType . $userId)){
            return false;
        }

        return true;

    }


}