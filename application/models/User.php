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
class User extends CI_Model{
    public function __construct(){
        parent::__construct();
    }

    public function addUser($userName, $school, $class, $userMobile, $userEmail){
        $this->load->database();

        //把用户信息写入redis
    }

}