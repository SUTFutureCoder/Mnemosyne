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

            $this->load->library('util/BosClient');
            $this->load->library('BosOptions');
            $strTempMime = $this->parseImageMimeFromUploadImgBase64($imgDecoded);
            if (false === $strTempMime || false === strpos($strTempMime, '/')){
                Response::responseErrorJson(ErrorCodes::ERROR_UPLOAD_STRING_MIME_MISSING);
            }
            $options[BosOptions::CONTENT_TYPE] = $strTempMime;


            $img  = str_replace('','+', $imgDecoded);
            $data = str_replace('data:image/png;base64,', '', $img);

            $arrBosConfig = $this->config->item('bos_bucket_list');
            $arrBosConfig = $arrBosConfig['146044910610'];
            
            //用于接收bos返回数据
            $bosResult    = BosClient::putObjectFromString('146044910610', $arrBosConfig['secret_key'], $data, 'testPng', 1, $options);
        }
    }


    public function testSchoolInfo(){
        $this->template->display("test/testSchoolInfo.html");
    }
    private function parseImageMimeFromUploadImgBase64($strBase64){
        //mime应该能在标准base64开头获得
        $strBase64  = substr($strBase64, strpos($strBase64, ':') + 1);
        $strMime   = substr($strBase64, 0, strpos($strBase64, ';'));

        return $strMime;
    }

}
