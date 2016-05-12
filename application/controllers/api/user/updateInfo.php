<?php

/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/5/9
 * Time: 下午10:31
 */
class updateInfo extends CI_Controller{
    public function __construct(){
        parent::__construct();
    }
    public function updateAvatar(){
        if($_POST){
            $imgDecoded = $this->input->post('imgDecoded');
            $img = str_replace('','+', $imgDecoded);
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = base64_decode($img);
            $file = "";
            $f = fopen('/Users/bricks/Desktop/png/test.png', 'w+');
            fwrite($f,$img);
            fclose($f);
        }
    }
}