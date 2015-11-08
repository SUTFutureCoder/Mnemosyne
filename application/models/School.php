<?php
/**
 * 校园类
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-10
 * Time: 下午11:39
 */
class School extends CI_Model{
    public function __construct(){
        parent::__construct();
    }

    public function getSchoolId($schoolName){
        $this->load->database();
        //学校信息写入redis
    }
}