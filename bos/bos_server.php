<?php
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 16-3-19
 * Time: 下午3:06
 */

//自动加载
spl_autoload_register(function ($class){
    include 'util/' . $class . '.php';
});

move_uploaded_file($_FILES["pic"]["tmp_name"], './');

