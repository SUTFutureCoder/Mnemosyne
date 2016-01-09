<?php

/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/1/9
 * Time: 下午1:55
 */
class Alumni extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->library('Validator');
        $this->load->library("session");
        $this->load->library('Response');
    }

    public function updateAlumni(){
        $this->load->modela("AlumniModels", 'alumni');

        $userId = $this->session->user_id;
        if (!(Validator::isNotEmpty($userId,      '您已经下线请重新登录')
        )){
            $this->response->jsonFail(Response::CODE_UNAUTHENTICATED, Validator::getMessage());
        }

        $alumniId     = trim($this->input->post('alumni_id',    true));
        $title        = trim($this->input->post('title',        true));
        $cover        = trim($this->input->post('cover',        true));
        $send_to      = trim($this->input->post('send_to',      true));
        $send_to_plus = trim($this->input->post('send_to_plus', true));


        if(!(Validator::isNotEmpty($title, "您的标题不能为空")
            && Validator::isNotEmpty($alumniId, "你的alumniId为空,目测也是hack行为")
            && Validator::mbStringRange($title, 0, 32, "您的标题不能超过32位")
            && Validator::isNotEmpty($cover, "您的封面不能为空")
            && Validator::Range($cover, 0, 999, "骚年这是hack行为, 一个选框出现莫名其妙的值")
            && Validator::isNotEmpty($send_to, "请选择您要发送的人的范围")
        )){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }

        if($alumniId == '-1') {
            $addStatus = $this->alumni->addAlumni($userId, $title, $cover);
            if(!$addStatus)
            {
                $this->response->jsonFail(Response::CODE_SERVER_ERROR, '抱歉,添加同学录失败');
            }
        }
        $this->response->jsonSuccess();

    }

}