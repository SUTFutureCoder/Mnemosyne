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
    const PREFIX_ADMIN_TOKEN  = 'Mne:token:admin:';
    const PREFIX_PC_TOKEN     = 'Mne:token:pc:';
    const PREFIX_MOBILE_TOKEN = 'Mne:token:mobile:';

    private static $platformToken  = array(
        'admin'  => self::PREFIX_ADMIN_TOKEN,
        'pc'     => self::PREFIX_PC_TOKEN,
        'mobile' => self::PREFIX_MOBILE_TOKEN,
    );

    //注意，用户相关信息只能使用websocket命名空间下的key。严禁跨域！
    const PREFIX_BAN    = 'Mne:websocket:ban:';
    const PREFIX_USER   = 'Mne:websocket:user:';

    public static function checkToken($userId, $token, $platform){
        if (!isset(self::$platformToken[$platform])){
            UtilLog::fatal(sprintf('check token error userId[%s] token[%s] platform[%s]'
                , json_encode($userId)
                , json_encode($token)
                , json_encode($platform)));
            return false;
        }

        $strRedisKey = self::$platformToken[$platform] . $userId;
        if (false === UtilRedis::exists($strRedisKey)){
            return false;
        }

        return $token == UtilRedis::get($strRedisKey);
    }
}