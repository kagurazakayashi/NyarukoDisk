<?php
include_once("src/nyad_functions.php");
include_once("lib/md6.php");
include_once("src/nyad_security.php");
include_once("config.php");
include_once("src/nyad_upload.php");
header('Content-type:application/json');
$nyaUpload = new nyaUpload;
echo $nyaUpload->savefile();
?>