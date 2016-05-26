<?php

/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/1/9
 * Time: 下午4:34
 */
class MessageModels extends CI_Model{
    private static $tableName = "message";
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('UserLogModels');
    }


    /*
    ** 新增消息
    *
    * @param $userId
    * @param $title
    * @param $cover 封面模板
    * @return bool
    */
    public function addMessage($userId, $toUser, $type, $title = '', $message = '', $describe = ''){
        $this->db->trans_start();
        $this->db->insert(
            self::$tableName, array(
            'user_id'     => $userId,
            'to_user'     => $toUser,
            'type'        => $type,
            'title'       => $title,
            'message'     => $message,
            'describe'    => $describe,
            'create_time' => time(),
            'update_time' => time(),
        ));

        $id = $this->db->insert_id();

        //打log
        $logContent = array(
            'message_id' => $id,
            'user_id'   => $userId,
            'title'     => $title,
            'des'       => "add message",
        );


        $this->db->trans_complete();
        if (!$this->db->trans_status()){
            $logContent['run_status'] = 0;
        } else {
            $logContent['run_status'] = 1;
        }
        $this->UserLogModels->addUserLog($userId, $logContent, self::$tableName, __METHOD__);
        return $logContent['run_status'];
    }

    public function getMessageByUserId($userId){
        $this->db->where('to_user', $userId);
        $row = $this->db->get(self::$tableName)->result_array();
        return $row;
    }

}
