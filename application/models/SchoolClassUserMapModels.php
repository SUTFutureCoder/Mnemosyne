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

        $logContent = array(
            'affected_row'  => $this->db->affected_rows(),
            'user_id'       => $userId,
            'school_id'     => $schoolId,
            'class_id'      => $classId,
            'student_id'    => $studentId,
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

    /**
     * 解绑用户班级
     *
     * @param $mapId
     * @param $userId
     * @return int
     */
    public function unBind($mapId, $userId){
        $this->db->trans_start();

        $this->db->delete('school_class_user_map',
            array('map_id' => $mapId, 'user_unique_id' => $userId));

        $this->db->trans_complete();
        $logContent = array(
                'map_id'            => $mapId,
                'user_unique_id'    => $userId,
            );
        if (!$this->db->trans_status()){
            $logContent['run_status'] = 0;
        } else {
            $logContent['run_status'] = 1;
        }
        $this->UserLogModels->addUserLog($userId, $logContent, self::$tableName, __METHOD__);
        return $logContent['run_status'];
    }

    /**
     * 解绑用户班级
     *
     * @param $userId      用户id
     * @param $userKnown   用户已经认识的id 列表,默认为空
     * @param $pageSize    一页记录数默认为 20
     * @param $page        第几页
     * @return array
     */
    public function getFriendRecordList($userId, $userKnown = array(), $pageSize = 20, $page = 0){
        $classIdList = array_column($this->getUserBindList($userId), 'class_unique_id');
        if(empty($classIdList)) {
            return array();
        }
        $offset = $pageSize * $page;
        array_push($userKnown, $userId);
        $this->db->select('user_unique_id', $offset, $pageSize);
        $this->db->where_not_in('user_unique_id', $userKnown);
        $this->db->where_in('class_unique_id', $classIdList);
        $res = $this->db->get(self::$tableName);
        return $res->result_array();
    }

}