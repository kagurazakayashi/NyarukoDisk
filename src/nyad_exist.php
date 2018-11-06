<?php
class nyaExist {
    private $config = null;
    private $sqlconn = null;
    private $security = null;
    /**
     * @description: 为初始变量赋值，如果没有提交内容则返回错误
     */
    function __construct() {
        // if ($this->getpost("upmode")) {
            $this->config = new nyaUploadConfig();
            $this->security = new nyaUploadSecurity();
            $this->sqlconn = new nyaUploadDB($this->config->databaseConfig);
        // } else {
        //     die($this->fail(-2));
        // }
    }
    /**
     * @description: 返回的错误内容
     */
    function fail($errid=-1) {
        header("HTTP/1.1 400 Bad Request");
        return json_encode(array(array("status"=>$errid)));
    }
    /**
     * @description: 自动识别 get/post
     * @param String argkey 传入参数的名称
     * @return {*} 此参数的内容。如果 get/post 都没有找到没有则返回 null。
     */
    function getpost($argkey) {
        if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET[$argkey])) {
            return $_GET[$argkey];
        } else if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST[$argkey])) {
            return $_POST[$argkey];
        }
        return null;
    }
    /**
     * @description: 根据 mode 参数选择模式
     */
    function isexist() {
        $jtimestart = microtime(true);
        
        $files = [];
        $mode = $this->getpost("upmode");
        switch ($mode) {
            case "hash":
                $files = $this->hashexist(false);
                return $files;
            case "hash-all":
                $files = $this->hashexist(true);
                $infoarr = array(
                    "status"=>0,
                    "memory"=>memory_get_usage(),
                    "proctime"=>(microtime(true) - $jtimestart),
                    "files"=>count($files)
                );
                return array(
                    "info"=>$infoarr,
                    "files"=>$files
                );
            case "id":
                $files = $this->fileidexist(1);
                return $files;
            case "id-all":
                $files = $this->fileidexist(0);
                $infoarr = array(
                    "status"=>0,
                    "memory"=>memory_get_usage(),
                    "proctime"=>(microtime(true) - $jtimestart),
                    "files"=>count($files)
                );
                return array(
                    "info"=>$infoarr,
                    "files"=>$files
                );
            case "id-dl":
                $files = $this->fileidexist(2);
                return $files;
            default:
                die($this->fail(-2));
                break;
        }
    }
    /**
     * @description: 根据 hash 读取文件列表
     * @param Bool isall 是否获取详细信息
     * @return Array<String:Array> 文件详细信息
     * @return Array<String:Bool> 文件是否存在
     */
    function hashexist($isall) {
        $hasharr = $this->getpost("hash");
        if (!is_array($hasharr)) $hasharr = array($hasharr);
        $allfiles = array();
        for ($hi=0; $hi < count($hasharr); $hi++) {
            $nowhash = $hasharr[$hi];
            $nowhash = $this->security->checkfilename($nowhash)[1];
            if (!$this->security->ishash($nowhash)) {
                die($this->fail(-3));
            }
            $files = $this->sqlconn->getFileWithHash($nowhash);
            if ($isall) {
                $allfiles[$nowhash] = $files;
            } else {
                $isfile = count($files) > 0 ? true : false;
                $allfiles[$nowhash] = $isfile;
            }
        }
        return $allfiles;
    }
    /**
     * @description: 根据 id 读取文件列表
     * @param Int showmode 回显模式 0显示所有信息 1只返回是否有 2只返回下载地址
     * @param String fileid 文件唯一识别码
     * @return Array<String:Array> 文件详细信息
     * @return Array<String:Bool> 文件是否存在
     */
    function fileidexist($showmode,$fileid=null,$arrayout=false) {
        $fileidarr = $fileid ? $fileid : $this->getpost("id");
        if (!is_array($fileidarr)) $fileidarr = array($fileidarr);
        $allfiles = array();
        for ($hi=0; $hi < count($fileidarr); $hi++) {
            $nowfileid = $fileidarr[$hi];
            $nowfileid = $this->security->checkfilename($nowfileid)[1];
            if (!$this->security->ishash($nowfileid,64)) {
                die($this->fail(-4));
            }
            $files = $this->sqlconn->getFileWithId($nowfileid);
            if ($showmode == 0) {
                $allfiles[$nowfileid] = $files;
            } else if ($showmode == 1) {
                $isfile = count($files) > 0 ? true : false;
                $allfiles[$nowfileid] = $isfile;
            } else if ($showmode == 2) {
                //重建二维数组
                $newfiles = array();
                for ($filei=0; $filei < count($files); $filei++) { 
                    $nowfile = $files[$filei];
                    $dlurl = $this->config->filebaseConfig["url"].$nowfile["dir"]."/".$nowfile["phyname"].".".$nowfile["ext"];
                    if ($arrayout) {
                        $newfile = array($nowfile["srcname"],$dlurl);
                    } else {
                        $newfile = array(
                            "srcname"=>$nowfile["srcname"],
                            "url"=>$dlurl
                        );
                    }
                    array_push($newfiles,$newfile);
                }
                $allfiles[$nowfileid] = $newfiles;
            }
        }
        return $allfiles;
    }
}
?>