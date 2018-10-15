<?php
header('Content-type:application/json');
$fileinfo = $_FILES["file"];
$jarr = null;
if ($fileinfo["error"] > 0)
{
    $jarr = array(
        "status" => $fileinfo["error"]
    );
}
else
{
    $extensionarr = explode(".", $fileinfo["name"]);
    $extension = end($extensionarr);
    $jarr = array(
        "status" => $fileinfo["error"],
        "name" => $fileinfo["name"],
        "ext" => $extension,
        "mime" => $fileinfo["type"],
        "size" => $fileinfo["size"],
    );
    if (file_exists("E:/www/upload/")) {
        if (file_exists("E:/www/upload/".$fileinfo["name"]))
        {
            $jarr["status"] = -102;
        }
        else
        {
            if (move_uploaded_file($fileinfo["tmp_name"], "E:/www/upload/" . $fileinfo["name"])) {
                $jarr["status"] = 0;
            } else {
                $jarr["status"] = -101;
            }
        }
    } else {
        $jarr["status"] = -100;
    }
}
echo json_encode($jarr);
?>