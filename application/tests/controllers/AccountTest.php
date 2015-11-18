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
        $this->requireController('Welcome');
        $this->controller = new Welcome();
    }

    public function testTestUnitTest(){
        $param = array(
            'test' => '123123123',
        );
        $this->setParam($param);
        $this->assertEquals('hello world-123123123', $this->getApi($this->controller, 'testUnitTest'));
    }
}