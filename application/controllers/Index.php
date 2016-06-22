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
        $this->template->display("./login/login.html");
    }

}