<?php

/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 15/12/15
 * Time: 下午1:20
 */
class Index extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("template");
        $this->load->helper("login");
    }
    public function login()
    {
        $this->template->display("login.html");
    }
    public function completeInfo()
    {
//        $this->load->library('session');
//        $this->session->set_userdata('test', '123456');
//        echo $this->session->test;
//        exit;
        $navbar     = getHorizontalNavbar(1);
        $userinfo   = checkLogin();
        $this->template->assign("navbar", $navbar);
        $this->template->display("completeinfo.html");
    }
    public function edit()
    {
        $navbar = getHorizontalNavbar(3);
        $this->template->assign("navbar", $navbar);
        $this->template->display("edit.html");
    }
    public function send()
    {
        $navbar = getHorizontalNavbar(2);
        $this->template->assign("navbar", $navbar);
        $this->template->display("send.html");
    }
    public function expressLove()
    {
        $navbar = getHorizontalNavbar(0);
        $this->template->assign("navbar", $navbar);
        die("此功能尚未添加");
    }
}