<?php
include_once("src/nyad_functions.php");
include_once("lib/md6.php");
include_once("src/nyad_security.php");
include_once("config.php");
include_once("src/nyad_sqlconn.php");
include_once("src/nyad_upload.php");
header('Content-type:application/json');
$nyaUpload = new nyaUpload;
echo $nyaUpload->savefile();
/*
返回值示例：
[
    {
        "status": 0,
        "fileid": "3665061501884871c133063685857e1f49f2faf9f12e1f0ee8031e6185611a51",
        "srcname": "QQ图片20190110153341.jpg",
        "phyname": "3665061501884871c133063685857e1f49f2faf9f12e1f0ee8031e6185611a51.jpg",
        "ext": "jpg",
        "dir": "2019/01/10/",
        "url": "http://192.168.2.100/upload/2019/01/10/3665061501884871c133063685857e1f49f2faf9f12e1f0ee8031e6185611a51.jpg",
        "mime": "image/jpeg",
        "size": 61201,
        "uptime": "2019-01-10 11:39:51",
        "md5": "c1dd19f919ac9767e939883f697ef267",
        "memory": 518096,
        "proctime": 0.07312202453613281
    }
]
*/
?>