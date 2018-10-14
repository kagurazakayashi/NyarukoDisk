<?php
header('Content-type:application/json');
echo json_encode(array(
    "status" => 0,
    "maxsize" => ini_get('post_max_size'),
    "maxfilesize" => ini_get('upload_max_filesize'),
    "timeout" => ini_get('max_execution_time')
));
?>