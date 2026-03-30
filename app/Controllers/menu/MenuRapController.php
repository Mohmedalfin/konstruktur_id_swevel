<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class MenuRapController extends BaseController
{
    /**
     * Mempertahankan state/URL slug proyek menggunakan session.
     * Jika link di sidebar diklik tanpa parameter ?slug=, user akan di-redirect ke URL yang benar.
     */
    private function preserveContext($path)
    {
        $slug = $this->request->getGet('slug');
        $mode = $this->request->getGet('mode');
        $session = session();

        if ($slug) {
            $session->set('active_project_slug', $slug);
            $session->set('active_project_mode', $mode ?? 'readonly');
            return null; 
        } elseif ($session->has('active_project_slug')) {
            return redirect()->to($path . '?mode=' . $session->get('active_project_mode') . '&slug=' . $session->get('active_project_slug'));
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->preserveContext('/menu-rap')) return $redirect;
        return view('proyek/menu/menu-rap');
    }

    public function rincianAHS()
    {
        if ($redirect = $this->preserveContext('/menu-rap/rincian-ahs')) return $redirect;
        return view('proyek/menu/main-ahs');
    }

    public function tambahAHS()
    {
        if ($redirect = $this->preserveContext('/menu-rap/tambah-ahs')) return $redirect;
        return view('proyek/menu/main-pekerjaan');
    }
}