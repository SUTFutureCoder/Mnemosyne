<?php
/**
 * 为mnemosyne websocket设计的Redis基类，用于单例
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-2-27
 * Time: 下午10:38
 */
class UtilRedis{

    public static $redis = null;

    const PREFIX = array(
        TOKEN => 'Mne:token:',
        //注意，用户相关信息只能使用websocket命名空间下的key。严禁跨域！
        BAN   => 'Mne:websocket:ban:',
        USER  => 'Mne:websocket:user:',
    );

    public function getInstance(){
        if (is_null(self::$redis)){
            self::$redis = new Redis();
        }
        return self::$redis;
    }

}