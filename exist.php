<?php
include_once("src/nyad_exist.php");
include_once("src/nyad_security.php");
include_once("src/nyad_sqlconn.php");
include_once("config.php");
header('Content-type:application/json');
$exists = new nyaExist();
echo json_encode($exists->isexist());
/*
/exist.php?mode=hash&hash=c1dd19f919ac9767e939883f697ef267 返回值示例：
{
    "c1dd19f919ac9767e939883f697ef267": true
}
*/

/*
/exist.php?mode=hash-all&hash=c1dd19f919ac9767e939883f697ef267 返回值示例：
{
    "info": {
        "status": 0,
        "memory": 452272,
        "proctime": 0.013216018676757812,
        "files": 1
    },
    "files": {
        "c1dd19f919ac9767e939883f697ef267": [
            {
                "fileid": "3665061501884871c133063685857e1f49f2faf9f12e1f0ee8031e6185611a51",
                "srcname": "QQ图片20190110153341.jpg",
                "phyname": "3665061501884871c133063685857e1f49f2faf9f12e1f0ee8031e6185611a51",
                "ext": "jpg",
                "dir": "2019/01/10",
                "mime": "image/jpeg",
                "size": "61201",
                "uptime": "2019-01-10 11:39:51",
                "hash": "c1dd19f919ac9767e939883f697ef267"
            },
            {
                "fileid": "81f3942d77f946032cf3bb4a7c851ceed4be36d949be521d6ea8dda05bd445b2",
                "srcname": "TIM图片20190110153341.jpg",
                "phyname": "3665061501884871c133063685857e1f49f2faf9f12e1f0ee8031e6185611a51",
                "ext": "jpg",
                "dir": "2019/01/10",
                "mime": "image/jpeg",
                "size": "61201",
                "uptime": "2019-01-10 11:27:52",
                "hash": "c1dd19f919ac9767e939883f697ef267"
            }
        ]
    }
}
*/
?>