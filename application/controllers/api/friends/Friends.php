<?php
/**
 * 用户信息相关接口
 *
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-1-3
 * Time: 下午3:01
 */
class Friends extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->helper('login_helper');
        $this->load->library('util/Response');
        $this->load->library('session');
        $this->load->library('util/Validator');
        $this->load->model('InfoConfirmModels', 'infoConfirm');
        $this->load->model("MessageModels", 'message');
        $this->load->model('UserRelationModels', 'UserRelation');        
    }


    //TODO 是否为好友的验证
    /**
     * 板砖大作,添加[单个]好友
     *
     * 话说为啥要加s?
     */
    public function addFriends(){
        checkLogin("api");
        $userId   = $this->session->user_id;
        $userName = $this->session->user_name;
        $send_to  = trim($this->input->post('send_to',      true));
        if(!Validator::isNotEmpty($send_to, "您要添加的好友id不能为空")) {
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }

        if($userId == $send_to){
             $this->response->jsonFail(Response::CODE_PARAMS_WRONG, "您不能添加自己为好友");
        }

        $friendConfirmMessageRet = $this->sendFriendConfirmMessage($userId, $userName, $send_to);
        if(!$friendConfirmMessageRet){
            $this->response->jsonFail(Response::CODE_SERVER_ERROR, '抱歉，添加好友消息发送失败');
        }
        $this->response->jsonSuccess();
    }

    /*
     * 通过列表批量添加好友
     */
    public function addFriendsByList(){
        checkLogin("api");
        $userId   = $this->session->user_id;
        $userName = $this->session->user_name;
        $arrSendList = $this->input->post('send_list', true);

        if (!Validator::isArray($arrSendList, '您要添加的好友列表异常')){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }


        foreach ($arrSendList as $send_to){
            if(!Validator::isNotEmpty($send_to, "您要添加的好友id不能为空")) {
                $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
            }

            if($userId == $send_to){
                $this->response->jsonFail(Response::CODE_PARAMS_WRONG, "您不能添加自己为好友");
            }

            $friendConfirmMessageRet = $this->sendFriendConfirmMessage($userId, $userName, $send_to);
            if(!$friendConfirmMessageRet){
                $this->response->jsonFail(Response::CODE_SERVER_ERROR, '抱歉，添加好友消息发送失败');
            }
        }

        $this->response->jsonSuccess();
    }

    /**
     *
     * 抽象出来的向好友发送申请方法
     *
     * @param $userId
     * @param $userName
     * @param $send_to
     * @return bool
     */
    private function sendFriendConfirmMessage($userId, $userName, $send_to){
        $isInfoConfirmExist = $this->infoConfirm->checkInfoIsExist($userId, $send_to,
            CoreConst::FRIEND_MESSAGE_CONFIRM, CoreConst::INFO_CONFRIM_STATUS_UNREAD);
        if(is_array($isInfoConfirmExist) && count($isInfoConfirmExist) > 0){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, "您的好友请求已经发送过");
        }
        $addInfoConfirm = $this->infoConfirm->addInfoConfirm($userId, $send_to,
            CoreConst::FRIEND_MESSAGE_CONFIRM, CoreConst::INFO_CONFRIM_STATUS_UNREAD);
        $addMessageStatus = 0;
        if($addInfoConfirm){
            $addMessageStatus  = $this->message->addMessage($userId, $send_to,
                $type    = CoreConst::ADD_FRIENDS_MES,
                $title   = '好友添加',
                $message = $userName . ' 请求添加为好友'
            );
        }
        //添加好友信息失败
        return ($addInfoConfirm && $addMessageStatus);
    }

    /**
     * 获取用户推荐列表
     */
    public function friendRecommend(){
        checkLogin("api");
        $userId = $this->session->user_id;

        $intPage = $this->input->get('page', true);
        $intPage = isset($intPage) && is_int($intPage) && $intPage ? $intPage : 0;
        $intSize = $this->input->get('size', true);
        $intSize = isset($intSize) && is_int($intSize) && $intSize ? $intSize : 20;

        $this->load->model('SchoolClassUserMapModels', 'scum');
        $this->load->model('UserModels', 'user');
        //获取已认识用户的id列表
        $userKnown = $this->UserRelation->getUserFriendIdList($userId);
        $userKnown = array_column($userKnown, 'user_id');
        //带着用户id和排除列表查询所有绑定过的班级的同班同学
        $recommendIdList   =  $this->scum->getFriendRecordList($userId, $userKnown, $intSize, $intPage);
        //获取推荐的用户id
        $recommendIdList   = array_column($recommendIdList, 'user_unique_id');
        //根据需要字段取数据
        $recommendInfoList = $this->user->getUserFullInfoList($recommendIdList, array(
            'user_id', 'user_nickname', 'user_name', 'user_sex', 'user_avatar',
        ));
        $this->response->jsonSuccess(array(
            'recommendInfoList' => $recommendInfoList,
        ));
    } 
    //TODO 添加页码 
    public function friendRequestList(){
        checkLogin("api");
        $userId = $this->session->user_id;
        $userList = $this->infoConfirm->getUserFullInfoListJoinInUser($userId, CoreConst::FRIEND_MESSAGE_CONFIRM);
        $this->response->jsonSuccess($userList);

    }

    public function friendRequestResponse(){
        checkLogin("api");
        $userId = $this->session->user_id;
        $request_friend_id = trim($this->input->post('request_friend_id'));
        $infoConfirmId = trim($this->input->post('info_confirm_id'));
        $chosen = trim($this->input->post('chosen')); 
        $updateInfoConfrimStatus = 0;
        if($chosen === 'accept'){
            $chosen = CoreConst::INFO_CONFRIM_STATUS_AGREE;
        }else if($chosen === 'refuse'){
            $chosen = CoreConst::INFO_CONFRIM_STATUS_REFUSE;
            $updateInfoConfrimStatus = $this->infoConfirm->updateInfoConfrimStatus($userId, $infoConfirmId, $chosen);
            if(!$updateInfoConfrimStatus){
                $this->response->jsonFail(Response::CODE_SERVER_ERROR, '抱歉, 信息确认数据库操作失败');                
            }
            $this->response->jsonSuccess();
        }else{
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, "请选择正确的操作选项");
        }
        $updateInfoConfrimStatus = $this->infoConfirm->updateInfoConfrimStatus($userId, $infoConfirmId, CoreConst::INFO_CONFRIM_STATUS_AGREE);
        if($this->UserRelation->isRelationExist($userId, $request_friend_id)){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, "你们已经是好友了");
        }

        if($updateInfoConfrimStatus){
            $addFriendStatus = $this->UserRelation->addUserRelation($userId, $request_friend_id, CoreConst::USER_RELATION_TYPE_FRIEND);
            $addFriendStatusAnother = $this->UserRelation->addUserRelation($request_friend_id, $userId, CoreConst::USER_RELATION_TYPE_FRIEND);
            if(!$addFriendStatus || !$addFriendStatusAnother){
                $this->response->jsonFail(Response::CODE_SERVER_ERROR, '抱歉, 更新好友数据库失败');
            }
        }else{
            $this->response->jsonFail(Response::CODE_SERVER_ERROR, '抱歉, 更新确认信息失败');
        }
        $this->response->jsonSuccess();
    }

    public function getFriendInfoList(){
        checkLogin('api');
        $userId = $this->session->user_id;
        $userInfoList = $this->UserRelation->getUserFriendInfoJoinInUser($userId);
        $this->response->jsonSuccess($userInfoList);
    }

    public function deleteFriend(){
        checkLogin('api');
        $userId = $this->session->user_id;
        $friendId = $this->input->post('friend_id', true);
        $deleteStatus = $this->UserRelation->deleteRelation($userId, $friendId);
        $deleteStatusAnother = $this->UserRelation->deleteRelation($friendId, $userId);
        if(!$deleteStatus || !$deleteStatusAnother){
            $this->response->jsonFail(Response::CODE_SERVER_ERROR, '抱歉, 执行数据库删除失败');
        }
        $this->response->jsonSuccess();
    }
}
