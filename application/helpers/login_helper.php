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
    $user_id = $CI->session->user_id;

    if ($type == 'view'){
        $token = $CI->session->token;
    } else if ($type == 'api'){
        $token = $CI->input->post('token');
    } else {
        $token = '';
    }

    if(empty($user_id) || (!$CI->token->checkToken($user_id, $token)))
    {
        if ($type == 'view'){
            redirect(base_url() . "index/login");
        } else {
            $CI->response->jsonFail(Response::CODE_UNAUTHENTICATED);
        }

    }
}