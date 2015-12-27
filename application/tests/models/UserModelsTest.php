<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-12-5
 * Time: 下午2:41
 */
class UserModelsTest extends PHPUnit_Framework_TestCase
{
    private static $ci;
    private static $model;
    private static $tableName = 'user';
    public static function setUpBeforeClass()
    {
        self::$ci =& get_instance();
        self::$ci->load->model('UserModels');
        self::$model = self::$ci->UserModels;
        self::$ci->load->database();
    }

    public function testAddUser(){
        $arrInput = array(
            'userName'      => 'test',
            'userPW'        => password_hash('passwd', PASSWORD_DEFAULT),
            'userMobile'    => '151' . substr(time(), 2),
            'userEmail'     =>  md5(time()) . '@aliyun.com',
        );
        $ret = self::$model->addUser($arrInput['userName'],
            $arrInput['userPW'],
            $arrInput['userMobile'],
            $arrInput['userEmail']);

        $this->assertEquals(1, $ret);
    }


    public function testGetUserBasicInfo(){
        $data = $this->getFirstUser();

        $ret = self::$model->getUserBasicInfo($data['user_id']);

        $this->assertArrayHasKey('user_id',     $ret);
        $this->assertArrayHasKey('user_name',   $ret);
        $this->assertArrayHasKey('user_birthday', $ret);
        $this->assertArrayHasKey('user_sex',    $ret);
        $this->assertArrayHasKey('user_password', $ret);
    }

    public function testUpdateUser(){
        $data = $this->getFirstUser();

        $arrInput = array(
            'user_id'       => $data['user_id'],
            'user_name'     => '*Chen',
            'user_birthday' => '1994-05-29',
            'user_sex'      => 1,
            'user_mobile'   => 15101669791,
            'user_email'    => 'linxingchen@iwaimai.baidu.com',
            'user_sign'     => 'f*ck',
            'user_status'   => 0,
        );
        $ret = self::$model->updateUser($arrInput['user_id'],
            $arrInput['user_name'],
            $arrInput['user_birthday'],
            $arrInput['user_sex'],
            $arrInput['user_mobile'],
            $arrInput['user_email'],
            $arrInput['user_sign'],
            $arrInput['user_status']
        );
        $this->assertEquals(1, $ret);

        $data = $this->getFirstUser();
        $this->assertArrayHasKey('user_id',     $data);
        $this->assertArrayHasKey('user_name',   $data);
        $this->assertArrayHasKey('user_birthday', $data);
        $this->assertArrayHasKey('user_sex',    $data);
        $this->assertArrayHasKey('user_password', $data);

        $this->assertEquals($arrInput['user_id'],   $data['user_id']);
        $this->assertEquals($arrInput['user_name'], $data['user_name']);
        $this->assertEquals($arrInput['user_birthday'], $data['user_birthday']);
        $this->assertEquals($arrInput['user_sex'],  $data['user_sex']);
        $this->assertEquals($arrInput['user_mobile'],   $data['user_mobile']);
        $this->assertEquals($arrInput['user_email'], $data['user_email']);
        $this->assertEquals($arrInput['user_sign'],     $data['user_sign']);
        $this->assertEquals($arrInput['user_status'],   $data['user_status']);
    }

    public function testEnableUser(){
        $data = $this->getFirstUser();
        if (empty($data['user_id'])){
            $this->testAddUser();
            $this->testUpdateUser();
        }
    }

    //可以被其他单测调用
    public function getFirstUser(){
        $result = self::$ci->db->get(self::$tableName);
        $data = $result->row_array();

        if (empty($data['user_id'])){
            $this->testAddUser();
            return $data;
        }
        return $data;
    }

    //重置密码
    public function testResetPassWd(){

    }

    //检测是否重复
    public function testCheckUserExists(){
        //主要通过手机号和邮箱进行验证
        $data = $this->getFirstUser();

        //传入手机和Email
        $arrInput = array(
            'user_mobile' => $data['user_mobile'],
            'user_email'  => $data['user_email'],
        );
        $ret = self::$model->checkUserExists($arrInput['user_mobile'], $arrInput['user_email']);
        $this->assertNotEquals(0, $ret);

        //传入手机和Email
        $arrInput = array(
            'user_mobile' => 15101669999,
            'user_email'  => 'linxingchen@baidu.com',
        );
        $ret = self::$model->checkUserExists($arrInput['user_mobile'], $arrInput['user_email']);
        $this->assertEquals(0, $ret);
    }

    public function testGetUserInfoByLoginName(){
        $data = $this->getFirstUser();
        $userMobile = $data['user_mobile'];
        $userEmail  = $data['user_email'];
        $retMobile  = self::$model->getUserInfoByLoginName($userMobile);
        $retEmail   = self::$model->getUserInfoByLoginName($userEmail);
        $retNull    = self::$model->getUserInfoByLoginName('test');
        $this->assertEquals($retMobile['user_id'], $retEmail['user_id']);
        $this->assertEquals(true, empty($retNull));
    }
}