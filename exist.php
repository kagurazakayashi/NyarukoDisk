<?php
include_once("src/nyad_exist.php");
include_once("src/nyad_security.php");
include_once("src/nyad_sqlconn.php");
include_once("config.php");
header('Content-type:application/json');
$exists = new nyaExist();
echo $exists->isexist();
?>