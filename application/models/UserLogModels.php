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
    public function addUserLog($userId, $content, $platform, $type){
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