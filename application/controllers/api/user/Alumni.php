<?php

/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/1/9
 * Time: 下午1:55
 */
class Alumni extends CI_Controller{

    private static $ALumniConst = array(
        "alumni_send_to_classmate" => '0',
        "alumni_send_to_friends" => '1'
    );

    public function __construct(){
        parent::__construct();
        $this->load->helper('login_helper');
        $this->load->library('util/Validator');
        $this->load->library("session");
        $this->load->library('util/Response');
        $this->load->model("UserAlumniModels", 'uam');
    }

    public function updateAlumni(){
        $this->load->model("AlumniModels", 'alumni');
        $this->load->model("SchoolClassUserMapModels", 'scu');
        $this->load->model("AlumniPageModels", "alumniPage");
        $this->load->model("MessageModels", 'message');
        $this->load->model("UserRelationModels", "UserRelation");

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
            $addRes = $this->alumni->addAlumni($userId, $title, $cover);
            $addStatus = $addRes['run_status'];
            if($addStatus !== false)
            {
                $alumniId   = $addRes['id'];
                $userIdList = array();
                if($send_to  === self::$ALumniConst['alumni_send_to_friends']){
                    $userIdList = $this->UserRelation->getUserFriendIdList($userId);
                }else if($send_to  === self::$ALumniConst['alumni_send_to_classmate']){
                    $userIdList = $this->scu->getClassmate($userId);
                }
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
    
    public function updateUserAlumni(){
        $userId = $this->session->user_id;
        $userAlumniExists = $this->uam->checkUserAlumniInfoExists($userId);
        $updateArr['user_favorite_food'] = trim($this->input->post("user_favorite_food"));
        $updateArr['user_bloodgroup'] = trim($this->input->post("user_bloodgroup"));
        $updateArr['user_favorite_animal'] = trim($this->input->post("user_favorite_animal"));
        $updateArr['user_worship_people'] = trim($this->input->post("user_worship_people"));
        $updateArr['user_want_to_go'] = trim($this->input->post("user_want_to_go"));
        $updateArr['user_desire'] = trim($this->input->post("user_desire"));
        $updateArr['user_favorite_star'] = trim($this->input->post("user_favorite_star"));
        $updateArr['user_favorite_color'] = trim($this->input->post("user_favorite_color"));

        if($userAlumniExists){
            $this->uam->updateAlumni($userId, $updateArr);             
        }else{
            $this->uam->addUserAlumni($userId, $updateArr);
        }
        $this->response->jsonSuccess();

    }

    public function getAlumni(){
        checkLogin('api');
        $this->load->model("AlumniModels");
        $userId = $this->session->user_id;
        $userAlumni = $this->AlumniModels->getAlumniByUserId($userId);
        $this->response->jsonSuccess($userAlumni);
    }
    public function getUserAlumniInfo(){
        checkLogin('api');
        $userId = $this->session->user_id;
        $userAlumniInfo = $this->uam->getUserAlumniInfo($userId);
        $this->response->jsonSuccess($userAlumniInfo);
    }

    public function fillInAlumniPage(){
        checkLogin('api');
        $userId = $this->session->user_id;
        $this->load->model("AlumniPageModels", "alumniPage");
        //$this->alumniPage->updateAlumniPage($userId, 1, array('status' => 1));
    }

    public function getAlumniNeedToFillIn(){
        checkLogin('api');
        $userId = $this->session->user_id;
        $this->load->model("AlumniPageModels", "alumniPage");
        $pageNum = $this->input->get('page_num');
        $pageSize = $this->input->get('page_size');
        $getUserInfoList = $this->alumniPage->getSendToUserInfoJoinUser($userId, $pageSize, $pageNum);
        $totalNum = $this->alumniPage->getResultCount(array('to_user' => $userId));
        $this->response->jsonSuccess(array(
            "userInfoList" => $getUserInfoList,
            "totalNum" => $totalNum
        ));
    }

}
