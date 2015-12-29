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
        $this->load->model('UserModels');

        if (Validator::isEmail($login_name) || Validator::isMobile($login_name)) {
            $userInfo = $this->UserModels->getUserInfoByLoginName($login_name);
        } else{
            Validator::setMessage("请输入合法的邮箱地址或手机号");
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }

        if(empty($userInfo)
            || (!empty($userInfo) && !password_verify($password, $userInfo['user_password']))) {
            Validator::setMessage("用户名或密码错误");
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }

        $token = $this->token->setTokenToRedis($userInfo['user_id']);
        $this->load->library('session');
        $this->session->set_userdata('user_id', $userInfo['user_id']) ;
        $this->session->set_userdata('user_name', $userInfo['user_name']);
        $this->session->set_userdata('token', $token);
        if(isset($_SESSION['user_id']))
        {
        }
        $this->response->jsonSuccess(array(
            'token' => $token,
        ));
    }

    public function regist(){

        $this->load->library('Token');
        /*开发期间验证码先注释掉
        //验证是否通过验证码验证
        $this->load->library('validcode');

        if (!$this->validcode->checkValidCodeAccess()){
            $this->response->jsonFail(Response::CODE_UNAUTHORIZED, '请输入正确的验证码');
        }
        */


        $userName       = trim($this->input->post('user_name',        true));
        $userMobile     = trim($this->input->post('user_mobile',      true));
        $userEmail      = trim($this->input->post('user_email',       true));
        $passWd         = trim($this->input->post('password',         true));
        $passWdConfirm  = trim($this->input->post('password_confirm', true));

        if (!(Validator::isNotEmpty($userName,      '您的姓名不能为空')
            && Validator::mbStringRange($userName, 0, 30, '您的姓名不能超过30个字符')
            && Validator::isNotEmpty($userMobile, '您的手机号码不能为空')
            && Validator::isMobile($userMobile, '请输入合法的手机号码')
            && Validator::isNotEmpty($userEmail,  '您的邮箱地址不能为空')
            && Validator::isEmail($userEmail,   '请输入合法的邮箱地址')
            && Validator::isNotEmpty($passWd, '您的密码不能为空')
            && Validator::isNotEmpty($passWdConfirm, '您再次输入密码不能为空')
            && Validator::mbStringRange($passWd, 0, 20, '您的密码不能超过20位')
            && Validator::mbStringRange($passWdConfirm, 0, 20, '您的密码不能超过20位')
            && Validator::isEqual($passWd, $passWdConfirm, "您两次输入的密码不一致")
        )){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }

        $this->load->model('UserModels');
        //判断手机号或者邮箱是否被注册
        if($this->UserModels->checkUserExists($userMobile, $userEmail))
        {
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '抱歉, 您的邮箱或手机号已经被注册');
        }

        //录入数据库
        $this->UserModels->addUser($userName, password_hash($passWd, PASSWORD_DEFAULT), $userMobile, $userEmail);

        $userId = 0;
        //返回token
        $this->response->jsonSuccess(array(
            'token' => $this->token->setTokenToRedis($userId),
        ));


    }
}