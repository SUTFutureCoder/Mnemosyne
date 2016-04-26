<?php
require 'config.php';
//载入目录
Config::definePath();
spl_autoload_register(function ($class){
    require BOSPATH . 'util/' . $class . '.php';
});

$strType         = $_GET['type'];
$strFunctionName = $_GET['qt'];

//验证函数白名单
if (!isset(Config::$funcWhiteList[$strType]) || !in_array($strFunctionName, Config::$funcWhiteList[$strType])){
    Response::responseErrorJson(ErrorCodes::ERROR_NO_SUCH_FUNCTION);
}

//直接调用函数
$ret = call_user_func(array($strType, $strFunctionName), $_GET);

//返回调用结果
Response::responseResultJson($ret);