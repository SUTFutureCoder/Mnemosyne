<?php

/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/4/14
 * Time: 下午3:27
 */
class ImageTest extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->helper('html');
        $this->load->library('template');
    }

    public function jqTest(){
        $this->template->display("test/jq_image_upload_demo.html");
    }
    public function test(){
        $this->template->display("test/image_test.html");
    }

    public function tt(){
        $this->template->display("test/test.html");
    }

    public function uploadImage(){
        if($_POST){
            $imgDecoded = $this->input->post('imgDecoded');
            $img = str_replace('','+', $imgDecoded);
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = base64_decode($img);
            $f = fopen('/Users/bricks/Desktop/png/test.png', 'w+');
            fwrite($f,$img);
            fclose($f);
        }
    }

}