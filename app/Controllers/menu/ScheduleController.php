<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use App\Services\ScheduleService;

class ScheduleController extends BaseController
{
    protected $scheduleService;

    public function __construct()
    {
        $this->scheduleService = new ScheduleService();
    }

    public function index($slug = null)
    {
        try {
            $data = $this->scheduleService->getSchedulePageData($slug);
            return view('proyek/menu/menu-schedule', $data);
        } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
            throw $e;
        } catch (\Throwable $e) {
            log_message('error', '[ScheduleController::index] ' . $e->getMessage());
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

            $categories = $this->scheduleService->getScheduleDataWithWeight($idProject);

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => [
                    'id_project' => $idProject,
                    'categories' => $categories,
                ],
            ]);
        } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Project tidak ditemukan',
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[ScheduleController::getData] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal pada server',
            ]);
        }
    }

    public function updateScheduleDates()
    {
        try {
            $payload = $this->request->getJSON(true) ?? [];
            $result = $this->scheduleService->updateScheduleDates($payload);

            return $this->response->setJSON($result);
        } catch (\InvalidArgumentException $e) {
            $statusCode = $e->getCode() ?: 400;
            return $this->response->setStatusCode($statusCode)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[ScheduleController::updateScheduleDates] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan internal pada server'
            ]);
        }
    }
}