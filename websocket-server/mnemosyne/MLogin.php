<?php
/**
 * 登录时逻辑
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-2-27
 * Time: 下午11:38
 */
class MLogin{
    public static function checkToken($strData){
        if (!$arrData = MBase::parseData($strData)){
            UtilLog::fatal(sprintf('parse data failed strData[%s]', $strData));
            return false;
        }

        if (empty($arrData['user_id']) || empty($arrData['token']) || empty($arrData['platform']) || empty($arrData['signature'])){
            UtilLog::fatal(sprintf('param is missing strData[%s]',
                $strData));
            return false;
        }

        if (UtilToken::checkTokenPlatform($arrData['user_id'], $arrData['token'], $arrData['signature'], $arrData['platform'])){
            UtilLog::fatal(sprintf('check token platform failed userId[%s] platform[%s]',
                $arrData['user_id'],
                $arrData['platform']));
            return false;
        }
        return true;
    }
}