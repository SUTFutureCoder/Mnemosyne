<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-21
 * Time: 下午3:04
 */
class SchoolModelsTest extends PHPUnit_Framework_TestCase
{
    private static $ci;
    private static $model;
    public static function setUpBeforeClass()
    {
        self::$ci =& get_instance();
        self::$ci->load->model('SchoolModels');
        self::$model = self::$ci->SchoolModels;
    }

    public function testGetSchoolIdByName(){
        $this->assertEquals('1', self::$model->getSchoolIdByName('沈阳工业大学'));
        $this->assertFalse(self::$model->getSchoolIdByName('123'));
        $this->assertFalse(self::$model->getSchoolIdByName('$@#@'));
    }

}