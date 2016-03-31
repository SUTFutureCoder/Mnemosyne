<?php
/**
 * 用于处理websocket相关逻辑
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-2-8
 * Time: 下午11:41
 */
require_once 'SAL.php';
class Websocket {

    const URL = '127.0.0.1:2121';

    public function send($from, $token, $to, $content, $type = 'publish'){
        $arrData = array(
            'type' => $type,
            'from' => $from,
            'token' => $token,
            'to'   => $to,
            'content' => $content,
        );
        $websocketRet = SAL::doHttp('get', self::URL, $arrData);

        if ($websocketRet === false){
            MLog::fatal(CoreConst::MODULE_WEBSOCKET, 'send message through websocket error data[%s] ret[%s]',
                json_encode($arrData),
                json_encode($websocketRet));
            return false;
        }

        return $websocketRet;
    }

}