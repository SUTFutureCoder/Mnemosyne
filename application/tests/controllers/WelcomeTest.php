<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-1
 * Time: 上午11:43
 */
require APPPATH . '../application/tests/CITestCase.php';
class WelcomeTest extends CITestCase{
    private $controller;

    public function __construct(){
        $this->requireController('Welcome');
        $this->controller = new Welcome();
    }

    public function testTestUnitTest(){
        $this->assertEquals('hello world', $this->controller->testUnitTest());
    }
}