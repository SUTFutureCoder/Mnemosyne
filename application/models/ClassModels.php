<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-15
 * Time: 上午12:17
 */
class ClassModels extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    //::NOTICE::目前仅支持单个学校的情况
    /*
     * 通过class id获取class name
     *
     * @param char $classId 班级id
     * @return string 班级名称
     */
    public function getClassNameById($classId){
        $this->db->select('class_name');
        $this->db->where('class_id', $classId);
        if ($row = $this->db->get('class')->row_array()){
            return $row['class_name'];
        }
        return false;
    }

    public function getClassIdByName($className){
        $this->db->select('class_id');
        $this->db->where('class_name', $className);
        if ($row = $this->db->get('class')->row_array()){
            return $row['class_id'];
        }
        return false;
    }

    public function geClassListBySchoolId($schoolId){

        $this->db->select('class_id , class_name');
        $this->db->where('school_id', $schoolId);
        $row = $this->db->get('class')->result_array();
        return $row;
    }

}