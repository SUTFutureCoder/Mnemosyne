<?php
/**
 *
 * 调试工具
 *
 *
 * 根据userId, token, salt算出签名
 *
 *
 *
 * eg:
 * php hackSignature.php 14572600262 f6f8666846f75360b4da339671681313165ad20b12241cced473de9138f5d47f salt
 *
 *
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-3-12
 * Time: 下午5:40
 */
$intUserId = $argv[1];
$strToken  = $argv[2];
$strSalt   = $argv[3];

print_r(PHP_EOL . hash('sha256', $strToken . 'WELCOME' . $intUserId . 'PROJECT M' . $strSalt . '@' . $intUserId) . PHP_EOL . PHP_EOL);