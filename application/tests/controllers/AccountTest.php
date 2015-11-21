<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-21
 * Time: 下午2:37
 */
require APPPATH . '../application/tests/CITestCase.php';
class AccountTest extends CITestCase{
    private $controller;

    public function __construct(){
        $this->requireController('Account');
        $this->controller = new Account();
    }

    public function testTestUnitTest(){
        $_POST['']
        $this->assertEquals('hello world', $this->controller->testUnitTest());
    }
}