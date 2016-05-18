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
        $this->load->library('util/Validator');
        $this->load->library('session');
        $this->load->model('UserModels');
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
            $strTempMime = $this->parseImageMimeFromUploadImgBase64($imgDecoded);
            if (false === $strTempMime || false === strpos($strTempMime, '/')){
                throw new MException(CoreConst::MODULE_ALUMNI, ErrorCodes::ERROR_UPLOAD_STRING_MIME_MISSING);
            }
            $options[BosOptions::CONTENT_TYPE] = $strTempMime;


            $img  = str_replace('','+', $imgDecoded);
            $data = str_replace('data:image/png;base64,', '', $img);

            $arrBosConfig = $this->config->item('bos_bucket_list');
            $arrBosConfig = $arrBosConfig['146044910610'];

            //用于接收bos返回数据,记录在数据库中
            $bosResult    = BosClient::putObjectFromString('146044910610', $arrBosConfig['secret_key'], $data, 'testPng', 1, $options);

            if ($bosResult['code'] != 0){
                throw new MException(CoreConst::MODULE_ALUMNI, ErrorCodes::ERROR_UPLOAD_FILE_ERROR);
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

    public function updateUserMobile(){
        checkLogin('api'); 
        if($_POST){
            $userMobile = $this->input->post('user_mobile');
            $userId = $this->session->user_id;
            if(!(Validator::isNotEmpty($userMobile,   '手机号不能为空') && 
                Validator::isMobile($userMobile, '请输入合法的手机号'))
                ){
                $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
            }
            if($this->UserModels->checkUserExists($userMobile, '0'))
            {
                $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '抱歉, 您的手机号已经被注册' . $userMobile);
            }
            $this->UserModels->updateUser($userId, false, false, false, $userMobile);
            $this->response->jsonSuccess();
        }
    }
    
    public function updateUserEmail(){
        checkLogin('api'); 
        if($_POST){
            $userEmail = $this->input->post('user_email');
            $userId = $this->session->user_id;
            if(!(Validator::isNotEmpty($userEmail,   '邮箱不能为空') && 
                Validator::isEmail($userEmail, '请输入合法的邮箱地址'))
                ){
                $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
            }
            if($this->UserModels->checkUserExists(false, $userEmail))
            {
                $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '抱歉, 您的邮箱已经被注册');
            }
            $this->UserModels->updateUser($userId, false, false, false, false, $userEmail);
            $this->response->jsonSuccess();
        }
    }
    public function updateUserPassword(){
        checkLogin('api');
        if($_POST){
            $userPassword = $this->input->post('user_password');
            $userPasswordNew1 = $this->input->post('user_password_new1');
            $userPasswordNew2 = $this->input->post('user_password_new2');
            $userId   = $this->session->user_id;
            if(!(Validator::isNotEmpty($userPassword,   '原密码输入不能为空') && 
                Validator::isNotEmpty($userPasswordNew1, '新密码不能为空') && 
                Validator::isNotEmpty($userPasswordNew2,   '确认新密码不能为空'))
                ){
                $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage()); 
            }
            $userInfo = $this->UserModels->getUserBasicInfo($userId);
            if(!password_verify($userPassword, $userInfo['user_password'])){
                Validator::setMessage('原密码错误');
                $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
            }
            if($userPasswordNew1 != $userPasswordNew2){
                Validator::setMessage('新密码两次输入不同');
                $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
            }
            $this->UserModels->updatePassword($userId, password_hash($userPasswordNew1, PASSWORD_DEFAULT)); 
            $this->response->jsonSuccess();
        }
    }


}



