<?php
/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 16/1/2
 * Time: 下午3:53
 */

class User extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->library('Validator');
        $this->load->library('Response');
    }

    /*
     *API 获取用户推荐朋友
     *
     * @param int $userId
     *
     * $return array
     */
    public function friendRecommend($userId){
        $this->load->model('SchoolClassUserMapModels', 'scum');
        $this->load->model('UserModels', 'user');
        $recommendIdList =  $this->scum->getFriendRecordList($userId);
        $recommendIdList = array_column($recommendIdList, 'user_unique_id');
        $recommendInfoList = $this->user->getUserBasicInfoList($recommendIdList);
        $this->response->jsonSuccess(array(
            'recommendInfoList' => $recommendInfoList,
        ));
    }

}
