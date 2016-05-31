<?php
/**
 * Created by VIM
 * User: bricks
 * Date: 16-5-29
 * Time: 下午11:28
 */
class Alumni extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->library("template");
        $this->load->helper("login");
    }
    private function paramsPrepared($navbarNum, $mainContent, $js){
        $navbar = getHorizontalNavbar(2);
        $navbarVeritical = getAlumniVerticalNavtar($navbarNum);
        $this->template->assign("navbar", $navbar);
        $this->template->assign("navbar_veritical", $navbarVeritical);
        $this->template->assign("main_content", $mainContent);
        $this->template->assign("js", $js);
        $this->template->display("alumni/alumni.html");
        $this->template->display("public/footer.html");
         
    }
    public function fillInAlumni(){
        $navbarNum = 0;
        $mainContent = "fillin_alumni.html";
        $js = "fillin_alumni_js.html";
        $this->paramsPrepared($navbarNum, $mainContent, $js);
    }

}
