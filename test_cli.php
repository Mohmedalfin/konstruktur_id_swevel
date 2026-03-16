<?php
$model = new \App\Models\PekerjaanModel();
$data = $model->findAll(2);
echo json_encode($data);
