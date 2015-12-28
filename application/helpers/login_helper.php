<?php
/**
 * Created by PhpStorm.
 * User: bricks
 * Date: 15/12/28
 * Time: 上午1:17
 */


function checkLogin(){
    $CI       =  & get_instance();
    $CI->load->library("session");
    $user_id  =  $CI->session->user_id;
    $token    =  $CI->session->token;
    var_dump($user_id);
    die();
    if(empty($user_id))
    {
        redirect("/index/login");
    }
}