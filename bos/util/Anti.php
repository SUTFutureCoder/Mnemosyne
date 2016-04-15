<?php
defined('BOSPATH') OR exit('No direct script access allowed');
/**
 * 反作弊
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-10
 * Time: 下午6:09
 */
class Anti{

    private function __construct(){
        //禁止new
    }

    /**
     * 反盗链规则
     *
     * @param string $enableHostList json串的合法链接域名
     * @param bool $enableNullReferer 是否允许空来源
     * @return bool
     */
    public static function antiStealingLink($enableHostList, $enableNullReferer){
        if (empty($enableHostList)){
            //当为空时则允许全域访问
            return true;
        }

        $referUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        if (empty($referUrl) && !$enableNullReferer){
            //是否允许空来源
            return false;
        }

        $arrEnableHostList = json_decode($enableHostList, true);
        if ($referUrl && !in_array(parse_url($referUrl, PHP_URL_HOST), $arrEnableHostList)){
            return false;
        }

        return true;
    }
}