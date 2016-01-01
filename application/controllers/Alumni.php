<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 16-1-1
 * Time: 下午4:31
 */
class Alumni extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->library("template");
        $this->load->helper("login");
    }

    public function edit()
    {
        $navbar = getHorizontalNavbar(3);
        $this->template->assign("navbar", $navbar);
//        $this->template->display("edit.html");
    }
    public function send()
    {
        $navbar = getHorizontalNavbar(2);
        $this->template->assign("navbar", $navbar);
//        $this->template->display("send.html");
    }
    public function expressLove()
    {
        $navbar = getHorizontalNavbar(0);
        $this->template->assign("navbar", $navbar);
        die("此功能尚未添加");
    }

}