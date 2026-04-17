<?php
$files = glob('writable/logs/*.log');
if(!empty($files)){
    $f = end($files);
    echo file_get_contents($f, false, null, max(0, filesize($f)-2000));
}
