<?php
$userId = 12;
$fileDir = getcwd() . "/" . "static" . "/" . $userId;
echo $fileDir . "\n";
if(file_exists($fileDir)){
    echo "test";
    //mkdir($fileDir, 0777, true);
}
$file = $fileDir . "/avatar.png";
echo "\n";
echo $file;
echo "\n";
