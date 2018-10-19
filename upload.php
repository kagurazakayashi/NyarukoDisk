<?php
include_once("functions.php");
include_once("md6.php");
class nyaUpload {
    const TODIR = "B:/UP/";
    private $fileinfo = null;
    private $allarr = array();
    private $isarray = false;

    function __construct() {
        if (isset($_FILES["file"])) {
            $this->fileinfo = $_FILES["file"];
            $this->isarray = is_array($this->fileinfo["error"]);
        } else {
            $this->fail();
        }
    }
    function fail() {
        header("HTTP/1.1 400 Bad Request");
        return null;
    }
    function getfileinfo($key,$fi) {
        if ($this->isarray) {
            return $this->fileinfo[$key][$fi];
        } else {
            return $this->fileinfo[$key];
        }
    }
    function savefile() {
        if (!$this->fileinfo) return $this->fail();
        $filecount = 1;
        if ($this->isarray) $filecount = count($this->fileinfo["error"]);
        for ($fi=0; $fi < $filecount; $fi++) {
            $jtimestart = microtime(true);
            $jarr = null;
            if ($this->getfileinfo("error",$fi) > 0)
            {
                $jarr = array(
                    "status" => $this->getfileinfo("error",$fi)
                );
            }
            else
            {
                $srcfilename = $this->getfileinfo("name",$fi);
                $extensionarr = explode(".", $srcfilename);
                $extension = end($extensionarr);
                $uptime = time();
                $fromaddress = $this->getfileinfo("tmp_name",$fi);
                $md6 = new md6hash;
                $filename = $md6->hex($uptime.mt_rand(-2147483648,2147483647)).'.'.$extension;
                $md5 = md5_file($fromaddress);
                $toaddress = self::TODIR.$filename;
                $jarr = array(
                    "status" => $this->getfileinfo("error",$fi),
                    "srcname" => $srcfilename,
                    "name" => $filename,
                    "ext" => $extension,
                    "mime" => $this->getfileinfo("type",$fi),
                    "size" => $this->getfileinfo("size",$fi),
                    "time" => $uptime,
                    "md5" => $md5
                );
                if (file_exists(self::TODIR)) {
                    if (file_exists(self::TODIR.$filename))
                    {
                        $jarr["status"] = -102;
                    }
                    else
                    {
                        if (move_uploaded_file($fromaddress, $toaddress)) {
                            $jarr["status"] = 0;
                        } else {
                            $jarr["status"] = -101;
                        }
                    }
                } else {
                    $jarr["status"] = -100;
                }
                $jtimeend = microtime(true);
                $jarr["memory"] = memory_get_usage();
                $jarr["proctime"] = $jtimeend - $jtimestart;
            }
            array_push($this->allarr,$jarr);
        }
        return json_encode($this->allarr);
    }
}
header('Content-type:application/json');
$nyaUpload = new nyaUpload;
echo $nyaUpload -> savefile();
?>