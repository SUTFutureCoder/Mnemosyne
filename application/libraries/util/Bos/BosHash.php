<?php
/**
 * 从数据流中进行哈希
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-14
 * Time: 下午1:16
 */
class BosHash{

    /**
     * @param resource $fp THE OPEN FILE
     * @param int $offset The offset
     * @param int $length max number of characters to copy from $fp into the hashing content
     * @return string
     */
    public static function md5FromStream($fp, $offset = 0, $length = -1){
        $intPos = ftell($fp);
        $ctx    = hash_init('md5');
        fseek($fp, $offset, SEEK_SET);
        //不断读入
        hash_update_stream($ctx, $fp, $length);
        if ($intPos !== false){
            fseek($fp, $intPos, SEEK_SET);
        }
        return hash_final($ctx, true);
    }

}