<?php
/**
 * 学校、班级、学生关联表
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 15-12-9
 * Time: 下午7:11
 */
class SchoolClassUserMapModels extends CI_Model{
    private static $tableName = 'school_class_user_map';

    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('UserLogModels');
    }

    /**
     * 用户绑定班级&学校
     *
     * @param $schoolId
     * @param $classId
     * @param $userId
     * @param string $studentId
     * @return bool
     */
    public function bind($schoolId, $classId, $userId, $studentId = ''){
        $this->db->trans_start();

        $this->db->insert(self::$tableName, array(
            'school_unique_id' => $schoolId,
            'class_unique_id'  => $classId,
            'user_unique_id'   => $userId,
            'student_id'       => $studentId,
        ));

        $logContent = sprintf('用户Id: %s, 学校Id: %s, 班级Id: %s, 学生Id: %s',
            $userId,
            $schoolId,
            $classId,
            $studentId
        );
        $this->UserLogModels->addUserLog($userId, $logContent, '绑定班级');

        $this->db->trans_complete();
        if (!$this->db->trans_status()){
            return false;
        } else {
            return true;
        }
    }

    /**
     * 检查是否已经重复绑定
     *
     * @param $schoolId
     * @param $classId
     * @param $userId
     */
    public function checkBindExists($schoolId, $classId, $userId){
        $this->db->where(array(
            'school_unique_id' => $schoolId,
            'class_unique_id'  => $classId,
            'user_unique_id'   => $userId,
        ));
        return $this->db->count_all_results(self::$tableName);
    }

    /**
     * 获取用户绑定班级列表
     *
     * @param $userId
     * @return array
     */
    public function getUserBindList($userId){
        $this->db->where('user_unique_id', $userId);
        $query      = $this->db->get(self::$tableName);
        return $query->result_array();
    }

    public function unBind($mapId){

    }
}