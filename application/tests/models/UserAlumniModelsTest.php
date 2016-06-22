<?php

/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/5/19
 * Time: 下午4:36
 */
class UserAlumniModelsTest extends PHPUnit_Framework_TestCase
{
    private static $ci;
    private static $model;
    private static $userObj;
    private static $userId;
    private static $tableName = 'user_alumni';

    public static function setUpBeforeClass()
    {
        self::$ci =& get_instance();
        self::$ci->load->model('UserAlumniModels');
        self::$model = self::$ci->userAlumniModels;
        self::$userObj = new UserModelsTest();
        self::$userObj->setUpBeforeClass();
        self::$userId = self::$userObj->getFirstUser()['user_id'];
        self::$ci->load->database();
    }

    public function testAddUserAlumni(){
        $this->assertEquals(1, 1);
    }
}

