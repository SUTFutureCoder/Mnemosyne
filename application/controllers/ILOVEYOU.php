<?php
/**
 * 虐狗专区，前方高能[DOGE]
 *
 * eg:http://localhost:10090/Mnemosyne/ILOVEYOU/geek/14646993366/14636674923/
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-6-10
 * Time: 下午8:15
 */
class ILOVEYOU extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('ModuleConst');
        $this->load->library('session');
        $this->load->library('template');
        $this->load->library('Showlove');
        $this->load->helper('login');
        checkLogin();
    }

    public function classic($fromUser = null, $toUser = null){
        var_dump($this->showlove->checkLoveMess($fromUser, $toUser));
    }

    public function geek($fromUser = null, $toUser = null){
        $arrMessInfo = $this->showlove->checkLoveMess($fromUser, $toUser);

        if (empty($arrMessInfo['describe'])){
            throw new MException(CoreConst::MODULE_SHOWLOVE, ErrorCodes::ERROR_SHOWLOVE_MESS_MISSING);
        }

        //注入模板
        $arrDescribe = json_decode($arrMessInfo['describe'], true);

        $this->template->assign('fromUserNickname', $arrDescribe['fromUserNickname']);
        $this->template->assign('toUserNickname',   $arrDescribe['toUserNickname']);
        $this->template->assign('taKnowTime_Y',     $arrDescribe['taKnowTime']['Y']);
        $this->template->assign('taKnowTime_m',     $arrDescribe['taKnowTime']['m']);
        $this->template->assign('taKnowTime_d',     $arrDescribe['taKnowTime']['d']);
        $this->template->assign('toUserId',         $toUser);
        $this->template->assign('fromUserId',       $fromUser);
        $this->template->assign("lovemess",         $arrDescribe['message']);
        $this->template->display("love/type/geek.html");
    }

    /**
     * 示例
     */
    public function expressLoveView(){
        $this->load->library('template');
        $this->template->display("love/type/geek.html");
    }

}