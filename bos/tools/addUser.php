<?php
/**
 * 添加用户
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-12
 * Time: 上午9:20
 */
require_once '../config.php';
//载入目录
Config::definePath();

require BOSPATH . 'util/Uuid.php';
require BOSPATH . 'util/Token.php';

//目前没有ui界面，先用脚本来跑
//尼玛才想起来只能用FTP操作共享主机
if (substr(php_sapi_name(), 0, 3) !== 'cli'){
    die('This Program can only be run in CLI mode');
}

$userId    = Uuid::genUUID('user');
$accessKey = Token::getToken($userId . 'acc');
$secretKey = Token::getToken($userId . 'sec');

echo 'user_id:  '  . $userId . PHP_EOL;
echo 'access_key:  ' . $accessKey . PHP_EOL;
echo 'secret_key:  ' . $secretKey . PHP_EOL;
