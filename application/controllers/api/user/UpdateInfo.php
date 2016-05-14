<?php

/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/5/9
 * Time: 下午10:31
 */
class UpdateInfo extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->helper('login_helper');
        $this->load->library('util/Response');
        $this->load->library('session');
    }

    public function updateAvatar(){
        checkLogin('api');
        $userId   = $this->session->user_id;

        if($_POST){
            $imgDecoded = $this->input->post('imgDecoded');
            $img = str_replace('','+', $imgDecoded);
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = base64_decode($img);
            $fileDir = getcwd() . "/" . "static/public/tmp/img/avatar" . "/" . $userId;
            if(!file_exists($fileDir)){
                mkdir($fileDir, 0777, true);
            }
            $file = $fileDir . "/avatar.png";
            $f = fopen($file, 'w+');
            fwrite($f,$img);
            fclose($f);
            $this->response->jsonSuccess();
        }
    }
}
