<?php
/**
 * 秀恩爱专场[DOGE]
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-6-11
 * Time: 下午9:01
 */
class Showlove extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('util/Validator');
        $this->load->library('util/Response');
    }

    public function kokuhaku(){

    }

    /**
     * 获取告白对象最小信息 [函数名恶意]
     *
     * 注意验证
     * 1-是否已经是情侣
     * 2-用户有其他情侣关系
     * 3-用户是否是和对象是好友关系
     *
     *
     * 1&3可合并，已是好友关系则无需担心不存在的情况
     *
     */
    public function getObjectInfo(){
        $this->load->model('UserModels');

        //确认用户搜索方法
        if ('search' == trim($this->input->post('type', true))){
            //使用登录名搜索
            $loginName  =  trim($this->input->post('searchLoginname', true));
            if (!(Validator::isNotEmpty($loginName,   '表白对象手机或邮箱不能为空')
                && (Validator::isEmail($loginName, '请输入合法的邮箱地址或手机号')
                    || Validator::isMobile($loginName, '请输入合法的邮箱地址或手机号')))){
                $this->response->jsonFail(Response::CODE_PARAMS_WRONG, Validator::getMessage());
            }
            $userInfo = $this->UserModels->getUserInfoByLoginName($loginName);
        } else if ('select' == trim($this->input->post('type', true))){
            $userInfo = $this->UserModels->getUserBasicInfo($this->input->post('userId', true), array('user_id', 'user_name', 'user_nickname'));
        } else {
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '选择表白对象方法有误');
        }


        if (empty($userInfo)){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '表白对象用户不存在');
        }
        $toUserId = $userInfo['user_id'];

        $this->load->model('UserRelationModels');
        $arrRelationData = $this->UserRelationModels->getRelation($this->session->user_id, $toUserId);
        if (empty($arrRelationData) || !in_array($arrRelationData['type'], array(CoreConst::USER_RELATION_TYPE_FRIEND, CoreConst::USER_RELATION_TYPE_AMBIGUOUS))){
            $this->response->jsonFail(Response::CODE_PARAMS_WRONG, '表白对象必须是好友或暧昧关系');
        }

        //返回表白用户信息
        $this->response->jsonSuccess(array(
            'user_id'   => $userInfo['user_id'],
            'user_name' => $userInfo['user_name'],
            'from_user_name'     => $this->session->user_name,
            'from_user_nickname' => $this->session->user_nickname,
            'to_user_nickname'   => $userInfo['user_nickname'],
        ));
    }

}