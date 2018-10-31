<?php
class nyaUpload {
    private $filebase = "";
    private $fileinfo = null;
    private $postinfo = null;
    private $allarr = array();
    private $fileisarray = false;
    private $postisarray = false;
    private $security = null;
    private $config = null;
    private $sqlconn = null;
    /**
     * @description: 为初始变量赋值，如果没有提交内容则返回错误
     */
    function __construct() {
        if (isset($_FILES["file"])) {
            $this->fileinfo = $_FILES["file"];
            if (isset($_FILES) && count($_FILES) > 0) {
                $this->fileisarray = is_array($this->fileinfo["error"]);
            }
            if (isset($_POST) && count($_POST) >= 2) {
                if (!isset($_POST["hash"]) || !isset($_POST["filename"])) return $this->fail(-103);
                $this->postinfo = array(
                    "hash"=>$_POST["hash"],
                    "filename"=>$_POST["filename"]
                );
                $this->postisarray = is_array($this->fileinfo["filename"]);
            }
            $this->config = new nyaUploadConfig();
            $this->security = new nyaUploadSecurity();
            $this->sqlconn = new nyaUploadDB($this->config->databaseConfig);
            $this->filebase = $this->config->filebaseConfig["dir"];
        } else {
            $this->fail();
        }
    }
    /**
     * @description: 返回的错误内容
     * @param String errid 要输出的状态码（留空则什么都不输出） 
     */
    function fail($errid=null) {
        header("HTTP/1.1 400 Bad Request");
        if ($errid) echo json_encode(array(array("status"=>$errid)));
        die;
    }
    /**
     * @description: 从文件信息数组中取出信息，区分单个或多个文件
     * @param String key 要获取的信息 
     * @param Int fi 数组中第几个文件 
     * @return String 取出的信息内容
     */
    function getfileinfo($key,$fi) {
        if ($this->fileisarray) {
            return $this->fileinfo[$key][$fi];
        } else {
            return $this->fileinfo[$key];
        }
    }
    function getpostinfo($key,$fi) {
        if ($this->postisarray) {
            return $this->postinfo[$key][$fi];
        } else {
            return $this->postinfo[$key];
        }
    }
    /**
     * @description: 按日期创建层级文件夹
     * @return String 文件夹相对路径
     */
    function datedir() {
        $nowdate = [date('Y'),date('m'),date('d')];
        $dir = $this->filebase;
        if (!is_dir($dir)) mkdir($dir);
        $dir .= $nowdate[0].'/';
        if (!is_dir($dir)) mkdir($dir);
        $dir .= $nowdate[1].'/';
        if (!is_dir($dir)) mkdir($dir);
        $dir .= $nowdate[2].'/';
        if (!is_dir($dir)) mkdir($dir);
        $subdir = implode("/", $nowdate).'/';
        return $subdir;
    }

    function optionalupload() {
        if ($this->postinfo) {
            
        }
    }
    /**
     * @description: 保存文件并返回相关信息
     */
    function savefile() {
        if (!$this->fileinfo && !$this->postinfo) return $this->fail();
        $filecount = 1;
        if ($this->fileisarray) $filecount = count($this->fileinfo["error"]);
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
                $uptimestr = date("Y-m-d H:i:s",$uptime);
                $fromaddress = $this->getfileinfo("tmp_name",$fi);
                $md6 = new md6hash;
                $fileid = $md6->hex($uptime.mt_rand(-2147483648,2147483647));
                $filename = $fileid.'.'.$extension;
                $md5 = md5_file($fromaddress);
                $todir = $this->datedir();
                $toaddress = $this->filebase.$todir.$filename;
                $url = $this->config->filebaseConfig["url"].$todir.$filename;
                $jarr = array(
                    "status" => $this->getfileinfo("error",$fi),
                    "fileid" => $fileid,
                    "srcname" => $srcfilename,
                    "phyname" => $filename,
                    "ext" => $extension,
                    "dir" => $todir,
                    "url" => $url,
                    "mime" => $this->getfileinfo("type",$fi),
                    "size" => $this->getfileinfo("size",$fi),
                    "uptime" => $uptimestr,
                    "hash" => $md5
                );
                //查询是否可秒传
                $dontuploadfile = false;
                $existdata = $this->sqlconn->getFileWithHash($md5);
                if ($existdata && !is_array($existdata)) {
                    $jarr["status"] = -201;
                    $jarr["error"] = $existdata;
                } else {
                    if (isset($existdata[0])) {
                        $firstexistfile = $existdata[0];
                        $jarr["phyname"] = $firstexistfile["phyname"];
                        $jarr["ext"] = $firstexistfile["ext"];
                        $jarr["dir"] = $firstexistfile["dir"];
                        $jarr["mime"] = $firstexistfile["mime"];
                        $jarr["size"] = $firstexistfile["size"];
                        $jarr["hash"] = $firstexistfile["hash"];
                        $jarr["status"] = 100;
                        $dontuploadfile = true;
                    }
                }
                if (file_exists($this->filebase)) {
                    if (!$dontuploadfile && file_exists($this->filebase.$filename))
                    {
                        $jarr["status"] = -102;
                    }
                    else
                    {
                        if ($dontuploadfile || move_uploaded_file($fromaddress, $toaddress)) {
                            $sqlr = $this->sqlconn->insertFile($fileid,$srcfilename,$jarr["phyname"],$jarr["ext"],$jarr["dir"],$jarr["mime"],$jarr["size"],$uptimestr,$jarr["hash"]);
                            if ($sqlr && !is_array($sqlr)) {
                                $jarr["status"] = 200;
                                $jarr["error"] = $sqlr;
                            } else {
                                // $jarr["status"] = 0;
                                if ($srcfilenamevfarr[0]) $jarr["status"] = 101;
                            }
                        } else {
                            $jarr["status"] = -101;
                        }
                    }
                } else {
                    $jarr["status"] = -100;
                }
                $jarr["memory"] = memory_get_usage();
                $jtimeend = microtime(true);
                $jarr["proctime"] = $jtimeend - $jtimestart;
            }
            array_push($this->allarr,$jarr);
        }
        return json_encode($this->allarr);
    }
}
?>