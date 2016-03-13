<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 16-1-1
 * Time: 下午4:55
 */
class User extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->helper('html');
        $this->load->library('template');
    }

    /**
     * 可以通过此处显示某用户的个人动态
     *
     * eg：http://localhost:10090/Mnemosyne/user/index/123
     *
     * 注意权限控制
     *
     * 置空则显示自己动态
     *
     * @param string $userId
     */
    public function index($userId = '')
    {
        $this->load->library('session');
        $this->load->helper('login_helper');

        $navbar     = getHorizontalNavbar(-1);
        $this->template->assign("navbar", $navbar);

        //检查是否登录，未登录则强制跳到登录页
        checkLogin();

        //显示通用第一眼
        $this->template->display("firstsight/index.html");

        //此处需要检测是否已经初始化
        if ($this->session->needinit){
            //如未初始化显示
            $this->template->display("firstsight/completeinfo.html");
        }

        //载入底端，注意不要重复载入
        $this->template->display("public/footer.html");

    }

    public function userInfo(){
        $navbar     = getHorizontalNavbar(0);
//	$this->load->lirbary('session');
        $this->template->assign("navbar", $navbar);
        $this->template->display("user/userinfo.html");
    }

}
