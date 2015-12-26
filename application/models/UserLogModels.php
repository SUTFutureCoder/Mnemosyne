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
        $this->load->library('CoreConst');
    }


    /**
     *
     * 记录用户log
     *
     *
     * @param $userId
     * @param $content
     * @param $platform
     * @param $type
     * @return bool
     */
    public function addUserLog($userId, $content, $type){
        //获取用户platform
        if ((!$platform = $this->session->userdata('platform'))
                || (!in_array($platform, CoreConst::$platform))){
            $platform = 0;
        }


        $this->db->trans_start();

        $this->db->insert(self::$tableName, array(
            'user_id'     => $userId,
            'content'     => $content,
            'platform'    => $platform,
            'type'        => $type,
            'create_time' => time(),
        ));

        $this->db->trans_complete();
        if (!$this->db->trans_status()){
            return false;
        }
        return true;
    }

}