<?php
define('ENVIRONMENT', 'development');
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';

$model = new \App\Models\PekerjaanModel();
try {
    $data = $model->findAll(5);
    echo "SUCCESS\n";
    print_r($data);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
