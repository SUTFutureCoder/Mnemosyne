<?php
/**
 * 用于存放模块的非核心静态变量
 *
 * 一定要存放非核心～核心静态变量请放在CoreConst里面
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-6-10
 * Time: 下午8:18
 */
class ModuleConst{

    /**
     *
     * 表白模块
     *
     */
    public static $showLoveTplList = array(
        0 => array(
            'name' => '经典',
            'url'  => 'ILOVEYOU/classic/',
        ),
        1 => array(
            'name' => '极客',
            'url'  => 'ILOVEYOU/geek/',
        ),
    );


}