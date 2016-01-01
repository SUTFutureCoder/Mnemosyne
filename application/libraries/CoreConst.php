<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 15-12-5
 * Time: 下午3:40
 */
//常量存储库
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
}