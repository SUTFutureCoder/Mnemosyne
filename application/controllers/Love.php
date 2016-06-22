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
     * 注意如果已经有情侣，则显示情侣相关信息。目前先暂时无视
     *
     *
     * 使用表白系统之后，如何能体现出表白系统的优势？
     *
     * 用户会不会使用完表白系统，然后就用陌陌、微信竞对？
     *
     */
    public function showlove(){
        //检测登录
        checkLogin();

        //注入模板
        $this->template->assign("navbar", getHorizontalNavbar(4));
        $this->template->assign("navbar_veritical", getVerticalNavtar(0));
        $this->template->assign("js", 'showlove_js.html');
        $this->template->display("love/showlove.html");
//        $this->template->display("public/footer.html");
    }
}
