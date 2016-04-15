<?php
/**
 * 定义http一些header
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-14
 * Time: 下午2:44
 */
class BosHttpHeaders{

    const CONTENT_ENCODING = 'Content-Encoding';
    const CONTENT_LENGTH   = 'Content-Length';
    const CONTENT_MD5      = 'Content-MD5';
    const CONTENT_RANGE    = 'Content-Range';
    const CONTENT_TYPE     = 'Content-Type';

    const JSON             = 'application/json; charset=utf-8';
    const USER_METADATA_PREFIX = 'x-bos-meta-';

}