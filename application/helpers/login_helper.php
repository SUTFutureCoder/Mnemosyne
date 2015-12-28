<?php
/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 15/12/28
 * Time: 上午1:17
 */

function checkLogin(){
    $CI       =& get_instance();
    $CI->load->library("session");
    $CI->load->library("Token");
    $user_id = $CI->session->user_id;
    $token = $CI->session->token;
    if(empty($user_id) || (!$CI->token->checkToken($user_id, $token)))
    {
        redirect(base_url() . "index/login");
    }
}