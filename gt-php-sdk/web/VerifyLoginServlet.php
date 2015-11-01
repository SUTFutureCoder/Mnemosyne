<?php 
/**
 * 本文件示例只是简单的输出 Yes or No
 */
// error_reporting(0);
require_once dirname(dirname(__FILE__)) . '/lib/class.geetestlib.php';
session_start();
$GtSdk = new GeetestLib();
if ($_SESSION['gtserver'] == 1) {
    $result = $GtSdk->validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode']);
    if ($result == TRUE) {
        $_SESSION['gtMnemosyneValid'] = 1;
    } else if ($result == FALSE) {
        $_SESSION['gtMnemosyneValid'] = 0;
    } else {
        $_SESSION['gtMnemosyneValid'] = 0;
    }
}else{
    if ($GtSdk->get_answer($_POST['geetest_validate'])) {
        $_SESSION['gtMnemosyneValid'] = 1;
    }else{
        $_SESSION['gtMnemosyneValid'] = 0;
    }
}



?>
