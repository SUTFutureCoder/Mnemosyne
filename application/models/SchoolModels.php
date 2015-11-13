<?php
/**
 * 校园类
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-10
 * Time: 下午11:39
 */
class SchoolModels extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function getSchoolIdByName($schoolName){
        $this->db->select('school_id');
        $this->db->where('school_name', $schoolName);
        if ($row = $this->db->get('school')->row_array()){
                return $row['school_id'];
        }
        return false;
    }
}