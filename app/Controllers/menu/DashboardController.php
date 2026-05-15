<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use App\Services\DashboardService;
use CodeIgniter\Exceptions\PageNotFoundException;

class DashboardController extends BaseController
{
    protected $dashboardService;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
    }

    public function index($slug = null)
    {
        try {
            if (!$slug) {
                // Dashboard requires a project context. Redirect to project list.
                return redirect()->to(base_url('proyek'))->with('error', 'Silakan pilih proyek terlebih dahulu.');
            }

            $idProject = null;

            $proyekModel = new \App\Models\ProyekModel();
            $project = $proyekModel->where('slug', $slug)->first();

            if (!$project) {
                throw PageNotFoundException::forPageNotFound('Proyek tidak ditemukan.');
            }

            $idProject = $project['id_project'];

            return view('proyek/menu/menu-dashboard', [
                'idProject' => $idProject,
                'slug'      => $slug,
            ]);
        } catch (PageNotFoundException $e) {
            throw $e;
        } catch (\Throwable $e) {
            log_message('error', '[DashboardController::index] ' . $e->getMessage());
            throw $e;
        }
    }

    public function getData()
    {
        try {
            $idProject = (int) ($this->request->getGet('id_project') ?? 0);

            if ($idProject <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project wajib diisi',
                ]);
            }

            $overview = $this->dashboardService->getOverviewData($idProject);
            $summary  = $this->dashboardService->getRingkasanPekerjaan($idProject);
            $chart    = $this->dashboardService->getChartData($idProject);
            $projectMetrics = $this->dashboardService->getProjectMetrics($idProject, $chart);
            $costChart = $this->dashboardService->getCostChartData($idProject);

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => [
                    'overview'   => array_merge($overview, $projectMetrics),
                    'summary'    => $summary,
                    'chart'      => $chart,
                    'cost_chart' => $costChart,
                ],
            ]);
        } catch (PageNotFoundException $e) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Project tidak ditemukan',
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[DashboardController::getData] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal pada server',
            ]);
        }
    }

    public function getCategoryDetail($idKategori)
    {
        try {
            $idProject = (int) ($this->request->getGet('id_project') ?? 0);
            $categoryId = (int) $idKategori;

            if ($idProject <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project wajib diisi',
                ]);
            }

            if ($categoryId <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_kategori wajib diisi',
                ]);
            }

            $categoryMetrics = $this->dashboardService->getCategoryMetrics($idProject, $categoryId);

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => [
                    'id_project'       => $idProject,
                    'category_detail'  => $categoryMetrics,
                ],
            ]);
        } catch (PageNotFoundException $e) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Project tidak ditemukan',
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[DashboardController::getCategoryDetail] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal pada server',
            ]);
        }
    }
}
