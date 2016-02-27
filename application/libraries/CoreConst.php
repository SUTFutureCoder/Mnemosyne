<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-12-5
 * Time: 下午3:40
 */
//常量、全局变量存储库
class CoreConst{
    //平台常量
    const PLATFORM_TEST     = -1;
    const PLATFORM_UNKNOWN  = 0;
    const PLATFORM_ADMIN    = 1;
    const PLATFORM_PC       = 2;
    const PLATFORM_MOBILE   = 3;

    public static $platform = array(
        self::PLATFORM_TEST,
        self::PLATFORM_ADMIN,
        self::PLATFORM_PC,
        self::PLATFORM_MOBILE,
    );

    //性别常量
    const USER_SEX_OTHERS   = 0;
    const USER_SEX_MALE     = 1;
    const USER_SEX_FEMALE   = 2;
    const USER_SEX_SECRET   = 3;

    public static $userSex = array(
        self::USER_SEX_OTHERS,
        self::USER_SEX_FEMALE,
        self::USER_SEX_MALE,
        self::USER_SEX_SECRET,
    );

    //用户状态常量
    const USER_STATUS_DISABLE = 0;
    const USER_STATUS_ENABLE = 1;

    public static $userStatus = array(
        self::USER_STATUS_DISABLE,
        self::USER_STATUS_ENABLE,
    );

    const AlUMNI_FILL_IN_MES = 0;
    public static $messageTyep = array(
        self::AlUMNI_FILL_IN_MES,
    );

    //Mnemosyne核心参数
    //UUID
    const LOG_UUID    = 'log_uuid';
    const USER_UUID   = 'user_uuid';
    const ALUMNI_UUID = 'alumni_uuid';
    public static $uuid = array(
        self::LOG_UUID,
        self::USER_UUID,
        self::ALUMNI_UUID,
    );

    //模块列表，用于打LOG等
    const MODULE_KERNEL  = 'kernel';
    const MODULE_ACCOUNT = 'account';
    const MODULE_ALUMNI  = 'alumni';
    const MODULE_DATABASE  = 'database';
    const MODULE_WEBSOCKET = 'websocket';
    const MODULE_SAL     = 'SAL';
    const MODULE_EMAIL   = 'EMAIL';

    public static $moduleList = array(
        self::MODULE_ACCOUNT,
        self::MODULE_ALUMNI,
        self::MODULE_KERNEL,
        self::MODULE_DATABASE,
        self::MODULE_WEBSOCKET,
        self::MODULE_SAL,
        self::MODULE_EMAIL,
    );

    //log是否打开
    const MNEMOSYNE_LOG = 1;

    //全局静态变量
    public static $userId = 0;
}