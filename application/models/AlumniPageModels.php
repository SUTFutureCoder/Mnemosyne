<?php

/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/1/9
 * Time: 下午4:33
 */
class AlumniPageModels extends CI_Model{
    private static $tableName = "alumni_page";
    private static $tableColumn = array(
        0 => 'alumni_id',
        1 => 'user_id',
        2 => 'to_user',
        3 => 'background_style',
        4 => 'info',
        5 => 'message',
        6 => 'status',
    );
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('UserLogModels');
    }

    /*
     ** 新增同学录页面
     *
     * @param $userId
     * @param $title
     * @param $cover 封面模板
     * @return bool
     */
    public function addAlumniPage($alumniId, $userId, $toUser, $backgroundStyle = 0, $info = ''){
        $this->db->trans_start();
        $this->db->insert(
            self::$tableName, array(
            'alumni_id'        => $alumniId,
            'user_id'          => $userId,
            'to_user'          => $toUser,
            'background_style' => $backgroundStyle,
            'info'             => $info,
            'create_time'      => time(),
            'update_time'      => time(),
        ));

        $id = $this->db->insert_id();

        //打log
        $logContent = array(
            'alumni_id' => $id,
            'user_id'   => $userId,
            'to_user'   => $toUser,
            'info'      => $info,
            'des'       => "add alumniPage",
        );


        $this->db->trans_complete();
        if (!$this->db->trans_status()){
            $logContent['run_status'] = false;
        } else {
            $logContent['run_status'] = $id;
        }
        $this->UserLogModels->addUserLog($userId, $logContent, self::$tableName, __METHOD__);
        return $logContent['run_status'];

    }

    public function updateAlumniPage($userId, $alumniPageId, $updateArr){
        $arrUpdateConds = array();
        foreach(self::$tableColumn  as $column){
            if(isset($updateArr[$column]) && !empty($updateArr[$column]) ){
                $arrUpdateConds[$column] = $updateArr[$column];
            }
        }
        $this->db->trans_start();

        $this->db->where('id', $alumniPageId);
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

    public function getSendToUserInfoJoinUser($userId, $pageSize = false, $page = 0){
        $this->db->select('alumni_page.id, user.user_name, user.user_avatar, user.user_nickname, user.user_id, alumni_page.status');
        $this->db->from('alumni_page');
        $this->db->where('alumni_page.to_user', $userId);
        $this->db->join('user', 'alumni_page.user_id = user.user_id' );
        $this->db->order_by('status', 'asc');
        $this->db->order_by('alumni_page.update_time', 'desc');
        if(isset($pageSize)){
            $this->db->limit($pageSize, $page*$pageSize);
        }
        $result = $this->db->get()->result_array(); 
        return $result;
    }
    
    public function getResultCount($whereCondition){
        $arrUpdateConds = array();
        $this->db->from(self::$tableName);
        foreach(self::$tableColumn  as $column){
            if(isset($whereCondition[$column]) && !empty($whereCondition[$column]) ){
                $arrUpdateConds[$column] = $whereCondition[$column];
                $this->db->where($column, $whereCondition[$column]);
            }
        }
        return $this->db->count_all_results();
    }

    public function getUserIdListByAlumniId($alumniId){
        $this->db->select('to_user');
        $this->db->where('alumni_id', $alumniId);
        $query = $this->db->get(self::$tableName);
        return $query->result_array();
    }

}
