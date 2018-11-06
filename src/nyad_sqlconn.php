<?php
class nyaUploadDB {
    private $conf = null;
    private $security = null;
    private $sqltable = "";

    function __construct($sqlconf) {
        $this->conf = $sqlconf;
        $this->security = new nyaUploadSecurity();
        $this->sqltable = "`".$this->conf["dbname"]."`.`".$this->conf["table"]."`";
    }
    /**
     * @description: 执行SQL语句
     * @param String SQL语句 
     * @param Bool 是否返回多行数据
     * @return Array 查询结果数组
     */
    function nyadb($sql,$multi=false) {
        $con = new mysqli($this->conf["servername"],$this->conf["username"],$this->conf["password"],$this->conf["dbname"]);
        //$con->query('set names utf8;');
        if($result = $con->query($sql)){
            if ($multi) {
                $row = "";
                if (!is_bool($result)) {
                    $row = $result->fetch_all(MYSQLI_ASSOC);
                }
            } else {
                $row = "";
                if (!is_bool($result)) {
                    $row = $result->fetch_row();
                }
            }
            return $row;
        }else{
            return mysqli_error($con);
        }
        $con->close();
    }
    /**
     * @description: 添加文件信息到数据库
     * @param String 文件相关信息
     * @return Array/String 数据库执行结果
     */
    function insertFile($fileid,$srcname,$phyname,$ext,$dir,$mime,$size,$uptime,$hash) {
        $key = "`fileid`,`srcname`,`phyname`,`ext`,`dir`,`mime`,`size`,`uptime`,`hash`";
        $valuearr = array($fileid,$srcname,$phyname,$ext,$dir,$mime,$size,$uptime,$hash);
        for ($vi=0; $vi < count($valuearr); $vi++) {
            $valuearr[$vi] = $this->security->checkfilename($valuearr[$vi])[1];
        }
        $value = implode("','", $valuearr);
        $sql = "INSERT INTO ".$this->sqltable." (".$key.") VALUES ('".$value."');";
        return $this->nyadb($sql);
    }
    /**
     * @description: 通过哈希查找文件
     * @param String hash 哈希值
     * @return Array 文件信息列表 
     */
    function getFileWithHash($hash) {
        $sql = "SELECT * FROM ".$this->sqltable." WHERE `hash`='".$this->security->checkfilename($hash)[1]."';";
        return $this->nyadb($sql,true);
    }
    /**
     * @description: 通过原文件名查找文件
     * @param String hash 哈希值
     * @return Array 文件信息列表 
     */
    function getFileWithSrcName($name) {
        $sql = "SELECT * FROM ".$this->sqltable." WHERE `srcname`='".$this->security->checkfilename($name)[1]."';";
        return $this->nyadb($sql,true);
    }
    /**
     * @description: 通过通过文件唯一识别码查找文件
     * @param String fileid 文件唯一识别码（ID）
     * @return Array 文件信息列表（正常情况下只有1个文件）
     */
    function getFileWithId($fileid) {
        $sql = "SELECT * FROM ".$this->sqltable." WHERE `fileid`='".$this->security->checkfilename($fileid)[1]."';";
        return $this->nyadb($sql,true);
    }
}
?>