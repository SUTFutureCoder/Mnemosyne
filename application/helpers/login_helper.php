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
    var_dump($CI->session->userdata);
    $token    =  $CI->session->token;
    die();
    if(empty($user_id))
    {
        redirect("/index/login");
    }
}