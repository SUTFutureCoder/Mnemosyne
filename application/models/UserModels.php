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
     * @param $userPW
     * @param $userMobile
     * @param $userEmail
     * @return bool
     */
    public function addUser($userPW, $userMobile, $userEmail){
        $this->db->trans_start();
        $this->db->insert(self::$tableName, array(
            'user_password'     => $userPW,
            'user_create_time'  => time(),
            'user_status'       => 1,
            'user_mobile'       => $userMobile,
            'user_email'        => $userEmail,
        ));

        $userId = $this->db->insert_id();

        //打log
        $logContent = array(
            'user_mobile'   => $userMobile,
            'user_email'    => $userEmail,
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
     * 获取用户基本信息
     *
     * @param $userIdList
     * @return array
     */
    public function getUserBasicInfoList($userIdList){
        if(empty($userIdList))
        {
            return array();
        }
        $this->db->select('user_id, user_name');
        $this->db->where_in('user_id', $userIdList);
        $query  = $this->db->get(self::$tableName);
        $result = $query->result_array();
        return $result;
    }



    /**
     * 通过登陆的手机号或邮箱获取用户信息
     *
     * @param $userLoginName
     * @return Array
     */
    public function getUserInfoByLoginName($userLoginName)
    {
        $this->db->where('user_mobile', $userLoginName);
        $this->db->or_where('user_email', $userLoginName);
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
    public function updateUser($userId, $userName = false, $userBirthday = false, $userSex = false,
                               $userMobile = false, $userEmail = false, $userSign = false, $userStatus = false){

        $arrUpdateConds = array();

        if (!empty($userName)){
            $arrUpdateConds['user_name']     = $userName;
        }

        if (!empty($userBirthday)){
            $arrUpdateConds['user_birthday'] = $userBirthday;
        }

        if (FALSE !== ($userSex)){
            $arrUpdateConds['user_sex']      = $userSex;
        }

        if (!empty($userMobile)){
            $arrUpdateConds['user_mobile']   = $userMobile;
        }

        if (!empty($userEmail)){
            $arrUpdateConds['user_email']    = $userEmail;
        }

        if (!empty($userSign)){
            $arrUpdateConds['user_sign']     = $userSign;
        }

        if (FALSE !== $userStatus){
            $arrUpdateConds['user_status']   = $userStatus;
        }

        if (empty($arrUpdateConds)){
            return 0;
        }

        $this->db->trans_start();

        $this->db->where('user_id', $userId);
        $this->db->update(self::$tableName, $arrUpdateConds);

        //打log
        $arrUpdateConds['affected_rows'] = $this->db->affected_rows();

        $this->db->trans_complete();
        if (!$this->db->trans_status()){
            $arrUpdateConds['run_status'] = 0;
        } else {
            $arrUpdateConds['run_status'] = 1;
        }
        $this->UserLogModels->addUserLog($userId, $arrUpdateConds, self::$tableName, __METHOD__);
        return $arrUpdateConds['run_status'];
    }

}