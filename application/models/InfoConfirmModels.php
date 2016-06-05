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

    public function getUserIdListByToUserId($userId, $status = 0, $pageSize = 5, $page = 0){
        $offset = $pageSize * $page;
        $this->db->select('user_id');
        $this->db->where('to_user', $userId);
        $row = $this->db->get(self::$tableName, $pageSize, $offset)->result_array();
        return $row;
    }

    public function getUserFullInfoListJoinInUser($userId, $type, $infoConfirmStatus = CoreConst::INFO_CONFRIM_STATUS_UNREAD){
        $this->db->select('info_confirm.id, user.user_name, user.user_avatar, user.user_nickname, user.user_id');
        $this->db->from('info_confirm');
        $this->db->where('info_confirm.to_user', $userId);
        $this->db->where('info_confirm.type', $type);
        $this->db->where('info_confirm.status', $infoConfirmStatus);
        $this->db->join('user', 'info_confirm.user_id = user.user_id' );
        $result = $this->db->get()->result_array(); 
        return $result;
    }

    public function checkInfoIsExist($userId, $toUser, $type, $status = 0){
        $this->db->where('user_id', $userId);
        $this->db->where('to_user', $toUser);
        $this->db->where('type', $type);
        $this->db->where('status', $status);
        return $this->db->count_all_results(self::$tableName);
    }
    public function updateInfoConfrimStatus($userId, $id, $status){
        $this->db->trans_start();
        $arrUpdateConds = array();
        $arrUpdateConds['status'] = $status;
        $this->db->where('id', $id);
        $this->db->update(self::$tableName, $arrUpdateConds);

        //打log
        $arrUpdateConds['affected_rows'] = $this->db->affected_rows();

        $this->db->trans_complete();
        if (!$this->db->trans_status()){
            $arrUpdateConds['run_status'] = 0;
        } else {
            $arrUpdateConds['run_status'] = 1;
        }
        $this->UserLogModels->addUserLog($userId, $arrUpdateConds, self::$tableName, __METHOD__);
        return $arrUpdateConds['run_status'];
    }
}
