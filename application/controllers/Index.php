<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-1
 * Time: 上午1:14
 */
class Index extends CI_Controller{
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        return 'test';
    }
}