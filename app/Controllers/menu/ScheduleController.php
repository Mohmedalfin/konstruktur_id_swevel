<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ScheduleController extends BaseController
{
    public function index()
    {
        return view('proyek/menu/menu-schedule');
    }
}   