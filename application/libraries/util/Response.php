<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-11-2
 * Time: 上午12:23
 */
//用于回复信息到客户端
//注意，在library禁止使用此类，library中使用返回false
class Response{
    /*
     * 成功返回码
     */
    const CODE_SUCCESS          = 0;

    /*
     * 客户端错误码
     */
    const CODE_BAD_REQUEST      = 400;

    /*
     * 未认证错误码
     */
    const CODE_UNAUTHENTICATED  = 401;

    /*
     * 未授权错误码
     */
    const CODE_UNAUTHORIZED     = 402;

    /*
     * 参数错误码
     */
    const CODE_PARAMS_MISSING   = 403;

    /*
     * 参数错误码
     */
    const CODE_PARAMS_WRONG     = 404;

    /*
     * 未知错误码
     */
    const CODE_UNEXPECTED       = 405;

    /*
     * 达到限制码
     */
    const CODE_REACHED_LIMIT    = 406;

    /*
     * 同名冲突码
     */
    const CODE_PARAMS_CONFLICT  = 407;

    /*
     * 系统错误码
     */
    const CODE_SERVER_ERROR     = 500;

    /*
     * 文件权限异常
     */
    const CODE_FILE_PERMISSION  = 501;

    const HEADER_CONTENT_TYPE_JSON = 'Content-Type:application/json; charset=utf-8';

    const HEADER_CONTENT_TYPE_XML = 'Content-Type:text/xml; charset=utf-8';

    const HEADER_CONTENT_TYPE_HTML = 'Content-Type:text/html; charset=utf-8';

    /*
     * 错误描述
     *
     * @var array
     */
    protected static $failDescList = array(
        self::CODE_BAD_REQUEST      => '抱歉，您请求的方式错误',
        self::CODE_UNAUTHENTICATED  => '您还未登录，请先登录',
        self::CODE_UNAUTHORIZED     => '很抱歉，您没有这个权限',
        self::CODE_PARAMS_MISSING   => '请求缺少相关参数',
        self::CODE_PARAMS_WRONG     => '请求参数有误',
        self::CODE_UNEXPECTED       => '请求失败',
        self::CODE_SERVER_ERROR     => '很抱歉，我们的程序可能出了点问题，您可以通过邮件组bug@bricksfx.cn向我们反馈问题！',
        self::CODE_REACHED_LIMIT    => '请求达到最大限制',
        self::CODE_PARAMS_CONFLICT  => '不允许同名',
        self::CODE_FILE_PERMISSION  => '文件权限异常'
    );

    /*
     * 成功返回
     *
     * @param null $data
     */
    public function jsonSuccess($data = null){
        $this->json(self::CODE_SUCCESS, 'ok', $data);
    }

    /*
     * 失败返回
     *
     * @param int $status
     * @param string $description
     */
    public function jsonFail($status, $description = ''){
        $description = $description ? $description :
            (isset(self::$failDescList[$status]) ? self::$failDescList[$status] : $description);
        $this->json($status, $description);
    }

    /*
     * json格式发送
     */
    public function json($status, $description, $data = null){
        header(self::HEADER_CONTENT_TYPE_JSON);
        $retArr = array(
            'status'        => $status,
            'description'   => $description,
        );

        $data === null || $retArr['data'] = $data;
        echo json_encode($retArr);
        exit;
    }

}