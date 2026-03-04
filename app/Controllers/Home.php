<?php

namespace App\Controllers;

use App\Models\RoleModel; // Import modelnya

class Home extends BaseController
{
    
    
    public function login()
    {
        return view('auth/loginUser');
    }
    
    public function register()
    {
        return view('auth/registerUser');
    }
}