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
     * @return array
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
        $res['run_status'] = $logContent['run_status'];
        $res['id'] = $id;
        return $res;

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

    public function deleteAlumni($alumniId, $userId){
        $alumniDelete = array('id' => $alumniId, 'user_id' => $userId);
        
        $this->db->trans_start();
        $this->db->delete(self::$tableName, $alumniDelete); 
        $alumniDelete['des'] = "delete Alumni column";
        $this->db->trans_complete();
        if (!$this->db->trans_status()){
            $alumniDelete['run_status'] = 0;
        } else {
            $alumniDelete['run_status'] = 1;
        }
        $this->UserLogModels->addUserLog($userId, $alumniDelete, self::$tableName, __METHOD__);
        return $alumniDelete['run_status'];
    }
    public function getAlumniByUserId($userId){
        $this->db->where("user_id", $userId);
        $query = $this->db->get(self::$tableName);
        return $query->result_array();
    }

    
    public function getUserAlumniInfoByAlumniId($alumniId){
        $this->db->where('id', $alumniId);
        $query  = $this->db->get(self::$tableName);
        $result = $query->result_array();
        return $result;
    }

    public function getUserAlumniPageInfo($userId, $alumniId, $pageSize = false, $pageNum = 0){
        $this->db->select('alumni_page.id alumni_page_id, alumni_id, user.user_name, user.user_nickname, user.user_birthday,
                            user.user_mobile, user_alumni.user_bloodgroup, user_alumni.user_favorite_food, user_alumni.user_favorite_animal,
                            user_alumni.user_worship_people, user_alumni.user_want_to_go, user_alumni.user_desire,
                            user_alumni.user_favorite_star, user_alumni.user_favorite_color, alumni_page.message');
        $this->db->from('alumni_page');
        $this->db->where('alumni_page.alumni_id', $alumniId);
        $this->db->where('alumni_page.user_id', $userId);
        $this->db->join('user', 'alumni_page.to_user = user.user_id');
        $this->db->join('user_alumni', 'alumni_page.to_user = user_alumni.user_id', 'left');
        if(isset($pageSize)){
            $this->db->limit($pageSize, $pageSize*$pageNum);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function countUserAlumniPageInfo($userId, $alumniId){
        $this->db->from('alumni_page');
        $this->db->where('alumni_page.alumni_id', $alumniId);
        $this->db->where('alumni_page.user_id', $userId);
        return $this->db->count_all_results();
        
    }

}
