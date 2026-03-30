<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek apakah session isLoggedIn bernilai true
        if (!session()->get('isLoggedIn')) {
            // Jika request berupa AJAX, kembalikan response JSON
            if ($request->isAJAX()) {
                return service('response')
                    ->setJSON(['status' => 'error', 'message' => 'Sesi berakhir, silakan login ulang.'])
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            // Jika akses dari URL browser biasa, redirect ke halaman login
            return redirect()->to(base_url('/'))->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada logic after yang dibutuhkan
    }
}
