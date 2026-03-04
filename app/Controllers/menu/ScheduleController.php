<?php

namespace App\Controllers\Menu;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ScheduleController extends BaseController
{
    public function index()
    {
        return view('proyek/menu/menu-schedule');
    }
}
