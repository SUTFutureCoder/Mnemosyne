<?php
/**
 * Api for user account controll
 *
 *
 * @author  *Chen <linxingchen@baidu.com>
 */
class Account extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->library('Validator');
        $this->load->library('Response');
    }

    /**
     * Set the account of user
     *
     * <table class="table table-hover table-bordered">
     *     <tr><th>POST</th><th>type</th><th>explain</th></tr>
     *     <tr><td>user_name</td><td>string</td><td>用户姓名</td></tr>
     *     <tr><td>user_mobile</td><td>string</td><td>用户手机号码</td></tr>
     *     <tr><td>user_email</td><td>string</td><td>用户邮箱</td></tr>
     * </table>
     *
     * [wiki-注册用户](http://wiki.sutapp.com/doku.php?id=project_m:api#注册用户)
     * @access public
     */

    public function login(){
        //验证是否通过;
        $this->load->library('validcode');
        $this->load->library('Token');
        if (!$this->validcode->checkValidCodeAccess()){
            $this->response->jsonFail(Response::CODE_UNAUTHORIZED, '请输入正确的验证码');
        }

        $login_name =  trim($this->input->post('login_name', true));
        $password   =  trim($this->input->post("password", true));

        if (!(Validator::isNotEmpty($login_name,   '您的姓名不能为空'))
            && !(Validator::isNotEmpty($password,  '您的密码不能为空'))) {
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }
        $this->load->model("UserModels");

        if (Validator::isEmail($login_name) || Validator::isMobile($login_name)) {
            $userInfo = $this->UserModels->getUserInfoByLoginName($login_name);
        } else{
            Validator::setMessage("请输入合法的邮箱地址或手机号");
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }

        if(empty($userInfo)
            && ($userInfo['password'] !== password_verify($password, PASSWORD_DEFAULT)))
        {
            Validator::setMessage("用户名或密码错误");
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }

        $this->response->jsonSuccess(array(
            'token' => $this->token->setTokenToRedis($userInfo['user_id']),
        ));
    }

    public function regist(){
        //验证是否通过验证码验证
        $this->load->library('validcode');
        $this->load->library('Token');


        if (!$this->validcode->checkValidCodeAccess()){
            $this->response->jsonFail(Response::CODE_UNAUTHORIZED, '请输入正确的验证码');
        }


        $userName   = trim($this->input->post('user_name',   true));
        $school     = trim($this->input->post('school',      true));
        $class      = trim($this->input->post('class',       true));
        $passWd     = trim($this->input->post('password',    true));
        $userMobile = trim($this->input->post('user_mobile', true));
        $userEmail  = trim($this->input->post('user_email',  true));

        if (!(Validator::isNotEmpty($userName,      '您的姓名不能为空')
            && Validator::mbStringRange($userName, 0, 30, '您的姓名不能超过30个字符')
            && Validator::isNotEmpty($school,   '您的学校名称不能为空')
            && Validator::mbStringRange($school, 0, 30, '您的学校名称不能超过30个字符')
            && Validator::isNotEmpty($class,    '您的班级不能为空')
            && Validator::mbStringRange($class, 0, 30, '您的班级名称不能超过30个字符')
            && Validator::mbStringRange($passWd, 0, 20, '您的密码不能超过20位')
            && Validator::isNotEmpty($userMobile, '您的手机号码不能为空')
            && Validator::isMobile($userMobile, '请输入合法的手机号码')
            && Validator::isNotEmpty($userEmail,  '您的邮箱地址不能为空')
            && Validator::isEmail($userEmail,   '请输入合法的邮箱地址'))){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }

        $this->load->model('SchoolModels');
        $this->load->model('ClassModels');

        //获取学校id
        if (!$schoolId = $this->SchoolModels->getSchoolIdByName($school)){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '抱歉，您的学校还未开放服务。请联系admin@bricksfx.cn');
        }

        //获取班级id
        if (!$classId  = $this->ClassModels->getClassIdByName($schoolId, $class)){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '抱歉，请选择已有班级');
        }

        //录入数据库


        $userId = 0;
        //返回token
        $this->response->jsonSuccess(array(
            'token' => $this->token->setTokenToRedis($userId),
        ));



    }
}