<?php
/**
 * 错误码
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-15
 * Time: 下午9:05
 */
class ErrorCodes{

    const ERROR_NO_SUCH_FUNCTION         = 'NoSuchFunction';
    const ERROR_NO_SUCH_FILE             = 'NoSuchFile';
    const ERROR_NO_SUCH_BUCKET           = 'NoSuchBucket';

    const ERROR_FILE_SIGN_ERROR          = 'FileSignError';

    const ERROR_PRIVATE_SHARE_KEY_ERROR  = 'PrivateShareKeyError';
    const ERROR_PRIVATE_ACCESS_KEY_ERROR = 'PrivateAccessKeyError';
    const ERROR_INVALID_HTTP_AUTH_HEADER = 'InvalidHTTPAuthHeader';

    const ERROR_DB_INSERT_OBJECT         = 'InsertObjectIntoDbError';
    const ERROR_ANTI_STEAL_LINK          = 'AntoStealLint';
    const ERROR_UPLOAD_FILE_ERROR        = 'Update file error';
    const ERROR_UPLOAD_STRING_ERROR      = 'Upload string error';

    public static $message = array(
        self::ERROR_NO_SUCH_FUNCTION           => 'The specified type and function does not exist.',
        self::ERROR_NO_SUCH_FILE               => 'The specified file does not exist.',
        self::ERROR_NO_SUCH_BUCKET             => 'The specified bucket does not exist.',

        self::ERROR_FILE_SIGN_ERROR            => 'The file signature error, please reupload file',

        self::ERROR_PRIVATE_SHARE_KEY_ERROR    => 'The private share key does not correct. Access denied.',
        self::ERROR_PRIVATE_ACCESS_KEY_ERROR   => 'The access key for private file does not correct. Access denied',
        self::ERROR_INVALID_HTTP_AUTH_HEADER   => 'The HTTP authorization header is invalid. Consult the service documentation for details',

        self::ERROR_DB_INSERT_OBJECT           => 'Object insert into database error',
        self::ERROR_ANTI_STEAL_LINK            => 'Please DO NOT link this object to other site',
        self::ERROR_UPLOAD_FILE_ERROR          => 'Please reupload file',
        self::ERROR_UPLOAD_STRING_ERROR        => 'Please reupload string',
    );

}