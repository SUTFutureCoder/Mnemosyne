<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-1
 * Time: 下午12:00
 */
class Token{
    private $_ci;

    public function __construct(){
        $this->_ci =& get_instance();
    }

    public function getToken($userId, $userPwd){
        $this->_ci->load->library('encryption');
        $token = $userId . '|' . substr(md5(md5($userPwd)), 6);
        echo $this->_ci->encryption->encrypt(md5(md5($userPwd))) . '<br/>';
        echo $token . '<br/>';
        $final = $this->_ci->encryption->encrypt($token);
        echo $final . '<br/>';
        echo $this->_ci->encryption->decrypt($final);
    }
}