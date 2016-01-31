<?php
/**
 * SAL - Service Access Layer
 *
 * 目前支持http协议、websocket协议、共享内存协议
 * 可用string/json/from三种数据打包协议
 *
 *
 * 预计支持nshead、fcgi协议 可能会支持消息队列push？未来的吧
 * mcpack1/mcpack2数据打包协议
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-1-31
 * Time: 下午5:20
 */
class SAL {

    private $_ci;

    public function __construct(){
        $this->_ci =& get_instance();
    }

    public function doHttp(){

    }
}