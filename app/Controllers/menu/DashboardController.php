<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
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
        if ($redirect = $this->preserveContext('/dashboard')) return $redirect;
        return view('proyek/menu/dashboard');
    }

    public function create()
    {
        return view('proyek/menu/dashboard');
    }

    public function store() {}
}