<?php
/**
 * 秀恩爱专场[DOGE]
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-6-11
 * Time: 下午9:01
 */
class Showlove extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('login_helper');
        $this->load->library('util/Validator');
        $this->load->library('util/Response');
    }

    /**
     * 表白提交接口
     */
    public function kokuhaku(){
        checkLogin();

        $this->load->library('ModuleConst');

        if (!(Validator::isNotEmpty($this->input->post('toUserId', true), '表白目标用户ID不能为空')
                && Validator::isNumberic($this->input->post('toUserId', true), '表白目标用户ID需要是数字')
                && Validator::mbStringRange($this->input->post('fromUserNickname', true), 1, 32, '您的昵称不能超过32个字符')
                && Validator::mbStringRange($this->input->post('toUserNickname', true),   1, 32, '表白对象昵称不能超过32个字符')
                && Validator::isNotEmpty($this->input->post('taKnowTime', true), '相识时间不能为空')
                && Validator::isTime($this->input->post('taKnowTime', true), '相识时间格式错误')
                && Validator::isTrue(isset(ModuleConst::$showLoveTplList[$this->input->post('tpl', true)]), '请重新选择表白模板')
                && Validator::mbStringRange($this->input->post('message', true), 1, 1024, '表白内容不能超过1024个字符'))){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }

        $this->load->model('UserRelationModels');
        $arrRelationData = $this->UserRelationModels->getRelation($this->session->user_id, $this->input->post('toUserId', true));
        if (empty($arrRelationData) || !in_array($arrRelationData['type'], array(CoreConst::USER_RELATION_TYPE_FRIEND, CoreConst::USER_RELATION_TYPE_AMBIGUOUS))){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '表白对象必须是好友或暧昧关系');
        }

        $strTimeStamp = strtotime($this->input->post('taKnowTime', true));

        $strDataPrepare = json_encode(array(
            'tpltype'           => ModuleConst::$showLoveTplList[$this->input->post('tpl', true)]['name'],
            'fromUserNickname'  => $this->input->post('fromUserNickname', true),
            'toUserNickname'    => $this->input->post('toUserNickname',   true),
            'taKnowTime'        => array(
                'orgin' => $this->input->post('taKnowTime', true),
                'Y'     => date('Y', $strTimeStamp),
                'm'     => date('m', $strTimeStamp),
                'd'     => date('d', $strTimeStamp),
            ),
            'message'           => $this->input->post('message',    true),
        ));


        //准备发送信息
        $this->load->model('InfoConfirmModels');
        $this->load->model('MessageModels');
        $isConfirmExists = $this->InfoConfirmModels->checkInfoIsExist($this->session->user_id,
            $this->input->post('toUserId', true),
            CoreConst::SHOWLOVE_MESSAGE_CONFIRM,
            CoreConst::INFO_CONFRIM_STATUS_UNREAD);

        if(is_array($isConfirmExists) && count($isConfirmExists) > 0){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '您的表白已发送过');
        }

        $addInfoConfirm = $this->InfoConfirmModels->addInfoConfirm(
            $this->session->user_id,
            $this->input->post('toUserId', true),
            CoreConst::SHOWLOVE_MESSAGE_CONFIRM,
            CoreConst::INFO_CONFRIM_STATUS_UNREAD
        );

        if ($addInfoConfirm){
            if ($this->MessageModels->addMessage(
                $this->session->user_id,
                $this->input->post('toUserId', true),
                CoreConst::SHOW_LOVE_MES,
                '收到了一封表白信',
                $this->session->user_name . '向您表白',
                $strDataPrepare
            )){
                $this->response->jsonSuccess();
            }
        }

        $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '抱歉，您的表白发送失败，请重新发送');

    }

    /**
     * 获取告白对象最小信息 [函数名恶意]
     *
     * 注意验证
     * 1-是否已经是情侣
     * 2-用户有其他情侣关系
     * 3-用户是否是和对象是好友关系
     *
     *
     * 1&3可合并，已是好友关系则无需担心不存在的情况
     *
     */
    public function getObjectInfo(){
        $this->load->model('UserModels');

        //确认用户搜索方法
        if ('search' == trim($this->input->post('type', true))){
            //使用登录名搜索
            $loginName  =  trim($this->input->post('searchLoginname', true));
            if (!(Validator::isNotEmpty($loginName,   '表白对象手机或邮箱不能为空')
                && (Validator::isEmail($loginName, '请输入合法的邮箱地址或手机号')
                    || Validator::isMobile($loginName, '请输入合法的邮箱地址或手机号')))){
                $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
            }
            $userInfo = $this->UserModels->getUserInfoByLoginName($loginName);
        } else if ('select' == trim($this->input->post('type', true))){
            $userInfo = $this->UserModels->getUserBasicInfo($this->input->post('userId', true), array('user_id', 'user_name', 'user_nickname'));
        } else {
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '选择表白对象方法有误');
        }


        if (empty($userInfo)){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '表白对象用户不存在');
        }
        $toUserId = $userInfo['user_id'];

        $this->load->model('UserRelationModels');
        $arrRelationData = $this->UserRelationModels->getRelation($this->session->user_id, $toUserId);
        if (empty($arrRelationData) || !in_array($arrRelationData['type'], array(CoreConst::USER_RELATION_TYPE_FRIEND, CoreConst::USER_RELATION_TYPE_AMBIGUOUS))){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '表白对象必须是好友或暧昧关系');
        }

        //返回表白用户信息
        $this->response->jsonSuccess(array(
            'user_id'   => $userInfo['user_id'],
            'user_name' => $userInfo['user_name'],
            'from_user_name'     => $this->session->user_name,
            'from_user_nickname' => $this->session->user_nickname,
            'to_user_nickname'   => $userInfo['user_nickname'],
        ));
    }

    /**
     *
     */
    public function determineLove(){
        if (!(Validator::isTrue(in_array($this->input->post('determine', true), array(0, 1)), '请选择正确的回复')
                && Validator::isNumberic($this->input->post('fromUserId',  true), '表白用户id出错')
                && Validator::isNumberic($this->input->post('toUserId',  true), '接收表白用户id出错')
                && Validator::isTrue($this->session->user_id == $this->input->post('toUserId', true), '只能被表白对象才能选择接收与否'))){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }

        $boolDetermine = $this->input->post('determine',  true);
        $intFromUserId = $this->input->post('fromUserId', true);
        $intToUserId   = $this->input->post('toUserId',   true);

        //确认是否发过
        $this->load->library('Showlovelib');
        $arrMessInfo = $this->showlovelib->checkLoveMess($intFromUserId, $intToUserId);

        if (empty($arrMessInfo['id'])){
            throw new MException(CoreConst::MODULE_SHOWLOVE, ErrorCodes::ERROR_SHOWLOVE_MESS_MISSING);
        }

        //标记已读
        $this->load->model('MessageModels');
        $MarkMessageStatus = $this->MessageModels->updateMessageStatus($intFromUserId, $arrMessInfo['id'], CoreConst::MESSAGE_STATUS_READ);
        if(!$MarkMessageStatus){
            throw new MException(CoreConst::MODULE_MESSAGE,  ErrorCodes::ERROR_MESSAGE_UPDATE_STATUS);
        }

        //更新info_confirm    status
        $this->load->model('InfoConfirmModels');
        //获取info_confirm status ID
        $arrInfoConfirmData = $this->InfoConfirmModels->checkInfoIsExist($intFromUserId, $intToUserId, CoreConst::SHOWLOVE_MESSAGE_CONFIRM, CoreConst::INFO_CONFRIM_STATUS_UNREAD);
        if (!is_array($arrInfoConfirmData) || count($arrInfoConfirmData) != 1){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '表白不存在或已作出决定');
        }

        $intInfoConfirmId   = $arrInfoConfirmData[0]['id'];
        //更新
        if (1 == $boolDetermine){
            //同意
            $this->InfoConfirmModels->updateInfoConfrimStatus($this->session->user_id, $intInfoConfirmId, CoreConst::INFO_CONFRIM_STATUS_AGREE);
        } else {
            $this->InfoConfirmModels->updateInfoConfrimStatus($this->session->user_id, $intInfoConfirmId, CoreConst::INFO_CONFRIM_STATUS_REFUSE);
        }

        //更新关系
        $this->load->model('UserRelationModels');
        if ($this->UserRelationModels->updateRelation($intFromUserId, $intToUserId, CoreConst::USER_RELATION_TYPE_LOVERS)){
            $this->response->jsonSuccess(array('determine' => $boolDetermine));
        } else {
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '决定失败');
        }
    }

}