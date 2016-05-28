<?php
/**
 * 用户表
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-10
 * Time: 下午11:26
 */
class UserRelationModels extends CI_Model{
    private static $tableName = 'user_relation';

    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('UserLogModels');
        $this->load->library('util/Uuid');
    }


    /**
     * 新增用户
     *
     * @param $userId
     * @param $userRelate
     * @param $type
     * @return bool
     */
    public function addUserRelation($userId, $userRelate, $type){
        $this->db->trans_start();
        $this->db->insert(self::$tableName, array(
            'user_id'   => $userId,
            'user_relate'   => $userRelate,
            'type'        => $type,
            'intimacy'        => 0,
            'create_time'  => time(),
        ));

        //打log
        $logContent = array(
            'user_id'   => $userId,
            'user_Relate'    => $userRelate,
            'type'    => $type,
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

    public function getUserFriendIdList($userId){
        $this->db->select('user_relate user_id');
        $this->db->where('user_id', $userId);
        $this->db->where_in('type ', CoreConst::$userFriendTypeList);
        $query = $this->db->get(self::$tableName);
        $result = $query->result_array();
        return $result;
    }

    public function getUserFriendInfoJoinInUser($userId){
        $this->db->select('*');
        $this->db->from(self::$tableName);
        $this->db->where('user_relation.user_id', $userId);
        $this->db->join('user', 'user.user_id = user_relation.user_relate');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function deleteRelation($userId, $userRelate){
        $this->db->trans_start();
        $this->db->delete(self::$tableName, array(
            'user_id'   => $userId,
            'user_relate'   => $userRelate,
        ));

        //打log
        $logContent = array(
            'user_id'   => $userId,
            'user_Relate'    => $userRelate,
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
    public function isRelationExist($userId, $userRelate){
        $this->db->where(array(
            'user_id' => $userId,
            'user_relate' => $userRelate,
        ));
        return $this->db->count_all_results(self::$tableName);
    }
}
