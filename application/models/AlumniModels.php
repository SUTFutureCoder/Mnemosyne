<?php
/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 15/12/30
 * Time: 下午11:11
 */



class AlumniModels extends CI_Model{
    private static $tableName = "alumni";
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('UserLogModels');
    }

    /*
     ** 新增同学录
     *
     * @param $userId
     * @param $title
     * @param $cover 封面模板
     * @return bool
     */
    public function addAlumni($userId, $title = '', $cover = 0){
        $this->db->trans_start();
        $this->db->insert(
            self::$tableName, array(
            'user_id'     => $userId,
            'title'       => $title,
            'cover'       => $cover,
            'create_time' => time(),
            'update_time' => time(),
        ));

        $id = $this->db->insert_id();

        //打log
        $logContent = array(
            'alumni_id' => $id,
            'user_id'   => $userId,
            'title'     => $title,
            'des'       => "add alumni",
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

    /*
     *
     * 修改同学录
     *
     *@param $alumniId
     *@param $userId
     *@param $alumniParams
     */
    public function updateAlumni($alumniId, $userId, $title = false, $cover = false){
        $alumniUpdate = array();
        if(false != $title){
            $alumniUpdate['title']  = $title;
        }
        if(false != $cover){
            $alumniUpdate['cover'] = $cover;
        }

        $alumniUpdate['update_time'] = time();

        $this->db->trans_start();

        $this->db->where('id', $alumniId);
        $this->db->update(self::$tableName, $alumniUpdate);

        //打log
        $alumniUpdate['affected_rows'] = $this->db->affected_rows();
        $alumniUpdate['des'] = "update Alumni table";

        $this->db->trans_complete();
        if (!$this->db->trans_status()){
            $alumniUpdate['run_status'] = 0;
        } else {
            $alumniUpdate['run_status'] = 1;
        }
        $this->UserLogModels->addUserLog($userId, $alumniUpdate, self::$tableName, __METHOD__);
        return $alumniUpdate['run_status'];
    }

    public function getAlumniByUserId($userId){
        $this->db->where("user_id", $userId);
        $query = $this->db->get(self::$tableName);
        return $query->result_array();
    }

}
