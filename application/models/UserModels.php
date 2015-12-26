<?php
/**
 * 用户表
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-10
 * Time: 下午11:26
 */
class UserModels extends CI_Model{
    private static $tableName = 'user';

    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('UserLogModels');
    }

    /**
     * 新增用户
     *
     * @param $userName
     * @param $userPW
     * @param $userMobile
     * @param $userEmail
     * @param $userPlatform
     * @return bool
     */
    public function addUser($userName, $userPW, $userMobile, $userEmail){
        $this->db->trans_start();
        $this->db->insert(self::$tableName, array(
            'user_name'         => $userName,
            'user_password'     => $userPW,
            'user_create_time'  => time(),
            'user_status'       => 1,
            'user_mobile'       => $userMobile,
            'user_email'        => $userEmail,
        ));

        $userId = $this->db->insert_id();

        //打log
        $logContent = sprintf('用户姓名: %s, 用户手机: %s, 用户邮箱: %s',
            $userName,
            $userMobile,
            $userEmail);
        $this->UserLogModels->addUserLog($userId, $logContent, '新建用户');

        $this->db->trans_complete();
        if (!$this->db->trans_status()){
            return false;
        }
        return true;
    }

    /**
     * 检查用户是否已经存在
     *
     *
     * @param $userMobile
     * @param $userEmail
     */
    public function checkUserExists($userMobile, $userEmail){
        $this->db->where('user_mobile', $userMobile);
        $this->db->or_where('user_email', $userEmail);
        return $this->db->count_all_results(self::$tableName);
    }


    /**
     * 获取用户基本信息
     *
     * @param $userId
     * @return array
     */
    public function getUserBasicInfo($userId){
        $this->db->where('user_id', $userId);
        $query  = $this->db->get(self::$tableName);
        $result = $query->row_array();
        return $result;
    }

    /**
     * 修改用户基础数据
     *
     * @param $userId
     * @param $userName
     * @param $userBirthday
     * @param $userSex
     * @param $userMobile
     * @param $userEmail
     * @param $userSign
     * @param $userStatus
     * @return bool
     */
    public function updateUser($userId, $userName, $userBirthday, $userSex,
                               $userMobile, $userEmail, $userSign, $userStatus){
        $this->db->trans_start();

        $this->db->where('user_id', $userId);
        $this->db->update(self::$tableName, array(
            'user_name'     => $userName,
            'user_birthday' => $userBirthday,
            'user_sex'      => $userSex,
            'user_mobile'   => $userMobile,
            'user_email'    => $userEmail,
            'user_sign'     => $userSign,
            'user_status'   => $userStatus,
        ));

        //打log
        $logContent = sprintf('影响行数: %s, 用户姓名: %s, 用户手机: %s, 用户邮箱:%s, ' .
            '用户生日: %s, 用户性别: %s, 用户签名: %s, 用户状态: %s',
            $this->db->affected_rows(),
            $userName,
            $userMobile,
            $userEmail,
            $userBirthday,
            $userSex,
            $userSign,
            $userStatus
        );
        $this->UserLogModels->addUserLog($userId, $logContent, '修改用户');


        $this->db->trans_complete();
        if (!$this->db->trans_status()){
            return false;
        } else {
            return true;
        }
    }

}