<?php
/**
 *
 * Token类
 *
 * 验证token时必须附带签名
 *
 * token在登录时赋予一次
 *
 *
 * redis：
 * 合法的token中包含了用户的重要信息
 *
 * user中包含token用于多平台
 *
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-1
 * Time: 下午12:00
 */
class Token{
    private $_ci;

    //redis中token存储
    private $_prefix     = 'token:user:';
    //redis中user platform table
    private $_userPrefix = 'token:usertable:';
    //redis中包含用户数据的token
    private $_userDataPrefix = 'token:userdata:';


    public function __construct(){
        $this->_ci =& get_instance();
        $this->_ci->load->library('RedisLib');
    }

    /**
     * 算出token
     *
     * @param $userId
     * @return string
     */
    private function getToken($userId){
        //确认敏感操作时必须验证密码并重新赋予token
        exec('ifconfig', $serverMac);
        return hash('sha256', $userId . '@' . $serverMac[0] . '-' . microtime(true) . rand(0, 2147483647));
    }

    /**
     * 将token记录到redis中
     *
     * 当token为空时，会自动生成
     *
     * @param $userId
     * @param $platform
     * @param string $token
     * @param string $tokenType
     * @param int $expire
     * @return bool|string
     */
    public function setTokenToRedis($userId, $platform, $token = '', $tokenType = '', $expire = CoreConst::TOKEN_EXPIRE){
        if (empty($userId)|| empty($platform)){
            MLog::fatal(CoreConst::MODULE_ACCOUNT, 'userId or platform is missing');
            return false;
        }

        if (empty($token)){
            $token = $this->getToken($userId);
        }

        if (false === $this->setUserTokenTable($userId, $token, $platform, $expire)){
            return false;
        }

        $redis = RedisLib::getInstance();
        if ($redis->setex(RedisLib::$prefix . $this->_prefix . $tokenType . $userId, $expire, $token)){
            return $token;
        } else {
            MLog::fatal(CoreConst::MODULE_ACCOUNT, sprintf('set token to redis error tokenType[%s] userId[%s] expire[%s] token[%s]',
                    $tokenType,
                    $userId,
                    $expire,
                    $token
                ));
            return false;
        }

    }

    /**
     * 用于检查token
     *
     * @param $userId
     * @param $userToken
     * @param $userSignature
     * @param string $tokenType
     * @return bool
     */
    public function checkToken($userId, $userToken, $userSignature, $tokenType = ''){
        if (empty($userId) || empty($userToken) || empty($userSignature)){
            MLog::fatal(CoreConst::MODULE_ACCOUNT, sprintf('check token error userId[%s] userToken[%s] tokenType[%s] signature[%s]',
                    $userId,
                    $userToken,
                    $tokenType,
                    $userSignature
                ));
            return false;
        }

        //先验证签名
        if (false === $this->checkTokenSignature($userToken, $userSignature, $userId)){
            MLog::fatal(CoreConst::MODULE_ACCOUNT, sprintf('user token signature check failed  userId[%s]', $userId));
            return false;
        }

        $redis = RedisLib::getInstance();
        if ($userToken != $redis->get(RedisLib::$prefix . $this->_prefix . $tokenType . $userId)){
            MLog::fatal(CoreConst::MODULE_ACCOUNT, sprintf('token check failed  userId[%s]', $userId));
            //token验证失败罚时三秒
            sleep(3);
            return false;
        }
        return true;
    }

    /**
     * 根据token算出签名
     *
     * @param $token
     * @param $userId
     * @return bool|string
     */
    public function getTokenSignature($token, $userId){
        $salt = $this->_ci->config->item('token_salt');
        if (empty($salt)){
            MLog::fatal(CoreConst::MODULE_ACCOUNT, sprintf('token salt not defined in config.php'));
            return false;
        }

        if (empty($userId)){
            MLog::fatal(CoreConst::MODULE_ACCOUNT, sprintf('userId was missing'));
            return false;
        }
        return hash('sha256', $token . 'WELCOME' . $userId . 'PROJECT M' . $salt . '@' . $userId);
    }

    /**
     * 检查token签名
     *
     * 注意全部和token相关的方法。必须验证签名！
     *
     * @param $token
     * @param $signature
     * @param $userId
     * @return bool
     */
    private function checkTokenSignature($token, $signature, $userId){
        if (empty($token) || empty($signature) || empty($userId)){
            MLog::fatal(CoreConst::MODULE_ACCOUNT, sprintf('token,signature,userid was missing'));
            return false;
        }

        if ($signature == $this->getTokenSignature($token, $userId)){
            return true;
        }
        return false;
    }

    /**
     * 可以强行下线，目前各种端每个限一个账号在线
     *
     *
     * @param $userId
     * @param $token
     * @param $platform
     * @param int $expire
     * @return bool
     */
    private function setUserTokenTable($userId, $token, $platform, $expire = CoreConst::TOKEN_EXPIRE){
        if (!in_array($platform, CoreConst::$platform)){
            MLog::fatal(CoreConst::MODULE_ACCOUNT, 'platform is not in valid list');
            return false;
        }

        $redis = RedisLib::getInstance();
        if ($redis->setex(RedisLib::$prefix . $this->_userPrefix . $userId . ':' . CoreConst::$platform[$platform], $expire, $token)){
            return true;
        } else {
            MLog::fatal(CoreConst::MODULE_ACCOUNT, sprintf('set token to user table in redis error userId[%s] platform[%s] token[%s] expire[%s]',
                $userId,
                $token,
                $platform,
                $expire
            ));
            return false;
        }
    }

    /**
     *
     * 设置用户数据到token （最小集合）
     *
     * @param $token
     * @param $signature
     * @param $userId
     * @param $userData
     * @param int $expire
     * @return bool
     */
    public function setUserDataToToken($token, $signature, $userId, $userData, $expire = CoreConst::TOKEN_EXPIRE){
        if (empty($token) || empty($signature)){
            MLog::fatal(CoreConst::MODULE_ACCOUNT, 'token or signature was missing');
            return false;
        }

        if (false === $this->checkTokenSignature($token, $signature, $userId)){
            return false;
        }

        $redis = RedisLib::getInstance();
        $redis->setex(RedisLib::$prefix . $this->_userDataPrefix . $token, $expire, json_encode($userData));
    }

    /**
     * 从token中获取用户数据
     *
     * @param $token
     * @param $signature
     * @param $userId
     * @return bool|mixed
     */
    public function getUserDataByToken($token, $signature, $userId){
        if (empty($token) || empty($signature)){
            MLog::fatal(CoreConst::MODULE_ACCOUNT, 'token or signature was missing');
            return false;
        }

        if (false === $this->checkTokenSignature($token, $signature, $userId)){
            return false;
        }

        $redis = RedisLib::getInstance();
        return json_decode($redis->get(RedisLib::$prefix . $this->_userDataPrefix . $token), true);
    }
}