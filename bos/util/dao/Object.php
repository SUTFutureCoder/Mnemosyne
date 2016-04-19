<?php
defined('BOSPATH') OR exit('No direct script access allowed');
/**
 * 文件数据控制
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-18
 * Time: 上午10:24
 */
require_once BOSPATH . 'util/dao/Base.php';
class Dao_Object extends Dao_Base{

    public function __construct(){
        parent::__construct('bos_object');
    }

    public static $FIELDS = array(
        'id', 'object_id' , 'object_index', 'name', 'mime', 'size', 'sign', 'user',
        'private_share_key', 'bucket_id' , 'is_public', 'is_delete', 'ctime',
    );

//    public function insert($intObjectId, $strObjectIndex, $strFileName, $strMime, $intSize, $strSign,
//                           $strUser, $intBucketId, $boolIsPublic, $intCtime){
//        $objDbConn = $this->getDbConn();
//        $arrArgs   = func_get_args();
//        $strQuery  = 'INSERT INTO ' . $this->_table . ' (' . implode(',', self::$FIELDS)
//            . ') VALUES("' . implode('","', array_map(array($this, 'realEscapeString'), $arrArgs)) . '")';
//        return $objDbConn->query($strQuery);
//    }
}