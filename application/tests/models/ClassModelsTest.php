<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-21
 * Time: 下午2:40
 */
class ClassModelsTest extends PHPUnit_Framework_TestCase
{
    private static $ci;
    private static $model;
    public static function setUpBeforeClass()
    {
        self::$ci =& get_instance();
        self::$ci->load->model('ClassModels');
        self::$model = self::$ci->ClassModels;
    }

    public function testGetClassNameById(){
        $this->assertEquals('机自1006班',       self::$model->getClassNameById('1001016'));
        $this->assertEquals('软件工程1503班',   self::$model->getClassNameById('1512033'));
        $this->assertEquals('会计学1301班',     self::$model->getClassNameById('1305041'));
        $this->assertFalse(self::$model->getClassNameById('123'));
        $this->assertFalse(self::$model->getClassNameById('$@#@'));
    }

    public function testGetClassIdByName(){
        $this->assertEquals('1305041', self::$model->getClassIdByName('会计学1301班'));
        $this->assertEquals('1001016', self::$model->getClassIdByName('机自1006班'));
        $this->assertFalse(self::$model->getClassIdByName('@$#$#$'));
    }
}