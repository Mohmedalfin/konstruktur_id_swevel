<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index()
    {
        return view('proyek/menu/dashboard');
    }

    public function create()
    {
        return view('proyek/menu/dashboard');
    }

    public function store() {}
}