<?php
class nyaUploadSecurity {
    /**
     * @description 保护SQL语句
     * @param String value 原始字符串
     * @return Array<Bool,String> [是否一致,修改后的值]
     */
    function checksql($value)   
    {   
        // 去除斜杠
        $newval = stripslashes($value);
        // 如果不是数字则加引号
        if (!is_numeric($newval))
        {
            $newval = "'" . mysql_real_escape_string($newval) . "'";      
        }
        $isok = strcasecmp($newval,$value) == 0 ? false : true;
        return [$isok,$newval];
    }

    /**
     * @description 清除文件名中的非法字符，截断超长的文件名
     * @param String value 原始字符串
     * @return Array<Bool,String> [是否一致,修改后的值]
     */
    function checkfilename($value) {
        $newval = trim($value,"\\/:*?\"'><|\0\t\n\x0B\r\x00\x1a");
        $newval = mb_substr($newval,0,64,"UTF-8");
        $isok = strcasecmp($newval,$value) == 0 ? false : true;
        return [$isok,$newval];
    }

    /**
     * @description: 只允许英文字母、空格、数字
     * @param String value 原始字符串
     * @return Array<Bool,String> [是否一致,修改后的值]
     */
    function checkusername($value,$isbool=true) {
        $newval = preg_replace("/[^a-zA-Z0-9 ]/i", '', $value);
        $isok = strcasecmp($newval,$value) == 0 ? false : true;
        return [$isok,$newval];
    }

    /**
     * @description: 是否为电子邮件地址
     * @param String value 原始字符串
     * @return Bool 返回是否允许
     */
    function checkemail($value,$isbool=true) {
        return preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$value) ? true : false;
    }

    /**
     * @description: 是否为网址
     * @param String value 原始字符串
     * @return Bool 返回是否允许
     */
    function checkurl($value,$isbool=true) {
        return preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$value) ? true : false;
    }
}
?>