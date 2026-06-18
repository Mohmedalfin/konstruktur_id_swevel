<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use CodeIgniter\API\ResponseTrait;

class NotifikasiApiController extends BaseController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new NotificationModel();
    }

    private function getUserContext()
    {
        return [
            'id'   => session()->get('id_pengguna') ?? session()->get('id') ?? 0,
            'role' => session()->get('kategori_akun') ?? session()->get('role') ?? 'kontraktor'
        ];
    }

    public function index()
    {
        $context = $this->getUserContext();
        $notifList = $this->model->getForUser($context['id'], $context['role']);
        
        // Format waktu agar ramah dibaca manusia (human diff)
        foreach ($notifList as &$n) {
            $n['waktu'] = $this->humanTimeDiff(strtotime($n['created_at']));
        }
        
        return $this->respond($notifList);
    }

    public function getUnread()
    {
        $context = $this->getUserContext();
        $unreadCount = $this->model->getUnreadCount($context['id'], $context['role']);
        $recentUnread = $this->model->where('is_read', 0)
                                    ->groupStart()
                                        ->where('user_id', $context['id'])
                                        ->orWhere('role_target', strtolower($context['role']))
                                    ->groupEnd()
                                    ->orderBy('created_at', 'DESC')
                                    ->findAll(5);

        foreach ($recentUnread as &$n) {
            $n['waktu'] = $this->humanTimeDiff(strtotime($n['created_at']));
        }

        return $this->respond([
            'unread_count' => $unreadCount,
            'recent' => $recentUnread
        ]);
    }

    public function markAsRead($id)
    {
        $this->model->update($id, ['is_read' => 1]);
        return $this->respond(['success' => true]);
    }

    public function markAllAsRead()
    {
        $context = $this->getUserContext();
        $this->model->where('is_read', 0)
                    ->groupStart()
                        ->where('user_id', $context['id'])
                        ->orWhere('role_target', strtolower($context['role']))
                    ->groupEnd()
                    ->set(['is_read' => 1])
                    ->update();

        return $this->respond(['success' => true]);
    }

    public function delete($id)
    {
        $this->model->delete($id);
        return $this->respond(['success' => true]);
    }

    private function humanTimeDiff($timestamp)
    {
        $diff = time() - $timestamp;
        if ($diff < 60) return 'Baru saja';
        $mins = round($diff / 60);
        if ($mins < 60) return $mins . ' menit yang lalu';
        $hours = round($diff / 3600);
        if ($hours < 24) return $hours . ' jam yang lalu';
        $days = round($diff / 86400);
        if ($days < 7) return $days . ' hari yang lalu';
        return date('d M Y', $timestamp);
    }
}
