<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = service('session');

        // Auto-login bypass & dynamic role switching untuk mode development
        $firstSegment = service('uri')->getSegment(1);
        log_message('error', "[DEBUG] BaseController initialized. firstSegment: '{$firstSegment}'");

        // Determinisasi role berdasarkan path halaman web
        // Fitur auto-switch role diaktifkan kembali dan disempurnakan (dinamis tanpa hardcode)
        $targetRole = null;
        if ($firstSegment === 'gudang') {
            $targetRole = 'gudang';
        } elseif ($firstSegment === 'purchasing') {
            $targetRole = 'purchasing';
        } elseif ($firstSegment === 'api') {
            // For API requests, check the referer to maintain the correct role session
            $referer = $this->request->getHeaderLine('referer');
            log_message('error', "[DEBUG] API Request Referer: '{$referer}'");
            if (strpos($referer, '/gudang-lapangan') !== false) {
                $targetRole = 'kontraktor';
            } elseif (strpos($referer, '/gudang') !== false) {
                $targetRole = 'gudang';
            } elseif (strpos($referer, '/purchasing') !== false) {
                $targetRole = 'purchasing';
            } else {
                $targetRole = 'kontraktor';
            }
        } elseif (in_array($firstSegment, [
            '', 'permintaan', 'proyek', 'dashboard', 'schedule', 
            'realisasi', 'kelola-akun', 'notifikasi'
        ], true)) {
            $targetRole = 'kontraktor';
        }

        if ($targetRole !== null) {
            $currentRole = strtolower((string)$this->session->get('kategori_akun'));
            $shouldSwitch = false;
            $userId = null;

            // Jika role saat ini tidak sesuai dengan target role, kita perlu switch
            if ($currentRole !== $targetRole || !$this->session->get('logged_in')) {
                $shouldSwitch = true;
                $db = \Config\Database::connect();
                
                // Cari user pertama yang memiliki role sesuai target
                // Untuk Gudang dan Purchasing
                $user = $db->table('pengguna')
                           ->where('kategori_akun', $targetRole)
                           ->orderBy('id_pengguna', 'DESC')
                           ->get()
                           ->getRow();
                           
                // Fallback khusus untuk kontraktor jika tidak nemu (biasanya ID 1)
                if (!$user && $targetRole === 'kontraktor') {
                    $user = $db->table('pengguna')->where('id_pengguna', 1)->get()->getRow();
                }

                if ($user) {
                    $userId = $user->id_pengguna;
                }
            }

            if ($shouldSwitch && $userId !== null) {
                $db = \Config\Database::connect();
                $user = $db->table('pengguna')->where('id_pengguna', $userId)->get()->getRow();
                if ($user) {
                    $id_perusahaan = !empty($user->parent_id) ? $user->parent_id : $user->id_pengguna;
                    $this->session->set([
                        'id_pengguna'   => $user->id_pengguna,
                        'id_user'       => $user->id_pengguna,
                        'nama_pengguna' => $user->nama_pengguna,
                        'nama'          => $user->nama_pengguna,
                        'username'      => $user->username,
                        'kategori_akun' => strtolower($user->kategori_akun),
                        'role'          => strtolower($user->kategori_akun),
                        'id_perusahaan' => $id_perusahaan,
                        'logged_in'     => true,
                    ]);
                    log_message('error', "[DEBUG] Session successfully switched to user ID {$userId} ({$user->nama_pengguna}) via dynamic query.");
                } else {
                    log_message('error', "[DEBUG] User ID {$userId} not found in database!");
                }
            }
        }
    }
}
