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
        $this->load->library('ModuleConst');
        $this->load->helper("login");
    }

    /**
     * 示爱前端
     *
     * 必须是好友才允许示爱
     *
     */
    public function showlove(){
        //检测登录
        checkLogin();

        //读取好友列表
        $this->template->assign("navbar", getHorizontalNavbar(4));
        $this->template->assign("navbar_veritical", getVerticalNavtar(0));
        $this->template->assign("main_content", 'friend_list_to_showlove.html');
        $this->template->assign("js", 'friend_list_to_showlove.html');
        $this->template->display("friends/friends.html");
        $this->template->display("public/footer.html");

    }
}
