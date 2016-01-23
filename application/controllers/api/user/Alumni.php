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
        $this->load->library('CoreConst');
    }

    public function updateAlumni(){
        $this->load->model("AlumniModels", 'alumni');
        $this->load->model("SchoolClassUserMapModels", 'scu');
        $this->load->model("AlumniPageModels", "alumniPage");
        $this->load->model("MessageModels", 'message');

        $userId   = $this->session->user_id;
        $userName = $this->session->user_name;
        if (!(Validator::isNotEmpty($userId,      '您已经下线请重新登录')
        )){
            $this->response->jsonFail(Response::CODE_UNAUTHENTICATED, Validator::getMessage());
        }

        $alumniId     = trim($this->input->post('alumni_id',    true));
        $title        = trim($this->input->post('title',        true));
        $cover        = trim($this->input->post('cover',        true));
        $send_to      = trim($this->input->post('send_to',      true));
        $send_to_plus = trim($this->input->post('send_to_plus', true));


        if(!(Validator::isNotEmpty($title,                            "您的标题不能为空")
            && Validator::isNotEmpty($alumniId,       "你的alumniId为空,目测也是hack行为")
            && Validator::mbStringRange($title, 0, 32,             "您的标题不能超过32位")
            && Validator::isNotEmpty($cover,                           "您的封面不能为空")
            && Validator::Range($cover, 0, 999, "骚年这是hack行为, 一个选框出现莫名其妙的值")
            && Validator::isNotEmpty($send_to,                  "请选择您要发送的人的范围")
        )){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
        }

        if($alumniId === '-1') {
            $addStatus = $this->alumni->addAlumni($userId, $title, $cover);
            if($addStatus !== false)
            {
                $alumniId   = $addStatus;
                $userIdList = $this->scu->getClassmate($userId);
//                $this->response->jsonFail(Response::CODE_SERVER_ERROR, print_r($userIdList, true));
                foreach($userIdList as $userIdtmp){
                    $addAlumniStatus   =  $this->alumniPage->addAlumniPage($alumniId, $userId, $userIdtmp['user_id']);
                    $addMessageStatus  = $this->message->addMessage(    $userId, $userIdtmp['user_id'],
                                                                        $type    = CoreConst::AlUMNI_FILL_IN_MES,
                                                                        $title   = '填写同学录',
                                                                        $message = $userName . ' 邀请您填写同学录'
                                                                    );
                    if(!$addAlumniStatus || !$addMessageStatus){
                        $this->response->jsonFail(Response::CODE_SERVER_ERROR, '抱歉，添加同学录失败');
                    }
                }

            }

        }
        $this->response->jsonSuccess();

    }

}
