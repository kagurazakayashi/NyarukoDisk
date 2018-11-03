<?php
class nyaExist {
    private $config = null;
    private $sqlconn = null;
    private $security = null;
    /**
     * @description: 为初始变量赋值，如果没有提交内容则返回错误
     */
    function __construct() {
        if ($this->getpost("mode")) {
            $this->config = new nyaUploadConfig();
            $this->security = new nyaUploadSecurity();
            $this->sqlconn = new nyaUploadDB($this->config->databaseConfig);
        } else {
            $this->fail();
        }
    }
    /**
     * @description: 返回的错误内容
     */
    function fail($errid=-1) {
        if ($errid) return array(array("status"=>$errid));
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
        $mode = $this->getpost("mode");
        if ($mode == "hash") {
            $files = $this->hashexist(false);
            return $files;
        } else if ($mode == "hash-all") {
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
                $this->fail();
                die();
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
}
?>