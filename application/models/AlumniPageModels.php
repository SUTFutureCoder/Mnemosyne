<?php

/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/1/9
 * Time: 下午4:33
 */
class AlumniPageModels extends CI_Model{
    private static $tableName = "alumni_page";
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('UserLogModels');
    }

    /*
     ** 新增同学录页面
     *
     * @param $userId
     * @param $title
     * @param $cover 封面模板
     * @return bool
     */
    public function addAlumniPage($alumniId, $userId, $toUser, $backgroundStyle = 0, $info = ''){
        $this->db->trans_start();
        $this->db->insert(
            self::$tableName, array(
            'alumni_id'        => $alumniId,
            'user_id'          => $userId,
            'to_user'          => $toUser,
            'background_style' => $backgroundStyle,
            'info'             => $info,
            'create_time'      => time(),
            'update_time'      => time(),
        ));

        $id = $this->db->insert_id();

        //打log
        $logContent = array(
            'alumni_id' => $id,
            'user_id'   => $userId,
            'to_user'   => $toUser,
            'info'      => $info,
            'des'       => "add alumniPage",
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




}