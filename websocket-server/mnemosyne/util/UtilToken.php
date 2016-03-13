<?php
/**
 *
 * Websocket token用
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-2-28
 * Time: 下午12:51
 */
class UtilToken{
    //公有redis部分，只能读取，严禁修改
    //redis中token存储
//    const TOKEN_PREFIX     = 'Mne:token:user:';
    //redis中user platform table
    const USER_PLATFORM_TOKEN_PREFIX = 'Mne:token:usertable:';
    //redis中包含用户数据的token
    const USER_DATA_PREFIX = 'Mne:token:userdata:';




    //注意，用户相关信息只能使用websocket命名空间下的key。严禁跨域！
    const PREFIX_BAN    = 'Mne:websocket:ban:';
    const PREFIX_USER   = 'Mne:websocket:user:';

    /**
     *
     * 根据用户平台验证token
     *
     * @param $userId
     * @param $token
     * @param $userSignature
     * @param $platform
     * @return bool
     */
    public static function checkTokenPlatform($userId, $token, $userSignature, $platform){
        if (empty($userId) || empty($token) || empty($userSignature) || empty($platform)){
            UtilLog::fatal(sprintf('params lost userId[%s] token[%s] usersignature[%s] platform[%s]',
                $userId,
                $token,
                $userSignature,
                $platform));
            return false;
        }

        //先验证签名
        if (false === self::checkTokenSignature($token, $userSignature, $userId)){
            UtilLog::fatal(sprintf('check token signature error userId[%s] token[%s] signature[%s]',
                $userId,
                $token,
                $userSignature));
            return false;
        }

        $strRedisKey = self::USER_PLATFORM_TOKEN_PREFIX . $userId . ':' . $platform;
        if (false === UtilRedis::exists($strRedisKey)){
            UtilLog::fatal(sprintf('check user token failed userId[%s] token[%s] platform[%s]',
                $userId,
                $token,
                $platform));
            //罚时一秒，防止暴力破解
            sleep(1);
            return false;
        }
        return $token == UtilRedis::get($strRedisKey);
    }

    /**
     * 根据token算出签名
     *
     * @param $token
     * @param $userId
     * @return bool|string
     */
    public static function getTokenSignature($token, $userId){
        global $config;
        $salt = $config['token_salt'];
        if (empty($salt)){
            UtilLog::fatal(sprintf('token salt not defined in config.php'));
            return false;
        }

        if (empty($userId)){
            UtilLog::fatal(sprintf('userId was missing'));
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
    private static function checkTokenSignature($token, $signature, $userId){
        if (empty($token) || empty($signature) || empty($userId)){
            UtilLog::fatal(sprintf('token,signature,userid was missing'));
            return false;
        }

        if ($signature != self::getTokenSignature($token, $userId)){
            return false;
        }
        return true;
    }


    /**
     * 从token中获取用户数据
     *
     * @param $token
     * @param $signature
     * @param $userId
     * @return bool|mixed
     */
    public static function getUserDataByToken($token, $signature, $userId){
        if (empty($token) || empty($signature)){
            UtilLog::fatal('token or signature was missing');
            return false;
        }

        if (false === self::checkTokenSignature($token, $signature, $userId)){
            return false;
        }

        return json_decode(UtilRedis::get(self::USER_DATA_PREFIX . $token), true);
    }
}