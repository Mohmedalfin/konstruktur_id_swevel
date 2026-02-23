<?php

namespace App\Controllers;

use App\Models\RoleModel; // Import modelnya

class Home extends BaseController
{
    public function index()
    {
        $roleModel = new RoleModel();
        
        // Ambil semua data role
        $data['daftar_role'] = $roleModel->findAll();

        // Kirim data ke view
        return view('tampil_role', $data);
    }
}