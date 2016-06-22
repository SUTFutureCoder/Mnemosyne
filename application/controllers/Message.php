<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 16-1-1
 * Time: 下午4:55
 */
class Message extends CI_Controller{
    private static $redirectURI = array(
        0 => "Alumni/fillInAlumni",
        1 => "/Friends/friendsRequest",
        //表白
        2 => 'ILOVEYOU',
    );

    public function __construct(){
        parent::__construct();
        $this->load->helper('html');
        $this->load->library('template');
        $this->load->helper('login_helper');
        $this->load->library('session');
        $this->load->model('MessageModels', 'message');
    }

    public function MessageResove()
    {
        checkLogin('api');
        $userId     = $this->session->user_id;
        $messageType = $this->input->get('message_type', true);
        $messageId  = $this->input->get('message_id',    true);
        $message    = $this->message->getMessageById($messageId);
        if($message[0]['to_user'] != $userId){
            die("骚年不是你的消息");
        }
        $MarkMessageStatus = $this->message->updateMessageStatus($userId, $messageId, CoreConst::MESSAGE_STATUS_READ);
        if(!$MarkMessageStatus){
            die("更新信息状态失败");
        }

        //可拓展,不同类型有不同的解析方法
        switch ($messageType){
            case 0:
            case 1:
                redirect(self::$redirectURI[$messageType]);
                break;
            case 2:
                if (empty($message[0]['describe'])){
                    die('解析表白信息失败');
                }
                $arrDescInfo = json_decode($message[0]['describe'], true);

                if (!is_array($arrDescInfo) || !$arrDescInfo['tpltype']){
                    die('解析表白地址失败');
                }
                redirect(self::$redirectURI[$messageType] . '/' . $arrDescInfo['tpltype'] . '/' . $message[0]['user_id'] . '/' . $message[0]['to_user']);
                break;
        }

    }

}
