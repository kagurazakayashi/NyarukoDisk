<?php
class nyaUploadConfig {
    //数据库
    public $databaseConfig = array(
        "servername" => "127.0.0.1:3306",
        "username" => "",
        "password" => "",
        "table" => ""
    );
    //文件资源库
    public $filebaseConfig = array(
        "dir" => "E:/www/upload/",
        "url" => "http://192.168.2.100/upload/"
    );
}
?>