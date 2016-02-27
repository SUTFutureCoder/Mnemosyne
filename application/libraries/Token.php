<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-1
 * Time: 下午12:00
 */
class Token{
    private $_ci;

    private $_prefix = 'token:';

    public function __construct(){
        $this->_ci =& get_instance();
    }

    public function getToken($userId){
        //确认敏感操作时必须验证密码并重新赋予token
        exec('ifconfig', $serverMac);
        return md5($userId . '-' . $serverMac[0] . '-' . microtime(true));
    }

    public function setTokenToRedis($userId, $token = '', $tokenType = '', $expire = 86400){
        $this->_ci->load->library('RedisLib');
        if (empty($token)){
            $token = $this->getToken($userId);
        }

        $redis = RedisLib::getInstance();
        if ($redis->setex(RedisLib::$prefix . $this->_prefix . $tokenType . $userId, $expire, $token)){
            return $token;
        } else {
            MLog::fatal(CoreConst::MODULE_KERNEL, sprintf('set token to redis error tokenType[%s] userId[%s] expire[%s] token[%s]',
                    $tokenType,
                    $userId,
                    $expire,
                    $token
                ));
            return false;
        }

    }

    public function checkToken($userId, $userToken, $tokenType = ''){
        if (empty($userId) || empty($userToken)){
            MLog::fatal(CoreConst::MODULE_KERNEL, sprintf('check token error userId[%s] userToken[%s] tokenType[%s]',
                    $userId,
                    $userToken,
                    $tokenType
                ));
            return false;
        }

        $this->_ci->load->library('RedisLib');
        $redis = RedisLib::getInstance();

        if ($userToken !== $redis->get(RedisLib::$prefix . $this->_prefix . $tokenType . $userId)){
            MLog::fatal(CoreConst::MODULE_ACCOUNT, sprintf('token check failed  userId[%s]', $userId));
            //token验证失败罚时三秒
            sleep(3);
            return false;
        }

        return true;

    }


}