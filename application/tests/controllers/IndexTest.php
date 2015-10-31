<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-1
 * Time: 上午1:12
 */
require BASEPATH . '../application/tests/CITestCase.php';
class IndexTest extends CITestCase
{
    private $indexController;

    public function __construct(){
        $this->requireController('Index');
        $this->indexController = new Index();
    }

    public function testIndex()
    {

        $this->assertEquals('test', $this->indexController->index());
    }
}
