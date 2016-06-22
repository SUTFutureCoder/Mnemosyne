<?php
/**
 * 表白类库
 *
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-6-22
 * Time: 上午11:17
 */
class Showlove {

    private static $_ci = null;

    private function getCiInstance(){
        if (null === self::$_ci){
            self::$_ci =& get_instance();
        }
        return self::$_ci;
    }

    /**
     * 检查表白信息是否合法，保护隐私
     *
     * @param $fromUser
     * @param $toUser
     * @return bool
     */
    public function checkLoveMess($fromUser, $toUser){
        if (null === $fromUser || null === $toUser){
            throw new MException(CoreConst::MODULE_SHOWLOVE, ErrorCodes::ERROR_SHOWLOVE_ID_MISSING);
        }

        $this->getCiInstance()->load->library('session');
        $userId = $this->getCiInstance()->session->user_id;
        if (empty($userId) || ($userId != $fromUser && $userId != $toUser)){
            throw new MException(CoreConst::MODULE_SHOWLOVE, ErrorCodes::ERROR_SHOWLOVE_USER_IDENTITY_ERROR);
        }

        //查消息记录确认
        $this->getCiInstance()->load->model('MessageModels');
        $arrMessInfo = $this->getCiInstance()->MessageModels->getMessageByUserId($toUser, null, $fromUser, 2);

        //选取最近的一条表白
        if (is_array($arrMessInfo) && isset($arrMessInfo[0])){
            return $arrMessInfo[0];
        } else {
            throw new MException(CoreConst::MODULE_SHOWLOVE, ErrorCodes::ERROR_SHOWLOVE_NOT_FOUND);
        }
    }


}