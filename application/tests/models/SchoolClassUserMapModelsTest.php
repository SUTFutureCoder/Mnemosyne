<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-12-9
 * Time: 下午7:14
 */
class SchoolClassUserMapModelsTest extends PHPUnit_Framework_TestCase{
    private static $ci;
    private static $model;
    private static $userObj = null;
    private static $userId  = 0;
    public static function setUpBeforeClass()
    {
        self::$ci =& get_instance();
        self::$ci->load->model('SchoolClassUserMapModels');
        self::$model = self::$ci->SchoolClassUserMapModels;

        //使用用户表单测
        require_once ('UserModelsTest.php');
        self::$userObj = new UserModelsTest();
        self::$userObj->setUpBeforeClass();
        self::$userId  = self::$userObj->getFirstUser()['user_id'];
    }

    public function testBind(){
        $arrInput = array(
            'school_unique_id' => 1,
            'class_unique_id'  => 291,
            'user_unique_id'   => self::$userId,
        );
        $ret = self::$model->bind($arrInput['school_unique_id'], $arrInput['class_unique_id'], $arrInput['user_unique_id']);
        $this->assertEquals(1, $ret);


        $arrInput = array(
            'school_unique_id' => 1,
            'class_unique_id'  => 291,
            'user_unique_id'   => self::$userId,
            'student_id'=> '120406305X',
        );
        $ret = self::$model->bind($arrInput['school_unique_id'], $arrInput['class_unique_id'], $arrInput['user_unique_id'], $arrInput['student_id']);
        $this->assertEquals(1, $ret);
    }

    //检查是否已经存在绑定
    public function testCheckBindExists(){
        $arrInput = array(
            'school_unique_id' => 1,
            'class_unique_id'  => 291,
            'user_unique_id'   => self::$userId,
        );
        $ret = self::$model->checkBindExists($arrInput['school_unique_id'], $arrInput['class_unique_id'], $arrInput['user_unique_id']);
        $this->assertNotEquals(0, $ret);

        $arrInput = array(
            'school_unique_id' => 1,
            'class_unique_id'  => 2,
            'user_unique_id'   => self::$userId,
        );
        $ret = self::$model->checkBindExists($arrInput['school_unique_id'], $arrInput['class_unique_id'], $arrInput['user_unique_id']);
        $this->assertEquals(0, $ret);
    }

    public function testGetUserBindList(){
        $arrInput = array(
            'user_unique_id' => self::$userId,
        );
        $ret = self::$model->getUserBindList($arrInput['user_unique_id']);
        print_r($ret);
    }

    public function testUnBind(){
        $arrInput = array(
            'map_id'    => 6,
        );
        $ret = self::$model->unBind($arrInput['map_id']);
        print_r($ret);
    }
}