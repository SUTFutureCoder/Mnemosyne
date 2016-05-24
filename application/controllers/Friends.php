<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 16-1-1
 * Time: 下午4:31
 */
class Friends extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->library("template");
        $this->load->helper("login");
    }

    public function friendsView(){
        $navbar = getHorizontalNavbar(3);
        $navbarVeritical = getVerticalNavtar(0);
        $mainContent = "my_friends.html";
        $this->template->assign("navbar", $navbar);
        $this->template->assign("navbar_veritical", $navbarVeritical);
        $this->template->assign("main_content", $mainContent);
        $this->template->display("friends/friends.html");
        $this->template->display("public/footer.html");
    }

    public function addFriends(){
        $navbar = getHorizontalNavbar(3);
        $navbarVeritical = getVerticalNavtar(1);
        $mainContent = "add_friends.html";
        $this->template->assign("navbar", $navbar);
        $this->template->assign("navbar_veritical", $navbarVeritical);
        $this->template->assign("main_content", $mainContent);
        $this->template->display("friends/friends.html");
        $this->template->display("public/footer.html");
    }

    public function friendsRequest(){
        $navbar = getHorizontalNavbar(3);
        $navbarVeritical = getVerticalNavtar(2);
        $mainContent = "friends_request.html";
        $this->template->assign("navbar", $navbar);
        $this->template->assign("navbar_veritical", $navbarVeritical);
        $this->template->assign("main_content", $mainContent);
        $this->template->display("friends/friends.html");
        $this->template->display("public/footer.html");
    }

}
