<?php
/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/1/1
 * Time: 下午10:42
 */

class School extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('util/Validator');
        $this->load->library('util/Response');
    }

    public function getSchoolList(){
        $this->load->model("SchoolModels", 'school');
        $schoolList = $this->school->getSchoolList();
        $this->response->jsonSuccess(array(
            'school_list' => $schoolList,
        ));
    }
    public function getClassListBySchoolId($schoolId){
        $this->load->model("ClassModels", 'class');
        $classList = $this->class->geClassListBySchoolId($schoolId);
        $this->response->jsonSuccess(array(
            'class_list' => $classList,
        ));
    }
}
