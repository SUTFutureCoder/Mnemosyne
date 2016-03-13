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

        $loginName  =  trim($this->input->post('login_name', true));
        $password   =  trim($this->input->post("password", true));
        $platform   =  trim($this->input->post('platform', true));

        if (!(Validator::isNotEmpty($loginName,   '您的手机或邮箱不能为空')
            && Validator::isNotEmpty($password,  '您的密码不能为空')
            && (Validator::isEmail($loginName, '请输入合法的邮箱地址或手机号')
                || Validator::isMobile($loginName, '请输入合法的邮箱地址或手机号'))
            && Validator::isTrue(in_array($platform, CoreConst::$platform), '您的请求平台未知'))) {
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }
        $this->load->model('UserModels');

        $userInfo = $this->UserModels->getUserInfoByLoginName($loginName);

        if(empty($userInfo)
            || (!empty($userInfo) && !password_verify($password, $userInfo['user_password']))) {
            Validator::setMessage('用户名或密码错误');
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }

        $token     = $this->token->setTokenToRedis($userInfo['user_id'], $platform);
        $signature = $this->token->getTokenSignature($token, $userInfo['user_id']);

        //根据user_name检测是否初始化
        if (empty($userInfo['user_name'])){
            //没有真实姓名未初始化
            $this->session->set_userdata('needinit', 1);
        } else {
            $this->session->set_userdata('user_name', $userInfo['user_name']);
        }

        $this->session->set_userdata('token', $token);

        //设置用户信息
        $this->recordUserInfo($userInfo, $platform, $token, $signature);

        $this->response->jsonSuccess(array(
            'token'     => $token,
            'signature' => $signature,
        ));
    }

    /**
     * 登出
     */
    public function logout(){
        $this->load->library('session');
        $this->load->library('Token');
        $this->load->helper('url');
        //清除redistoken等
        $userId    = $this->session->userdata('user_id');
        $token     = $_COOKIE[CoreConst::TOKEN_COOKIES];
        $signature = $_COOKIE[CoreConst::TOKEN_SIGNATURE_COOKIES];

        if (false === $this->token->flushToken($token, $signature, $userId)){
            MLog::fatal(CoreConst::MODULE_ACCOUNT, sprintf('logout failed userId[%s] token[%s] signature[%s]',
                $userId,
                $token,
                $signature));
            return false;
        }
        //最后清除session
        $this->session->sess_destroy();
        unset($_COOKIE);

        //重定向到首页
        header('Location: ' . base_url('index/login'));
    }

    /**
     * 注册
     */
    public function regist(){

        $this->load->library('Token');
        /*开发期间验证码先注释掉
        //验证是否通过验证码验证
        $this->load->library('validcode');

        if (!$this->validcode->checkValidCodeAccess()){
            $this->response->jsonFail(Response::CODE_UNAUTHORIZED, '请输入正确的验证码');
        }
        */


        $userMobile     = trim($this->input->post('user_mobile',      true));
        $userEmail      = trim($this->input->post('user_email',       true));
        $passWd         = trim($this->input->post('password',         true));
        $passWdConfirm  = trim($this->input->post('password_confirm', true));
        $platform       = trim($this->input->post('platform', true));

        if (!(Validator::isNotEmpty($userMobile, '您的手机号码不能为空')
            && Validator::isMobile($userMobile, '请输入合法的手机号码')
            && Validator::isNotEmpty($userEmail,  '您的邮箱地址不能为空')
            && Validator::isEmail($userEmail,   '请输入合法的邮箱地址')
            && Validator::isNotEmpty($passWd, '您的密码不能为空')
            && Validator::isNotEmpty($passWdConfirm, '您再次输入密码不能为空')
            && Validator::mbStringRange($passWd, 0, 20, '您的密码不能超过20位')
            && Validator::mbStringRange($passWdConfirm, 0, 20, '您的密码不能超过20位')
            && Validator::isEqual($passWd, $passWdConfirm, "您两次输入的密码不一致")
            && Validator::isTrue(in_array($platform, CoreConst::$platform), '您的请求平台未知')
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
        if (!$this->UserModels->addUser(password_hash($passWd, PASSWORD_DEFAULT), $userMobile, $userEmail)){
            $this->response->jsonFail(Response::CODE_SERVER_ERROR, '抱歉，注册用户失败');
        }

        $this->response->jsonSuccess();
    }

    /**
     * 获取用户校友录信息
     */
    public function loadUserAlumniInfo(){
        $this->load->library("session");
        $this->load->model('UserModels');
        $this->load->model('AlumniModels');

        $userId = $this->session->user_id;
        if (!(Validator::isNotEmpty($userId, '您已经下线请重新登录')
        )){
            $this->response->jsonFail(Response::CODE_UNAUTHENTICATED, Validator::getMessage());
        }
        $userInfo  = $this->UserModels->getUserBasicInfo($userId);
        //$usrAlumni = $this->AlumniModels->getUserInfoByUserId($userId);
        $this->response->jsonSuccess(array(
            'userinfo' => $userInfo,
        ));
    }

    /**
     * 记录用户信息到session或常量中
     *
     * @param $userInfo
     * @param $strPlatform
     * @param $token
     * @param $signature
     */
    private function recordUserInfo($userInfo, $strPlatform, $token, $signature){
        $this->load->library('session');
        $this->load->library('Token');
        $this->session->set_userdata('user_id',     $userInfo['user_id']);
        $this->session->set_userdata('platform',    $strPlatform);

        //根据user_name检测是否初始化
        if (empty($userInfo['user_name'])){
            //没有真实姓名未初始化
            $this->session->set_userdata('needinit', 1);
        } else {
            $this->session->set_userdata('user_name', $userInfo['user_name']);
        }

        //添加一行便于测试
//        $this->session->set_userdata('needinit', 1);

        //token不会通过session进行读取，统一使用redis
//        $this->session->set_userdata('token', $token);

        //token数据配置
        $arrDataToRedis = array(
            'user_name' => $userInfo['user_name'],
            'platform'  => $strPlatform,
        );
        $this->token->setUserDataToToken($token, $signature, $userInfo['user_id'], $arrDataToRedis);

        //设置到cookies中

        $this->input->set_cookie(CoreConst::TOKEN_COOKIES, $token, time() + CoreConst::TOKEN_EXPIRE);
        $this->input->set_cookie(CoreConst::TOKEN_SIGNATURE_COOKIES, $signature, time() + CoreConst::TOKEN_EXPIRE);
    }
}
