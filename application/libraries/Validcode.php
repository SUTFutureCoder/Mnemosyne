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

    private $gtsdk;

    public function __construct(){
        $this->_ci =& get_instance();
        $this->_ci->load->helper('url');
        require_once APPPATH . '../gt-php-sdk/lib/class.geetestlib.php';
        $this->gtsdk = new GeetestLib();
    }

    public function checkValidCodeAccess($type = 0){
        if (!$type){
            //使用第三方极限验证进行确认是否已经通过验证,需要使用原生session实现
            session_start();
            if (empty($_SESSION['gtserver'])){
                return false;
            }

            if ($_SESSION['gtserver'] == 1) {
                unset($_SESSION['gtserver']);
                $result = $this->gtsdk->validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode']);
                if ($result == TRUE) {
                    return true;
                } else if ($result == FALSE) {
                    return false;
                } else {
                    return false;
                }
            }else{
                if ($this->gtsdk->get_answer($_POST['geetest_validate'])) {
                    return true;
                }else{
                    return false;
                }
            }
        }
    }
}