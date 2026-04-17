<?php
$content = file_get_contents('http://localhost:8080/api/pekerjaan?sumber=SNI&limit=5');
print_r(json_decode($content, true)['data']);
