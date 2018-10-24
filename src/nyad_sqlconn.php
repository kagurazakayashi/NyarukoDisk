<?php
class nyaUploadDB {
    private $conf = null;
    private $security = null;

    function __construct($sqlconf) {
        $this->conf = $sqlconf;
        $this->security = new nyaUploadSecurity();
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
                    $row = $result->fetch_all();
                }
            } else {
                $row = "";
                if (!is_bool($result)) {
                    $row = $result->fetch_array();
                }
            }
            return $row;
        }else{
            return mysqli_error($con);
        }
        $con->close();
    }

    function insertfile($srcname,$phyname,$ext,$dir,$mime,$size,$uptime,$hash) {
        $key = "`srcname`,`phyname`,`ext`,`dir`,`mime`,`size`,`uptime`,`hash`";
        $valuearr = array($srcname,$phyname,$ext,$dir,$mime,$size,$uptime,$hash);
        for ($vi=0; $vi < count($valuearr); $vi++) {
            $valuearr[$vi] = $this->security->checkfilename($valuearr[$vi])[1];
        }
        $value = implode("','", $valuearr);
        $sql = "INSERT INTO `".$this->conf["dbname"]."`.`".$this->conf["table"]."` (".$key.") VALUES ('".$value."');";
        return $this->nyadb($sql);
    }
    
}
?>