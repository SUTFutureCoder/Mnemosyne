<?php
/**
 * Created by VIM
 * User: bricks
 * Date: 16-06-02
 * Time: 下午19:24
 */
class Love extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->library("template");
        $this->load->helper("login");
    }

    /**
     * 示爱前端
     *
     * 必须是好友才允许示爱
     *
     */
    public function showlove(){
        //


    }

    public function expressLoveView(){
        $this->template->display("love/index.html");
    }

}
