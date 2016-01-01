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
        //$userinfo   = checkLogin();
        $this->template->assign("navbar", $navbar);
        $this->template->display("user/completeinfo.html");
    }

}
