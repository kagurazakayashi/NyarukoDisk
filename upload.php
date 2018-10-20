<?php
include_once("functions.php");
include_once("md6.php");
include_once("security.php");
class nyaUpload {
    const TODIR = "B:/UP/";
    private $fileinfo = null;
    private $allarr = array();
    private $isarray = false;
    private $security = null;
    /**
     * @description: 为初始变量赋值，如果没有提交内容则返回错误
     */
    function __construct() {
        if (isset($_FILES["file"])) {
            $this->fileinfo = $_FILES["file"];
            $this->isarray = is_array($this->fileinfo["error"]);
            $this->security = new nyaUploadSecurity();
        } else {
            $this->fail();
        }
    }
    /**
     * @description: 返回的错误内容
     * @return null
     */
    function fail() {
        header("HTTP/1.1 400 Bad Request");
        return null;
    }
    /**
     * @description: 从文件信息数组中取出信息，区分单个或多个文件
     * @param String key 要获取的信息 
     * @param Int fi 数组中第几个文件 
     * @return String 取出的信息内容
     */
    function getfileinfo($key,$fi) {
        if ($this->isarray) {
            return $this->fileinfo[$key][$fi];
        } else {
            return $this->fileinfo[$key];
        }
    }
    /**
     * @description: 保存文件并返回相关信息
     */
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
                $srcfilenamevfarr = $this->security->checkfilename($srcfilename);
                $srcfilename = $srcfilenamevfarr[1];
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
                    "phyname" => $filename,
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
                            if ($srcfilenamevfarr[0]) $jarr["status"] = 101;
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