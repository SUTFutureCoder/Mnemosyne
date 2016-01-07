<?php

/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/1/7
 * Time: 下午11:30
 */
class AlumniModelsTest  extends PHPUnit_Framework_TestCase {
    private static $ci;
    private static $model;
    private static $userObj;
    private static $userId;
    private static $tableName = 'alumni';
    public static function setUpBeforeClass(){
        self::$ci =& get_instance();
        self::$ci->load->model('AlumniModels');
        self::$model = self::$ci->AlumniModels;
        require_once('UserModelsTest.php');
        self::$userObj = new UserModelsTest();
        self::$userObj->setUpBeforeClass();
        self::$userId = self::$userObj->getFirstUser()['user_id'];
        self::$ci->load->database();
    }

    public function testAddAlumni(){

        $alumni = array(
            'userId' => self::$userId,
            'title' => '同学录',
            'cover' =>  0,
        );
        $ret = self::$model->addAlumni($alumni['userId'], $alumni['title'], $alumni['cover']);
        $this->assertEquals(1, $ret);
    }

    public function testupdateAlumni(){
        sleep(10);
        $alumni = $this->getFirstAlumni();
        $alumniUpdate = array(
            'alumniId' => $alumni['id'],
            'userId'   => $alumni['user_id'],
            'title'    => "板砖",
            'cover'    => 0,
        );
        $ret = self::$model->updateAlumni($alumniUpdate['alumniId'],
            $alumniUpdate['userId'], $alumniUpdate['title'], $alumniUpdate['cover']);

        $this->assertEquals(1, $ret);

    }

    public function getFirstAlumni(){
        $result = self::$ci->db->get(self::$tableName);
        $data = $result->row_array();

        if (empty($data['id'])){
            $this->testAddAlumni();
            return $data;
        }
        return $data;
    }

}