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
        $this->load->library('validator.php');
        $this->load->library('Response');
    }

    /**
     * Set the account of user
     *
     * <table class="table table-hover table-bordered">
     *     <tr><th>POST</th><th>type</th><th>explain</th></tr>
     *     <tr><td>user_name</td><td>string</td><td>用户姓名</td></tr>
     *     <tr><td>school</td><td>string</td><td>用户学校</td></tr>
     *     <tr><td>class</td><td>string</td><td>用户班级</td></tr>
     *     <tr><td>user_mobile</td><td>string</td><td>用户手机号码</td></tr>
     *     <tr><td>user_email</td><td>string</td><td>用户邮箱</td></tr>
     * </table>
     *
     * [wiki-注册用户](http://wiki.sutapp.com/doku.php?id=project_m:api#注册用户)
     * @access public
     */
    public function regist(){
        //验证是否通过验证码验证
        $this->load->library('validcode');
        $this->validcode->checkValidCodeAccess();


        $userName   = $this->input->post('user_name',   true);
        $school     = $this->input->post('school',      true);
        $class      = $this->input->post('class',       true);
        $userMobile = $this->input->post('user_mobile', true);
        $userEmail  = $this->input->post('user_email',  true);

        if (!(Validator::isNotEmpty($userName,      '您的姓名不能为空')
                && Validator::isNotEmpty($school,   '您的学校名称不能为空')
                && Validator::isNotEmpty($class,    '您的班级不能为空')
                && Validator::isNotEmpty($userMobile, '您的手机号码不能为空')
                && Validator::isMobile($userMobile, '请输入合法的手机号码')
                && Validator::isNotEmpty($userEmail,  '您的邮箱地址不能为空')
                && Validator::isEmail($userEmail,   '请输入合法的邮箱地址'))){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }

    }
}