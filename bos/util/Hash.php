<?php
/**
 * 用于计算文件哈希值
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-4-13
 * Time: 下午10:49
 */
class Hash{

    /**
     *
     * 用于验证上传文件MD5是否正确
     *
     * 注意别忘了包一层base64_encode
     *
     *
     * @param $fp
     * @param int $offset
     * @param int $length
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