<?php

/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/1/10
 * Time: 下午2:53
 */
class MessageModelsTest extends PHPUnit_Framework_TestCase {
    private static $ci;
    private static $model;
    private static $userObj;
    private static $userFirst;
    private static $userSecond;
    public static function setUpBeforeClass(){
        self::$ci = & get_instance();
        self::$ci->load->model('MessageModels');
        self::$model = self::$ci->MessageModels;
        require_once('UserModelsTest.php');
        self::$userObj = new UserModelsTest();
        self::$userObj->setUpBeforeClass();
        self::$userFirst = self::$userObj->getFirstUser();
        self::$userSecond = self::$userObj->getSecondUser();
    }
    public function testaddMessage(){
        $message = array(
            'user_id' =>  self::$userFirst['user_id'],
            'to_user' =>  self::$userSecond['user_id'],
            'type'    =>  0,
            'title'   => '测试添加信息',
            'message' => '测试添加信息',
            'describe'=> '测试',
        );
        $ret = self::$model->addMessage($message['user_id'], $message['to_user'], $message['type'],
            $message['title'], $message['message'], $message['describe']);
        $this->assertEquals(1, $ret);
    }
}