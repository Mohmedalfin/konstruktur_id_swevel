<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class MenuRapController extends BaseController
{
    public function index()
    {
        return view('proyek/menu/menu-rap');
    }

    public function create()
    {
        return view('proyek/menu/menu-rap');
    }

    public function store() {}
}