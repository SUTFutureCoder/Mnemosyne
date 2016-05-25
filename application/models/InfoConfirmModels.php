<?php

/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/5/25
 * Time: 下午11:40
 */
class InfoConfirmModels extends CI_Model
{
    private static $tableName = "info_confirm";
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('UserLogModels');
    }

    public function addInfoConfirm($userId, $toUser, $type, $status = 0){
        $this->db->trans_start();
        $this->db->insert(
            self::$tableName, array(
            'user_id'     => $userId,
            'to_user'     => $toUser,
            'type'        => $type,
            'status'      => $status,
            'create_time' => time(),
            'update_time' => time(),
        ));

        $id = $this->db->insert_id();

        //打log
        $logContent = array(
            'message_id' => $id,
            'user_id'   => $userId,
            'des'       => "add info confirm",
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

    public function getInfoConfirmByToUserId($userId){
        $this->db->where('to_user', $userId);
        $row = $this->db->get(self::$tableName)->result_array();
        return $row;
    }


}