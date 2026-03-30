<?php

namespace App\Controllers;

use App\Services\AuthService; // Import Service-nya

class AuthController extends BaseController
{
    protected $authService;

    public function __construct()
    {
        // Inisialisasi Service
        $this->authService = new AuthService();
    }

    public function register()
    {
        // Gunakan cache bawaan CI4 selama 24 jam (86400 detik) agar efisien & tak membebani server/API terus-menerus
        $provinces = cache('provinces_api');
        
        if (!$provinces) {
            try {
                // Timeout 3 detik untuk menghindari freeze ketika API bermasalah
                $ctx = stream_context_create(['http' => ['timeout' => 3]]);
                $json = @file_get_contents('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json', false, $ctx);
                
                if ($json) {
                    $provinces = json_decode($json, true);
                    cache()->save('provinces_api', $provinces, 86400); // Simpan 1 hari
                } else {
                    $provinces = []; // Fallback
                }
            } catch (\Exception $e) {
                $provinces = []; // Fallback saat error
            }
        }

        return view('auth/registerUser', ['provinces' => $provinces]);
    }

    public function login()
    {
        return view('auth/loginUser');
    }

    public function processRegister()
    {
        // 1. Controller HANYA mengurus Validasi HTTP Input
        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'email'        => 'required|valid_email|is_unique[users.email]',
            'password'     => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Controller melempar data ke Service Layer
        try {
            $this->authService->registerUser($this->request->getPost());
            
            // 3. Controller mengatur Respon HTTP
            return redirect()->to('/')->with('success', 'Akun berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
    
    public function processLogin()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        try {
            // Lempar ke service
            $this->authService->attemptLogin($email, $password);
            
            return $this->_respond(
                'success', 
                'Selamat datang kembali! Anda akan dialihkan...', 
                base_url('/proyek')
            );
        } catch (\Exception $e) {
            // Tangkap error (misal password salah)
            return $this->_respond('error', $e->getMessage(), null);
        }
    }
    private function _respond($status, $message, $redirectUrl = null)
    {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'   => $status,
                'message'  => $message,
                'redirect' => $redirectUrl
            ]);
        }

        if ($status === 'success') {
            // Jika fallback non-AJAX, redirect sesuai destinasi
            return redirect()->to($redirectUrl ?? base_url('/'))->with('success', $message);
        }

        return redirect()->back()->withInput()->with('error', $message);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/'))->with('success', 'Anda telah berhasil logout.');
    }
}