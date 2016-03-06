<?php
/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 15/12/28
 * Time: 上午1:17
 */

/**
 * 检查是否登录
 *
 * @param string $type view - 浏览   api - 调用api
 */
function checkLogin($type = 'view'){
    $CI       =& get_instance();
    $CI->load->library("session");
    $CI->load->library("Token");
    $CI->load->library('Response');

    if ($type == 'view' || $type == 'api'){
        $user_id = $CI->session->user_id;
        $token   = isset($_COOKIE[CoreConst::TOKEN_COOKIES]) ? $_COOKIE[CoreConst::TOKEN_COOKIES] : '';
        $signature = isset($_COOKIE[CoreConst::TOKEN_SIGNATURE_COOKIES]) ? $_COOKIE[CoreConst::TOKEN_SIGNATURE_COOKIES] : '';
    } else{
//        if ($type == 'api'){
//        $user_id = !empty($CI->input->post('user_id')) ? $CI->input->post('user_id') : $CI->input->get('token');
//        $token   = !empty($CI->input->post('token')) ? $CI->input->post('token') : $CI->input->get('token');
//        $signature = !empty($CI->input->post('signature')) ? $CI->input->post('signature') : $CI->input->get('signature');
//    } else {
        $token = '';
    }

    if(empty($user_id) || empty($token) || empty($signature) || !$CI->token->checkToken($user_id, $token, $signature))
    {
        if ($type == 'view'){
            redirect(base_url() . "index/login");
        } else {
            $CI->response->jsonFail(Response::CODE_UNAUTHENTICATED);
        }

    }

    //从redis token中获取需要的信息
    $arrData = $CI->token->getUserDataByToken($token, $signature, $user_id);
    if (empty($arrData)){
        $CI->response->jsonFail(Response::CODE_UNAUTHENTICATED);
    }

    CoreConst::$userId       = $user_id;
    CoreConst::$userPlatform = $arrData['platform'];
}