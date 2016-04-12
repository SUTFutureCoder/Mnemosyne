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
    Response::responseErrorJson('NoSuchFunction', 'The specified type and function does not exist.');
}

//直接调用函数
$ret = call_user_func_array(array($strType, $strFunctionName), array('14604488844', '65895050358e80c543a7677c32fd6ae54abbc0e766e0c42f7457b4b10c5e0cb3'));

//返回调用结果
Response::responseResultJson($ret);