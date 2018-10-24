<?php
class nyaUploadConfig {
    //数据库
    public $databaseConfig = array(
        "servername" => "127.0.0.1:3306",
        "dbname" => "netdisk",
        "username" => "netdiskuser",
        "password" => "KDcbu1BP!sz$1jdD7UA8e%3vt86Fdt0%g6oS3tEu!xegufhEUXjYJmejbQp#wCT8QtSmWwyFDU6w!Jl03gBY5hqX6RLlwHX0TCw2fS105KJlCgCUg1A%O!98h@EEQR83",
        "table" => "netdiskfiles"
    );
    //文件资源库
    public $filebaseConfig = array(
        "dir" => "E:/www/upload/",
        "url" => "http://192.168.2.100/upload/"
    );
}
?>