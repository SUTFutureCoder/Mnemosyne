<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-1
 * Time: 上午1:00
 */
class Token{
    protected $_ci;
    public function __construct(){
        $this->_ci = &get_instance();
    }

    public function getToken($userId, $passWd){
        $this->_ci->load->library('encryption');
        $encryptWord = $userId . '|' . md5(md5($passWd));
        return $this->_ci->encryption->encrypt($encryptWord);
    }
}