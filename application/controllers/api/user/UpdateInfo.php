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

//    public function updateAvatar(){
//        checkLogin('api');
//        $userId   = $this->session->user_id;
//
//        if($_POST){
//            $imgDecoded = $this->input->post('imgDecoded');
//            $img = str_replace('','+', $imgDecoded);
//            $img = str_replace('data:image/png;base64,', '', $img);
//            $img = base64_decode($img);
//            $fileDir = getcwd() . "/" . "static/public/tmp/img/avatar" . "/" . $userId;
//            if(!file_exists($fileDir)){
//                mkdir($fileDir, 0777, true);
//            }
//            $file = $fileDir . "/avatar.png";
//            $f = fopen($file, 'w+');
//            fwrite($f,$img);
//            fclose($f);
//            $this->response->jsonSuccess();
//        }
//    }

    public function updateAvatar(){
        checkLogin('api');

        if($_POST){
            $imgDecoded = $this->input->post('imgDecoded');

            $this->load->library('util/BosClient');
            $this->load->library('BosOptions');
            $this->load->model('UserModels');
            $strTempMime = $this->parseImageMimeFromUploadImgBase64($imgDecoded);
            if (false === $strTempMime || false === strpos($strTempMime, '/')){
                Response::responseErrorJson(ErrorCodes::ERROR_UPLOAD_STRING_MIME_MISSING);
            }
            $options[BosOptions::CONTENT_TYPE] = $strTempMime;


            $img  = str_replace('','+', $imgDecoded);
            $data = str_replace('data:image/png;base64,', '', $img);

            $arrBosConfig = $this->config->item('bos_bucket_list');
            $arrBosConfig = $arrBosConfig['146044910610'];

            //用于接收bos返回数据,记录在数据库中
            $bosResult    = BosClient::putObjectFromString('146044910610', $arrBosConfig['secret_key'], $data, 'testPng', 1, $options);

            if ($bosResult['code'] != 0){
                Response::responseErrorJson(ErrorCodes::ERROR_UPLOAD_FILE_ERROR);
            }

            $this->UserModels->modifyAvatar($this->session->userdata('user_id'), $bosResult['data']['url']);
            $this->response->jsonSuccess($bosResult['data']['url']);
        }
    }


    private function parseImageMimeFromUploadImgBase64($strBase64){
        //mime应该能在上传头像后的base64开头获得
        $strBase64  = substr($strBase64, strpos($strBase64, ':') + 1);
        $strMime   = substr($strBase64, 0, strpos($strBase64, ';'));

        return $strMime;
    }


}



