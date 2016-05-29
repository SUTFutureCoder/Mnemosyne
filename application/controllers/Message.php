<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 16-1-1
 * Time: 下午4:55
 */
class Message extends CI_Controller{
    private static $redirectURI = array(
        0 => "",
        1 => "/Friends/friendsRequest"
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
        $userId = $this->session->user_id;
        $messageType = $this->input->get('message_type', true);
        $messageId = $this->input->get('message_id', true);
        $message =  $this->message->getMessageById($messageId);
        if($message[0]['to_user'] != $userId){
            die("骚年不是你的消息");
        }
        $MarkMessageStatus = $this->message->updateMessageStatus($userId, $messageId, CoreConst::MESSAGE_STATUS_READ);
        if(!$MarkMessageStatus){
            die("test");
        }
        redirect(self::$redirectURI[$messageType]);
    }

}
