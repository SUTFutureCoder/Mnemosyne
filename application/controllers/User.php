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

    public function completeInfo()
    {
        $this->load->library('session');
        $navbar     = getHorizontalNavbar(1);
//        $userinfo   = checkLogin();




        $this->template->assign("navbar", $navbar);

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

}
