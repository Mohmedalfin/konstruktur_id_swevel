<?php
namespace App\Controllers\Api;
use App\Controllers\BaseController;

class TestDbAhsController extends BaseController {
    public function index() {
        try {
            $db = \Config\Database::connect('estimator');
            $data = $db->query("SELECT * FROM bahan_utama LIMIT 2")->getResultArray();
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }
}
