<?php
$url = 'http://localhost:8080/api/ahs/rincian';
$data = [
    'id_rap_detail' => 491,
    'items' => [
        [
            'tipe' => 'bahan',
            'uraian' => 'Custom Bahan Test',
            'merk' => '',
            'spesifikasi' => '',
            'koefisien' => 2,
            'satuan' => 'kg',
            'hargaSatuan' => 5000,
            'sumber' => 'Pergub Test|https://test.com'
        ]
    ]
];

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data)
    ]
];
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
echo "SAVE RESULT: " . $result . "\n";

$url2 = 'http://localhost:8080/api/ahs/rincian/491';
$result2 = file_get_contents($url2);
echo "GET RESULT: " . $result2 . "\n";
