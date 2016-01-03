<?php
/**
 * Api for user identity check
 *
 *
 * @author  *Chen <linxingchen@baidu.com>
 */
class Passport extends CI_Controller{
    public function __construct(){
        parent::__construct();
    }

    public function testToken(){
        $this->load->library('Token');
        $token = $this->token->getToken('123456789', 'abc123');
        echo $token;
    }



}