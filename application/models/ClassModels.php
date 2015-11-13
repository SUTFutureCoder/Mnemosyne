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
}