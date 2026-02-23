<?php

namespace App\Controllers;

use App\Models\RoleModel; // Import modelnya

class Home extends BaseController
{
    public function index()
    {
        return view('auth/loginUser');
    }
}