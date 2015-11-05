<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-1
 * Time: 下午11:19
 */
//用于使用第三方库或验证用户是否通过了验证码审查
class Validcode{
    private $_ci;

    public function __construct(){
        $this->_ci =& get_instance();
    }

    public function checkValidCodeAccess($type = 0){
        if (!$type){
            //使用第三方极限验证进行确认是否已经通过验证,需要使用原生session实现
            session_start();
            if ($_SESSION['gtMnemosyneValid'] === 1){
                unset($_SESSION['gtMnemosyneValid']);
                return true;
            }
        }
    }
}