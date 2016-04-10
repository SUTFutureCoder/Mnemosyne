<?php
/**
 * 通用异常
 *
 * 说明 exception和log的使用情景
 *
 * Exception 用于出错马上终止的情况，无需return。会打一条log
 *
 * Log 用于出错或debug后逻辑仍然继续往下走
 *
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-3-31
 * Time: 下午4:34
 */
class MException extends Exception {

    const WARNING = 'warning';
    const TRACE   = 'trace';
    const FATAL   = 'fatal';
    const NOTICE  = 'notice';

    private $ci;
    private $errno;
    private $errstr;
    private $arg;
    private $level;

    private $_functionName;
    private $_fileName;
    private $_lineName;
    private $_module;

    public function __construct($module = '', $errno = 0, $arg = null, $errstr = '', $level = self::FATAL){
        if ('' === $module){
            //跳过loader自construct
            return false;
        }

        $this->ci    =& get_instance();

        $this->level = $level;
        $this->errno = $errno;
        $this->arg   = $arg;

        $errstr = ($errstr != '') ? $errstr : ErrorCodes::errMsg($errno);
        if (empty($errstr)){
            $errstr = 'Errno msg not found . errno:' . $errno;
        }

        $stackTrace = $this->getTrace();
        $class      = $stackTrace[0]['class'];
        $type       = $stackTrace[0]['type'];
        $function   = $stackTrace[0]['function'];

        $file       = $this->file;
        $line       = $this->line;
        if ($class != null){
            $function = $class . $type . $function;
        }
        $this->errstr = $errstr;
        $this->_functionName = $function;
        $this->_fileName     = $file;
        $this->_lineName     = $line;
        $this->_module       = $module;

        //写log，并且使用Response模块输出
        MLog::throwError($this->_module, $errno, $this->errstr, array(
            'file'  => $this->_fileName,
            'line'  => $this->_lineName,
            'args'  => $this->arg,
        ));

        //此处终结一切
//        parent::__construct($this->errstr, $errno);
    }
}