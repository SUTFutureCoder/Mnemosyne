<?php
/**
 * 用户表
 *
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-10
 * Time: 下午11:26
 */
class UserModels extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function addUser($userName, $schoolId, $classId, $userMobile, $userEmail){
        $this->db->insert('user', array(
            'user_name' => $userName,
        ));

        //把用户信息写入redis
    }

}