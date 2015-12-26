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
     * 登陆验证
     *
     * @Param $loginName
     * @Param  $password
     * $return array()
     */
    public function isValidlogin($loginName, $password, $type)
    {
        $this->load->library('validcode');
        if($type == 'email') {
            $this->db->where('user_mobile', $loginName);
        }else if($type == 'mobile'){
            $this->db->where('user_email', $loginName) ;
        }else{
            return array();
        }

        $userInfo = $this->db->from(self::$tableName)->result_array();

        if(!empty($userInfo) && $userInfo['password'] === password_verify($password, PASSWORD_DEFAULT)){
            return $userInfo;
        }
        return array();
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
        $logContent = array(
            'user_name'     => $userName,
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
        $arrUpdateConds = array(
            'user_name'     => $userName,
            'user_birthday' => $userBirthday,
            'user_sex'      => $userSex,
            'user_mobile'   => $userMobile,
            'user_email'    => $userEmail,
            'user_sign'     => $userSign,
            'user_status'   => $userStatus,
        );

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