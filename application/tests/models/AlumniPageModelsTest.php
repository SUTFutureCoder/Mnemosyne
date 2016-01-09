<?php

/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/1/9
 * Time: 下午4:59
 */
class AlumniPageModelsTest extends PHPUnit_Framework_TestCase {
    private static $ci;
    private static $model;
    private static $alumniObj;
    private static $userObj;
    private static $tableName = 'alumni_page';
    public static function setUpBeforeClass(){
        self::$ci =& get_instance();
        self::$ci->load->model('AlumniPageModels');
        self::$model = self::$ci->AlumniPageModels;
        require_once('AlumniModelsTest.php');
        require_once('UserModelsTest.php');
        self::$alumniObj = new AlumniModelsTest();
        self::$alumniObj->setUpBeforeClass();
        self::$alumniObj = self::$alumniObj->getFirstAlumni();
        self::$userObj = new UserModelsTest();
        self::$ci->load->database();
    }

    public function testAddAlumniPage(){
        $alumni = self::$alumniObj;
        $secondUser = self::$userObj->getSecondUser();
        $alumniPage = array(
            'alumni_id' => $alumni['id'],
            'user_id'   => $alumni['user_id'],
            'to_user'   => $secondUser['user_id'],
            'info'      => "单测添加"
        );
        $ret = self::$model->addAlumniPage($alumniPage['alumni_id'], $alumniPage['user_id'], $alumniPage['to_user'],
            $backgroundStyle = 0, $info = $alumniPage['info']);
        $this->assertEquals(1, $ret);
    }

}