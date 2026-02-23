<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ProyekController extends BaseController
{
    public function index()
    {
        return view('proyek/index');
    }
    public function create()
    {
        return view('proyek/create');
    }
    public function store() {}
}
