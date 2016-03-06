<?php
/**
 *
 * 用户日志表
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 15-12-5
 * Time: 下午4:13
 */
class UserLogModels extends CI_Model{
    private static $tableName = 'user_log';

    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
    }


    /**
     *
     * 记录用户log
     *
     *
     * @param $userId
     * @param $content
     * @param $module
     * @param $method
     * @return bool
     */
    public function addUserLog($userId, $content, $module, $method){
        //获取用户platform
        if ((!$userPlatform = $this->session->userdata('platform'))
                || !in_array($userPlatform, CoreConst::$platform)){
            $userPlatform = 0;
        }

        $content = json_encode($content);

        $this->db->trans_start();
        $this->db->insert(self::$tableName, array(
            'user_id'     => $userId,
            'content'     => $content,
            'platform'    => $userPlatform,
            'module'      => $module,
            'method'      => $method,
            'create_time' => time(),
        ));

        $this->db->trans_complete();
        if (!$this->db->trans_status()){
            return false;
        }
        return true;
    }

}