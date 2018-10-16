<?php
function formatBytes($size) {
    $units = array(' B', ' KiB', ' MiB', ' GiB', ' TiB');
    for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024; 
    return round($size, 2).$units[$i];
}
function unformatBytes($size) {
    $unitstr = preg_replace("/\\d+/",'',$size);
    $unitstr = str_replace(' ','', $unitstr);
    $unitnum = preg_match_all('/\d+/',$size,$unitarr);
    $unitnum = floatval(join('',$unitarr[0]));
    $units = array('B', 'K', 'M', 'G', 'T');
    for ($uniti = 0; $uniti < count($units); $uniti++) {
        if (strpos($unitstr,$units[$uniti]) !== false) {
            $unitnum *= pow(1024,$uniti);
            break;
        }
    }
    return $unitnum;
}
?>