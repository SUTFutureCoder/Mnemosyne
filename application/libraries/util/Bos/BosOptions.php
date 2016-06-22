<?php
/**
 * 一些常量，用于约定server常量
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-14
 * Time: 上午10:08
 */
class BosOptions
{
    const CONTENT_TYPE   = 'contentType';
    const CONTENT_LENGTH = 'contentLength';
    const CONTENT_MD5    = 'contentMd5';
    const CONTENT_SHA256 = 'contentSHA256';
    const USER_METADATA  = 'userMetaData';

    const PUT            = 'PUT';
    const CONFIG         = 'config';

    //函数对应远程关系
    const FILE           = 'File';
    const BUCKET         = 'Bucket';

    const putObjectFromFile   = 'saveFileStream';
    const putObjectFromString = 'saveStringStream';
}