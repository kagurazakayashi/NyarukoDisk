<?php
include_once("src/nyad_sysinfo.php");
header('Content-type:application/json');
$sysinfo = new nyaSysInfo();
echo json_encode($sysinfo->sysinfo());
?>