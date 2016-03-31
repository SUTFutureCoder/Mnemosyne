<?php
/**
 * 包装过的email发送类
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-2-27
 * Time: 下午3:51
 */
class MEmail{

    private $_ci;

    private static $_config = array();

    public function __construct(){
        $this->_ci =& get_instance();

        //检查email配置文件
        $this->checkEmailConfig();
        $this->_ci->load->library('util/Validator');

        //加载配置文件
        $this->_ci->load->library('email', self::$_config);
    }

    private function checkEmailConfig(){

        if (empty(self::$_config['protocol']  = $this->_ci->config->item('email_protocol'))){
            MLog::fatal(CoreConst::MODULE_EMAIL, 'email_protocol config is missing');
            return false;
        }

        if (empty(self::$_config['smtp_host'] = $this->_ci->config->item('email_smtp_host'))){
            MLog::fatal(CoreConst::MODULE_EMAIL, 'email_smtp_host config is missing');
            return false;
        }

        if (empty(self::$_config['smtp_user'] = $this->_ci->config->item('email_smtp_user'))){
            MLog::fatal(CoreConst::MODULE_EMAIL, 'email_smtp_host config is missing');
            return false;
        }

        if (empty(self::$_config['smtp_pass'] = $this->_ci->config->item('email_smtp_pass'))){
            MLog::fatal(CoreConst::MODULE_EMAIL, 'email_smtp_pass config is missing');
            return false;
        }

        if (empty(self::$_config['smtp_post'] = $this->_ci->config->item('email_smtp_post'))){
            MLog::fatal(CoreConst::MODULE_EMAIL, 'email_smtp_post config is missing');
            return false;
        }

        if (empty(self::$_config['charset'] = $this->_ci->config->item('email_charset'))){
            MLog::fatal(CoreConst::MODULE_EMAIL, 'email_charset config is missing');
            return false;
        }

        if (empty(self::$_config['mailtype'] = $this->_ci->config->item('email_mailtype'))){
            MLog::fatal(CoreConst::MODULE_EMAIL, 'email_mailtype config is missing');
            return false;
        }

        if (empty(self::$_config['wordwrap'] = $this->_ci->config->item('email_wordwrap'))){
            MLog::fatal(CoreConst::MODULE_EMAIL, 'email_wordwrap config is missing');
            return false;
        }
    }

    /**
     * @param $strFrom 发件地址
     * @param $strSenderName 发件人
     * @param $strTo   收件人
     * @param $strSubject  邮件标题
     * @param $strMessage  邮件内容
     * @param null $arrAttach 附件文件地址列表
     * @param null $strCC  抄送
     * @param null $strBCC 密送
     * @return bool
     */
    public function send($strFrom, $strSenderName, $strTo, $strSubject, $strMessage, $arrAttach = null, $strCC = null, $strBCC = null){

        if (!Validator::isEmail($strFrom)){
            MLog::fatal(CoreConst::MODULE_EMAIL, 'from email address is not valid');
            return false;
        }


        if (!Validator::isNotEmpty($strSenderName) || !Validator::isString($strSenderName)){
            MLog::fatal(CoreConst::MODULE_EMAIL, 'sender name is not string or null');
            return false;
        }

        if (!Validator::isNotEmpty($strSubject) || !Validator::isString($strSubject)){
            MLog::fatal(CoreConst::MODULE_EMAIL, '不写标题你是要请鸡翅吗?');
            return false;
        }


        if (!Validator::isNotEmpty($strMessage) || !Validator::isString($strMessage)){
            MLog::fatal(CoreConst::MODULE_EMAIL, 'email content is empty');
            return false;
        }


        $this->_ci->email->from($strFrom, $strSenderName);
        $this->_ci->email->to($strTo);
        $this->_ci->email->subject($strSubject);
        $this->_ci->email->message($strMessage);

        if (Validator::isNotEmpty($strCC)){
            if (!Validator::isEmail($strCC)){
                MLog::warning(CoreConst::MODULE_EMAIL, 'email cc must be string');
            } else {
                $this->_ci->email->cc($strCC);
            }
        }

        if (Validator::isNotEmpty($strBCC)){
            if (!Validator::isEmail($strBCC)){
                MLog::warning(CoreConst::MODULE_EMAIL, 'email bcc must be string');
            } else {
                $this->_ci->email->bcc($strBCC);
            }
        }

        if (Validator::isNotEmpty($arrAttach) && !Validator::isArray($arrAttach)){
            MLog::warning(CoreConst::MODULE_EMAIL, 'email attach file list is not array');
        } else {
            foreach ($arrAttach as $strAttachFilePath){
                $this->_ci->email->attach($strAttachFilePath);
            }
        }

        $this->_ci->email->set_newline("\r\n");

        if (false === $this->_ci->email->send()){
            MLog::fatal(CoreConst::MODULE_EMAIL, 'send email failed');
            return false;
        }
        return true;
    }



}