<?php

/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/5/19
 * Time: 下午3:40
 */
class userAlumniModels extends CI_Model {
    private static $tableName = 'user_alumni';
    private static $tableColumn = array(
        0 => "user_bloodgroup",
        1 => "user_favorite_food",
        2 => "user_favorite_animal",
        3 => "user_worship_people",
        4 => "user_want_to_go",
        5 => "user_desire",
        6 => "user_desire",
        7 => "user_favorite_star",
        8 => "user_favorite_color",

    );

    public function __construct(){
        $this->load->database();
        $this->load->model('UserLogModels');

    }

    public function addUserAlumni($userId, $arrAddConds){
        $arrAddConds = $this->generateUpdateArr($arrAddConds);
        $arrAddConds['user_id'] = $userId;
        $this->db->trans_start();
        $this->db->insert($arrAddConds);

        $logContent = array(
            "userId" => $userId,
            "behavior" => "add user Alumni",
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

    public function updateAlumni($userId, $arrUpdateConds){
        $arrUpdateConds = $this->generateUpdateArr($arrUpdateConds);
        $this->db->trans_start();

        $this->db->where('user_id', $userId);
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

    public function getUserAlumniInfo($userId){

        $this->db->where('user_id', $userId);
        $query  = $this->db->get(self::$tableName);
        $result = $query->row_array();
        return $result;
    }

    public function checkUserAlumniInfoExists($userId){
        $this->db->where('user_mobile', $userId);
        return $this->db->count_all_results(self::$tableName);
    }

    private function generateUpdateArr($columnArr){
        $arrUpdateConds = array();
        foreach (self::$tableColumn as $column){
            if(isset($columnArr[$column]) && !empty($columnArr[$column])) {
                $arrUpdateConds[$column] = $columnArr[$column];
            }
        }
        return $arrUpdateConds;
    }
}