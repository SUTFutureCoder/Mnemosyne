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
        $this->load->helper('login');
    }

    public function classic($fromUser = null, $toUser = null){
        var_dump($this->checkLoveMess($fromUser, $toUser));
    }

    public function geek($fromUser = null, $toUser = null){
        $this->checkLoveMess($fromUser, $toUser);
        $this->template->display("love/type/geek.html");
    }

    /**
     * 检查表白信息是否合法，保护隐私
     *
     * @param $fromUser
     * @param $toUser
     * @return bool
     */
    private function checkLoveMess($fromUser, $toUser){
        if (null === $fromUser || null === $toUser){
            die('参数缺失');
        }

        checkLogin();

        $userId = $this->session->user_id;
        if (empty($userId) || ($userId != $fromUser && $userId != $toUser)){
            die('身份验证失败');
        }

        //查消息记录确认
        $this->load->model('MessageModels');
        $arrMessInfo = $this->MessageModels->getMessageByUserId($toUser, null, $fromUser, 2);

        //选取最近的一条表白
        if (is_array($arrMessInfo) && isset($arrMessInfo[0])){
            return $arrMessInfo[0];
        } else {
            die('没能够找到表白信息');
        }
    }


    /**
     * 示例
     */
    public function expressLoveView(){
        $this->load->library('template');
        $this->template->display("love/type/geek.html");
    }

}