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
    }

    public function addFriends(){
        checkLogin("api");
        $userId = $this->session->user_id;
        $userName = $this->session->user_name;
        $send_to      = trim($this->input->post('send_to',      true));
        if(!Validator::isNotEmpty($send_to, "您要添加的好友id不能为空")) {
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }
        $addInfoConfirm = $this->infoConfirm->addInfoConfirm($userId, $send_to,
            CoreConst::INFO_CONFRIM_STATUS_UNREAD, CoreConst::FRIEND_MESSAGE_CONFIRM);

        $addMessageStatus  = $this->message->addMessage($userId, $send_to,
            $type    = CoreConst::ADD_FRIENDS_MES,
            $title   = '好友添加',
            $message = $userName . ' 请求添加为好友'
        );
        if($addInfoConfirm || $addMessageStatus){
            $this->response->jsonFail(Response::CODE_SERVER_ERROR, '抱歉，添加好友失败');
        }
        $this->response->jsonSuccess();

    }

}
