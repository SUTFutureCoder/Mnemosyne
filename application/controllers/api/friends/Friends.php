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
    }

    public function addFriends(){
        checkLogin("api");
        $userId = $this->session->user_id;
        $alumniId     = trim($this->input->post('alumni_id',    true));
        $title        = trim($this->input->post('title',        true));
        $cover        = trim($this->input->post('cover',        true));
        $send_to      = trim($this->input->post('send_to',      true));

    }

    public function loadUserInfo(){
        checkLogin("api");
        $userId = $this->session->user_id;
        $this->load->model('UserModels', 'um');
        $userInfo = $this->um->getUserBasicInfo($userId);
        $userInfoRes = $userInfo;
        if($_POST){
            $userInfoRes = Array();
            $info = $this->input->post('info', true);
            $infoArr = array_unique(explode(',', $info));
            foreach ($infoArr as $item){
                if(isset($userInfo[$item])){
                    $userInfoRes[$item] = $userInfo[$item]; 
                }
            }
        }
        $this->response->jsonSuccess(array(
            'userinfo' => $userInfoRes,
        ));
    }

    public function getUserSchoolAndClass(){
        checkLogin();
        $this->load->model('SchoolClassUserMapModels', 'scum');
        $userId= $this->session->user_id;
        $SchoolAndClassInfo = $this->scum->getUserBindList($userId);
        $this->response->jsonSuccess(
            $SchoolAndClassInfo
        );
    }

    public function getUserMessage(){
        $userId = $this->session->user_id;
        $this->load->model('MessageModels', 'message');
        $message = $this->message->getMessageByUserId($userId);
        $this->response->jsonSuccess(array(
            'message' => $message,
        ));
    }

    public function getUserMessageByLoginName(){
        checkLogin();
        $loginName  =  trim($this->input->post('login_name', true));
        if (!(Validator::isNotEmpty($loginName,   '您的手机或邮箱不能为空')
            && (Validator::isEmail($loginName, '请输入合法的邮箱地址或手机号')
            || Validator::isMobile($loginName, '请输入合法的邮箱地址或手机号')))){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }
        $this->load->model('UserModels');
        $userInfo = $this->UserModels->getUserInfoByLoginName($loginName);
        $this->response->jsonSuccess(array(
            'userInfo' => $userInfo,
        ));

    }

}
