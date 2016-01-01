<?php
/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 15/12/30
 * Time: 下午11:11
 */



class AlumniModels extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    /*
     * 通过user id获取user alumni info
     *
     * @param int $userId 用户
     * @return array 用户信息
     */
    public function getUserInfoByUserId($userId){
        $this->db->where('user_id', $userId);
        if ($row = $this->db->get('user_alumni')->row_array()){
            return $row;
        }
        return false;
    }

}