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
class UserInfo extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->helper('login_helper');
        $this->load->library('util/Response');
        $this->load->library('session');
        $this->load->library('util/Validator');
    }

    /**
     * 初始化用户信息
     */
    public function userInfoInit(){
        //检查是否合法用户
        checkLogin('api');
        //完善用户信息
        $this->load->library('util/Validator');

        //转为utf8
        $userName       = trim($this->input->post('userName', true));

        $userSex        = trim($this->input->post('userSex', true));
        $userBirthday   = trim($this->input->post('userBirthDay', true));
        //工大快速绑定
        $userSUTFastBind= trim($this->input->post('userSUTFastBind', true));
        $userStudentId  = trim($this->input->post('userStudentId', true));

        //学校ID，以后再拓展
        $userClassId    = trim($this->input->post('userClassId', true));
        $userSchoolId   = trim($this->input->post('userSchoolId', true));

        if (!(Validator::isTrue(1 == preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $userName), '您的真实姓名请填写中文')
            && Validator::mbStringRange($userName, 1, 16, '您的姓名不能超过16个字符')
            && Validator::isTrue(in_array($userSex, CoreConst::$userSex), '请输入正确的性别')
            && Validator::isTrue(false !== strtotime($userBirthday), '请输入正确的出生日期')
            //临时方法
            && Validator::isTrue(1 == $userSUTFastBind) && Validator::mbStringRange($userStudentId, 9, 9, '您的学号有误')
        )) {
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }

        $this->load->model('UserModels');

        $this->load->model('ClassModels');
        $this->load->model('SchoolClassUserMapModels');
        $this->load->model('SchoolModels');

        $userId = $this->session->user_id;

        if (!$this->UserModels->updateUser($userId, $userName, $userBirthday, $userSex)){
            $this->response->jsonFail(Response::CODE_SERVER_ERROR, '抱歉，提交失败');
        }

        //绑定班级
        //获取班级
        if ($userSUTFastBind){
            $userSchoolId = 1;
            $userClassId  = substr($userStudentId, 0, 7);

        }

        if (!$this->ClassModels->getClassNameById($userClassId)) {
            //未找到班级
            $this->response->jsonFail(Response::CODE_SERVER_ERROR, '抱歉，未找到此班级');
        }

        //检查是否重复绑定
        if ($this->SchoolClassUserMapModels->checkBindExists($userSchoolId, $userClassId, $userId)){
            $this->response->jsonFail(Response::CODE_PARAMS_CONFLICT, '抱歉，您已经是这个班级的一员');
        }

        //开始绑定
        if (!$this->SchoolClassUserMapModels->bind($userSchoolId, $userClassId, $userId, $userStudentId)){
            $this->response->jsonFail(Response::CODE_SERVER_ERROR, '抱歉，绑定失败');
        }

        $this->session->unset_userdata('needinit');
        $this->response->jsonSuccess();
    }

    public function addFrientInit(){
        checkLogin('api');
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

    public function getUserInfoByLoginName(){
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
