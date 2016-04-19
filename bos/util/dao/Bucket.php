<?php
/**
 * bos_bucket DAO层
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-19
 * Time: 上午10:33
 */
require_once BOSPATH . 'util/dao/Base.php';
class Dao_Bucket extends Dao_Base{

    public function __construct()
    {
        parent::__construct('bos_bucket');
    }

    public static $FIELDS = array(
        'id', 'bucket_id', 'bucket_name', 'user_id', 'access_key', 'secret_key',
        'bucket_root', 'enable_host_list', 'enable_null_referer', 'is_public', 'key_need', 'ctime',
    );

}